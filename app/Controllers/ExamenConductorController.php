<?php

namespace App\Controllers;

use App\Models\ExamenConductorModel;
use App\Models\ExamenModel;
use App\Models\ConductorModel;

class ExamenConductorController extends BaseController
{
    protected $model;
    protected $examenModel;
    protected $conductorModel;

    public function __construct()
    {
        $this->model = new ExamenConductorModel();
        $this->examenModel = new ExamenModel();
        $this->conductorModel = new ConductorModel();
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
     * Asignar examen a conductor
     */
    public function asignarExamen()
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
            if (!isset($data['examen_id']) || !isset($data['conductor_id'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'examen_id y conductor_id son requeridos'
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

            // Verificar que no esté ya asignado
            if ($this->model->tieneExamenAsignado($data['examen_id'], $data['conductor_id'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Este examen ya está asignado al conductor'
                ]);
            }

            // Asignar el examen
            $intentosRestantes = $data['intentos_restantes'] ?? 3;
            $result = $this->model->asignarExamen($data['examen_id'], $data['conductor_id'], $intentosRestantes);

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
     * Actualizar estado de un examen asignado
     */
    public function actualizarEstado()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!$data || !isset($data['examen_conductor_id']) || !isset($data['estado'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'examen_conductor_id y estado son requeridos'
                ]);
            }

            $examenConductor = $this->model->find($data['examen_conductor_id']);
            if (!$examenConductor) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Asignación de examen no encontrada'
                ]);
            }

            $updateData = ['estado' => $data['estado']];
            
            // Agregar campos opcionales si están presentes
            if (isset($data['puntaje_obtenido'])) {
                $updateData['puntaje_obtenido'] = $data['puntaje_obtenido'];
            }
            if (isset($data['tiempo_utilizado'])) {
                $updateData['tiempo_utilizado'] = $data['tiempo_utilizado'];
            }
            if (isset($data['fecha_fin'])) {
                $updateData['fecha_fin'] = $data['fecha_fin'];
            }

            $result = $this->model->update($data['examen_conductor_id'], $updateData);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Estado actualizado exitosamente'
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Error al actualizar el estado',
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
     * Eliminar asignación de examen
     */
    public function eliminarAsignacion($examenConductorId = null)
    {
        try {
            if (!$examenConductorId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'ID de la asignación es requerido'
                ]);
            }

            $examenConductor = $this->model->find($examenConductorId);
            if (!$examenConductor) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Asignación de examen no encontrada'
                ]);
            }

            $result = $this->model->delete($examenConductorId);

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
