<?php

namespace App\Models;

use CodeIgniter\Model;

class EscuelaModel extends Model
{
    protected $table = 'escuelas';
    protected $primaryKey = 'escuela_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nombre',
        'direccion',
        'ciudad',
        'telefono',
        'email',
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
        'nombre' => 'required|min_length[3]|max_length[100]',
        'direccion' => 'required|min_length[5]|max_length[255]',
        'ciudad' => 'required|min_length[2]|max_length[100]',
        'telefono' => 'required|min_length[8]|max_length[15]',
        'email' => 'required|valid_email|max_length[100]',
        'estado' => 'required|in_list[activo,inactivo]'
    ];
    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede tener más de 100 caracteres'
        ],
        'direccion' => [
            'required' => 'La dirección es obligatoria',
            'min_length' => 'La dirección debe tener al menos 5 caracteres',
            'max_length' => 'La dirección no puede tener más de 255 caracteres'
        ],
        'ciudad' => [
            'required' => 'La ciudad es obligatoria',
            'min_length' => 'La ciudad debe tener al menos 2 caracteres',
            'max_length' => 'La ciudad no puede tener más de 100 caracteres'
        ],
        'telefono' => [
            'required' => 'El teléfono es obligatorio',
            'min_length' => 'El teléfono debe tener al menos 8 caracteres',
            'max_length' => 'El teléfono no puede tener más de 15 caracteres'
        ],
        'email' => [
            'required' => 'El email es obligatorio',
            'valid_email' => 'El email debe ser válido',
            'max_length' => 'El email no puede tener más de 100 caracteres'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio',
            'in_list' => 'El estado debe ser activo o inactivo'
        ]
    ];
    protected $skipValidation = true;
    protected $cleanValidationRules = true;

    // Relaciones
    public function examenes()
    {
        return $this->hasMany('App\Models\ExamenModel', 'escuela_id', 'escuela_id');
    }
} 