<?php

namespace App\Models;

use CodeIgniter\Model;

class SesionExamenModel extends Model
{
    protected $table = 'sesiones_examen';
    protected $primaryKey = 'sesion_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'examen_conductor_id',
        'fecha_inicio',
        'fecha_fin',
        'tiempo_total',
        'ip_address',
        'user_agent'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'examen_conductor_id' => 'required|integer|is_not_unique[examen_conductor.examen_conductor_id]',
        'fecha_inicio' => 'required|valid_date',
        'fecha_fin' => 'permit_empty|valid_date',
        'tiempo_total' => 'permit_empty|integer',
        'ip_address' => 'permit_empty|valid_ip',
        'user_agent' => 'permit_empty|max_length[255]'
    ];

    protected $validationMessages = [
        'examen_conductor_id' => [
            'required' => 'El ID del examen-conductor es requerido',
            'integer' => 'El ID debe ser un número entero',
            'is_not_unique' => 'El examen-conductor especificado no existe'
        ],
        'fecha_inicio' => [
            'required' => 'La fecha de inicio es requerida',
            'valid_date' => 'La fecha de inicio debe ser una fecha válida'
        ],
        'fecha_fin' => [
            'valid_date' => 'La fecha de fin debe ser una fecha válida'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene el examen-conductor asociado a esta sesión
     * @return \CodeIgniter\Database\BaseResult
     */
    public function examenConductor()
    {
        return $this->belongsTo('App\Models\ExamenConductorModel', 'examen_conductor_id', 'examen_conductor_id');
    }
} 