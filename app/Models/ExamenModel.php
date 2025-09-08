<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamenModel extends Model
{
    protected $table = 'examenes';
    protected $primaryKey = 'examen_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'titulo',
        'nombre',
        'descripcion',
        'tiempo_limite',
        'duracion_minutos',
        'puntaje_minimo',
        'fecha_inicio',
        'fecha_fin',
        'numero_preguntas',
        'estado',
        'dificultad'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'titulo' => 'required|min_length[3]|max_length[255]',
        'nombre' => 'required|min_length[3]|max_length[255]',
        'descripcion' => 'permit_empty|max_length[1000]',
        'tiempo_limite' => 'required|integer|greater_than[0]',
        'duracion_minutos' => 'required|integer|greater_than[0]',
        'puntaje_minimo' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
        'fecha_inicio' => 'required|valid_date',
        'fecha_fin' => 'required|valid_date',
        'numero_preguntas' => 'permit_empty|integer|greater_than_equal_to[0]',
        'estado' => 'required|in_list[activo,inactivo]',
        'dificultad' => 'permit_empty|in_list[facil,medio,dificil]'
    ];

    protected $validationMessages = [
        'titulo' => [
            'required' => 'El título es obligatorio',
            'min_length' => 'El título debe tener al menos 3 caracteres',
            'max_length' => 'El título no puede tener más de 255 caracteres'
        ],
        'nombre' => [
            'required' => 'El nombre es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede tener más de 255 caracteres'
        ],
        'tiempo_limite' => [
            'required' => 'El tiempo límite es obligatorio',
            'integer' => 'El tiempo límite debe ser un número entero',
            'greater_than' => 'El tiempo límite debe ser mayor a 0'
        ],
        'duracion_minutos' => [
            'required' => 'La duración es obligatoria',
            'integer' => 'La duración debe ser un número entero',
            'greater_than' => 'La duración debe ser mayor a 0'
        ],
        'puntaje_minimo' => [
            'required' => 'El puntaje mínimo es obligatorio',
            'numeric' => 'El puntaje mínimo debe ser un número',
            'greater_than' => 'El puntaje mínimo debe ser mayor a 0',
            'less_than_equal_to' => 'El puntaje mínimo no puede ser mayor a 100'
        ],
        'fecha_inicio' => [
            'required' => 'La fecha de inicio es obligatoria',
            'valid_date' => 'La fecha de inicio debe ser válida'
        ],
        'fecha_fin' => [
            'required' => 'La fecha de fin es obligatoria',
            'valid_date' => 'La fecha de fin debe ser válida',
            'valid_date_greater_than' => 'La fecha de fin debe ser posterior a la fecha de inicio'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio',
            'in_list' => 'El estado debe ser activo o inactivo'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    /**
     * Obtiene todas las categorías asociadas a un examen específico
     * @param int $examen_id ID del examen
     * @return array Lista de categorías
     */
    public function getCategorias($examen_id)
    {
        return $this->db->table('examen_categoria')
            ->select('categorias.*')
            ->join('categorias', 'categorias.categoria_id = examen_categoria.categoria_id')
            ->where('examen_categoria.examen_id', $examen_id)
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene todas las preguntas asociadas al examen
     * @return \CodeIgniter\Database\BaseResult
     */
    public function preguntas()
    {
        return $this->belongsToMany(
            'App\Models\PreguntaModel',
            'examen_pregunta',
            'examen_id',
            'pregunta_id'
        );
    }

    /**
     * Obtiene todas las preguntas del examen con orden y respuestas
     * @param int $examen_id ID del examen
     * @return array Lista de preguntas con respuestas
     */
    public function getPreguntasConRespuestas($examen_id)
    {
        $examenPreguntaModel = new \App\Models\ExamenPreguntaModel();
        return $examenPreguntaModel->getPreguntasPorExamen($examen_id);
    }

    /**
     * Obtiene todas las escuelas asociadas al examen
     * @return \CodeIgniter\Database\BaseResult
     */
    public function escuelas()
    {
        return $this->belongsToMany(
            'App\Models\EscuelaModel',
            'examen_escuela',
            'examen_id',
            'escuela_id'
        );
    }

    /**
     * Obtiene todas las categorías aprobadas relacionadas con este examen
     * @return \CodeIgniter\Database\BaseResult
     */
    public function categoriasAprobadas()
    {
        return $this->hasMany('App\Models\CategoriaAprobadaModel', 'examen_id', 'examen_id');
    }

    /**
     * Obtiene exámenes activos
     * @return array Lista de exámenes activos
     */
    public function getActivos()
    {
        $fechaActual = date('Y-m-d H:i:s');
        
        return $this->where('estado', 'activo')
                   ->where('fecha_inicio <=', $fechaActual)
                   ->where('fecha_fin >=', $fechaActual)
                   ->findAll();
    }

    /**
     * Obtiene exámenes por categoría
     * @param int $categoria_id ID de la categoría
     * @return array Lista de exámenes
     */
    public function getPorCategoria($categoria_id)
    {
        return $this->db->table('examen_categoria')
            ->select('examenes.*')
            ->join('examenes', 'examenes.examen_id = examen_categoria.examen_id')
            ->where('examen_categoria.categoria_id', $categoria_id)
            ->where('examenes.estado', 'activo')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene exámenes por escuela
     * @param int $escuela_id ID de la escuela
     * @return array Lista de exámenes
     */
    public function getPorEscuela($escuela_id)
    {
        return $this->db->table('examen_escuela')
            ->select('examenes.*')
            ->join('examenes', 'examenes.examen_id = examen_escuela.examen_id')
            ->where('examen_escuela.escuela_id', $escuela_id)
            ->where('examen_escuela.estado', 'activo')
            ->where('examenes.estado', 'activo')
            ->get()
            ->getResultArray();
    }

    /**
     * Verifica si un examen está activo
     * @param int $examen_id ID del examen
     * @return bool True si está activo
     */
    public function estaActivo($examen_id)
    {
        $examen = $this->find($examen_id);
        if (!$examen) return false;

        $fechaActual = date('Y-m-d H:i:s');
        
        return $examen['estado'] === 'activo' &&
               $examen['fecha_inicio'] <= $fechaActual &&
               $examen['fecha_fin'] >= $fechaActual;
    }

    /**
     * Obtiene estadísticas básicas del examen
     * @param int $examen_id ID del examen
     * @return array Estadísticas
     */
    public function getEstadisticas($examen_id)
    {
        $examen = $this->find($examen_id);
        if (!$examen) return null;

        // Contar preguntas
        $totalPreguntas = $this->db->table('preguntas')
            ->where('examen_id', $examen_id)
            ->countAllResults();

        // Contar categorías aprobadas
        $totalCategoriasAprobadas = $this->db->table('categorias_aprobadas')
            ->where('examen_id', $examen_id)
            ->countAllResults();

        return [
            'examen_id' => $examen_id,
            'nombre' => $examen['nombre'],
            'total_preguntas' => $totalPreguntas,
            'total_categorias_aprobadas' => $totalCategoriasAprobadas,
            'estado' => $examen['estado'],
            'fecha_inicio' => $examen['fecha_inicio'],
            'fecha_fin' => $examen['fecha_fin']
        ];
    }
} 