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
        'nombre',
        'apellido',
        'email',
        'password',
        'rol',
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
        'nombre' => 'required|min_length[3]|max_length[50]',
        'apellido' => 'required|min_length[3]|max_length[50]',
        'email' => 'required|valid_email|is_unique[usuarios.email,usuario_id,{usuario_id}]',
        'password' => 'required|min_length[8]',
        'rol' => 'required|in_list[tecnico]',
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
} 