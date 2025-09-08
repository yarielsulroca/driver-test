<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamenEscuelaModel extends Model
{
    protected $table = 'examen_escuela';
    protected $primaryKey = 'examen_escuela_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'examen_id',
        'escuela_id',
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
        'examen_id' => 'required|integer',
        'escuela_id' => 'required|integer',
        'estado' => 'required|in_list[activo,inactivo]'
    ];

    protected $validationMessages = [
        'examen_id' => [
            'required' => 'El ID del examen es requerido',
            'integer' => 'El ID del examen debe ser un número entero'
        ],
        'escuela_id' => [
            'required' => 'El ID de la escuela es requerido',
            'integer' => 'El ID de la escuela debe ser un número entero'
        ],
        'estado' => [
            'required' => 'El estado es requerido',
            'in_list' => 'El estado debe ser activo o inactivo'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    public function examen()
    {
        return $this->belongsTo('App\Models\ExamenModel', 'examen_id', 'examen_id');
    }

    public function escuela()
    {
        return $this->belongsTo('App\Models\EscuelaModel', 'escuela_id', 'escuela_id');
    }

    /**
     * Asignar un examen a una escuela
     */
    public function asignarExamenEscuela($examen_id, $escuela_id)
    {
        // Verificar si ya existe la relación
        $existe = $this->where('examen_id', $examen_id)
                      ->where('escuela_id', $escuela_id)
                      ->first();
        
        if ($existe) {
            return false; // Ya existe la relación
        }

        return $this->insert([
            'examen_id' => $examen_id,
            'escuela_id' => $escuela_id,
            'estado' => 'activo'
        ]);
    }

    /**
     * Desasignar un examen de una escuela
     */
    public function desasignarExamenEscuela($examen_id, $escuela_id)
    {
        return $this->where('examen_id', $examen_id)
                   ->where('escuela_id', $escuela_id)
                   ->delete();
    }

    /**
     * Obtener todas las escuelas de un examen
     */
    public function getEscuelasExamen($examen_id)
    {
        return $this->select('escuelas.*')
                   ->join('escuelas', 'escuelas.escuela_id = examen_escuela.escuela_id')
                   ->where('examen_escuela.examen_id', $examen_id)
                   ->where('examen_escuela.estado', 'activo')
                   ->findAll();
    }

    /**
     * Obtener todos los exámenes de una escuela
     */
    public function getExamenesEscuela($escuela_id)
    {
        return $this->select('examenes.*')
                   ->join('examenes', 'examenes.examen_id = examen_escuela.examen_id')
                   ->where('examen_escuela.escuela_id', $escuela_id)
                   ->where('examen_escuela.estado', 'activo')
                   ->findAll();
    }
}
