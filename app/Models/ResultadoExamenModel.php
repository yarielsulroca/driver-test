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
        'estado',
        'bloqueado',
        'fecha_bloqueo'
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

    public function respuestas()
    {
        return $this->hasMany('App\Models\RespuestaConductorModel', 'resultado_examen_id', 'resultado_id');
    }

    /**
     * Verifica si un conductor puede presentar un examen
     */
    public function puedePresentarExamen($conductor_id, $categoria_id)
    {
        $ultimoResultado = $this->where('conductor_id', $conductor_id)
                               ->join('examenes', 'examenes.examen_id = resultados_examenes.examen_id')
                               ->where('examenes.categoria_id', $categoria_id)
                               ->orderBy('fecha_realizacion', 'DESC')
                               ->first();

        if (!$ultimoResultado) {
            return [
                'puede_presentar' => true,
                'mensaje' => 'Puede presentar el examen'
            ];
        }

        if ($ultimoResultado['estado'] === 'aprobado') {
            return [
                'puede_presentar' => true,
                'mensaje' => 'Puede presentar el examen'
            ];
        }

        if ($ultimoResultado['bloqueado'] && $ultimoResultado['fecha_bloqueo']) {
            $fechaActual = new \DateTime();
            $fechaBloqueo = new \DateTime($ultimoResultado['fecha_bloqueo']);
            $diasTranscurridos = $this->calcularDiasLaborales($fechaBloqueo, $fechaActual);

            if ($diasTranscurridos >= 7) {
                $this->update($ultimoResultado['resultado_id'], [
                    'bloqueado' => false,
                    'fecha_bloqueo' => null
                ]);

                return [
                    'puede_presentar' => true,
                    'mensaje' => 'Puede presentar el examen'
                ];
            }

            return [
                'puede_presentar' => false,
                'mensaje' => 'Debe esperar ' . (7 - $diasTranscurridos) . ' días laborales para presentar el examen nuevamente'
            ];
        }

        return [
            'puede_presentar' => true,
            'mensaje' => 'Puede presentar el examen'
        ];
    }

    /**
     * Calcula los días laborales entre dos fechas
     */
    private function calcularDiasLaborales($fechaInicio, $fechaFin)
    {
        $diasLaborales = 0;
        $fechaActual = clone $fechaInicio;

        while ($fechaActual <= $fechaFin) {
            if ($fechaActual->format('N') < 6) {
                $diasLaborales++;
            }
            $fechaActual->modify('+1 day');
        }

        return $diasLaborales;
    }

    /**
     * Registra un nuevo resultado de examen
     */
    public function registrarResultado($data)
    {
        if ($data['estado'] === 'reprobado') {
            $data['bloqueado'] = true;
            $data['fecha_bloqueo'] = date('Y-m-d H:i:s');
        }

        return $this->insert($data);
    }
} 