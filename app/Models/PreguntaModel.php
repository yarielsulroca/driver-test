<?php

namespace App\Models;

use CodeIgniter\Model;

class PreguntaModel extends Model
{
    protected $table = 'preguntas';
    protected $primaryKey = 'pregunta_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'enunciado',
        'tipo_pregunta',
        'puntaje',
        'dificultad',
        'es_critica'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'enunciado' => 'required|min_length[10]|max_length[1000]',
        'tipo_pregunta' => 'required|in_list[multiple,unica,verdadero_falso]',
        'puntaje' => 'required|numeric|greater_than[0]',
        'dificultad' => 'required|in_list[baja,media,alta]',
        'es_critica' => 'required|in_list[0,1]'
    ];
    protected $validationMessages = [
        'enunciado' => [
            'required' => 'El enunciado es requerido',
            'min_length' => 'El enunciado debe tener al menos 10 caracteres',
            'max_length' => 'El enunciado no puede exceder los 1000 caracteres'
        ],
        'tipo_pregunta' => [
            'required' => 'El tipo de pregunta es requerido',
            'in_list' => 'El tipo de pregunta debe ser múltiple, única o verdadero/falso'
        ],
        'puntaje' => [
            'required' => 'El puntaje es requerido',
            'numeric' => 'El puntaje debe ser un número',
            'greater_than' => 'El puntaje debe ser mayor a 0'
        ],
        'dificultad' => [
            'required' => 'La dificultad es requerida',
            'in_list' => 'La dificultad debe ser baja, media o alta'
        ],
        'es_critica' => [
            'required' => 'El campo es_critica es requerido',
            'in_list' => 'El valor debe ser 0 o 1'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    /**
     * Obtiene todas las respuestas asociadas a la pregunta
     * @return \CodeIgniter\Database\BaseResult
     */
    public function respuestas()
    {
        return $this->hasMany('App\Models\RespuestaModel', 'pregunta_id', 'pregunta_id');
    }

    /**
     * Obtiene todos los exámenes en los que aparece esta pregunta
     * @return \CodeIgniter\Database\BaseResult
     */
    public function examenes()
    {
        return $this->belongsToMany(
            'App\Models\ExamenModel',
            'examen_pregunta',
            'pregunta_id',
            'examen_id'
        );
    }

    /**
     * Obtiene todas las respuestas de conductores para esta pregunta
     * @return \CodeIgniter\Database\BaseResult
     */
    public function respuestasConductor()
    {
        return $this->hasMany('App\Models\RespuestaConductorModel', 'pregunta_id', 'pregunta_id');
    }
} 