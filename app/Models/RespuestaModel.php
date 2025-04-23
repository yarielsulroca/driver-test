<?php

namespace App\Models;

use CodeIgniter\Model;

class RespuestaModel extends Model
{
    protected $table = 'respuestas';
    protected $primaryKey = 'respuesta_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'pregunta_id',
        'texto',
        'es_correcta'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'pregunta_id' => 'required|integer',
        'texto' => 'required|min_length[1]',
        'es_correcta' => 'required|in_list[0,1]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    public function pregunta()
    {
        return $this->belongsTo('App\Models\PreguntaModel', 'pregunta_id', 'pregunta_id');
    }
} 