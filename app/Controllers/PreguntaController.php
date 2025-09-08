<?php

namespace App\Controllers;

use App\Models\PreguntaModel;
use App\Models\ExamenPreguntaModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class PreguntaController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $examenPreguntaModel;
    protected $db;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new PreguntaModel();
        $this->examenPreguntaModel = new ExamenPreguntaModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Listar todas las preguntas
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            $categoria_id = $this->request->getGet('categoria_id');
            $dificultad = $this->request->getGet('dificultad');
            $tipo = $this->request->getGet('tipo');
            $es_critica = $this->request->getGet('es_critica');
            $texto = $this->request->getGet('texto');
            
            // Construir query base
            $builder = $this->db->table('preguntas')
                ->select('preguntas.*, categorias.nombre as categoria_nombre, categorias.codigo as categoria_codigo')
                ->join('categorias', 'categorias.categoria_id = preguntas.categoria_id', 'left');

            // Aplicar filtros
            if ($categoria_id && $categoria_id !== '') {
                $builder->where('preguntas.categoria_id', $categoria_id);
            }
            if ($dificultad && $dificultad !== '') {
                $builder->where('preguntas.dificultad', $dificultad);
            }
            if ($tipo && $tipo !== '') {
                $builder->where('preguntas.tipo_pregunta', $tipo);
            }
            if ($es_critica !== null && $es_critica !== '') {
                $builder->where('preguntas.es_critica', $es_critica === 'true' ? 1 : 0);
            }
            if ($texto && $texto !== '') {
                $builder->like('preguntas.enunciado', $texto);
            }

            // Obtener total para paginación
            $total = $builder->countAllResults(false);
            
            // Aplicar paginación
            $offset = ($page - 1) * $perPage;
            $preguntas = $builder->limit($perPage, $offset)->get()->getResultArray();

            // Obtener las respuestas para cada pregunta
            $respuestaModel = new \App\Models\RespuestaModel();
            foreach ($preguntas as &$pregunta) {
                $respuestas = $respuestaModel->where('pregunta_id', $pregunta['pregunta_id'])->findAll();
                
                // Convertir es_correcta a boolean en cada respuesta
                foreach ($respuestas as &$respuesta) {
                    $respuesta['es_correcta'] = (bool)$respuesta['es_correcta'];
                }
                
                $pregunta['respuestas'] = $respuestas;
                
                // Convertir es_critica a boolean
                $pregunta['es_critica'] = (bool)$pregunta['es_critica'];
                
                // Obtener exámenes donde aparece esta pregunta
                try {
                    $examenes = $this->examenPreguntaModel->getExamenesPorPregunta($pregunta['pregunta_id']);
                    $pregunta['examenes'] = $examenes;
                } catch (\Exception $e) {
                    // Si hay error, asignar array vacío
                    $pregunta['examenes'] = [];
                }
            }

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'preguntas' => $preguntas,
                    'pagination' => [
                        'current_page' => (int)$page,
                        'last_page' => ceil($total / $perPage),
                        'per_page' => (int)$perPage,
                        'total' => $total
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
            
            // Convertir es_correcta a boolean en cada respuesta
            foreach ($respuestas as &$respuesta) {
                $respuesta['es_correcta'] = (bool)$respuesta['es_correcta'];
            }

            // Obtener la categoría
            $categoriaModel = new \App\Models\CategoriaModel();
            $categoria = $categoriaModel->find($pregunta['categoria_id']);

            // Obtener exámenes donde aparece esta pregunta
            $examenes = $this->examenPreguntaModel->getExamenesPorPregunta($id);

            // Agregar las relaciones a la pregunta
            $pregunta['respuestas'] = $respuestas;
            $pregunta['categoria'] = $categoria;
            $pregunta['examenes'] = $examenes;
            
            // Convertir es_critica a boolean
            $pregunta['es_critica'] = (bool)$pregunta['es_critica'];

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

            // Log para debugging
            log_message('info', 'Datos recibidos en create pregunta: ' . json_encode($data));

            // Validar datos requeridos
            if (!isset($data['enunciado']) || !isset($data['categoria_id']) || !isset($data['respuestas'])) {
                return $this->fail('Faltan datos requeridos: enunciado, categoria_id, respuestas', 400);
            }

            // Validar que haya al menos 2 respuestas
            if (count($data['respuestas']) < 2) {
                return $this->fail('Debe haber al menos 2 respuestas', 400);
            }

            // Validar que al menos una respuesta sea correcta
            $respuestasCorrectas = array_filter($data['respuestas'], function($resp) {
                return $resp['es_correcta'] ?? false;
            });
            
            if (empty($respuestasCorrectas)) {
                return $this->fail('Al menos una respuesta debe ser correcta', 400);
            }

            // Extraer las respuestas del data
            $respuestas = $data['respuestas'] ?? [];
            unset($data['respuestas']);

            // Mapear campos del frontend a los del modelo
            $preguntaData = [
                'categoria_id' => $data['categoria_id'],
                'enunciado' => $data['enunciado'],
                'tipo_pregunta' => $data['tipo'] ?? 'multiple',
                'puntaje' => $data['puntaje'] ?? 1,
                'dificultad' => $data['dificultad'] ?? 'medio',
                'es_critica' => isset($data['es_critica']) ? ($data['es_critica'] ? 1 : 0) : 0
            ];

            // Log para debugging
            log_message('info', 'Datos mapeados para pregunta: ' . json_encode($preguntaData));

            // Iniciar transacción
            $db = \Config\Database::connect();
            $db->transStart();

            if (!$this->model->insert($preguntaData)) {
                $db->transRollback();
                log_message('error', 'Error al insertar pregunta: ' . json_encode($this->model->errors()));
                
                // Devolver errores de validación específicos
                $errorMessages = [];
                foreach ($this->model->errors() as $field => $message) {
                    $errorMessages[$field] = $message;
                }
                
                return $this->fail([
                    'status' => 400,
                    'error' => 'Error de validación',
                    'messages' => $errorMessages
                ], 400);
            }

            $pregunta_id = $this->model->getInsertID();
            
            // Crear las respuestas
            if (!empty($respuestas)) {
                $respuestaModel = new \App\Models\RespuestaModel();
                foreach ($respuestas as $respuesta) {
                    $respuestaData = [
                        'pregunta_id' => $pregunta_id,
                        'texto' => $respuesta['texto'],
                        'es_correcta' => $respuesta['es_correcta'] ? 1 : 0,
                        'imagen' => $respuesta['imagen'] ?? null
                    ];
                    
                    if (!$respuestaModel->insert($respuestaData)) {
                        $db->transRollback();
                        log_message('error', 'Error al insertar respuesta: ' . json_encode($respuestaModel->errors()));
                        return $this->fail('Error al crear las respuestas', 500);
                    }
                }
            }

            // Si se especifican exámenes, asignar la pregunta a ellos
            if (isset($data['examenes']) && is_array($data['examenes'])) {
                foreach ($data['examenes'] as $examen_id) {
                    $this->examenPreguntaModel->insert([
                        'examen_id' => $examen_id,
                        'pregunta_id' => $pregunta_id,
                        'orden' => 1 // Orden por defecto
                    ]);
                }
            }

            if (!$db->transStatus()) {
                $db->transRollback();
                return $this->failServerError('Error en la transacción');
            }

            $db->transCommit();

            // Obtener la pregunta creada con sus relaciones
            $pregunta = $this->model->find($pregunta_id);
            $pregunta['respuestas'] = $respuestaModel->where('pregunta_id', $pregunta_id)->findAll();

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Pregunta creada exitosamente',
                'data' => $pregunta
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Excepción en create pregunta: ' . $e->getMessage());
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

            // Log para debugging
            log_message('info', 'Datos recibidos en update pregunta: ' . json_encode($data));

            // Validar datos requeridos
            if (!isset($data['enunciado']) || !isset($data['categoria_id']) || !isset($data['respuestas'])) {
                return $this->fail('Faltan datos requeridos: enunciado, categoria_id, respuestas', 400);
            }

            // Validar que haya al menos 2 respuestas
            if (count($data['respuestas']) < 2) {
                return $this->fail('Debe haber al menos 2 respuestas', 400);
            }

            // Validar que al menos una respuesta sea correcta
            $respuestasCorrectas = array_filter($data['respuestas'], function($resp) {
                return $resp['es_correcta'] ?? false;
            });
            
            if (empty($respuestasCorrectas)) {
                return $this->fail('Al menos una respuesta debe ser correcta', 400);
            }

            // Extraer las respuestas del data
            $respuestas = $data['respuestas'] ?? [];
            unset($data['respuestas']);

            // Mapear campos del frontend a los del modelo
            $preguntaData = [
                'categoria_id' => $data['categoria_id'],
                'enunciado' => $data['enunciado'],
                'tipo_pregunta' => $data['tipo'] ?? 'multiple',
                'puntaje' => $data['puntaje'] ?? 1,
                'dificultad' => $data['dificultad'] ?? 'medio',
                'es_critica' => isset($data['es_critica']) ? ($data['es_critica'] ? 1 : 0) : 0
            ];

            // Log para debugging
            log_message('info', 'Datos mapeados para actualizar pregunta: ' . json_encode($preguntaData));

            // Iniciar transacción
            $db = \Config\Database::connect();
            $db->transStart();

            if (!$this->model->update($id, $preguntaData)) {
                $db->transRollback();
                log_message('error', 'Error al actualizar pregunta: ' . json_encode($this->model->errors()));
                
                // Devolver errores de validación específicos
                $errorMessages = [];
                foreach ($this->model->errors() as $field => $message) {
                    $errorMessages[$field] = $message;
                }
                
                return $this->fail([
                    'status' => 400,
                    'error' => 'Error de validación',
                    'messages' => $errorMessages
                ], 400);
            }

            // Actualizar las respuestas si existen
            if (!empty($respuestas)) {
                $respuestaModel = new \App\Models\RespuestaModel();
                
                // Eliminar respuestas existentes
                $respuestaModel->where('pregunta_id', $id)->delete();
                
                // Crear nuevas respuestas
                foreach ($respuestas as $respuesta) {
                    $respuestaData = [
                        'pregunta_id' => $id,
                        'texto' => $respuesta['texto'],
                        'es_correcta' => $respuesta['es_correcta'] ? 1 : 0,
                        'imagen' => $respuesta['imagen'] ?? null
                    ];
                    
                    if (!$respuestaModel->insert($respuestaData)) {
                        $db->transRollback();
                        return $this->fail('Error al actualizar las respuestas', 500);
                    }
                }
            }

            // Actualizar asignaciones de exámenes si se especifican
            if (isset($data['examenes']) && is_array($data['examenes'])) {
                // Eliminar asignaciones existentes
                $this->examenPreguntaModel->eliminarAsignacionesPregunta($id);
                
                // Crear nuevas asignaciones
                foreach ($data['examenes'] as $examen_id) {
                    $this->examenPreguntaModel->insert([
                        'examen_id' => $examen_id,
                        'pregunta_id' => $id,
                        'orden' => 1 // Orden por defecto
                    ]);
                }
            }

            if (!$db->transStatus()) {
                $db->transRollback();
                return $this->failServerError('Error en la transacción');
            }

            $db->transCommit();

            $pregunta = $this->model->find($id);
            
            // Obtener las respuestas actualizadas
            if (!empty($respuestas)) {
                $respuestaModel = new \App\Models\RespuestaModel();
                $pregunta['respuestas'] = $respuestaModel->where('pregunta_id', $id)->findAll();
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Pregunta actualizada exitosamente',
                'data' => $pregunta
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Excepción en update pregunta: ' . $e->getMessage());
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

            // Iniciar transacción
            $db = \Config\Database::connect();
            $db->transStart();

            // Eliminar respuestas asociadas
            $respuestaModel = new \App\Models\RespuestaModel();
            $respuestaModel->where('pregunta_id', $id)->delete();

            // Eliminar asignaciones de exámenes
            $this->examenPreguntaModel->eliminarAsignacionesPregunta($id);

            // Eliminar la pregunta
            if (!$this->model->delete($id)) {
                $db->transRollback();
                return $this->fail('No se pudo eliminar la pregunta');
            }

            if (!$db->transStatus()) {
                $db->transRollback();
                return $this->failServerError('Error en la transacción');
            }

            $db->transCommit();

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Pregunta eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener preguntas por examen usando la tabla intermedia
     */
    public function porExamen($examen_id)
    {
        try {
            $preguntas = $this->examenPreguntaModel->getPreguntasPorExamen($examen_id);
            
            // Obtener respuestas para cada pregunta
            $respuestaModel = new \App\Models\RespuestaModel();
            foreach ($preguntas as &$pregunta) {
                $respuestas = $respuestaModel->where('pregunta_id', $pregunta['pregunta_id'])->findAll();
                
                // Convertir es_correcta a boolean en cada respuesta
                foreach ($respuestas as &$respuesta) {
                    $respuesta['es_correcta'] = (bool)$respuesta['es_correcta'];
                }
                
                $pregunta['respuestas'] = $respuestas;
                
                // Convertir es_critica a boolean
                $pregunta['es_critica'] = (bool)$pregunta['es_critica'];
            }
            
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
            $preguntas = $this->model->getPreguntasPorCategoria($categoria_id);
            
            // Obtener respuestas para cada pregunta
            $respuestaModel = new \App\Models\RespuestaModel();
            foreach ($preguntas as &$pregunta) {
                $respuestas = $respuestaModel->where('pregunta_id', $pregunta['pregunta_id'])->findAll();
                
                // Convertir es_correcta a boolean en cada respuesta
                foreach ($respuestas as &$respuesta) {
                    $respuesta['es_correcta'] = (bool)$respuesta['es_correcta'];
                }
                
                $pregunta['respuestas'] = $respuestas;
                
                // Convertir es_critica a boolean
                $pregunta['es_critica'] = (bool)$pregunta['es_critica'];
            }
            
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
            $preguntas = $this->model->getPreguntasCriticas();
            
            // Obtener respuestas para cada pregunta
            $respuestaModel = new \App\Models\RespuestaModel();
            foreach ($preguntas as &$pregunta) {
                $respuestas = $respuestaModel->where('pregunta_id', $pregunta['pregunta_id'])->findAll();
                
                // Convertir es_correcta a boolean en cada respuesta
                foreach ($respuestas as &$respuesta) {
                    $respuesta['es_correcta'] = (bool)$respuesta['es_correcta'];
                }
                
                $pregunta['respuestas'] = $respuestas;
                
                // Convertir es_critica a boolean
                $pregunta['es_critica'] = (bool)$pregunta['es_critica'];
            }
            
            return $this->respond([
                'status' => 'success',
                'data' => $preguntas
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Subir imagen para una respuesta
     */
    public function subirImagenRespuesta()
    {
        try {
            $rules = [
                'imagen' => 'uploaded[imagen]|max_size[imagen,2048]|is_image[imagen]'
            ];

            if (!$this->validate($rules)) {
                return $this->fail($this->validator->getErrors());
            }

            $file = $this->request->getFile('imagen');
            
            if (!$file->isValid() || $file->hasMoved()) {
                return $this->fail('Archivo inválido');
            }

            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/respuestas', $newName);

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'filename' => $newName,
                    'path' => 'uploads/respuestas/' . $newName
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener imagen de respuesta
     */
    public function obtenerImagenRespuesta($filename)
    {
        try {
            $path = WRITEPATH . 'uploads/respuestas/' . $filename;
            
            if (!file_exists($path)) {
                return $this->failNotFound('Imagen no encontrada');
            }

            $file = new \CodeIgniter\Files\File($path);
            $mime = $file->getMimeType();

            return $this->response->setContentType($mime)
                                ->setBody(file_get_contents($path));
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
} 