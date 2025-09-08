<?php

namespace App\Controllers;

use App\Models\RespuestaModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class RespuestaController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new RespuestaModel();
    }

    /**
     * Listar todas las respuestas
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            $preguntaId = $this->request->getGet('pregunta_id');
            
            $query = $this->model;
            
            if ($preguntaId) {
                $query = $query->where('pregunta_id', $preguntaId);
            }
            
            $respuestas = $query->paginate($perPage, 'default', $page);
            $pager = $query->pager;

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'respuestas' => $respuestas,
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
     * Obtener una respuesta específica
     */
    public function show($id = null)
    {
        try {
            $respuesta = $this->model->find($id);
            
            if (!$respuesta) {
                return $this->failNotFound('Respuesta no encontrada');
            }

            return $this->respond([
                'status' => 'success',
                'data' => $respuesta
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Crear una nueva respuesta
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->fail('No se recibieron datos', 400);
            }

            // Log para debugging
            log_message('info', 'Datos recibidos en create respuesta: ' . json_encode($data));

            // Validar datos requeridos
            if (!isset($data['pregunta_id']) || !isset($data['texto'])) {
                return $this->fail('Faltan datos requeridos: pregunta_id, texto', 400);
            }

            // Validar que la pregunta existe
            $preguntaModel = new \App\Models\PreguntaModel();
            $pregunta = $preguntaModel->find($data['pregunta_id']);
            if (!$pregunta) {
                return $this->fail('La pregunta especificada no existe', 400);
            }

            // Preparar datos para inserción
            $respuestaData = [
                'pregunta_id' => $data['pregunta_id'],
                'texto' => $data['texto'],
                'es_correcta' => isset($data['es_correcta']) ? ($data['es_correcta'] ? 1 : 0) : 0
            ];

            // Si se proporciona imagen
            if (isset($data['imagen'])) {
                $respuestaData['imagen'] = $data['imagen'];
            }

            // Insertar la respuesta
            $respuestaId = $this->model->insert($respuestaData);
            
            if (!$respuestaId) {
                return $this->fail('Error al crear la respuesta', 500);
            }

            // Obtener la respuesta creada
            $respuestaCreada = $this->model->find($respuestaId);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Respuesta creada exitosamente',
                'data' => $respuestaCreada
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al crear respuesta: ' . $e->getMessage());
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Actualizar una respuesta existente
     */
    public function update($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('ID de respuesta no proporcionado', 400);
            }

            $respuesta = $this->model->find($id);
            if (!$respuesta) {
                return $this->failNotFound('Respuesta no encontrada');
            }

            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->fail('No se recibieron datos', 400);
            }

            // Validar que la pregunta existe si se está actualizando
            if (isset($data['pregunta_id'])) {
                $preguntaModel = new \App\Models\PreguntaModel();
                $pregunta = $preguntaModel->find($data['pregunta_id']);
                if (!$pregunta) {
                    return $this->fail('La pregunta especificada no existe', 400);
                }
            }

            // Preparar datos para actualización
            $updateData = [];
            
            if (isset($data['texto'])) {
                $updateData['texto'] = $data['texto'];
            }
            
            if (isset($data['es_correcta'])) {
                $updateData['es_correcta'] = $data['es_correcta'] ? 1 : 0;
            }
            
            if (isset($data['imagen'])) {
                $updateData['imagen'] = $data['imagen'];
            }
            
            if (isset($data['pregunta_id'])) {
                $updateData['pregunta_id'] = $data['pregunta_id'];
            }

            if (empty($updateData)) {
                return $this->fail('No hay datos para actualizar', 400);
            }

            // Actualizar la respuesta
            $result = $this->model->update($id, $updateData);
            
            if (!$result) {
                return $this->fail('Error al actualizar la respuesta', 500);
            }

            // Obtener la respuesta actualizada
            $respuestaActualizada = $this->model->find($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Respuesta actualizada exitosamente',
                'data' => $respuestaActualizada
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar respuesta: ' . $e->getMessage());
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Eliminar una respuesta
     */
    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('ID de respuesta no proporcionado', 400);
            }

            $respuesta = $this->model->find($id);
            if (!$respuesta) {
                return $this->failNotFound('Respuesta no encontrada');
            }

            // Verificar si la respuesta está siendo usada
            $respuestaConductorModel = new \App\Models\RespuestaConductorModel();
            $uso = $respuestaConductorModel->where('respuesta_id', $id)->countAllResults();
            
            if ($uso > 0) {
                return $this->fail('No se puede eliminar la respuesta porque está siendo utilizada', 400);
            }

            // Eliminar la respuesta
            $result = $this->model->delete($id);
            
            if (!$result) {
                return $this->fail('Error al eliminar la respuesta', 500);
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Respuesta eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar respuesta: ' . $e->getMessage());
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener respuestas por pregunta
     */
    public function porPregunta($preguntaId = null)
    {
        try {
            if (!$preguntaId) {
                return $this->fail('ID de pregunta no proporcionado', 400);
            }

            // Verificar que la pregunta existe
            $preguntaModel = new \App\Models\PreguntaModel();
            $pregunta = $preguntaModel->find($preguntaId);
            if (!$pregunta) {
                return $this->failNotFound('Pregunta no encontrada');
            }

            $respuestas = $this->model->where('pregunta_id', $preguntaId)->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'pregunta' => $pregunta,
                    'respuestas' => $respuestas
                ]
            ]);

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener respuestas correctas por pregunta
     */
    public function correctas($preguntaId = null)
    {
        try {
            if (!$preguntaId) {
                return $this->fail('ID de pregunta no proporcionado', 400);
            }

            $respuestas = $this->model->where('pregunta_id', $preguntaId)
                                    ->where('es_correcta', 1)
                                    ->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $respuestas
            ]);

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
} 