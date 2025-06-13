<?php

namespace App\Controllers;

use App\Models\PreguntaModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class PreguntaController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new PreguntaModel();
    }

    /**
     * Listar todas las preguntas
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            
            $preguntas = $this->model->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'preguntas' => $preguntas,
                    'pagination' => [
                        'current_page' => $pager->getCurrentPage(),
                        'total_pages' => $pager->getPageCount(),
                        'total_items' => $pager->getTotal(),
                        'per_page' => $perPage
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener una pregunta específica
     */
    public function show($id = null)
    {
        try {
            $pregunta = $this->model->find($id);
            
            if (!$pregunta) {
                return $this->failNotFound('Pregunta no encontrada');
            }

            // Obtener las respuestas asociadas a la pregunta
            $respuestaModel = new \App\Models\RespuestaModel();
            $respuestas = $respuestaModel->where('pregunta_id', $id)->findAll();

            // Agregar las respuestas a la pregunta
            $pregunta['respuestas'] = $respuestas;

            return $this->respond([
                'status' => 'success',
                'data' => $pregunta
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Crear una nueva pregunta
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->fail('No se recibieron datos', 400);
            }

            if (!$this->model->insert($data)) {
                return $this->fail($this->model->errors());
            }

            $pregunta_id = $this->model->getInsertID();
            $pregunta = $this->model->find($pregunta_id);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Pregunta creada exitosamente',
                'data' => $pregunta
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Actualizar una pregunta existente
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data || !$id) {
                return $this->fail('Datos inválidos', 400);
            }

            $preguntaExiste = $this->model->find($id);
            if (!$preguntaExiste) {
                return $this->failNotFound('Pregunta no encontrada');
            }

            if (!$this->model->update($id, $data)) {
                return $this->fail($this->model->errors());
            }

            $pregunta = $this->model->find($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Pregunta actualizada exitosamente',
                'data' => $pregunta
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Eliminar una pregunta
     */
    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('ID no proporcionado', 400);
            }

            $preguntaExiste = $this->model->find($id);
            if (!$preguntaExiste) {
                return $this->failNotFound('Pregunta no encontrada');
            }

            if (!$this->model->delete($id)) {
                return $this->fail('No se pudo eliminar la pregunta');
            }

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Pregunta eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener preguntas por examen
     */
    public function porExamen($examen_id)
    {
        try {
            $preguntas = $this->model->where('examen_id', $examen_id)->findAll();
            
            return $this->respond([
                'status' => 'success',
                'data' => $preguntas
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener preguntas por categoría
     */
    public function porCategoria($categoria_id)
    {
        try {
            $preguntas = $this->model->where('categoria_id', $categoria_id)->findAll();
            
            return $this->respond([
                'status' => 'success',
                'data' => $preguntas
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener preguntas críticas
     */
    public function criticas()
    {
        try {
            $preguntas = $this->model->where('es_critica', 1)->findAll();
            
            return $this->respond([
                'status' => 'success',
                'data' => $preguntas
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
} 