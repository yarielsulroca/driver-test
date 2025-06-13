<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\EscuelaModel;

class EscuelaController extends ResourceController
{
    protected $escuelaModel;

    public function __construct()
    {
        $this->escuelaModel = new EscuelaModel();
    }

    public function index()
    {
        $escuelas = $this->escuelaModel->findAll();
        return $this->respond($escuelas);
    }

    public function show($id = null)
    {
        $escuela = $this->escuelaModel->find($id);
        if (!$escuela) {
            return $this->failNotFound('Escuela no encontrada');
        }
        return $this->respond($escuela);
    }

    public function create()
    {
        $rules = [
            'codigo' => [
                'rules' => 'required|min_length[1]|max_length[10]|is_unique[escuelas.codigo]',
                'errors' => [
                    'required' => 'El código es obligatorio',
                    'min_length' => 'El código debe tener al menos 1 carácter',
                    'max_length' => 'El código no puede tener más de 10 caracteres',
                    'is_unique' => 'Ya existe una escuela con este código'
                ]
            ],
            'nombre' => [
                'rules' => 'required|min_length[1]|max_length[100]|is_unique[escuelas.nombre]',
                'errors' => [
                    'required' => 'El nombre es obligatorio',
                    'min_length' => 'El nombre debe tener al menos 1 carácter',
                    'max_length' => 'El nombre no puede tener más de 100 caracteres',
                    'is_unique' => 'Ya existe una escuela con este nombre'
                ]
            ],
            'direccion' => [
                'rules' => 'required|min_length[1]|max_length[255]',
                'errors' => [
                    'required' => 'La dirección es obligatoria',
                    'min_length' => 'La dirección debe tener al menos 1 carácter',
                    'max_length' => 'La dirección no puede tener más de 255 caracteres'
                ]
            ],
            'telefono' => [
                'rules' => 'required|min_length[1]|max_length[20]',
                'errors' => [
                    'required' => 'El teléfono es obligatorio',
                    'min_length' => 'El teléfono debe tener al menos 1 carácter',
                    'max_length' => 'El teléfono no puede tener más de 20 caracteres'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|max_length[100]',
                'errors' => [
                    'required' => 'El email es obligatorio',
                    'valid_email' => 'El email debe ser válido',
                    'max_length' => 'El email no puede tener más de 100 caracteres'
                ]
            ],
            'horario' => [
                'rules' => 'required|min_length[1]|max_length[100]',
                'errors' => [
                    'required' => 'El horario es obligatorio',
                    'min_length' => 'El horario debe tener al menos 1 carácter',
                    'max_length' => 'El horario no puede tener más de 100 caracteres'
                ]
            ],
            'estado' => [
                'rules' => 'required|in_list[activo,inactivo]',
                'errors' => [
                    'required' => 'El estado es obligatorio',
                    'in_list' => 'El estado debe ser activo o inactivo'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $response = [
                'status' => 400,
                'error' => true,
                'messages' => $errors
            ];
            return $this->fail($response);
        }

        $data = [
            'codigo' => $this->request->getVar('codigo'),
            'nombre' => $this->request->getVar('nombre'),
            'direccion' => $this->request->getVar('direccion'),
            'telefono' => $this->request->getVar('telefono'),
            'email' => $this->request->getVar('email'),
            'horario' => $this->request->getVar('horario'),
            'estado' => $this->request->getVar('estado')
        ];

        try {
            $this->escuelaModel->insert($data);
            $response = [
                'status' => 201,
                'error' => null,
                'messages' => [
                    'success' => 'Escuela creada exitosamente'
                ]
            ];
            return $this->respondCreated($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 500,
                'error' => true,
                'messages' => [
                    'error' => 'Error al crear la escuela: ' . $e->getMessage()
                ]
            ];
            return $this->fail($response);
        }
    }

    public function update($id = null)
    {
        $escuela = $this->escuelaModel->find($id);
        if (!$escuela) {
            return $this->failNotFound('Escuela no encontrada');
        }

        // Obtener los datos actuales de la escuela
        $currentData = $this->escuelaModel->find($id);
        
        // Obtener los datos del request
        $requestData = [
            'nombre' => $this->request->getVar('nombre'),
            'direccion' => $this->request->getVar('direccion'),
            'telefono' => $this->request->getVar('telefono'),
            'email' => $this->request->getVar('email'),
            'horario' => $this->request->getVar('horario'),
            'estado' => $this->request->getVar('estado')
        ];

        // Si se proporciona un código, agregarlo a los datos
        if ($this->request->getVar('codigo')) {
            $requestData['codigo'] = $this->request->getVar('codigo');
        }

        // Verificar si los campos han cambiado
        $codigoChanged = isset($requestData['codigo']) && $requestData['codigo'] !== $currentData['codigo'];
        $nombreChanged = $requestData['nombre'] !== $currentData['nombre'];
        $emailChanged = $requestData['email'] !== $currentData['email'];

        // Si el nombre no ha cambiado, mantener el actual
        if (!$nombreChanged) {
            $requestData['nombre'] = $currentData['nombre'];
        }

        // Si el email no ha cambiado, mantener el actual
        if (!$emailChanged) {
            $requestData['email'] = $currentData['email'];
        }

        $rules = [
            'nombre' => [
                'rules' => 'required|min_length[1]|max_length[100]' . ($nombreChanged ? "|is_unique[escuelas.nombre,escuela_id,$id]" : ''),
                'errors' => [
                    'required' => 'El nombre es obligatorio',
                    'min_length' => 'El nombre debe tener al menos 1 carácter',
                    'max_length' => 'El nombre no puede tener más de 100 caracteres',
                    'is_unique' => 'Ya existe una escuela con este nombre'
                ]
            ],
            'direccion' => [
                'rules' => 'required|min_length[1]|max_length[200]',
                'errors' => [
                    'required' => 'La dirección es obligatoria',
                    'min_length' => 'La dirección debe tener al menos 1 carácter',
                    'max_length' => 'La dirección no puede tener más de 200 caracteres'
                ]
            ],
            'telefono' => [
                'rules' => 'required|min_length[1]|max_length[20]',
                'errors' => [
                    'required' => 'El teléfono es obligatorio',
                    'min_length' => 'El teléfono debe tener al menos 1 carácter',
                    'max_length' => 'El teléfono no puede tener más de 20 caracteres'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|max_length[100]' . ($emailChanged ? "|is_unique[escuelas.email,escuela_id,$id]" : ''),
                'errors' => [
                    'required' => 'El email es obligatorio',
                    'valid_email' => 'El email debe ser válido',
                    'max_length' => 'El email no puede tener más de 100 caracteres',
                    'is_unique' => 'Ya existe una escuela con este email'
                ]
            ],
            'horario' => [
                'rules' => 'required|min_length[1]|max_length[100]',
                'errors' => [
                    'required' => 'El horario es obligatorio',
                    'min_length' => 'El horario debe tener al menos 1 carácter',
                    'max_length' => 'El horario no puede tener más de 100 caracteres'
                ]
            ],
            'estado' => [
                'rules' => 'required|in_list[activo,inactivo]',
                'errors' => [
                    'required' => 'El estado es obligatorio',
                    'in_list' => 'El estado debe ser activo o inactivo'
                ]
            ]
        ];

        // Agregar reglas para el código solo si se proporciona
        if (isset($requestData['codigo'])) {
            $rules['codigo'] = [
                'rules' => 'min_length[1]|max_length[10]' . ($codigoChanged ? "|is_unique[escuelas.codigo,escuela_id,$id]" : ''),
                'errors' => [
                    'min_length' => 'El código debe tener al menos 1 carácter',
                    'max_length' => 'El código no puede tener más de 10 caracteres',
                    'is_unique' => 'Ya existe una escuela con este código'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->fail($errors);
        }

        try {
            // Intentar actualizar
            if ($this->escuelaModel->update($id, $requestData)) {
                $updatedEscuela = $this->escuelaModel->find($id);
                return $this->respond([
                    'status' => 200,
                    'error' => null,
                    'messages' => [
                        'success' => 'Escuela actualizada exitosamente'
                    ],
                    'data' => $updatedEscuela
                ]);
            }

            // Si la actualización falla, verificar errores
            $dbError = $this->escuelaModel->errors();
            if (!empty($dbError)) {
                return $this->fail([
                    'status' => 400,
                    'error' => true,
                    'messages' => $dbError
                ]);
            }

            return $this->fail([
                'status' => 500,
                'error' => true,
                'messages' => [
                    'error' => 'Error al actualizar la escuela'
                ]
            ]);

        } catch (\Exception $e) {
            return $this->fail([
                'status' => 500,
                'error' => true,
                'messages' => [
                    'error' => 'Error al actualizar la escuela: ' . $e->getMessage()
                ]
            ]);
        }
    }

    public function delete($id = null)
    {
        $escuela = $this->escuelaModel->find($id);
        if (!$escuela) {
            return $this->failNotFound('Escuela no encontrada');
        }

        try {
            $this->escuelaModel->delete($id);
            $response = [
                'status' => 200,
                'error' => null,
                'messages' => [
                    'success' => 'Escuela eliminada exitosamente'
                ]
            ];
            return $this->respond($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 500,
                'error' => true,
                'messages' => [
                    'error' => 'Error al eliminar la escuela: ' . $e->getMessage()
                ]
            ];
            return $this->fail($response);
        }
    }
} 