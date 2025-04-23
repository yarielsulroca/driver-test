<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'categoria_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'sigla',
        'nombre',
        'descripcion',
        'requisitos',
        'edad_minima',
        'experiencia_requerida'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'sigla' => 'required|min_length[1]|max_length[2]|is_unique[categorias.sigla]',
        'nombre' => 'required|min_length[3]|max_length[100]',
        'descripcion' => 'required|min_length[10]',
        'requisitos' => 'required|min_length[10]',
        'edad_minima' => 'required|integer|greater_than[17]',
        'experiencia_requerida' => 'required|integer|greater_than_equal_to[0]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    public function examenes()
    {
        return $this->hasMany('App\Models\ExamenModel', 'categoria_id', 'categoria_id');
    }

    public function preguntas()
    {
        return $this->hasMany('App\Models\PreguntaModel', 'categoria_id', 'categoria_id');
    }
} 