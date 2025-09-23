<?php

namespace App\Controllers;

use App\Models\ExamenAsignadoModel;
use App\Models\ExamenModel;
use App\Models\ConductorModel;

class ExamenAsignadoController extends BaseController
{
    protected $model;
    protected $examenModel;
    protected $conductorModel;

    public function __construct()
    {
        $this->model = new ExamenAsignadoModel();
        $this->examenModel = new ExamenModel();
        $this->conductorModel = new ConductorModel();
    }

    /**
     * Asignar examen a conductor
     */
    public function asignar()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!$data) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'No se recibieron datos válidos'
                ]);
            }

            // Validar datos requeridos
            if (!isset($data['conductor_id']) || !isset($data['examen_id'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'conductor_id y examen_id son requeridos'
                ]);
            }

            // Verificar que el examen existe
            $examen = $this->examenModel->find($data['examen_id']);
            if (!$examen) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'El examen especificado no existe'
                ]);
            }

            // Verificar que el conductor existe
            $conductor = $this->conductorModel->find($data['conductor_id']);
            if (!$conductor) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'El conductor especificado no existe'
                ]);
            }

            // Verificar que el conductor tenga estado "bueno" (b)
            if ($conductor['estado'] !== 'b') {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'No se puede habilitar examen a un conductor con estado pendiente'
                ]);
            }

            // Verificar que no esté ya asignado
            if ($this->model->tieneExamenAsignado($data['conductor_id'], $data['examen_id'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Este examen ya está asignado al conductor'
                ]);
            }

            // Asignar el examen
            $intentosDisponibles = $data['intentos_disponibles'] ?? 3;
            $result = $this->model->asignarExamen($data['conductor_id'], $data['examen_id'], $intentosDisponibles);

            if ($result) {
                return $this->response->setStatusCode(201)->setJSON([
                    'status' => 'success',
                    'message' => 'Examen asignado exitosamente al conductor'
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Error al asignar el examen',
                    'errors' => $this->model->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener exámenes asignados a un conductor
     */
    public function getExamenesConductor($conductorId = null)
    {
        try {
            if (!$conductorId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'ID del conductor es requerido'
                ]);
            }

            $examenes = $this->model->getExamenesConductor($conductorId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Exámenes del conductor obtenidos exitosamente',
                'data' => $examenes
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error al obtener exámenes del conductor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener conductores asignados a un examen
     */
    public function getConductoresExamen($examenId = null)
    {
        try {
            if (!$examenId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'ID del examen es requerido'
                ]);
            }

            $conductores = $this->model->getConductoresExamen($examenId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Conductores del examen obtenidos exitosamente',
                'data' => $conductores
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error al obtener conductores del examen: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de un conductor
     */
    public function getEstadisticasConductor($conductorId = null)
    {
        try {
            if (!$conductorId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'ID del conductor es requerido'
                ]);
            }

            $estadisticas = $this->model->getEstadisticasConductor($conductorId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => $estadisticas
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Marcar examen como aprobado
     */
    public function marcarAprobado()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!$data || !isset($data['id']) || !isset($data['puntaje_final'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'id y puntaje_final son requeridos'
                ]);
            }

            $result = $this->model->marcarComoAprobado($data['id'], $data['puntaje_final']);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Examen marcado como aprobado exitosamente'
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Error al marcar el examen como aprobado'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar asignación de examen
     */
    public function eliminar($id = null)
    {
        try {
            if (!$id) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'ID de la asignación es requerido'
                ]);
            }

            $asignacion = $this->model->find($id);
            if (!$asignacion) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Asignación de examen no encontrada'
                ]);
            }

            $result = $this->model->delete($id);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Asignación eliminada exitosamente'
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Error al eliminar la asignación'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }
}
