<?php

namespace App\Models;

use CodeIgniter\Model;

class ResultadoExamenModel extends Model
{
    protected $table = 'resultados_examenes';
    protected $primaryKey = 'resultado_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'conductor_id',
        'examen_id',
        'puntaje_total',
        'preguntas_correctas',
        'preguntas_incorrectas',
        'tiempo_empleado',
        'fecha_realizacion',
        'estado'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'conductor_id' => 'required|integer',
        'examen_id' => 'required|integer',
        'puntaje_total' => 'required|numeric',
        'preguntas_correctas' => 'required|integer',
        'preguntas_incorrectas' => 'required|integer',
        'tiempo_empleado' => 'required|integer',
        'fecha_realizacion' => 'required|valid_date',
        'estado' => 'required|in_list[aprobado,reprobado]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    public function conductor()
    {
        return $this->belongsTo('App\Models\ConductorModel', 'conductor_id', 'conductor_id');
    }

    public function examen()
    {
        return $this->belongsTo('App\Models\ExamenModel', 'examen_id', 'examen_id');
    }
} 