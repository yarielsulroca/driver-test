<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\UsuarioRolModel;
use App\Models\RolModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class UsuarioController extends ResourceController
{
    use ResponseTrait;

    protected $usuarioModel;
    protected $usuarioRolModel;
    protected $rolModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->usuarioRolModel = new UsuarioRolModel();
        $this->rolModel = new RolModel();
    }

    /**
     * Listar todos los usuarios con sus roles
     */
    public function index()
    {
        try {
            $usuarios = $this->usuarioModel->findAll();
            
            // Agregar roles a cada usuario
            foreach ($usuarios as &$usuario) {
                $usuario['roles'] = $this->usuarioRolModel->getRolesUsuario($usuario['usuario_id']);
            }

            return $this->respond([
                'status' => 'success',
                'data' => $usuarios
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener usuarios: ' . $e->getMessage());
        }
    }

    /**
     * Obtener un usuario específico con sus roles
     */
    public function show($id = null)
    {
        try {
            $usuario = $this->usuarioModel->find($id);
            
            if (!$usuario) {
                return $this->failNotFound('Usuario no encontrado');
            }

            // Agregar roles del usuario
            $usuario['roles'] = $this->usuarioRolModel->getRolesUsuario($id);

            return $this->respond([
                'status' => 'success',
                'data' => $usuario
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener usuario: ' . $e->getMessage());
        }
    }

    /**
     * Crear un nuevo usuario
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->fail('No se recibieron datos válidos');
            }

            // Validar datos del usuario
            if (!$this->usuarioModel->insert($data)) {
                return $this->failValidationError('Error de validación', $this->usuarioModel->errors());
            }

            $usuarioId = $this->usuarioModel->getInsertID();

            // Asignar roles si se proporcionan
            if (!empty($data['roles'])) {
                $this->usuarioRolModel->actualizarRoles($usuarioId, $data['roles']);
            }

            // Obtener usuario creado con sus roles
            $usuario = $this->usuarioModel->find($usuarioId);
            $usuario['roles'] = $this->usuarioRolModel->getRolesUsuario($usuarioId);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Usuario creado exitosamente',
                'data' => $usuario
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al crear usuario: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar un usuario
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->fail('No se recibieron datos válidos');
            }

            // Verificar que el usuario existe
            if (!$this->usuarioModel->find($id)) {
                return $this->failNotFound('Usuario no encontrado');
            }

            // Separar roles de los datos del usuario
            $roles = $data['roles'] ?? [];
            unset($data['roles']);

            // Actualizar datos del usuario
            if (!empty($data)) {
                if (!$this->usuarioModel->update($id, $data)) {
                    return $this->failValidationError('Error de validación', $this->usuarioModel->errors());
                }
            }

            // Actualizar roles si se proporcionan
            if (isset($roles)) {
                $this->usuarioRolModel->actualizarRoles($id, $roles);
            }

            // Obtener usuario actualizado con sus roles
            $usuario = $this->usuarioModel->find($id);
            $usuario['roles'] = $this->usuarioRolModel->getRolesUsuario($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Usuario actualizado exitosamente',
                'data' => $usuario
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al actualizar usuario: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un usuario
     */
    public function delete($id = null)
    {
        try {
            $usuario = $this->usuarioModel->find($id);
            
            if (!$usuario) {
                return $this->failNotFound('Usuario no encontrado');
            }

            // Eliminar usuario (los roles se eliminan automáticamente por CASCADE)
            $this->usuarioModel->delete($id);

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Usuario eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al eliminar usuario: ' . $e->getMessage());
        }
    }

    /**
     * Asignar roles a un usuario
     */
    public function asignarRoles($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data || !isset($data['roles'])) {
                return $this->fail('No se proporcionaron roles');
            }

            // Verificar que el usuario existe
            if (!$this->usuarioModel->find($id)) {
                return $this->failNotFound('Usuario no encontrado');
            }

            // Actualizar roles
            $this->usuarioRolModel->actualizarRoles($id, $data['roles']);

            // Obtener usuario con sus roles actualizados
            $usuario = $this->usuarioModel->find($id);
            $usuario['roles'] = $this->usuarioRolModel->getRolesUsuario($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Roles asignados exitosamente',
                'data' => $usuario
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al asignar roles: ' . $e->getMessage());
        }
    }

    /**
     * Obtener roles de un usuario
     */
    public function getRoles($id = null)
    {
        try {
            // Verificar que el usuario existe
            if (!$this->usuarioModel->find($id)) {
                return $this->failNotFound('Usuario no encontrado');
            }

            $roles = $this->usuarioRolModel->getRolesUsuario($id);

            return $this->respond([
                'status' => 'success',
                'data' => $roles
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener roles: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si un usuario tiene un rol específico
     */
    public function tieneRol($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data || !isset($data['rol_id'])) {
                return $this->fail('No se proporcionó rol_id');
            }

            // Verificar que el usuario existe
            if (!$this->usuarioModel->find($id)) {
                return $this->failNotFound('Usuario no encontrado');
            }

            $tieneRol = $this->usuarioRolModel->tieneRol($id, $data['rol_id']);

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'tiene_rol' => $tieneRol,
                    'usuario_id' => $id,
                    'rol_id' => $data['rol_id']
                ]
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al verificar rol: ' . $e->getMessage());
        }
    }

    /**
     * Obtener usuarios con un rol específico
     */
    public function usuariosConRol($rolId = null)
    {
        try {
            // Verificar que el rol existe
            if (!$this->rolModel->find($rolId)) {
                return $this->failNotFound('Rol no encontrado');
            }

            $usuarios = $this->usuarioRolModel->getUsuariosRol($rolId);

            return $this->respond([
                'status' => 'success',
                'data' => $usuarios
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener usuarios: ' . $e->getMessage());
        }
    }
}
