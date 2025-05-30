<?php

namespace App\Models;

use CodeIgniter\Model;

class PaginaModel extends Model
{
    protected $table = 'paginas';
    protected $primaryKey = 'pagina_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'examen_id',
        'orden',
        'preguntas',
        'respuesta_correcta'
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
        'orden' => 'required|integer',
        'preguntas' => 'required|valid_json',
        'respuesta_correcta' => 'required|integer'
    ];

    // Relaciones
    public function examen()
    {
        return $this->belongsTo('App\Models\ExamenModel', 'examen_id', 'examen_id');
    }

    public function estadoConductor()
    {
        return $this->hasMany('App\Models\PaginaConductorModel', 'pagina_id', 'pagina_id');
    }

    /**
     * Obtener las páginas de un examen
     */
    public function getPaginasExamen($examen_id)
    {
        return $this->where('examen_id', $examen_id)
                   ->orderBy('orden', 'ASC')
                   ->findAll();
    }

    /**
     * Crear páginas para un examen
     */
    public function crearPaginas($examen_id, $paginas)
    {
        $data = [];
        foreach ($paginas as $index => $pagina) {
            $data[] = [
                'examen_id' => $examen_id,
                'orden' => $index + 1,
                'preguntas' => json_encode($pagina['preguntas']),
                'respuesta_correcta' => $pagina['respuesta_correcta']
            ];
        }

        return $this->insertBatch($data);
    }

    /**
     * Verificar si una respuesta es correcta
     */
    public function verificarRespuesta($pagina_id, $respuesta_id)
    {
        $pagina = $this->find($pagina_id);
        return $pagina && $pagina['respuesta_correcta'] == $respuesta_id;
    }
} 