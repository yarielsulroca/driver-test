<?php

namespace App\Controllers;

use App\Models\ConductorModel;
use App\Models\UsuarioModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ConductoresSimple extends ResourceController
{
    use ResponseTrait;

    protected $conductorModel;
    protected $usuarioModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->conductorModel = new ConductorModel();
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Listar todos los conductores con información básica
     */
    public function index()
    {
        try {
            // Obtener conductores con información básica de usuario
            $conductores = $this->conductorModel->select('
                conductores.*,
                usuarios.nombre,
                usuarios.apellido,
                usuarios.email,
                usuarios.dni
            ')
            ->join('usuarios', 'usuarios.usuario_id = conductores.usuario_id')
            ->findAll();

            // Agregar información del usuario a cada conductor
            foreach ($conductores as &$conductor) {
                $conductor['usuario'] = [
                    'usuario_id' => $conductor['usuario_id'],
                    'nombre' => $conductor['nombre'],
                    'apellido' => $conductor['apellido'],
                    'email' => $conductor['email'],
                    'dni' => $conductor['dni']
                ];
            }

            return $this->respond([
                'status' => 'success',
                'data' => $conductores,
                'total' => count($conductores)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener conductores: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Error al obtener conductores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un conductor específico
     */
    public function show($id = null)
    {
        try {
            $conductor = $this->conductorModel->select('
                conductores.*,
                usuarios.nombre,
                usuarios.apellido,
                usuarios.email,
                usuarios.dni
            ')
            ->join('usuarios', 'usuarios.usuario_id = conductores.usuario_id')
            ->where('conductores.conductor_id', $id)
            ->first();

            if (!$conductor) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Conductor no encontrado'
                ], 404);
            }

            // Agregar información del usuario
            $conductor['usuario'] = [
                'usuario_id' => $conductor['usuario_id'],
                'nombre' => $conductor['nombre'],
                'apellido' => $conductor['apellido'],
                'email' => $conductor['email'],
                'dni' => $conductor['dni']
            ];

            return $this->respond([
                'status' => 'success',
                'data' => $conductor
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener conductor: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Error al obtener conductor: ' . $e->getMessage()
            ], 500);
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
                return $this->respond([
                    'status' => 'error',
                    'message' => 'No se recibieron datos válidos'
                ], 400);
            }

            // Validar que el usuario existe
            if (!isset($data['usuario_id']) || !$this->usuarioModel->find($data['usuario_id'])) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Usuario no válido'
                ], 400);
            }

            // Verificar que no exista ya un conductor para este usuario
            $conductorExistente = $this->conductorModel->where('usuario_id', $data['usuario_id'])->first();
            if ($conductorExistente) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Ya existe un conductor para este usuario'
                ], 400);
            }

            // Crear el conductor
            $conductorId = $this->conductorModel->insert($data);

            if ($conductorId) {
                // Obtener el conductor creado con información del usuario
                $conductor = $this->conductorModel->select('
                    conductores.*,
                    usuarios.nombre,
                    usuarios.apellido,
                    usuarios.email,
                    usuarios.dni
                ')
                ->join('usuarios', 'usuarios.usuario_id = conductores.usuario_id')
                ->where('conductores.conductor_id', $conductorId)
                ->first();

                $conductor['usuario'] = [
                    'usuario_id' => $conductor['usuario_id'],
                    'nombre' => $conductor['nombre'],
                    'apellido' => $conductor['apellido'],
                    'email' => $conductor['email'],
                    'dni' => $conductor['dni']
                ];
                
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Conductor creado exitosamente',
                    'data' => $conductor
                ], 201);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Error al crear el conductor'
                ], 500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al crear conductor: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un conductor
     */
    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'ID de conductor requerido'
                ], 400);
            }

            // Verificar que el conductor existe
            $conductor = $this->conductorModel->find($id);
            if (!$conductor) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Conductor no encontrado'
                ], 404);
            }

            // Eliminar conductor
            $resultado = $this->conductorModel->delete($id);

            if ($resultado) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Conductor eliminado exitosamente'
                ]);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Error al eliminar el conductor'
                ], 500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar conductor: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
