<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaAsignadaModel extends Model
{
    protected $table = 'categorias_asignadas';
    protected $primaryKey = 'categoria_asignada_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'conductor_id',
        'categoria_id',
        'examen_id',
        'estado',
        'intentos_realizados',
        'intentos_maximos',
        'fecha_asignacion',
        'fecha_ultimo_intento',
        'fecha_aprobacion',
        'puntaje_obtenido',
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
        'estado' => 'required|in_list[Reprobado,Iniciado,Aprobada]',
        'intentos_realizados' => 'permit_empty|integer|greater_than_equal_to[0]',
        'intentos_maximos' => 'permit_empty|integer|greater_than_equal_to[1]',
        'fecha_asignacion' => 'permit_empty|valid_date',
        'fecha_ultimo_intento' => 'permit_empty|valid_date',
        'fecha_aprobacion' => 'permit_empty|valid_date',
        'puntaje_obtenido' => 'permit_empty|numeric|less_than_equal_to[100]',
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
        'estado' => [
            'required' => 'El estado es requerido',
            'in_list' => 'El estado debe ser uno de: pendiente, iniciado, aprobado'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Obtener categorías asignadas de un conductor con información completa
     */
    public function getCategoriasAsignadasCompletas($conductorId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                ca.categoria_asignada_id,
                ca.conductor_id,
                ca.categoria_id,
                ca.examen_id,
                ca.estado,
                ca.intentos_realizados,
                ca.intentos_maximos,
                ca.fecha_asignacion,
                ca.fecha_ultimo_intento,
                ca.fecha_aprobacion,
                ca.puntaje_obtenido,
                ca.observaciones,
                c.codigo as categoria_codigo,
                c.nombre as categoria_nombre,
                c.descripcion as categoria_descripcion,
                e.titulo as examen_titulo,
                e.dificultad as examen_dificultad,
                e.puntaje_minimo as examen_puntaje_minimo
            FROM categorias_asignadas ca
            LEFT JOIN categorias c ON c.categoria_id = ca.categoria_id
            LEFT JOIN examenes e ON e.examen_id = ca.examen_id
            WHERE ca.conductor_id = ? AND ca.deleted_at IS NULL
            ORDER BY ca.fecha_asignacion DESC
        ", [$conductorId]);
        
        return $query->getResultArray();
    }

    /**
     * Obtener categorías aprobadas de un conductor
     */
    public function getCategoriasAprobadasCompletas($conductorId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                cap.categoria_aprobada_id,
                cap.conductor_id,
                cap.categoria_id,
                cap.examen_id,
                cap.estado,
                cap.fecha_aprobacion,
                cap.puntaje_obtenido,
                cap.fecha_vencimiento,
                cap.observaciones,
                c.codigo as categoria_codigo,
                c.nombre as categoria_nombre,
                c.descripcion as categoria_descripcion,
                e.titulo as examen_titulo,
                e.dificultad as examen_dificultad
            FROM categorias_aprobadas cap
            LEFT JOIN categorias c ON c.categoria_id = cap.categoria_id
            LEFT JOIN examenes e ON e.examen_id = cap.examen_id
            WHERE cap.conductor_id = ? AND cap.estado = 'aprobado' AND cap.deleted_at IS NULL
            ORDER BY cap.fecha_aprobacion DESC
        ", [$conductorId]);
        
        return $query->getResultArray();
    }

    /**
     * Obtener categorías pendientes de un conductor (no aprobadas)
     */
    public function getCategoriasPendientesCompletas($conductorId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                ca.categoria_asignada_id,
                ca.conductor_id,
                ca.categoria_id,
                ca.examen_id,
                ca.estado,
                ca.intentos_realizados,
                ca.intentos_maximos,
                ca.fecha_asignacion,
                ca.fecha_ultimo_intento,
                ca.fecha_aprobacion,
                ca.puntaje_obtenido,
                ca.observaciones,
                c.codigo as categoria_codigo,
                c.nombre as categoria_nombre,
                c.descripcion as categoria_descripcion,
                e.titulo as examen_titulo,
                e.dificultad as examen_dificultad,
                e.puntaje_minimo as examen_puntaje_minimo
            FROM categorias_asignadas ca
            LEFT JOIN categorias c ON c.categoria_id = ca.categoria_id
            LEFT JOIN examenes e ON e.examen_id = ca.examen_id
            WHERE ca.conductor_id = ? 
            AND ca.estado IN ('Reprobado', 'Iniciado') 
            AND ca.deleted_at IS NULL
            ORDER BY ca.fecha_asignacion DESC
        ", [$conductorId]);
        
        return $query->getResultArray();
    }
}
