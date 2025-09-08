<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use App\Models\UsuarioModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;

class PerfilController extends ResourceController
{
    protected $perfilModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->perfilModel = new PerfilModel();
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Obtener el perfil completo de un usuario
     * GET /api/perfiles/usuario/{id}
     */
    public function getPerfilUsuario($id = null)
    {
        if (!$id) {
            return $this->failValidationError('ID de usuario requerido');
        }

        try {
            $perfil = $this->perfilModel->getPerfilCompleto($id);
            
            if (!$perfil) {
                return $this->failNotFound('Perfil no encontrado para el usuario especificado');
            }

            return $this->respond([
                'status' => 'success',
                'data' => $perfil
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener perfil del usuario: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Obtener todos los perfiles con información de usuario
     * GET /api/perfiles
     */
    public function index()
    {
        try {
            $filtros = $this->request->getGet();
            $perfiles = $this->perfilModel->getPerfilesConUsuario($filtros);

            return $this->respond([
                'status' => 'success',
                'data' => $perfiles,
                'total' => count($perfiles)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener perfiles: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Obtener un perfil específico
     * GET /api/perfiles/{id}
     */
    public function show($id = null)
    {
        if (!$id) {
            return $this->failValidationError('ID de perfil requerido');
        }

        try {
            $perfil = $this->perfilModel->find($id);
            
            if (!$perfil) {
                return $this->failNotFound('Perfil no encontrado');
            }

            // Obtener información completa del usuario
            $perfilCompleto = $this->perfilModel->getPerfilCompleto($perfil['usuario_id']);

            return $this->respond([
                'status' => 'success',
                'data' => $perfilCompleto
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener perfil: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Crear o actualizar un perfil
     * POST /api/perfiles
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->failValidationError('Datos requeridos');
            }

            // Validar que el usuario existe
            if (!isset($data['usuario_id']) || !$this->usuarioModel->find($data['usuario_id'])) {
                return $this->failValidationError('Usuario no válido');
            }

            // Crear o actualizar perfil
            $resultado = $this->perfilModel->crearOActualizarPerfil($data['usuario_id'], $data);

            if ($resultado) {
                $perfil = $this->perfilModel->getPerfilCompleto($data['usuario_id']);
                
                return $this->respondCreated([
                    'status' => 'success',
                    'message' => 'Perfil creado/actualizado exitosamente',
                    'data' => $perfil
                ]);
            } else {
                return $this->failServerError('Error al crear/actualizar el perfil');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al crear/actualizar perfil: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Actualizar un perfil existente
     * PUT /api/perfiles/{id}
     */
    public function update($id = null)
    {
        if (!$id) {
            return $this->failValidationError('ID de perfil requerido');
        }

        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->failValidationError('Datos requeridos');
            }

            // Verificar que el perfil existe
            $perfilExistente = $this->perfilModel->find($id);
            if (!$perfilExistente) {
                return $this->failNotFound('Perfil no encontrado');
            }

            // Actualizar perfil
            $resultado = $this->perfilModel->update($id, $data);

            if ($resultado) {
                $perfilActualizado = $this->perfilModel->getPerfilCompleto($perfilExistente['usuario_id']);
                
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Perfil actualizado exitosamente',
                    'data' => $perfilActualizado
                ]);
            } else {
                return $this->failServerError('Error al actualizar el perfil');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar perfil: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Eliminar un perfil
     * DELETE /api/perfiles/{id}
     */
    public function delete($id = null)
    {
        if (!$id) {
            return $this->failValidationError('ID de perfil requerido');
        }

        try {
            // Verificar que el perfil existe
            $perfil = $this->perfilModel->find($id);
            if (!$perfil) {
                return $this->failNotFound('Perfil no encontrado');
            }

            // Eliminar perfil
            $resultado = $this->perfilModel->delete($id);

            if ($resultado) {
                return $this->respondDeleted([
                    'status' => 'success',
                    'message' => 'Perfil eliminado exitosamente'
                ]);
            } else {
                return $this->failServerError('Error al eliminar el perfil');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar perfil: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Obtener estadísticas de perfiles
     * GET /api/perfiles/estadisticas
     */
    public function estadisticas()
    {
        try {
            $stats = [
                'total_perfiles' => $this->perfilModel->countAllResults(),
                'por_genero' => $this->perfilModel->select('genero, COUNT(*) as total')
                    ->groupBy('genero')
                    ->findAll(),
                'con_telefono' => $this->perfilModel->where('telefono IS NOT NULL')
                    ->where('telefono !=', '')
                    ->countAllResults(),
                'con_foto' => $this->perfilModel->where('foto IS NOT NULL')
                    ->where('foto !=', '')
                    ->countAllResults()
            ];

            return $this->respond([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener estadísticas: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }
}
