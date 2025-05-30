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
        'codigo',
        'nombre',
        'descripcion',
        'requisitos'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'codigo' => 'required|max_length[10]|is_unique[categorias.codigo,categoria_id,{categoria_id}]',
        'nombre' => 'required|max_length[100]',
        'descripcion' => 'required',
        'requisitos' => 'required|valid_json'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    public function examenes()
    {
        return $this->belongsToMany('App\Models\ExamenModel', 'examen_categoria', 'categoria_id', 'examen_id');
    }

    /**
     * Obtener requisitos formateados
     */
    public function getRequisitos($categoria_id)
    {
        $categoria = $this->find($categoria_id);
        return $categoria ? json_decode($categoria['requisitos'], true) : [];
    }

    /**
     * Verificar si un conductor cumple con los requisitos
     */
    public function verificarRequisitos($categoria_id, $conductor_id)
    {
        $categoria = $this->find($categoria_id);
        if (!$categoria) return false;

        $requisitos = json_decode($categoria['requisitos'], true);
        $conductor = model('ConductorModel')->find($conductor_id);
        
        if (!$conductor) return false;

        foreach ($requisitos as $requisito) {
            if (strpos($requisito, 'Edad mínima:') !== false) {
                $edad_minima = (int) preg_replace('/[^0-9]/', '', $requisito);
                if ($conductor['edad'] < $edad_minima) {
                    return false;
                }
            }
            // Aquí se pueden agregar más validaciones según los requisitos
        }

        return true;
    }

    /**
     * Obtener categorías por nivel
     */
    public function getCategoriasPorNivel()
    {
        $categorias = $this->findAll();
        $niveles = [
            'básico' => ['A1', 'B'],
            'intermedio' => ['A', 'C'],
            'avanzado' => ['D', 'E']
        ];

        $resultado = [];
        foreach ($niveles as $nivel => $codigos) {
            $resultado[$nivel] = array_filter($categorias, function($cat) use ($codigos) {
                return in_array($cat['codigo'], $codigos);
            });
        }

        return $resultado;
    }
} 