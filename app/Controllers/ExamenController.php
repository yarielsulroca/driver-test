<?php

namespace App\Controllers;

use App\Models\ExamenModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ExamenController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new ExamenModel();
    }

    /**
     * Listar todos los exámenes
     */
    public function index()
    {
        try {
            $examenes = $this->model->findAll();
            return $this->respond([
                'status' => 'success',
                'data' => $examenes
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener un examen específico
     */
    public function show($id = null)
    {
        try {
            $examen = $this->model->find($id);
            
            if (!$examen) {
                return $this->failNotFound('Examen no encontrado');
            }

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

            if (!$this->model->insert($data)) {
                return $this->fail($this->model->errors());
            }

            $examen_id = $this->model->getInsertID();
            $examen = $this->model->find($examen_id);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Examen creado exitosamente',
                'data' => $examen
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

            $examenExiste = $this->model->find($id);
            if (!$examenExiste) {
                return $this->failNotFound('Examen no encontrado');
            }

            if (!$this->model->update($id, $data)) {
                return $this->fail($this->model->errors());
            }

            $examen = $this->model->find($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Examen actualizado exitosamente',
                'data' => $examen
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
            if (!$id) {
                return $this->fail('ID no proporcionado', 400);
            }

            $examenExiste = $this->model->find($id);
            if (!$examenExiste) {
                return $this->failNotFound('Examen no encontrado');
            }

            if (!$this->model->delete($id)) {
                return $this->fail('No se pudo eliminar el examen');
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
            $examenes = $this->model->where('categoria_id', $categoria_id)->findAll();
            
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
            $examenes = $this->model->where('fecha_inicio <=', $fechaActual)
                                  ->where('fecha_fin >=', $fechaActual)
                                  ->findAll();
            
            return $this->respond([
                'status' => 'success',
                'data' => $examenes
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
} 