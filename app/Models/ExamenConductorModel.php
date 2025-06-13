<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamenConductorModel extends Model
{
    protected $table = 'examen_conductor';
    protected $primaryKey = 'examen_conductor_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
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
        'estado' => 'required|in_list[pendiente,en_progreso,completado,aprobado,reprobado]',
        'fecha_inicio' => 'permit_empty|valid_date',
        'fecha_fin' => 'permit_empty|valid_date',
        'puntaje_obtenido' => 'permit_empty|numeric',
        'tiempo_utilizado' => 'permit_empty|integer',
        'intentos_restantes' => 'permit_empty|integer'
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
            'required' => 'El estado es requerido',
            'in_list' => 'El estado debe ser uno de: pendiente, en_progreso, completado, aprobado, reprobado'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene el examen asociado
     * @return \CodeIgniter\Database\BaseResult
     */
    public function examen()
    {
        return $this->belongsTo('App\Models\ExamenModel', 'examen_id', 'examen_id');
    }

    /**
     * Obtiene el conductor asociado
     * @return \CodeIgniter\Database\BaseResult
     */
    public function conductor()
    {
        return $this->belongsTo('App\Models\ConductorModel', 'conductor_id', 'conductor_id');
    }

    /**
     * Obtiene el estado del examen
     * @return \CodeIgniter\Database\BaseResult
     */
    public function estado()
    {
        return $this->belongsTo('App\Models\EstadoExamenModel', 'estado_id', 'estado_id');
    }

    /**
     * Obtiene todas las sesiones de este examen
     * @return \CodeIgniter\Database\BaseResult
     */
    public function sesiones()
    {
        return $this->hasMany('App\Models\SesionExamenModel', 'examen_conductor_id', 'examen_conductor_id');
    }

    /**
     * Obtiene todas las respuestas del conductor en este examen
     * @return \CodeIgniter\Database\BaseResult
     */
    public function respuestas()
    {
        return $this->hasMany('App\Models\RespuestaConductorModel', 'examen_conductor_id', 'examen_conductor_id');
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