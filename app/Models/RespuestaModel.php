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
        'valor',
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
        'texto' => 'required|min_length[1]|max_length[500]',
        'valor' => 'required|numeric',
        'es_correcta' => 'required|in_list[0,1]'
    ];
    protected $validationMessages = [
        'pregunta_id' => [
            'required' => 'La pregunta es requerida',
            'integer' => 'La pregunta debe ser un número entero'
        ],
        'texto' => [
            'required' => 'El texto de la respuesta es requerido',
            'min_length' => 'El texto debe tener al menos 1 carácter',
            'max_length' => 'El texto no puede exceder los 500 caracteres'
        ],
        'valor' => [
            'required' => 'El valor es requerido',
            'numeric' => 'El valor debe ser un número'
        ],
        'es_correcta' => [
            'required' => 'El campo es_correcta es requerido',
            'in_list' => 'El valor debe ser 0 o 1'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene la pregunta a la que pertenece esta respuesta
     * @return \CodeIgniter\Database\BaseResult
     */
    public function pregunta()
    {
        return $this->belongsTo('App\Models\PreguntaModel', 'pregunta_id', 'pregunta_id');
    }

    /**
     * Obtiene todas las respuestas de conductores que seleccionaron esta respuesta
     * @return \CodeIgniter\Database\BaseResult
     */
    public function respuestasConductor()
    {
        return $this->hasMany('App\Models\RespuestaConductorModel', 'respuesta_id', 'respuesta_id');
    }
} 