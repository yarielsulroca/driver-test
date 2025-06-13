<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PreguntaModel;
use App\Models\RespuestaModel;

class PaginasController extends ResourceController
{
    protected $preguntaModel;
    protected $respuestaModel;

    public function __construct()
    {
        $this->preguntaModel = new PreguntaModel();
        $this->respuestaModel = new RespuestaModel();
    }

    public function index()
    {
        $categoria_id = $this->request->getGet('categoria_id');
        
        if (!$categoria_id) {
            return $this->fail('Se requiere el ID de la categoría');
        }

        // Obtener todas las preguntas de la categoría
        $preguntas = $this->preguntaModel->where('categoria_id', $categoria_id)->findAll();
        
        // Agrupar preguntas en páginas de 3
        $paginas = [];
        $preguntasPorPagina = 3;
        
        for ($i = 0; $i < count($preguntas); $i += $preguntasPorPagina) {
            $pagina = array_slice($preguntas, $i, $preguntasPorPagina);
            
            // Obtener las respuestas para cada pregunta
            foreach ($pagina as &$pregunta) {
                $pregunta['respuestas'] = $this->respuestaModel->where('pregunta_id', $pregunta['id'])->findAll();
            }
            
            $paginas[] = $pagina;
        }

        return $this->respond([
            'status' => 'success',
            'data' => $paginas
        ]);
    }

    public function show($id = null)
    {
        if (!$id) {
            return $this->fail('Se requiere el ID de la página');
        }

        // Calcular el rango de preguntas para esta página
        $inicio = ($id - 1) * 3;
        
        // Obtener las 3 preguntas de esta página
        $preguntas = $this->preguntaModel->limit(3, $inicio)->findAll();
        
        if (empty($preguntas)) {
            return $this->failNotFound('Página no encontrada');
        }

        // Obtener las respuestas para cada pregunta
        foreach ($preguntas as &$pregunta) {
            $pregunta['respuestas'] = $this->respuestaModel->where('pregunta_id', $pregunta['pregunta_id'])->findAll();
        }

        return $this->respond([
            'status' => 'success',
            'data' => $preguntas
        ]);
    }
} 