<?php

namespace App\Models;

use CodeIgniter\Model;

class RolModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'rol_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nombre',
        'descripcion'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[50]|is_unique[roles.nombre,rol_id,{rol_id}]',
        'descripcion' => 'permit_empty|max_length[255]'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre del rol es requerido',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder los 50 caracteres',
            'is_unique' => 'Ya existe un rol con este nombre'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene todos los usuarios con este rol (relación muchos a muchos)
     * @return array
     */
    public function getUsuarios()
    {
        $usuarioRolModel = new \App\Models\UsuarioRolModel();
        return $usuarioRolModel->getUsuariosRol($this->rol_id);
    }

    /**
     * Cuenta cuántos usuarios tienen este rol
     * @return int
     */
    public function contarUsuarios()
    {
        $usuarioRolModel = new \App\Models\UsuarioRolModel();
        return $usuarioRolModel->where('rol_id', $this->rol_id)->countAllResults();
    }

    /**
     * Verifica si el rol está asignado a algún usuario
     * @return bool
     */
    public function tieneUsuarios()
    {
        return $this->contarUsuarios() > 0;
    }
} 