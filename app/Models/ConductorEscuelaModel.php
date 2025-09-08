<?php

namespace App\Models;

use CodeIgniter\Model;

class ConductorEscuelaModel extends Model
{
    protected $table = 'conductor_escuela';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'conductor_id',
        'escuela_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'conductor_id' => 'required|integer|is_not_unique[conductores.conductor_id]',
        'escuela_id' => 'required|integer|is_not_unique[escuelas.escuela_id]'
    ];

    protected $validationMessages = [
        'conductor_id' => [
            'required' => 'El ID del conductor es requerido',
            'integer' => 'El ID del conductor debe ser un número entero',
            'is_not_unique' => 'El conductor especificado no existe'
        ],
        'escuela_id' => [
            'required' => 'El ID de la escuela es requerido',
            'integer' => 'El ID de la escuela debe ser un número entero',
            'is_not_unique' => 'La escuela especificada no existe'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Asignar escuelas a un conductor
     */
    public function asignarEscuelas($conductorId, $escuelaIds)
    {
        // Eliminar asignaciones existentes
        $this->where('conductor_id', $conductorId)->delete();

        // Insertar nuevas asignaciones
        $data = [];
        foreach ($escuelaIds as $escuelaId) {
            $data[] = [
                'conductor_id' => $conductorId,
                'escuela_id' => $escuelaId
            ];
        }

        if (!empty($data)) {
            return $this->insertBatch($data);
        }

        return true;
    }

    /**
     * Obtener escuelas de un conductor
     */
    public function getEscuelasConductor($conductorId)
    {
        return $this->select('escuelas.*')
                    ->join('escuelas', 'escuelas.escuela_id = conductor_escuela.escuela_id')
                    ->where('conductor_escuela.conductor_id', $conductorId)
                    ->findAll();
    }

    /**
     * Obtener conductores de una escuela
     */
    public function getConductoresEscuela($escuelaId)
    {
        return $this->select('conductores.*')
                    ->join('conductores', 'conductores.conductor_id = conductor_escuela.conductor_id')
                    ->where('conductor_escuela.escuela_id', $escuelaId)
                    ->findAll();
    }

    /**
     * Obtener IDs de escuelas de un conductor
     */
    public function getEscuelaIdsConductor($conductorId)
    {
        $result = $this->select('escuela_id')
                       ->where('conductor_id', $conductorId)
                       ->findAll();
        
        return array_column($result, 'escuela_id');
    }
} 