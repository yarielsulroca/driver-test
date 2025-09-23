<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamenConductorModel extends Model
{
    protected $table = 'examen_conductor';
    protected $primaryKey = 'examen_conductor_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'examen_id',
        'conductor_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'puntaje_obtenido',
        'tiempo_utilizado',
        'intentos_restantes'
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
        'conductor_id' => 'required|integer',
        'estado' => 'in_list[pendiente,en_progreso,completado,aprobado,reprobado]',
        'puntaje_obtenido' => 'permit_empty|decimal|less_than_equal_to[100]',
        'tiempo_utilizado' => 'permit_empty|integer',
        'intentos_restantes' => 'permit_empty|integer|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'examen_id' => [
            'required' => 'El ID del examen es requerido',
            'integer' => 'El ID del examen debe ser un número entero'
        ],
        'conductor_id' => [
            'required' => 'El ID del conductor es requerido',
            'integer' => 'El ID del conductor debe ser un número entero'
        ],
        'estado' => [
            'in_list' => 'El estado debe ser: pendiente, en_progreso, completado, aprobado o reprobado'
        ],
        'puntaje_obtenido' => [
            'decimal' => 'El puntaje debe ser un número decimal',
            'less_than_equal_to' => 'El puntaje no puede ser mayor a 100'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener exámenes asignados a un conductor
     */
    public function getExamenesConductor($conductorId)
    {
        return $this->select('
            examen_conductor.*,
            examenes.titulo,
            examenes.descripcion,
            examenes.tiempo_limite,
            examenes.duracion_minutos,
            examenes.puntaje_minimo,
            examenes.dificultad
        ')
        ->join('examenes', 'examenes.examen_id = examen_conductor.examen_id')
        ->where('examen_conductor.conductor_id', $conductorId)
        ->findAll();
    }

    /**
     * Obtener conductores asignados a un examen
     */
    public function getConductoresExamen($examenId)
    {
        return $this->select('
            examen_conductor.*,
            conductores.conductor_id,
            usuarios.nombre,
            usuarios.apellido,
            usuarios.dni,
            usuarios.email
        ')
        ->join('conductores', 'conductores.conductor_id = examen_conductor.conductor_id')
        ->join('usuarios', 'usuarios.usuario_id = conductores.usuario_id')
        ->where('examen_conductor.examen_id', $examenId)
        ->findAll();
    }

    /**
     * Asignar examen a conductor
     */
    public function asignarExamen($examenId, $conductorId, $intentosRestantes = 3)
    {
        $data = [
            'examen_id' => $examenId,
            'conductor_id' => $conductorId,
            'estado' => 'pendiente',
            'intentos_restantes' => $intentosRestantes
        ];

        return $this->insert($data);
    }

    /**
     * Verificar si un conductor ya tiene asignado un examen
     */
    public function tieneExamenAsignado($examenId, $conductorId)
    {
        return $this->where('examen_id', $examenId)
                   ->where('conductor_id', $conductorId)
                   ->first() !== null;
    }

    /**
     * Obtener estadísticas de un conductor
     */
    public function getEstadisticasConductor($conductorId)
    {
        $stats = $this->select('
            COUNT(*) as total_examenes,
            SUM(CASE WHEN estado = "pendiente" THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN estado = "en_progreso" THEN 1 ELSE 0 END) as en_progreso,
            SUM(CASE WHEN estado = "completado" THEN 1 ELSE 0 END) as completados,
            SUM(CASE WHEN estado = "aprobado" THEN 1 ELSE 0 END) as aprobados,
            SUM(CASE WHEN estado = "reprobado" THEN 1 ELSE 0 END) as reprobados,
            AVG(puntaje_obtenido) as promedio_puntaje
        ')
        ->where('conductor_id', $conductorId)
        ->first();

        return $stats ?: [
            'total_examenes' => 0,
            'pendientes' => 0,
            'en_progreso' => 0,
            'completados' => 0,
            'aprobados' => 0,
            'reprobados' => 0,
            'promedio_puntaje' => 0
        ];
    }
}
