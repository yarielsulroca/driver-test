<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamenPreguntaModel extends Model
{
    protected $table = 'examen_pregunta';
    protected $primaryKey = 'examen_pregunta_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'examen_id',
        'pregunta_id',
        'orden'
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
        'pregunta_id' => 'required|integer',
        'orden' => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'examen_id' => [
            'required' => 'El examen es requerido',
            'integer' => 'El examen debe ser un número entero'
        ],
        'pregunta_id' => [
            'required' => 'La pregunta es requerida',
            'integer' => 'La pregunta debe ser un número entero'
        ],
        'orden' => [
            'integer' => 'El orden debe ser un número entero'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene todas las preguntas de un examen específico
     * @param int $examen_id ID del examen
     * @return array Lista de preguntas con sus respuestas
     */
    public function getPreguntasPorExamen($examen_id)
    {
        return $this->db->table('examen_pregunta')
            ->select('preguntas.*, examen_pregunta.orden')
            ->join('preguntas', 'preguntas.pregunta_id = examen_pregunta.pregunta_id')
            ->where('examen_pregunta.examen_id', $examen_id)
            ->orderBy('examen_pregunta.orden', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene todos los exámenes de una pregunta específica
     * @param int $pregunta_id ID de la pregunta
     * @return array Lista de exámenes
     */
    public function getExamenesPorPregunta($pregunta_id)
    {
        try {
            $result = $this->db->table('examen_pregunta')
                ->select('examenes.*')
                ->join('examenes', 'examenes.examen_id = examen_pregunta.examen_id')
                ->where('examen_pregunta.pregunta_id', $pregunta_id)
                ->get();
            
            return $result ? $result->getResultArray() : [];
        } catch (\Exception $e) {
            // Log del error para debugging
            log_message('error', 'Error en getExamenesPorPregunta: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Asigna preguntas a un examen
     * @param int $examen_id ID del examen
     * @param array $pregunta_ids Array de IDs de preguntas
     * @return bool True si se asignaron correctamente
     */
    public function asignarPreguntasAExamen($examen_id, $pregunta_ids)
    {
        // Eliminar asignaciones existentes
        $this->where('examen_id', $examen_id)->delete();

        // Insertar nuevas asignaciones
        $data = [];
        foreach ($pregunta_ids as $index => $pregunta_id) {
            $data[] = [
                'examen_id' => $examen_id,
                'pregunta_id' => $pregunta_id,
                'orden' => $index + 1
            ];
        }

        return $this->insertBatch($data);
    }

    /**
     * Elimina todas las asignaciones de un examen
     * @param int $examen_id ID del examen
     * @return bool True si se eliminaron correctamente
     */
    public function eliminarAsignacionesExamen($examen_id)
    {
        return $this->where('examen_id', $examen_id)->delete();
    }

    /**
     * Elimina todas las asignaciones de una pregunta
     * @param int $pregunta_id ID de la pregunta
     * @return bool True si se eliminaron correctamente
     */
    public function eliminarAsignacionesPregunta($pregunta_id)
    {
        return $this->where('pregunta_id', $pregunta_id)->delete();
    }
}
