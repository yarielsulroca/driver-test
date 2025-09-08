<?php

namespace App\Controllers;

use App\Models\EscuelaModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class EscuelaController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new EscuelaModel();
    }

    /**
     * Listar todas las escuelas
     */
    public function index()
    {
        try {
            $escuelas = $this->model->where('estado', 'activo')->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $escuelas
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Mostrar una escuela específica
     */
    public function show($id = null)
    {
        try {
            $escuela = $this->model->find($id);

            if (!$escuela) {
                return $this->failNotFound('Escuela no encontrada');
            }

            return $this->respond([
                'status' => 'success',
                'data' => $escuela
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Crear una nueva escuela
     */
    public function create()
    {
        try {
            // Log del raw input para debugging
            $rawInput = $this->request->getBody();
            log_message('debug', 'Raw input: ' . $rawInput);
            
            $json = $this->request->getJSON(true);
            
            // Log del JSON decodificado
            log_message('debug', 'JSON decoded: ' . json_encode($json));
            
            if (empty($json)) {
                log_message('error', 'JSON vacío o inválido');
                return $this->fail([
                    'status' => 'error',
                    'message' => 'No se recibieron datos JSON válidos'
                ], 400);
            }

            // Limpiar espacios extra de los campos
            $json = array_map(function($value) {
                return is_string($value) ? trim($value) : $value;
            }, $json);

            // Validar campos requeridos
            $rules = [
                'nombre' => [
                    'rules' => 'required|min_length[3]|max_length[100]',
                    'errors' => [
                        'required' => 'El nombre es obligatorio',
                        'min_length' => 'El nombre debe tener al menos 3 caracteres',
                        'max_length' => 'El nombre no puede exceder los 100 caracteres'
                    ]
                ],
                'direccion' => [
                    'rules' => 'required|min_length[5]|max_length[255]',
                    'errors' => [
                        'required' => 'La dirección es obligatoria',
                        'min_length' => 'La dirección debe tener al menos 5 caracteres',
                        'max_length' => 'La dirección no puede exceder los 255 caracteres'
                    ]
                ],
                'ciudad' => [
                    'rules' => 'required|min_length[2]|max_length[100]',
                    'errors' => [
                        'required' => 'La ciudad es obligatoria',
                        'min_length' => 'La ciudad debe tener al menos 2 caracteres',
                        'max_length' => 'La ciudad no puede exceder los 100 caracteres'
                    ]
                ],
                'telefono' => [
                    'rules' => 'required|min_length[8]|max_length[15]',
                    'errors' => [
                        'required' => 'El teléfono es obligatorio',
                        'min_length' => 'El teléfono debe tener al menos 8 caracteres',
                        'max_length' => 'El teléfono no puede exceder los 15 caracteres'
                    ]
                ],
                'email' => [
                    'rules' => 'required|valid_email|max_length[100]|is_unique[escuelas.email]',
                    'errors' => [
                        'required' => 'El email es obligatorio',
                        'valid_email' => 'El formato del email no es válido',
                        'max_length' => 'El email no puede exceder los 100 caracteres',
                        'is_unique' => 'Ya existe una escuela registrada con este email'
                    ]
                ]
            ];

            // Validar los datos
            $validation = \Config\Services::validation();
            $validation->setRules($rules);

            if (!$validation->run($json)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ], 400);
            }

            // Preparar datos para inserción
            $escuelaData = [
                'nombre' => $json['nombre'],
                'direccion' => $json['direccion'],
                'ciudad' => $json['ciudad'],
                'telefono' => $json['telefono'],
                'email' => $json['email'],
                'estado' => isset($json['estado']) ? $json['estado'] : 'activo'
            ];

            if (!$this->model->insert($escuelaData)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al crear la escuela',
                    'errors' => $this->model->errors()
                ], 400);
            }

            $escuela_id = $this->model->getInsertID();
            $escuela = $this->model->find($escuela_id);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Escuela creada exitosamente',
                'data' => $escuela
            ]);

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Actualizar una escuela existente
     */
    public function update($id = null)
    {
        try {
            $escuela = $this->model->find($id);
            if (!$escuela) {
                return $this->failNotFound('Escuela no encontrada');
            }

            $json = $this->request->getJSON(true);
            
            if (empty($json)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'No se recibieron datos JSON válidos'
                ], 400);
            }

            // Limpiar espacios extra de los campos
            $json = array_map(function($value) {
                return is_string($value) ? trim($value) : $value;
            }, $json);

            // Determinar si es una actualización completa (PUT) o parcial (PATCH)
            $isCompleteUpdate = $this->request->getMethod() === 'PUT';
            
            // Para actualizaciones parciales (PATCH), solo validar los campos enviados
            // Para actualizaciones completas (PUT), validar todos los campos requeridos
            $rules = [];
            $escuelaData = [];
            
            // Campos que pueden ser actualizados
            $updatableFields = ['nombre', 'direccion', 'ciudad', 'telefono', 'email', 'estado'];
            
            foreach ($updatableFields as $field) {
                if (isset($json[$field])) {
                    $escuelaData[$field] = $json[$field];
                    
                    // Definir reglas de validación según el campo
                    switch ($field) {
                        case 'nombre':
                            $rules['nombre'] = [
                                'rules' => 'required|min_length[3]|max_length[100]',
                                'errors' => [
                                    'required' => 'El nombre es obligatorio',
                                    'min_length' => 'El nombre debe tener al menos 3 caracteres',
                                    'max_length' => 'El nombre no puede exceder los 100 caracteres'
                                ]
                            ];
                            break;
                        case 'direccion':
                            $rules['direccion'] = [
                                'rules' => 'required|min_length[5]|max_length[255]',
                                'errors' => [
                                    'required' => 'La dirección es obligatoria',
                                    'min_length' => 'La dirección debe tener al menos 5 caracteres',
                                    'max_length' => 'La dirección no puede exceder los 255 caracteres'
                                ]
                            ];
                            break;
                        case 'ciudad':
                            $rules['ciudad'] = [
                                'rules' => 'required|min_length[2]|max_length[100]',
                                'errors' => [
                                    'required' => 'La ciudad es obligatoria',
                                    'min_length' => 'La ciudad debe tener al menos 2 caracteres',
                                    'max_length' => 'La ciudad no puede exceder los 100 caracteres'
                                ]
                            ];
                            break;
                        case 'telefono':
                            $rules['telefono'] = [
                                'rules' => 'required|min_length[8]|max_length[15]',
                                'errors' => [
                                    'required' => 'El teléfono es obligatorio',
                                    'min_length' => 'El teléfono debe tener al menos 8 caracteres',
                                    'max_length' => 'El teléfono no puede exceder los 15 caracteres'
                                ]
                            ];
                            break;
                        case 'email':
                            $rules['email'] = [
                                'rules' => "required|valid_email|max_length[100]|is_unique[escuelas.email,escuela_id,{$id}]",
                                'errors' => [
                                    'required' => 'El email es obligatorio',
                                    'valid_email' => 'El formato del email no es válido',
                                    'max_length' => 'El email no puede exceder los 100 caracteres',
                                    'is_unique' => 'Ya existe otra escuela registrada con este email'
                                ]
                            ];
                            break;
                        case 'estado':
                            $rules['estado'] = [
                                'rules' => 'required|in_list[activo,inactivo]',
                                'errors' => [
                                    'required' => 'El estado es obligatorio',
                                    'in_list' => 'El estado debe ser activo o inactivo'
                                ]
                            ];
                            break;
                    }
                } elseif ($isCompleteUpdate) {
                    // Para PUT, todos los campos son requeridos
                    return $this->fail([
                        'status' => 'error',
                        'message' => 'Para actualización completa (PUT), todos los campos son requeridos',
                        'missing_fields' => array_diff($updatableFields, array_keys($json))
                    ], 400);
                }
            }

            // Si no hay campos para actualizar
            if (empty($escuelaData)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'No se proporcionaron campos válidos para actualizar'
                ], 400);
            }

            // Validar los datos
            $validation = \Config\Services::validation();
            $validation->setRules($rules);

            if (!$validation->run($escuelaData)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ], 400);
            }

            if (!$this->model->update($id, $escuelaData)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al actualizar la escuela',
                    'errors' => $this->model->errors()
                ], 400);
            }

            $escuelaActualizada = $this->model->find($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Escuela actualizada exitosamente',
                'data' => $escuelaActualizada
            ]);

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Eliminar una escuela
     */
    public function delete($id = null)
    {
        try {
            $escuela = $this->model->find($id);
            if (!$escuela) {
                return $this->failNotFound('Escuela no encontrada');
            }

            // En lugar de eliminar, cambiar el estado a inactivo
            if (!$this->model->update($id, ['estado' => 'inactivo'])) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al desactivar la escuela'
                ], 400);
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Escuela desactivada exitosamente'
            ]);

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
} 