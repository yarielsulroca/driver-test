<?php

namespace App\Models;

use CodeIgniter\Model;

class ResultadoModel extends Model
{
    protected $table            = 'resultados';
    protected $primaryKey       = 'resultado_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'examen_id',
        'usuario_id',
        'puntuacion',
        'fecha_realizacion',
        'estado',
        'fecha_bloqueo',
        'bloqueado'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'examen_id' => 'required|numeric',
        'usuario_id' => 'required|numeric',
        'puntuacion' => 'required|numeric',
        'estado' => 'required|in_list[aprobado,reprobado]',
    ];

    protected $validationMessages = [
        'examen_id' => [
            'required' => 'El ID del examen es requerido',
            'numeric' => 'El ID del examen debe ser numérico'
        ],
        'usuario_id' => [
            'required' => 'El ID del usuario es requerido',
            'numeric' => 'El ID del usuario debe ser numérico'
        ],
        'puntuacion' => [
            'required' => 'La puntuación es requerida',
            'numeric' => 'La puntuación debe ser numérica'
        ],
        'estado' => [
            'required' => 'El estado es requerido',
            'in_list' => 'El estado debe ser aprobado o reprobado'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Verifica si un usuario puede presentar un examen
     * 
     * @param int $usuario_id
     * @return array
     */
    public function puedePresentarExamen($usuario_id)
    {
        $ultimoResultado = $this->where('usuario_id', $usuario_id)
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
                // Actualizar el estado de bloqueo
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
     * 
     * @param \DateTime $fechaInicio
     * @param \DateTime $fechaFin
     * @return int
     */
    private function calcularDiasLaborales($fechaInicio, $fechaFin)
    {
        $diasLaborales = 0;
        $fechaActual = clone $fechaInicio;

        while ($fechaActual <= $fechaFin) {
            // Verificar si es día laboral (lunes a viernes)
            if ($fechaActual->format('N') < 6) {
                $diasLaborales++;
            }
            $fechaActual->modify('+1 day');
        }

        return $diasLaborales;
    }

    /**
     * Registra un nuevo resultado de examen
     * 
     * @param array $data
     * @return int|false
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
