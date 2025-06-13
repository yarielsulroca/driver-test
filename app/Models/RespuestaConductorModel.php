<?php

namespace App\Models;

use CodeIgniter\Model;

class RespuestaConductorModel extends Model
{
    protected $table = 'respuestas_conductor';
    protected $primaryKey = 'respuesta_conductor_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'examen_conductor_id',
        'pregunta_id',
        'respuesta_id',
        'puntaje_obtenido'
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
        'pregunta_id' => 'required|integer|is_not_unique[preguntas.pregunta_id]',
        'respuesta_id' => 'permit_empty|integer|is_not_unique[respuestas.respuesta_id]',
        'puntaje_obtenido' => 'permit_empty|numeric'
    ];

    protected $validationMessages = [
        'examen_conductor_id' => [
            'required' => 'El ID del examen-conductor es requerido',
            'integer' => 'El ID debe ser un número entero',
            'is_not_unique' => 'El examen-conductor especificado no existe'
        ],
        'pregunta_id' => [
            'required' => 'El ID de la pregunta es requerido',
            'integer' => 'El ID debe ser un número entero',
            'is_not_unique' => 'La pregunta especificada no existe'
        ],
        'respuesta_id' => [
            'integer' => 'El ID debe ser un número entero',
            'is_not_unique' => 'La respuesta especificada no existe'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene el examen-conductor asociado
     * @return \CodeIgniter\Database\BaseResult
     */
    public function examenConductor()
    {
        return $this->belongsTo('App\Models\ExamenConductorModel', 'examen_conductor_id', 'examen_conductor_id');
    }

    /**
     * Obtiene la pregunta asociada
     * @return \CodeIgniter\Database\BaseResult
     */
    public function pregunta()
    {
        return $this->belongsTo('App\Models\PreguntaModel', 'pregunta_id', 'pregunta_id');
    }

    /**
     * Obtiene la respuesta seleccionada
     * @return \CodeIgniter\Database\BaseResult
     */
    public function respuesta()
    {
        return $this->belongsTo('App\Models\RespuestaModel', 'respuesta_id', 'respuesta_id');
    }
} 