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
        'examen_id',
        'categoria_id',
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
        'examen_id' => 'required|integer',
        'categoria_id' => 'required|integer',
        'enunciado' => 'required|min_length[10]',
        'tipo_pregunta' => 'required|in_list[multiple,verdadero_falso]',
        'puntaje' => 'required|numeric|greater_than[0]',
        'dificultad' => 'required|in_list[baja,media,alta]',
        'es_critica' => 'required|in_list[0,1]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    public function examen()
    {
        return $this->belongsTo('App\Models\ExamenModel', 'examen_id', 'examen_id');
    }

    public function categoria()
    {
        return $this->belongsTo('App\Models\CategoriaModel', 'categoria_id', 'categoria_id');
    }

    public function respuestas()
    {
        return $this->hasMany('App\Models\RespuestaModel', 'pregunta_id', 'pregunta_id');
    }
} 