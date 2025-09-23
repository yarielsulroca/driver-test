<?php

namespace App\Controllers;

use App\Models\RoleModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class RoleController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new RoleModel();
    }

    /**
     * Listar todos los registros
     */
    public function index()
    {
        try {
            $data = $this->model->findAll();
            return $this->respond([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener datos: ' . $e->getMessage());
        }
    }

    /**
     * Obtener un registro específico
     */
    public function show($id = null)
    {
        try {
            $data = $this->model->find($id);
            if (!$data) {
                return $this->failNotFound('Registro no encontrado');
            }
            return $this->respond([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener registro: ' . $e->getMessage());
        }
    }

    /**
     * Crear un nuevo registro
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            if (!$data) {
                return $this->fail('No se recibieron datos válidos');
            }

            if (!$this->model->insert($data)) {
                return $this->failValidationError('Error de validación', $this->model->errors());
            }

            $id = $this->model->getInsertID();
            $data = $this->model->find($id);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Registro creado exitosamente',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error al crear registro: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar un registro
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            if (!$data) {
                return $this->fail('No se recibieron datos válidos');
            }

            if (!$this->model->find($id)) {
                return $this->failNotFound('Registro no encontrado');
            }

            if (!$this->model->update($id, $data)) {
                return $this->failValidationError('Error de validación', $this->model->errors());
            }

            $data = $this->model->find($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Registro actualizado exitosamente',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error al actualizar registro: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un registro
     */
    public function delete($id = null)
    {
        try {
            if (!$this->model->find($id)) {
                return $this->failNotFound('Registro no encontrado');
            }

            $this->model->delete($id);

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Registro eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error al eliminar registro: ' . $e->getMessage());
        }
    }
}
