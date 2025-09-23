<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioRolModel extends Model
{
    protected $table = 'usuario_roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'usuario_id',
        'rol_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'usuario_id' => 'required|integer',
        'rol_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'usuario_id' => [
            'required' => 'El ID del usuario es requerido',
            'integer' => 'El ID del usuario debe ser un número entero'
        ],
        'rol_id' => [
            'required' => 'El ID del rol es requerido',
            'integer' => 'El ID del rol debe ser un número entero'
        ]
    ];

    /**
     * Asigna un rol a un usuario
     * @param int $usuario_id
     * @param int $rol_id
     * @return bool
     */
    public function asignarRol($usuario_id, $rol_id)
    {
        // Verificar si ya existe la asignación
        $existe = $this->where('usuario_id', $usuario_id)
                      ->where('rol_id', $rol_id)
                      ->first();

        if ($existe) {
            return true; // Ya existe la asignación
        }

        return $this->insert([
            'usuario_id' => $usuario_id,
            'rol_id' => $rol_id
        ]);
    }

    /**
     * Desasigna un rol de un usuario
     * @param int $usuario_id
     * @param int $rol_id
     * @return bool
     */
    public function desasignarRol($usuario_id, $rol_id)
    {
        return $this->where('usuario_id', $usuario_id)
                   ->where('rol_id', $rol_id)
                   ->delete();
    }

    /**
     * Asigna múltiples roles a un usuario
     * @param int $usuario_id
     * @param array $roles_ids
     * @return bool
     */
    public function asignarRoles($usuario_id, $roles_ids)
    {
        if (empty($roles_ids)) {
            return true;
        }

        $data = [];
        foreach ($roles_ids as $rol_id) {
            $data[] = [
                'usuario_id' => $usuario_id,
                'rol_id' => $rol_id
            ];
        }

        return $this->insertBatch($data);
    }

    /**
     * Actualiza los roles de un usuario (elimina los actuales y asigna los nuevos)
     * @param int $usuario_id
     * @param array $roles_ids
     * @return bool
     */
    public function actualizarRoles($usuario_id, $roles_ids)
    {
        // Eliminar roles actuales
        $this->where('usuario_id', $usuario_id)->delete();

        // Asignar nuevos roles si hay alguno
        if (!empty($roles_ids)) {
            return $this->asignarRoles($usuario_id, $roles_ids);
        }

        return true;
    }

    /**
     * Obtiene los roles de un usuario
     * @param int $usuario_id
     * @return array
     */
    public function getRolesUsuario($usuario_id)
    {
        return $this->db
            ->select('r.*')
            ->from('roles r')
            ->join('usuario_roles ur', 'r.rol_id = ur.rol_id')
            ->where('ur.usuario_id', $usuario_id)
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene los usuarios con un rol específico
     * @param int $rol_id
     * @return array
     */
    public function getUsuariosRol($rol_id)
    {
        return $this->db
            ->select('u.*')
            ->from('usuarios u')
            ->join('usuario_roles ur', 'u.usuario_id = ur.usuario_id')
            ->where('ur.rol_id', $rol_id)
            ->get()
            ->getResultArray();
    }

    /**
     * Verifica si un usuario tiene un rol específico
     * @param int $usuario_id
     * @param int $rol_id
     * @return bool
     */
    public function tieneRol($usuario_id, $rol_id)
    {
        $result = $this->where('usuario_id', $usuario_id)
                      ->where('rol_id', $rol_id)
                      ->first();

        return !empty($result);
    }

    /**
     * Verifica si un usuario tiene alguno de los roles especificados
     * @param int $usuario_id
     * @param array $roles_ids
     * @return bool
     */
    public function tieneAlgunRol($usuario_id, $roles_ids)
    {
        if (empty($roles_ids)) {
            return false;
        }

        $result = $this->where('usuario_id', $usuario_id)
                      ->whereIn('rol_id', $roles_ids)
                      ->first();

        return !empty($result);
    }
}
