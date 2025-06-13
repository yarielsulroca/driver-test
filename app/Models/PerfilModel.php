<?php

namespace App\Models;

use CodeIgniter\Model;

class PerfilModel extends Model
{
    protected $table = 'perfiles';
    protected $primaryKey = 'perfil_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'usuario_id',
        'nombre',
        'apellido',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'genero',
        'foto'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'usuario_id' => 'required|integer|is_not_unique[usuarios.usuario_id]',
        'nombre' => 'required|min_length[2]|max_length[50]',
        'apellido' => 'required|min_length[2]|max_length[50]',
        'telefono' => 'permit_empty|min_length[7]|max_length[15]',
        'direccion' => 'permit_empty|max_length[255]',
        'fecha_nacimiento' => 'permit_empty|valid_date',
        'genero' => 'permit_empty|in_list[M,F,O]',
        'foto' => 'permit_empty|max_length[255]'
    ];

    protected $validationMessages = [
        'usuario_id' => [
            'required' => 'El ID del usuario es requerido',
            'integer' => 'El ID del usuario debe ser un número entero',
            'is_not_unique' => 'El usuario especificado no existe'
        ],
        'nombre' => [
            'required' => 'El nombre es requerido',
            'min_length' => 'El nombre debe tener al menos 2 caracteres',
            'max_length' => 'El nombre no puede exceder los 50 caracteres'
        ],
        'apellido' => [
            'required' => 'El apellido es requerido',
            'min_length' => 'El apellido debe tener al menos 2 caracteres',
            'max_length' => 'El apellido no puede exceder los 50 caracteres'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene el usuario asociado a este perfil
     * @return \CodeIgniter\Database\BaseResult
     */
    public function usuario()
    {
        return $this->belongsTo('App\Models\UsuarioModel', 'usuario_id', 'usuario_id');
    }
} 