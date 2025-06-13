<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CategoriaModel;

class CategoriaController extends ResourceController
{
    protected $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    public function index()
    {
        $categorias = $this->categoriaModel->findAll();
        return $this->respond($categorias);
    }

    public function show($id = null)
    {
        $categoria = $this->categoriaModel->find($id);
        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }
        return $this->respond($categoria);
    }

    public function create()
    {
        $rules = [
            'codigo' => [
                'rules' => 'required|min_length[1]|max_length[10]|is_unique[categorias.codigo]',
                'errors' => [
                    'required' => 'El código es obligatorio',
                    'min_length' => 'El código debe tener al menos 1 carácter',
                    'max_length' => 'El código no puede tener más de 10 caracteres',
                    'is_unique' => 'Ya existe una categoría con este código'
                ]
            ],
            'nombre' => [
                'rules' => 'required|min_length[1]|max_length[50]|is_unique[categorias.nombre]',
                'errors' => [
                    'required' => 'El nombre es obligatorio',
                    'min_length' => 'El nombre debe tener al menos 1 carácter',
                    'max_length' => 'El nombre no puede tener más de 50 caracteres',
                    'is_unique' => 'Ya existe una categoría con este nombre'
                ]
            ],
            'descripcion' => [
                'rules' => 'required|min_length[1]|max_length[255]',
                'errors' => [
                    'required' => 'La descripción es obligatoria',
                    'min_length' => 'La descripción debe tener al menos 1 carácter',
                    'max_length' => 'La descripción no puede tener más de 255 caracteres'
                ]
            ],
            'requisitos' => [
                'rules' => 'required|valid_json',
                'errors' => [
                    'required' => 'Los requisitos son obligatorios',
                    'valid_json' => 'Los requisitos deben ser un JSON válido'
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
            'descripcion' => $this->request->getVar('descripcion'),
            'requisitos' => $this->request->getVar('requisitos'),
            'estado' => $this->request->getVar('estado')
        ];

        try {
            $this->categoriaModel->insert($data);
            $response = [
                'status' => 201,
                'error' => null,
                'messages' => [
                    'success' => 'Categoría creada exitosamente'
                ]
            ];
            return $this->respondCreated($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 500,
                'error' => true,
                'messages' => [
                    'error' => 'Error al crear la categoría: ' . $e->getMessage()
                ]
            ];
            return $this->fail($response);
        }
    }

    public function update($id = null)
    {
        $categoria = $this->categoriaModel->find($id);
        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }

        $rules = [
            'codigo' => [
                'rules' => "required|min_length[1]|max_length[10]|is_unique[categorias.codigo,categoria_id,$id]",
                'errors' => [
                    'required' => 'El código es obligatorio',
                    'min_length' => 'El código debe tener al menos 1 carácter',
                    'max_length' => 'El código no puede tener más de 10 caracteres',
                    'is_unique' => 'Ya existe una categoría con este código'
                ]
            ],
            'nombre' => [
                'rules' => "required|min_length[1]|max_length[50]|is_unique[categorias.nombre,categoria_id,$id]",
                'errors' => [
                    'required' => 'El nombre es obligatorio',
                    'min_length' => 'El nombre debe tener al menos 1 carácter',
                    'max_length' => 'El nombre no puede tener más de 50 caracteres',
                    'is_unique' => 'Ya existe una categoría con este nombre'
                ]
            ],
            'descripcion' => [
                'rules' => 'required|min_length[1]|max_length[255]',
                'errors' => [
                    'required' => 'La descripción es obligatoria',
                    'min_length' => 'La descripción debe tener al menos 1 carácter',
                    'max_length' => 'La descripción no puede tener más de 255 caracteres'
                ]
            ],
            'requisitos' => [
                'rules' => 'required|valid_json',
                'errors' => [
                    'required' => 'Los requisitos son obligatorios',
                    'valid_json' => 'Los requisitos deben ser un JSON válido'
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
            'descripcion' => $this->request->getVar('descripcion'),
            'requisitos' => $this->request->getVar('requisitos'),
            'estado' => $this->request->getVar('estado')
        ];

        try {
            $this->categoriaModel->update($id, $data);
            $response = [
                'status' => 200,
                'error' => null,
                'messages' => [
                    'success' => 'Categoría actualizada exitosamente'
                ]
            ];
            return $this->respond($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 500,
                'error' => true,
                'messages' => [
                    'error' => 'Error al actualizar la categoría: ' . $e->getMessage()
                ]
            ];
            return $this->fail($response);
        }
    }

    public function delete($id = null)
    {
        $categoria = $this->categoriaModel->find($id);
        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }

        try {
            $this->categoriaModel->delete($id);
            $response = [
                'status' => 200,
                'error' => null,
                'messages' => [
                    'success' => 'Categoría eliminada exitosamente'
                ]
            ];
            return $this->respond($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 500,
                'error' => true,
                'messages' => [
                    'error' => 'Error al eliminar la categoría: ' . $e->getMessage()
                ]
            ];
            return $this->fail($response);
        }
    }
} 