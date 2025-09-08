<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamenCategoriaModel extends Model
{
    protected $table = 'examen_categoria';
    protected $primaryKey = 'examen_categoria_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'examen_id',
        'categoria_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'examen_id' => 'required|integer|greater_than[0]',
        'categoria_id' => 'required|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'examen_id' => [
            'required' => 'El ID del examen es requerido',
            'integer' => 'El ID del examen debe ser un número entero',
            'greater_than' => 'El ID del examen debe ser mayor a 0'
        ],
        'categoria_id' => [
            'required' => 'El ID de la categoría es requerido',
            'integer' => 'El ID de la categoría debe ser un número entero',
            'greater_than' => 'El ID de la categoría debe ser mayor a 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene las categorías de un examen específico
     * @param int $examen_id ID del examen
     * @return array Lista de categorías
     */
    public function getCategoriasExamen($examen_id)
    {
        return $this->select('categorias.*')
            ->join('categorias', 'categorias.categoria_id = examen_categoria.categoria_id')
            ->where('examen_categoria.examen_id', $examen_id)
            ->findAll();
    }

    /**
     * Obtiene los exámenes de una categoría específica
     * @param int $categoria_id ID de la categoría
     * @return array Lista de exámenes
     */
    public function getExamenesCategoria($categoria_id)
    {
        return $this->select('examenes.*')
            ->join('examenes', 'examenes.examen_id = examen_categoria.examen_id')
            ->where('examen_categoria.categoria_id', $categoria_id)
            ->findAll();
    }

    /**
     * Asigna categorías a un examen
     * @param int $examen_id ID del examen
     * @param array $categoria_ids Array de IDs de categorías
     * @return bool True si la operación fue exitosa
     */
    public function asignarCategorias($examen_id, $categoria_ids)
    {
        // Primero eliminamos las categorías existentes
        $this->where('examen_id', $examen_id)->delete();

        // Insertamos las nuevas categorías
        $data = [];
        foreach ($categoria_ids as $categoria_id) {
            $data[] = [
                'examen_id' => $examen_id,
                'categoria_id' => $categoria_id
            ];
        }

        if (!empty($data)) {
            return $this->insertBatch($data);
        }

        return true;
    }

    /**
     * Verifica si un examen tiene una categoría específica
     * @param int $examen_id ID del examen
     * @param int $categoria_id ID de la categoría
     * @return bool True si existe la relación
     */
    public function tieneCategoria($examen_id, $categoria_id)
    {
        return $this->where('examen_id', $examen_id)
            ->where('categoria_id', $categoria_id)
            ->countAllResults() > 0;
    }

    /**
     * Elimina todas las categorías de un examen
     * @param int $examen_id ID del examen
     * @return bool True si la operación fue exitosa
     */
    public function eliminarCategoriasExamen($examen_id)
    {
        return $this->where('examen_id', $examen_id)->delete();
    }
} 