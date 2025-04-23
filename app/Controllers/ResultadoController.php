<?php

namespace App\Controllers;

use App\Models\ResultadoModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ResultadoController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new ResultadoModel();
    }

    /**
     * Verifica si un usuario puede presentar un examen
     */
    public function verificarEstado($usuario_id)
    {
        try {
            $resultado = $this->model->puedePresentarExamen($usuario_id);
            
            return $this->respond([
                'status' => 'success',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Registra un nuevo resultado de examen
     */
    public function registrar()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!$data) {
                return $this->fail('No se recibieron datos', 400);
            }

            // Verificar si el usuario puede presentar el examen
            $verificacion = $this->model->puedePresentarExamen($data['usuario_id']);
            
            if (!$verificacion['puede_presentar']) {
                return $this->fail([
                    'status' => 'error',
                    'message' => $verificacion['mensaje']
                ], 400);
            }

            // Registrar el resultado
            $resultado_id = $this->model->registrarResultado($data);

            if ($resultado_id) {
                return $this->respondCreated([
                    'status' => 'success',
                    'message' => 'Resultado registrado correctamente',
                    'data' => [
                        'resultado_id' => $resultado_id
                    ]
                ]);
            }

            return $this->fail('Error al registrar el resultado', 500);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtiene el historial de resultados de un usuario
     */
    public function historial($usuario_id)
    {
        try {
            $resultados = $this->model->where('usuario_id', $usuario_id)
                                    ->orderBy('fecha_realizacion', 'DESC')
                                    ->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $resultados
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtiene el último resultado de un usuario
     */
    public function ultimoResultado($usuario_id)
    {
        try {
            $resultado = $this->model->where('usuario_id', $usuario_id)
                                   ->orderBy('fecha_realizacion', 'DESC')
                                   ->first();

            if (!$resultado) {
                return $this->failNotFound([
                    'status' => 'error',
                    'message' => 'No se encontraron resultados para este usuario'
                ]);
            }

            return $this->respond([
                'status' => 'success',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
