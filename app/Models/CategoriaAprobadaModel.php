<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaAprobadaModel extends Model
{
    protected $table = 'categorias_aprobadas';
    protected $primaryKey = 'categoria_aprobada_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'conductor_id',
        'categoria_id',
        'examen_id',
        'estado',
        'fecha_aprobacion',
        'puntaje_obtenido',
        'fecha_vencimiento',
        'observaciones'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'conductor_id' => 'required|integer',
        'categoria_id' => 'required|integer',
        'examen_id' => 'permit_empty|integer',
        'estado' => 'required|in_list[pendiente,aprobado,rechazado]',
        'fecha_aprobacion' => 'permit_empty|valid_date',
        'puntaje_obtenido' => 'permit_empty|numeric',
        'fecha_vencimiento' => 'permit_empty|valid_date',
        'observaciones' => 'permit_empty|string'
    ];

    protected $validationMessages = [
        'conductor_id' => [
            'required' => 'El ID del conductor es requerido',
            'integer' => 'El ID del conductor debe ser un número entero'
        ],
        'categoria_id' => [
            'required' => 'El ID de la categoría es requerido',
            'integer' => 'El ID de la categoría debe ser un número entero'
        ],
        'examen_id' => [
            'integer' => 'El ID del examen debe ser un número entero'
        ],
        'estado' => [
            'required' => 'El estado es requerido',
            'in_list' => 'El estado debe ser uno de: pendiente, aprobado, rechazado'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene el conductor que aprobó la categoría
     * @return \CodeIgniter\Database\BaseResult
     */
    public function conductor()
    {
        return $this->belongsTo('App\Models\ConductorModel', 'conductor_id', 'conductor_id');
    }

    /**
     * Obtiene la categoría que fue aprobada
     * @return \CodeIgniter\Database\BaseResult
     */
    public function categoria()
    {
        return $this->belongsTo('App\Models\CategoriaModel', 'categoria_id', 'categoria_id');
    }

    /**
     * Obtiene el examen en el que se aprobó la categoría
     * @return \CodeIgniter\Database\BaseResult
     */
    public function examen()
    {
        return $this->belongsTo('App\Models\ExamenModel', 'examen_id', 'examen_id');
    }

    /**
     * Obtiene categorías aprobadas por conductor
     * @param int $conductor_id ID del conductor
     * @return array Lista de categorías aprobadas
     */
    public function getPorConductor($conductor_id)
    {
        return $this->where('conductor_id', $conductor_id)
                   ->findAll();
    }

    /**
     * Obtiene conductores por categoría
     * @param int $categoria_id ID de la categoría
     * @return array Lista de conductores
     */
    public function getPorCategoria($categoria_id)
    {
        return $this->where('categoria_id', $categoria_id)
                   ->findAll();
    }

    /**
     * Obtiene estadísticas de categorías aprobadas
     * @param int|null $conductor_id ID del conductor (opcional)
     * @param int|null $categoria_id ID de la categoría (opcional)
     * @return array Estadísticas
     */
    public function getEstadisticas($conductor_id = null, $categoria_id = null)
    {
        $builder = $this;

        if ($conductor_id) {
            $builder->where('conductor_id', $conductor_id);
        }
        if ($categoria_id) {
            $builder->where('categoria_id', $categoria_id);
        }

        $categoriasAprobadas = $builder->findAll();

        $estadisticas = [
            'total' => count($categoriasAprobadas),
            'aprobados' => count(array_filter($categoriasAprobadas, fn($ca) => $ca['estado'] === 'aprobado')),
            'rechazados' => count(array_filter($categoriasAprobadas, fn($ca) => $ca['estado'] === 'rechazado')),
            'pendientes' => count(array_filter($categoriasAprobadas, fn($ca) => $ca['estado'] === 'pendiente')),
            'promedio_puntaje' => 0
        ];

        // Calcular promedio de puntaje
        $puntajes = array_filter(array_column($categoriasAprobadas, 'puntaje_obtenido'), 'is_numeric');
        if (!empty($puntajes)) {
            $estadisticas['promedio_puntaje'] = round(array_sum($puntajes) / count($puntajes), 2);
        }

        return $estadisticas;
    }

    /**
     * Verifica si un conductor ya tiene una categoría aprobada
     * @param int $conductor_id ID del conductor
     * @param int $categoria_id ID de la categoría
     * @return bool True si ya tiene la categoría aprobada
     */
    public function tieneCategoriaAprobada($conductor_id, $categoria_id)
    {
        $categoriaAprobada = $this->where('conductor_id', $conductor_id)
                                 ->where('categoria_id', $categoria_id)
                                 ->where('estado', 'aprobado')
                                 ->first();

        if (!$categoriaAprobada) {
            return false;
        }

        // Verificar si la licencia no ha vencido
        if ($categoriaAprobada['fecha_vencimiento'] && 
            $categoriaAprobada['fecha_vencimiento'] < date('Y-m-d H:i:s')) {
            return false;
        }

        return true;
    }

    /**
     * Obtiene categorías aprobadas activas (no vencidas) de un conductor
     * @param int $conductor_id ID del conductor
     * @return array Lista de categorías aprobadas activas
     */
    public function getCategoriasActivas($conductor_id)
    {
        $fechaActual = date('Y-m-d H:i:s');
        
        return $this->where('conductor_id', $conductor_id)
                   ->where('estado', 'aprobado')
                   ->where('fecha_vencimiento >', $fechaActual)
                   ->orWhere('fecha_vencimiento IS NULL')
                   ->findAll();
    }
} 