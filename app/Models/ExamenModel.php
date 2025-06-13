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
        'categoria_id',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'duracion_minutos',
        'puntaje_minimo',
        'numero_preguntas'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'categoria_id' => 'required|integer',
        'nombre' => 'required|min_length[3]|max_length[255]',
        'descripcion' => 'permit_empty|max_length[1000]',
        'fecha_inicio' => 'required|valid_date',
        'fecha_fin' => 'required|valid_date',
        'duracion_minutos' => 'required|integer|greater_than[0]',
        'puntaje_minimo' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
        'numero_preguntas' => 'required|integer|greater_than[0]'
    ];
    protected $validationMessages = [
        'categoria_id' => [
            'required' => 'La categoría es requerida',
            'integer' => 'La categoría debe ser un número entero'
        ],
        'nombre' => [
            'required' => 'El nombre es requerido',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder los 255 caracteres'
        ],
        'fecha_inicio' => [
            'required' => 'La fecha de inicio es requerida',
            'valid_date' => 'La fecha de inicio debe ser una fecha válida'
        ],
        'fecha_fin' => [
            'required' => 'La fecha de fin es requerida',
            'valid_date' => 'La fecha de fin debe ser una fecha válida'
        ],
        'duracion_minutos' => [
            'required' => 'La duración es requerida',
            'integer' => 'La duración debe ser un número entero',
            'greater_than' => 'La duración debe ser mayor a 0'
        ],
        'puntaje_minimo' => [
            'required' => 'El puntaje mínimo es requerido',
            'numeric' => 'El puntaje mínimo debe ser un número',
            'greater_than' => 'El puntaje mínimo debe ser mayor a 0',
            'less_than_equal_to' => 'El puntaje mínimo no puede ser mayor a 100'
        ],
        'numero_preguntas' => [
            'required' => 'El número de preguntas es requerido',
            'integer' => 'El número de preguntas debe ser un número entero',
            'greater_than' => 'El número de preguntas debe ser mayor a 0'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene la categoría a la que pertenece el examen
     * @return \CodeIgniter\Database\BaseResult
     */
    public function categoria()
    {
        return $this->belongsTo('App\Models\CategoriaModel', 'categoria_id', 'categoria_id');
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
     * Obtiene todos los conductores que han presentado el examen
     * @return \CodeIgniter\Database\BaseResult
     */
    public function conductores()
    {
        return $this->belongsToMany(
            'App\Models\ConductorModel',
            'examen_conductor',
            'examen_id',
            'conductor_id'
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
     * Asigna categorías a un examen específico
     * @param int $examen_id ID del examen
     * @param array $categorias Array de IDs de categorías
     * @return bool True si la operación fue exitosa
     */
    public function asignarCategorias($examen_id, $categorias)
    {
        // Primero eliminamos las categorías existentes
        $this->db->table('examen_categoria')
            ->where('examen_id', $examen_id)
            ->delete();

        // Insertamos las nuevas categorías
        $data = [];
        foreach ($categorias as $categoria_id) {
            $data[] = [
                'examen_id' => $examen_id,
                'categoria_id' => $categoria_id
            ];
        }

        if (!empty($data)) {
            return $this->db->table('examen_categoria')->insertBatch($data);
        }

        return true;
    }
} 