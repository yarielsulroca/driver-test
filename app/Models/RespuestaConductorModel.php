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
        'resultado_examen_id',
        'pregunta_id',
        'respuesta_id',
        'es_correcta',
        'tiempo_respuesta'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'resultado_examen_id' => 'required|integer',
        'pregunta_id' => 'required|integer',
        'respuesta_id' => 'required|integer',
        'es_correcta' => 'required|in_list[0,1]',
        'tiempo_respuesta' => 'required|integer'
    ];

    // Relaciones
    public function resultadoExamen()
    {
        return $this->belongsTo('App\Models\ResultadoExamenModel', 'resultado_examen_id', 'resultado_id');
    }

    public function pregunta()
    {
        return $this->belongsTo('App\Models\PreguntaModel', 'pregunta_id', 'pregunta_id');
    }

    public function respuesta()
    {
        return $this->belongsTo('App\Models\RespuestaModel', 'respuesta_id', 'respuesta_id');
    }
} 