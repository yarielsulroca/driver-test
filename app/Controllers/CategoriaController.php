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
        return $this->respond([
            'status' => 'success',
            'data' => $categorias
        ]);
    }

    public function show($id = null)
    {
        $categoria = $this->categoriaModel->find($id);
        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }
        return $this->respond([
            'status' => 'success',
            'data' => $categoria
        ]);
    }

    public function create()
    {
        // Log del JSON recibido para debugging
        $jsonInput = $this->request->getJSON(true);
        log_message('info', 'JSON recibido en create categoría: ' . json_encode($jsonInput));
        
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
                'rules' => 'required',
                'errors' => [
                    'required' => 'Los requisitos son obligatorios'
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

        // Validar JSON de requisitos manualmente
        $requisitos = $this->request->getVar('requisitos');
        if (!json_decode($requisitos)) {
            $response = [
                'status' => 400,
                'error' => true,
                'messages' => [
                    'requisitos' => 'Los requisitos deben ser un JSON válido'
                ]
            ];
            return $this->fail($response);
        }

        $data = [
            'codigo' => $this->request->getVar('codigo'),
            'nombre' => $this->request->getVar('nombre'),
            'descripcion' => $this->request->getVar('descripcion'),
            'requisitos' => $requisitos,
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

        // Log del JSON recibido para debugging
        $jsonInput = $this->request->getJSON(true);
        log_message('info', 'JSON recibido en update categoría: ' . json_encode($jsonInput));

        // Solo validar los campos que se envían desde el frontend
        $rules = [
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

        // Solo actualizar los campos que se envían, manteniendo los existentes
        $data = [
            'nombre' => $this->request->getVar('nombre'),
            'descripcion' => $this->request->getVar('descripcion'),
            'estado' => $this->request->getVar('estado')
        ];

        try {
            $this->categoriaModel->update($id, $data);
            return $this->respond([
                'status' => 'success',
                'message' => 'Categoría actualizada exitosamente',
                'data' => $this->categoriaModel->find($id)
            ]);
        } catch (\Exception $e) {
            return $this->fail([
                'status' => 'error',
                'message' => 'Error al actualizar la categoría: ' . $e->getMessage()
            ], 500);
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
            return $this->respond([
                'status' => 'success',
                'message' => 'Categoría eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->fail([
                'status' => 'error',
                'message' => 'Error al eliminar la categoría: ' . $e->getMessage()
            ], 500);
        }
    }
} 