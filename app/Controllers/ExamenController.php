<?php

namespace App\Controllers;

use App\Models\ExamenModel;
use App\Models\PreguntaModel;
use App\Models\ExamenPreguntaModel;
use App\Models\ExamenCategoriaModel;
use App\Models\RespuestaModel;
use App\Models\ExamenConductorModel;
use App\Models\RespuestaConductorModel;
use App\Models\CategoriaAprobadaModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\BaseConnection;

class ExamenController extends ResourceController
{
    use ResponseTrait;

    protected $examenModel;
    protected $preguntaModel;
    protected $examenPreguntaModel;
    protected $examenCategoriaModel;
    protected $respuestaModel;
    protected $examenConductorModel;
    protected $respuestaConductorModel;
    protected $categoriaAprobadaModel;
    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->examenModel = new ExamenModel();
        $this->preguntaModel = new PreguntaModel();
        $this->examenPreguntaModel = new ExamenPreguntaModel();
        $this->examenCategoriaModel = new ExamenCategoriaModel();
        $this->respuestaModel = new RespuestaModel();
        $this->examenConductorModel = new ExamenConductorModel();
        $this->respuestaConductorModel = new RespuestaConductorModel();
        $this->categoriaAprobadaModel = new CategoriaAprobadaModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Listar todos los exámenes
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            
            $examenes = $this->examenModel->paginate($perPage, 'default', $page);
            $pager = $this->examenModel->pager;

            // Obtener categorías para cada examen
            foreach ($examenes as &$examen) {
                $examen['categorias'] = $this->examenCategoriaModel
                    ->select('categorias.*')
                    ->join('categorias', 'categorias.categoria_id = examen_categoria.categoria_id')
                    ->where('examen_categoria.examen_id', $examen['examen_id'])
                    ->findAll();
            }

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'examenes' => $examenes,
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
     * Obtener un examen específico con sus preguntas
     */
    public function show($id = null)
    {
        try {
            $examen = $this->examenModel->find($id);
            
            if (!$examen) {
                return $this->failNotFound('Examen no encontrado');
            }

            // Obtener las categorías del examen
            $examen['categorias'] = $this->examenCategoriaModel
                ->select('categorias.*')
                ->join('categorias', 'categorias.categoria_id = examen_categoria.categoria_id')
                ->where('examen_categoria.examen_id', $id)
                ->findAll();

            // Obtener las preguntas del examen con sus respuestas
            $preguntas = $this->examenPreguntaModel->select('examen_pregunta.*, preguntas.*')
                                                 ->join('preguntas', 'preguntas.pregunta_id = examen_pregunta.pregunta_id')
                                                 ->where('examen_pregunta.examen_id', $id)
                                                 ->orderBy('examen_pregunta.orden', 'ASC')
                                                 ->findAll();

            // Obtener las respuestas para cada pregunta
            foreach ($preguntas as &$pregunta) {
                $pregunta['respuestas'] = $this->respuestaModel
                    ->where('pregunta_id', $pregunta['pregunta_id'])
                    ->findAll();
            }

            $examen['preguntas'] = $preguntas;

            return $this->respond([
                'status' => 'success',
                'data' => $examen
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Crear un nuevo examen
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->fail('No se recibieron datos', 400);
            }

            // Validar datos requeridos
            if (!isset($data['nombre']) || !isset($data['categorias']) || !isset($data['preguntas'])) {
                return $this->fail('Faltan datos requeridos', 400);
            }

            // Validar que existan las preguntas
            foreach ($data['preguntas'] as $pregunta) {
                if (!isset($pregunta['pregunta_id']) || !isset($pregunta['orden'])) {
                    return $this->fail('Formato de preguntas inválido', 400);
                }
            }

            // Iniciar transacción
            $this->db->transStart();

            // Insertar el examen
            $examenData = [
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'],
                'duracion_minutos' => $data['duracion_minutos'],
                'puntaje_minimo' => $data['puntaje_minimo'] ?? 70.00,
                'numero_preguntas' => count($data['preguntas'])
            ];

            if (!$this->examenModel->insert($examenData)) {
                return $this->fail($this->examenModel->errors());
            }

            $examen_id = $this->examenModel->getInsertID();

            // Asignar categorías al examen
            foreach ($data['categorias'] as $categoria_id) {
                $this->examenCategoriaModel->insert([
                    'examen_id' => $examen_id,
                    'categoria_id' => $categoria_id
                ]);
            }

            // Asignar preguntas al examen
            foreach ($data['preguntas'] as $pregunta) {
                $this->examenPreguntaModel->insert([
                    'examen_id' => $examen_id,
                    'pregunta_id' => $pregunta['pregunta_id'],
                    'orden' => $pregunta['orden']
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->fail('Error al crear el examen');
            }

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Examen creado exitosamente',
                'data' => $this->show($examen_id)
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Actualizar un examen existente
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data || !$id) {
                return $this->fail('Datos inválidos', 400);
            }

            $examenExiste = $this->examenModel->find($id);
            if (!$examenExiste) {
                return $this->failNotFound('Examen no encontrado');
            }

            // Iniciar transacción
            $this->db->transStart();

            // Actualizar datos básicos del examen
            $examenData = [
                'nombre' => $data['nombre'] ?? $examenExiste['nombre'],
                'descripcion' => $data['descripcion'] ?? $examenExiste['descripcion'],
                'fecha_inicio' => $data['fecha_inicio'] ?? $examenExiste['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'] ?? $examenExiste['fecha_fin'],
                'duracion_minutos' => $data['duracion_minutos'] ?? $examenExiste['duracion_minutos'],
                'puntaje_minimo' => $data['puntaje_minimo'] ?? $examenExiste['puntaje_minimo']
            ];

            if (!$this->examenModel->update($id, $examenData)) {
                return $this->fail($this->examenModel->errors());
            }

            // Si se proporcionan nuevas categorías, actualizarlas
            if (isset($data['categorias'])) {
                // Eliminar categorías anteriores
                $this->examenCategoriaModel->where('examen_id', $id)->delete();

                // Insertar nuevas categorías
                foreach ($data['categorias'] as $categoria_id) {
                    $this->examenCategoriaModel->insert([
                        'examen_id' => $id,
                        'categoria_id' => $categoria_id
                    ]);
                }
            }

            // Si se proporcionan nuevas preguntas, actualizar el orden
            if (isset($data['preguntas'])) {
                // Eliminar asignaciones anteriores
                $this->examenPreguntaModel->where('examen_id', $id)->delete();

                // Insertar nuevas asignaciones
                foreach ($data['preguntas'] as $pregunta) {
                    $this->examenPreguntaModel->insert([
                        'examen_id' => $id,
                        'pregunta_id' => $pregunta['pregunta_id'],
                        'orden' => $pregunta['orden']
                    ]);
                }

                // Actualizar número de preguntas
                $this->examenModel->update($id, [
                    'numero_preguntas' => count($data['preguntas'])
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->fail('Error al actualizar el examen');
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Examen actualizado exitosamente',
                'data' => $this->show($id)
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Eliminar un examen
     */
    public function delete($id = null)
    {
        try {
            $examen = $this->examenModel->find($id);
            
            if (!$examen) {
                return $this->failNotFound('Examen no encontrado');
            }

            // Iniciar transacción
            $this->db->transStart();

            // Eliminar relaciones
            $this->examenCategoriaModel->where('examen_id', $id)->delete();
            $this->examenPreguntaModel->where('examen_id', $id)->delete();
            
            // Eliminar el examen
            $this->examenModel->delete($id);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->fail('Error al eliminar el examen');
            }

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Examen eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener exámenes por categoría
     */
    public function porCategoria($categoria_id)
    {
        try {
            $examenes = $this->examenCategoriaModel
                ->select('examenes.*')
                ->join('examenes', 'examenes.examen_id = examen_categoria.examen_id')
                ->where('examen_categoria.categoria_id', $categoria_id)
                ->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $examenes
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener exámenes activos
     */
    public function activos()
    {
        try {
            $fechaActual = date('Y-m-d H:i:s');
            
            $examenes = $this->examenModel
                ->where('fecha_inicio <=', $fechaActual)
                ->where('fecha_fin >=', $fechaActual)
                ->findAll();

            // Obtener categorías para cada examen
            foreach ($examenes as &$examen) {
                $examen['categorias'] = $this->examenCategoriaModel
                    ->select('categorias.*')
                    ->join('categorias', 'categorias.categoria_id = examen_categoria.categoria_id')
                    ->where('examen_categoria.examen_id', $examen['examen_id'])
                    ->findAll();
            }

            return $this->respond([
                'status' => 'success',
                'data' => $examenes
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
} 