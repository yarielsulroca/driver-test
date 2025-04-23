<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamenModel extends Model
{
    protected $table = 'examenes';
    protected $primaryKey = 'examen_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'escuela_id',
        'categoria_id',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'duracion_minutos',
        'puntaje_minimo',
        'numero_preguntas'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'escuela_id' => 'required|integer',
        'categoria_id' => 'required|integer',
        'nombre' => 'required|min_length[3]|max_length[100]',
        'descripcion' => 'required|min_length[10]',
        'fecha_inicio' => 'required|valid_date',
        'fecha_fin' => 'required|valid_date',
        'duracion_minutos' => 'required|integer|greater_than[0]',
        'puntaje_minimo' => 'required|numeric|greater_than[0]',
        'numero_preguntas' => 'required|integer|greater_than[0]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    public function escuela()
    {
        return $this->belongsTo('App\Models\EscuelaModel', 'escuela_id', 'escuela_id');
    }

    public function categoria()
    {
        return $this->belongsTo('App\Models\CategoriaModel', 'categoria_id', 'categoria_id');
    }

    public function resultados()
    {
        return $this->hasMany('App\Models\ResultadoExamenModel', 'examen_id', 'examen_id');
    }

    public function preguntas()
    {
        return $this->hasMany('App\Models\PreguntaModel', 'examen_id', 'examen_id');
    }
} 