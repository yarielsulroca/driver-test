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
        'supervisor_id',
        'fecha_asignacion',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'aprobado',
        'puntuacion_final'
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
        'supervisor_id' => 'required|integer',
        'fecha_asignacion' => 'required|valid_date',
        'estado' => 'required|in_list[pendiente,en_progreso,completado,aprobado,reprobado]'
    ];

    protected $validationMessages = [
        'examen_id' => [
            'required' => 'El ID del examen es obligatorio',
            'integer' => 'El ID del examen debe ser un número entero'
        ],
        'conductor_id' => [
            'required' => 'El ID del conductor es obligatorio',
            'integer' => 'El ID del conductor debe ser un número entero'
        ],
        'supervisor_id' => [
            'required' => 'El ID del supervisor es obligatorio',
            'integer' => 'El ID del supervisor debe ser un número entero'
        ],
        'fecha_asignacion' => [
            'required' => 'La fecha de asignación es obligatoria',
            'valid_date' => 'La fecha de asignación debe ser una fecha válida'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio',
            'in_list' => 'El estado debe ser uno de: pendiente, en_progreso, completado, aprobado, reprobado'
        ]
    ];

    // Relaciones
    public function examen()
    {
        return $this->belongsTo('App\Models\ExamenModel', 'examen_id', 'examen_id');
    }

    public function conductor()
    {
        return $this->belongsTo('App\Models\ConductorModel', 'conductor_id', 'conductor_id');
    }

    public function supervisor()
    {
        return $this->belongsTo('App\Models\SupervisorModel', 'supervisor_id', 'supervisor_id');
    }

    public function paginas()
    {
        return $this->hasMany('App\Models\PaginaConductorModel', 'examen_conductor_id', 'examen_conductor_id');
    }

    /**
     * Asignar un examen a un conductor
     */
    public function asignarExamen($examen_id, $conductor_id, $supervisor_id)
    {
        $data = [
            'examen_id' => $examen_id,
            'conductor_id' => $conductor_id,
            'supervisor_id' => $supervisor_id,
            'fecha_asignacion' => date('Y-m-d H:i:s'),
            'estado' => 'pendiente'
        ];

        return $this->insert($data);
    }

    /**
     * Iniciar un examen
     */
    public function iniciarExamen($examen_conductor_id)
    {
        return $this->update($examen_conductor_id, [
            'estado' => 'en_progreso',
            'fecha_inicio' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Finalizar un examen
     */
    public function finalizarExamen($examen_conductor_id, $aprobado = false)
    {
        return $this->update($examen_conductor_id, [
            'estado' => $aprobado ? 'aprobado' : 'reprobado',
            'fecha_fin' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Obtener exámenes de un conductor
     */
    public function getExamenesConductor($conductor_id)
    {
        return $this->select('examen_conductor.*, examenes.*')
                    ->join('examenes', 'examenes.examen_id = examen_conductor.examen_id')
                    ->where('conductor_id', $conductor_id)
                    ->findAll();
    }

    /**
     * Obtener conductores de un examen
     */
    public function getConductoresExamen($examen_id)
    {
        return $this->select('examen_conductor.*, conductores.*')
                    ->join('conductores', 'conductores.conductor_id = examen_conductor.conductor_id')
                    ->where('examen_id', $examen_id)
                    ->findAll();
    }

    /**
     * Verifica si un conductor puede presentar un examen
     */
    public function puedePresentarExamen($conductor_id, $examen_id)
    {
        $examen = $this->where('conductor_id', $conductor_id)
                      ->where('examen_id', $examen_id)
                      ->first();

        if (!$examen) {
            return [
                'puede_presentar' => false,
                'mensaje' => 'No tiene este examen asignado'
            ];
        }

        if ($examen['estado'] === 'completado' || $examen['estado'] === 'aprobado' || $examen['estado'] === 'reprobado') {
            return [
                'puede_presentar' => false,
                'mensaje' => 'Ya ha completado este examen'
            ];
        }

        if ($examen['estado'] === 'en_progreso') {
            return [
                'puede_presentar' => true,
                'mensaje' => 'Puede continuar con el examen en progreso'
            ];
        }

        return [
            'puede_presentar' => true,
            'mensaje' => 'Puede iniciar el examen'
        ];
    }
} 