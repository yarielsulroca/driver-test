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
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'email',
        'horario',
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
        'codigo' => 'required|min_length[1]|max_length[10]|is_unique[escuelas.codigo]',
        'nombre' => 'required|min_length[1]|max_length[100]|is_unique[escuelas.nombre]',
        'direccion' => 'required|min_length[1]|max_length[255]',
        'telefono' => 'required|min_length[1]|max_length[20]',
        'email' => 'required|valid_email|max_length[100]',
        'horario' => 'required|min_length[1]|max_length[100]',
        'estado' => 'required|in_list[activo,inactivo]'
    ];
    protected $validationMessages = [
        'codigo' => [
            'required' => 'El código es obligatorio',
            'min_length' => 'El código debe tener al menos 1 carácter',
            'max_length' => 'El código no puede tener más de 10 caracteres',
            'is_unique' => 'Ya existe una escuela con este código'
        ],
        'nombre' => [
            'required' => 'El nombre es obligatorio',
            'min_length' => 'El nombre debe tener al menos 1 carácter',
            'max_length' => 'El nombre no puede tener más de 100 caracteres',
            'is_unique' => 'Ya existe una escuela con este nombre'
        ],
        'direccion' => [
            'required' => 'La dirección es obligatoria',
            'min_length' => 'La dirección debe tener al menos 1 carácter',
            'max_length' => 'La dirección no puede tener más de 255 caracteres'
        ],
        'telefono' => [
            'required' => 'El teléfono es obligatorio',
            'min_length' => 'El teléfono debe tener al menos 1 carácter',
            'max_length' => 'El teléfono no puede tener más de 20 caracteres'
        ],
        'email' => [
            'required' => 'El email es obligatorio',
            'valid_email' => 'El email debe ser válido',
            'max_length' => 'El email no puede tener más de 100 caracteres'
        ],
        'horario' => [
            'required' => 'El horario es obligatorio',
            'min_length' => 'El horario debe tener al menos 1 carácter',
            'max_length' => 'El horario no puede tener más de 100 caracteres'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio',
            'in_list' => 'El estado debe ser activo o inactivo'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    public function examenes()
    {
        return $this->hasMany('App\Models\ExamenModel', 'escuela_id', 'escuela_id');
    }
} 