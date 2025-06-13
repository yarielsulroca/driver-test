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
} 