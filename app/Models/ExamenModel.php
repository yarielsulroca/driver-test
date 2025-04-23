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
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'duracion_minutos',
        'puntaje_minimo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    public function escuela()
    {
        return $this->belongsTo('App\Models\EscuelaModel', 'escuela_id', 'escuela_id');
    }

    public function resultados()
    {
        return $this->hasMany('App\Models\ResultadoModel', 'examen_id', 'examen_id');
    }

    public function preguntas()
    {
        return $this->hasMany('App\Models\PreguntaModel', 'examen_id', 'examen_id');
    }
} 