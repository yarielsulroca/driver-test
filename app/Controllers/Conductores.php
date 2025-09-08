<?php

namespace App\Controllers;

use App\Models\ConductorModel;
use App\Models\UsuarioModel;
use App\Models\PerfilModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Conductores extends ResourceController
{
    use ResponseTrait;

    protected $conductorModel;
    protected $usuarioModel;
    protected $perfilModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->conductorModel = new ConductorModel();
        $this->usuarioModel = new UsuarioModel();
        $this->perfilModel = new PerfilModel();
    }

    /**
     * Listar todos los conductores con información completa
     */
    public function index()
    {
        try {
            $filtros = $this->request->getGet();
            $conductores = $this->conductorModel->getConductoresConPerfil($filtros);

            // Agregar las escuelas a cada conductor
            foreach ($conductores as &$conductor) {
                $conductor['escuelas'] = $this->conductorModel->getEscuelas($conductor['conductor_id']);
                $conductor['categorias_aprobadas'] = $this->conductorModel->categoriasAprobadas()
                    ->where('conductor_id', $conductor['conductor_id'])
                    ->findAll();
            }

            return $this->respond([
                'status' => 'success',
                'data' => $conductores,
                'total' => count($conductores)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener conductores: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Mostrar un conductor específico con información completa
     */
    public function show($id = null)
    {
        try {
            $conductor = $this->conductorModel->getPerfilCompleto($id);

            if (!$conductor) {
                return $this->failNotFound('Conductor no encontrado');
            }

            // Agregar información adicional
            $conductor['escuelas'] = $this->conductorModel->getEscuelas($id);
            $conductor['categorias_aprobadas'] = $this->conductorModel->categoriasAprobadas()
                ->where('conductor_id', $id)
                ->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $conductor
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener conductor: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Crear un nuevo conductor
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (empty($data)) {
                return $this->fail('No se recibieron datos válidos', 400);
            }

            // Validar que el usuario existe
            if (!isset($data['usuario_id']) || !$this->usuarioModel->find($data['usuario_id'])) {
                return $this->fail('Usuario no válido', 400);
            }

            // Verificar que no exista ya un conductor para este usuario
            $conductorExistente = $this->conductorModel->where('usuario_id', $data['usuario_id'])->first();
            if ($conductorExistente) {
                return $this->fail('Ya existe un conductor para este usuario', 400);
            }

            // Crear el conductor
            $conductorId = $this->conductorModel->insert($data);

            if ($conductorId) {
                $conductor = $this->conductorModel->getPerfilCompleto($conductorId);
                
                return $this->respondCreated([
                    'status' => 'success',
                    'message' => 'Conductor creado exitosamente',
                    'data' => $conductor
                ]);
            } else {
                return $this->failServerError('Error al crear el conductor');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al crear conductor: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Actualizar un conductor existente
     */
    public function update($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('ID de conductor requerido', 400);
            }

            $data = $this->request->getJSON(true);
            
            if (empty($data)) {
                return $this->fail('No se recibieron datos para actualizar', 400);
            }

            // Verificar que el conductor existe
            $conductorExistente = $this->conductorModel->find($id);
            if (!$conductorExistente) {
                return $this->failNotFound('Conductor no encontrado');
            }

            // Actualizar conductor
            $resultado = $this->conductorModel->update($id, $data);

            if ($resultado) {
                $conductor = $this->conductorModel->getPerfilCompleto($id);
                
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Conductor actualizado exitosamente',
                    'data' => $conductor
                ]);
            } else {
                return $this->failServerError('Error al actualizar el conductor');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar conductor: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Eliminar un conductor
     */
    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('ID de conductor requerido', 400);
            }

            // Verificar que el conductor existe
            $conductor = $this->conductorModel->find($id);
            if (!$conductor) {
                return $this->failNotFound('Conductor no encontrado');
            }

            // Eliminar conductor
            $resultado = $this->conductorModel->delete($id);

            if ($resultado) {
                return $this->respondDeleted([
                    'status' => 'success',
                    'message' => 'Conductor eliminado exitosamente'
                ]);
            } else {
                return $this->failServerError('Error al eliminar el conductor');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar conductor: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Obtener perfil del conductor autenticado
     */
    public function perfil()
    {
        try {
            // Obtener ID del usuario autenticado (implementar según tu sistema de auth)
            $usuarioId = $this->request->getHeaderLine('X-User-ID') ?? 1; // Temporal
            
            $conductor = $this->conductorModel->where('usuario_id', $usuarioId)->first();
            
            if (!$conductor) {
                return $this->failNotFound('Conductor no encontrado para este usuario');
            }

            $perfilCompleto = $this->conductorModel->getPerfilCompleto($conductor['conductor_id']);
            $perfilCompleto['escuelas'] = $this->conductorModel->getEscuelas($conductor['conductor_id']);

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
     * Obtener exámenes disponibles para el conductor
     */
    public function examenes()
    {
        try {
            // Obtener ID del usuario autenticado
            $usuarioId = $this->request->getHeaderLine('X-User-ID') ?? 1; // Temporal
            
            $conductor = $this->conductorModel->where('usuario_id', $usuarioId)->first();
            
            if (!$conductor) {
                return $this->failNotFound('Conductor no encontrado');
            }

            // Obtener exámenes disponibles (implementar según tu lógica de negocio)
            $examenes = []; // Placeholder
            
            return $this->respond([
                'status' => 'success',
                'data' => $examenes
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener exámenes: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Obtener categorías disponibles
     */
    public function categorias()
    {
        try {
            // Implementar lógica para obtener categorías disponibles
            $categorias = []; // Placeholder
            
            return $this->respond([
                'status' => 'success',
                'data' => $categorias
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener categorías: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }

    /**
     * Obtener historial del conductor
     */
    public function historial()
    {
        try {
            // Obtener ID del usuario autenticado
            $usuarioId = $this->request->getHeaderLine('X-User-ID') ?? 1; // Temporal
            
            $conductor = $this->conductorModel->where('usuario_id', $usuarioId)->first();
            
            if (!$conductor) {
                return $this->failNotFound('Conductor no encontrado');
            }

            $historial = $this->conductorModel->getExamenesInfo($conductor['conductor_id']);
            
            return $this->respond([
                'status' => 'success',
                'data' => $historial
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener historial: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor');
        }
    }
} 