<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamenPreguntaModel extends Model
{
    protected $table = 'examen_pregunta';
    protected $primaryKey = 'examen_pregunta_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'examen_id',
        'pregunta_id',
        'orden'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'examen_id' => 'required|integer',
        'pregunta_id' => 'required|integer',
        'orden' => 'required|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'examen_id' => [
            'required' => 'El examen es requerido',
            'integer' => 'El examen debe ser un número entero'
        ],
        'pregunta_id' => [
            'required' => 'La pregunta es requerida',
            'integer' => 'La pregunta debe ser un número entero'
        ],
        'orden' => [
            'required' => 'El orden es requerido',
            'integer' => 'El orden debe ser un número entero',
            'greater_than' => 'El orden debe ser mayor a 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene el examen al que pertenece esta asignación
     * @return \CodeIgniter\Database\BaseResult
     */
    public function examen()
    {
        return $this->belongsTo('App\Models\ExamenModel', 'examen_id', 'examen_id');
    }

    /**
     * Obtiene la pregunta asignada a este examen
     * @return \CodeIgniter\Database\BaseResult
     */
    public function pregunta()
    {
        return $this->belongsTo('App\Models\PreguntaModel', 'pregunta_id', 'pregunta_id');
    }
} 