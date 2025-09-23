<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamenAsignadoModel extends Model
{
    protected $table = 'examen_asignado';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'conductor_id',
        'examen_id',
        'intentos_disponibles',
        'aprobado',
        'fecha_asignacion',
        'fecha_aprobacion',
        'puntaje_final'
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
        'intentos_disponibles' => 'permit_empty|integer|greater_than_equal_to[0]',
        'aprobado' => 'permit_empty|in_list[0,1]',
        'puntaje_final' => 'permit_empty|decimal|less_than_equal_to[100]'
    ];

    protected $validationMessages = [
        'conductor_id' => [
            'required' => 'El ID del conductor es requerido',
            'integer' => 'El ID del conductor debe ser un número entero'
        ],
        'examen_id' => [
            'required' => 'El ID del examen es requerido',
            'integer' => 'El ID del examen debe ser un número entero'
        ],
        'intentos_disponibles' => [
            'integer' => 'Los intentos disponibles deben ser un número entero',
            'greater_than_equal_to' => 'Los intentos disponibles deben ser mayor o igual a 0'
        ],
        'aprobado' => [
            'in_list' => 'El estado de aprobado debe ser 0 o 1'
        ],
        'puntaje_final' => [
            'decimal' => 'El puntaje final debe ser un número decimal',
            'less_than_equal_to' => 'El puntaje final no puede ser mayor a 100'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Asignar examen a conductor
     */
    public function asignarExamen($conductorId, $examenId, $intentosDisponibles = 3)
    {
        $data = [
            'conductor_id' => $conductorId,
            'examen_id' => $examenId,
            'intentos_disponibles' => $intentosDisponibles,
            'aprobado' => 0
        ];

        return $this->insert($data);
    }

    /**
     * Verificar si un conductor ya tiene asignado un examen
     */
    public function tieneExamenAsignado($conductorId, $examenId)
    {
        return $this->where('conductor_id', $conductorId)
                   ->where('examen_id', $examenId)
                   ->first() !== null;
    }

    /**
     * Obtener exámenes asignados a un conductor
     */
    public function getExamenesConductor($conductorId)
    {
        return $this->select('
            examen_asignado.*,
            examenes.titulo,
            examenes.descripcion,
            examenes.tiempo_limite,
            examenes.duracion_minutos,
            examenes.puntaje_minimo,
            examenes.dificultad
        ')
        ->join('examenes', 'examenes.examen_id = examen_asignado.examen_id')
        ->where('examen_asignado.conductor_id', $conductorId)
        ->findAll();
    }

    /**
     * Obtener conductores asignados a un examen
     */
    public function getConductoresExamen($examenId)
    {
        return $this->select('
            examen_asignado.*,
            conductores.conductor_id,
            usuarios.nombre,
            usuarios.apellido,
            usuarios.dni,
            usuarios.email
        ')
        ->join('conductores', 'conductores.conductor_id = examen_asignado.conductor_id')
        ->join('usuarios', 'usuarios.usuario_id = conductores.usuario_id')
        ->where('examen_asignado.examen_id', $examenId)
        ->findAll();
    }

    /**
     * Marcar examen como aprobado
     */
    public function marcarComoAprobado($id, $puntajeFinal)
    {
        $data = [
            'aprobado' => 1,
            'puntaje_final' => $puntajeFinal,
            'fecha_aprobacion' => date('Y-m-d H:i:s')
        ];

        return $this->update($id, $data);
    }

    /**
     * Reducir intentos disponibles
     */
    public function reducirIntentos($id)
    {
        $asignacion = $this->find($id);
        if ($asignacion && $asignacion['intentos_disponibles'] > 0) {
            return $this->update($id, ['intentos_disponibles' => $asignacion['intentos_disponibles'] - 1]);
        }
        return false;
    }

    /**
     * Obtener estadísticas de un conductor
     */
    public function getEstadisticasConductor($conductorId)
    {
        $stats = $this->select('
            COUNT(*) as total_examenes,
            SUM(CASE WHEN aprobado = 0 THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN aprobado = 1 THEN 1 ELSE 0 END) as aprobados,
            AVG(puntaje_final) as promedio_puntaje
        ')
        ->where('conductor_id', $conductorId)
        ->first();

        return $stats ?: [
            'total_examenes' => 0,
            'pendientes' => 0,
            'aprobados' => 0,
            'promedio_puntaje' => 0
        ];
    }
}
