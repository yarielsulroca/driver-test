<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'usuario_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'dni',
        'nombre',
        'apellido',
        'email',
        'password',
        'estado'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'dni' => 'required|min_length[8]|max_length[20]|is_unique[usuarios.dni,usuario_id,{usuario_id}]',
        'nombre' => 'required|min_length[3]|max_length[50]',
        'apellido' => 'required|min_length[3]|max_length[50]',
        'email' => 'required|valid_email|is_unique[usuarios.email,usuario_id,{usuario_id}]',
        'password' => 'required|min_length[8]',
        'estado' => 'required|in_list[activo,inactivo]'
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) return $data;

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    public function verificarCredenciales($email, $password)
    {
        $usuario = $this->where('email', $email)
                       ->where('estado', 'activo')
                       ->first();

        if (!$usuario) return false;

        return password_verify($password, $usuario['password']);
    }

    /**
     * Obtiene los roles del usuario (relación muchos a muchos)
     * @return array
     */
    public function getRoles()
    {
        $usuarioRolModel = new \App\Models\UsuarioRolModel();
        return $usuarioRolModel->getRolesUsuario($this->usuario_id);
    }

    /**
     * Asigna roles al usuario
     * @param array $roles_ids
     * @return bool
     */
    public function asignarRoles($roles_ids)
    {
        $usuarioRolModel = new \App\Models\UsuarioRolModel();
        return $usuarioRolModel->actualizarRoles($this->usuario_id, $roles_ids);
    }

    /**
     * Verifica si el usuario tiene un rol específico
     * @param int $rol_id
     * @return bool
     */
    public function tieneRol($rol_id)
    {
        $usuarioRolModel = new \App\Models\UsuarioRolModel();
        return $usuarioRolModel->tieneRol($this->usuario_id, $rol_id);
    }

    /**
     * Verifica si el usuario tiene alguno de los roles especificados
     * @param array $roles_ids
     * @return bool
     */
    public function tieneAlgunRol($roles_ids)
    {
        $usuarioRolModel = new \App\Models\UsuarioRolModel();
        return $usuarioRolModel->tieneAlgunRol($this->usuario_id, $roles_ids);
    }

    /**
     * Obtiene el rol principal del usuario (el primero)
     * @return array|null
     */
    public function getRolPrincipal()
    {
        $roles = $this->getRoles();
        return !empty($roles) ? $roles[0] : null;
    }

    /**
     * Obtiene el perfil del usuario
     * @return \CodeIgniter\Database\BaseResult
     */
    public function perfil()
    {
        return $this->hasOne('App\Models\PerfilModel', 'usuario_id', 'usuario_id');
    }
} 