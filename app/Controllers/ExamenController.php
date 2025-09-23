<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ExamenModel;
use App\Models\CategoriaModel;
use App\Models\CategoriaAsignadaModel;
use CodeIgniter\HTTP\ResponseInterface;

class ExamenController extends BaseController
{
    protected $examenModel;
    protected $categoriaModel;
    protected $categoriaAsignadaModel;

    public function __construct()
    {
        $this->examenModel = new ExamenModel();
        $this->categoriaModel = new CategoriaModel();
        $this->categoriaAsignadaModel = new CategoriaAsignadaModel();
    }

    /**
     * Obtener exámenes disponibles para un conductor
     */
    public function disponibles()
    {
        try {
            $conductorId = $this->request->getGet('conductor_id');
            
            if (!$conductorId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID de conductor requerido'
            ])->setStatusCode(400);
            }

            // Obtener categorías ya aprobadas por el conductor
            $categoriasAprobadas = $this->categoriaAsignadaModel->getCategoriasAprobadasCompletas($conductorId);
            $categoriasAprobadasIds = array_column($categoriasAprobadas, 'categoria_id');

            // Obtener exámenes disponibles (no aprobados por este conductor)
            $query = $this->examenModel
                ->select('
                    e.examen_id,
                    e.titulo,
                    e.descripcion,
                    e.dificultad,
                    e.puntaje_minimo,
                    e.tiempo_limite,
                    e.duracion_minutos,
                    e.estado,
                    c.categoria_id,
                    c.codigo as categoria_codigo,
                    c.nombre as categoria_nombre
                ')
                ->from('examenes e')
                ->join('categoria_examen ce', 'ce.examen_id = e.examen_id')
                ->join('categorias c', 'c.categoria_id = ce.categoria_id')
                ->where('e.estado', 'activo')
                ->where('e.deleted_at IS NULL');

            // Solo aplicar whereNotIn si hay categorías aprobadas
            if (!empty($categoriasAprobadasIds)) {
                $query->whereNotIn('c.categoria_id', $categoriasAprobadasIds);
            }

            $examenesDisponibles = $query->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $examenesDisponibles,
                'total' => count($examenesDisponibles)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en ExamenController::disponibles: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ])->setStatusCode(500);
        }
    }

    /**
     * Asignar un examen a un conductor
     */
    public function asignar()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Datos JSON requeridos'
                ])->setStatusCode(400);
            }

            $conductorId = $data['conductor_id'] ?? null;
            $examenId = $data['examen_id'] ?? null;
            $categoriaId = $data['categoria_id'] ?? null;

            if (!$conductorId || !$examenId || !$categoriaId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'conductor_id, examen_id y categoria_id son requeridos'
                ])->setStatusCode(400);
            }

            // Verificar que el examen existe y está activo
            $examen = $this->examenModel->where('examen_id', $examenId)
                ->where('estado', 'activo')
                ->where('deleted_at IS NULL')
                ->first();

            if (!$examen) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Examen no encontrado o inactivo'
                ])->setStatusCode(404);
            }

            // Verificar que el conductor no tiene ya esta categoría aprobada
            $categoriaAprobada = $this->categoriaAsignadaModel
                ->where('conductor_id', $conductorId)
                ->where('categoria_id', $categoriaId)
                ->where('estado', 'Aprobada')
                ->where('deleted_at IS NULL')
                ->first();

            if ($categoriaAprobada) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'El conductor ya tiene esta categoría aprobada'
                ])->setStatusCode(400);
            }

            // Verificar si ya existe una asignación pendiente
            $asignacionExistente = $this->categoriaAsignadaModel
                ->where('conductor_id', $conductorId)
                ->where('categoria_id', $categoriaId)
                ->where('estado IN', ['Reprobado', 'Iniciado'])
                ->where('deleted_at IS NULL')
                ->first();

            if ($asignacionExistente) {
                // Actualizar la asignación existente a 'Iniciado'
                $this->categoriaAsignadaModel->update($asignacionExistente['categoria_asignada_id'], [
                    'estado' => 'Iniciado',
                    'examen_id' => $examenId,
                    'fecha_asignacion' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Crear nueva asignación
                $this->categoriaAsignadaModel->insert([
                    'conductor_id' => $conductorId,
                    'categoria_id' => $categoriaId,
                    'examen_id' => $examenId,
                    'estado' => 'Iniciado',
                    'intentos_realizados' => 0,
                    'intentos_maximos' => 3,
                    'fecha_asignacion' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Examen asignado exitosamente',
                'data' => [
                    'conductor_id' => $conductorId,
                    'examen_id' => $examenId,
                    'categoria_id' => $categoriaId,
                    'estado' => 'Iniciado'
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en ExamenController::asignar: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ])->setStatusCode(500);
        }
    }
}