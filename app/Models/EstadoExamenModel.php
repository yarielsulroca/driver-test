<?php

namespace App\Models;

use CodeIgniter\Model;

class EstadoExamenModel extends Model
{
    protected $table = 'estados_examen';
    protected $primaryKey = 'estado_id';
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
        'nombre' => 'required|min_length[3]|max_length[50]|is_unique[estados_examen.nombre,estado_id,{estado_id}]',
        'descripcion' => 'permit_empty|max_length[255]'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre del estado es requerido',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder los 50 caracteres',
            'is_unique' => 'Ya existe un estado con este nombre'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene todos los exámenes con este estado
     * @return \CodeIgniter\Database\BaseResult
     */
    public function examenes()
    {
        return $this->hasMany('App\Models\ExamenConductorModel', 'estado_id', 'estado_id');
    }
} 