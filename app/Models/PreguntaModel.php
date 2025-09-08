<?php

namespace App\Models;

use CodeIgniter\Model;

class PreguntaModel extends Model
{
    protected $table = 'preguntas';
    protected $primaryKey = 'pregunta_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'categoria_id',
        'enunciado',
        'tipo_pregunta',
        'puntaje',
        'dificultad',
        'es_critica'
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
        'enunciado' => 'required|min_length[10]|max_length[1000]',
        'tipo_pregunta' => 'required|in_list[multiple,unica,verdadero_falso,completar_espacios,ordenar,emparejar]',
        'puntaje' => 'required|numeric|greater_than[0]',
        'dificultad' => 'required|in_list[facil,medio,dificil]',
        'es_critica' => 'required|in_list[0,1]'
    ];
    protected $validationMessages = [
        'categoria_id' => [
            'required' => 'La categoría es requerida',
            'integer' => 'La categoría debe ser un número entero'
        ],
        'enunciado' => [
            'required' => 'El enunciado es requerido',
            'min_length' => 'El enunciado debe tener al menos 10 caracteres',
            'max_length' => 'El enunciado no puede exceder los 1000 caracteres'
        ],
        'tipo_pregunta' => [
            'required' => 'El tipo de pregunta es requerido',
            'in_list' => 'El tipo de pregunta debe ser múltiple, única o verdadero/falso'
        ],
        'puntaje' => [
            'required' => 'El puntaje es requerido',
            'numeric' => 'El puntaje debe ser un número',
            'greater_than' => 'El puntaje debe ser mayor a 0'
        ],
        'dificultad' => [
            'required' => 'La dificultad es requerida',
            'in_list' => 'La dificultad debe ser fácil, medio o difícil'
        ],
        'es_critica' => [
            'required' => 'El campo es_critica es requerido',
            'in_list' => 'El valor debe ser 0 o 1'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    /**
     * Obtiene todas las respuestas asociadas a la pregunta
     * @return \CodeIgniter\Database\BaseResult
     */
    public function respuestas()
    {
        return $this->hasMany('App\Models\RespuestaModel', 'pregunta_id', 'pregunta_id');
    }

    /**
     * Obtiene la categoría a la que pertenece esta pregunta
     * @return \CodeIgniter\Database\BaseResult
     */
    public function categoria()
    {
        return $this->belongsTo('App\Models\CategoriaModel', 'categoria_id', 'categoria_id');
    }

    /**
     * Obtiene todos los exámenes donde aparece esta pregunta
     * @return \CodeIgniter\Database\BaseResult
     */
    public function examenes()
    {
        return $this->belongsToMany(
            'App\Models\ExamenModel',
            'examen_pregunta',
            'pregunta_id',
            'examen_id'
        );
    }

    /**
     * Obtiene preguntas con sus respuestas y categoría
     * @param int $limit Límite de preguntas
     * @param int $offset Offset para paginación
     * @return array Array de preguntas con relaciones
     */
    public function getPreguntasConRelaciones($limit = null, $offset = 0)
    {
        $builder = $this->db->table('preguntas')
            ->select('preguntas.*, categorias.nombre as categoria_nombre')
            ->join('categorias', 'categorias.categoria_id = preguntas.categoria_id', 'left');

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Obtiene preguntas por categoría con sus respuestas
     * @param int $categoria_id ID de la categoría
     * @return array Array de preguntas
     */
    public function getPreguntasPorCategoria($categoria_id)
    {
        return $this->where('categoria_id', $categoria_id)->findAll();
    }

    /**
     * Obtiene preguntas críticas
     * @return array Array de preguntas críticas
     */
    public function getPreguntasCriticas()
    {
        return $this->where('es_critica', 1)->findAll();
    }
} 