<?php

namespace App\Models;

use CodeIgniter\Model;

class PaginaConductorModel extends Model
{
    protected $table = 'pagina_conductor';
    protected $primaryKey = 'pagina_conductor_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'examen_conductor_id',
        'pagina_id',
        'orden',
        'estado',
        'fecha_vista',
        'respuesta_seleccionada'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'examen_conductor_id' => 'required|integer',
        'pagina_id' => 'required|integer',
        'orden' => 'required|integer',
        'estado' => 'required|in_list[pendiente,vista,completada]'
    ];

    // Relaciones
    public function examenConductor()
    {
        return $this->belongsTo('App\Models\ExamenConductorModel', 'examen_conductor_id', 'examen_conductor_id');
    }

    public function pagina()
    {
        return $this->belongsTo('App\Models\PaginaModel', 'pagina_id', 'pagina_id');
    }

    /**
     * Inicializar páginas para un examen de conductor
     */
    public function inicializarPaginas($examen_conductor_id, $paginas)
    {
        $data = [];
        $orden = range(1, count($paginas));
        shuffle($orden); // Orden aleatorio

        foreach ($paginas as $index => $pagina) {
            $data[] = [
                'examen_conductor_id' => $examen_conductor_id,
                'pagina_id' => $pagina['pagina_id'],
                'orden' => $orden[$index],
                'estado' => 'pendiente'
            ];
        }

        return $this->insertBatch($data);
    }

    /**
     * Marcar página como vista
     */
    public function marcarVista($pagina_conductor_id)
    {
        return $this->update($pagina_conductor_id, [
            'estado' => 'vista',
            'fecha_vista' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Marcar página como completada
     */
    public function marcarCompletada($pagina_conductor_id, $respuesta_id)
    {
        return $this->update($pagina_conductor_id, [
            'estado' => 'completada',
            'respuesta_seleccionada' => $respuesta_id
        ]);
    }

    /**
     * Obtener siguiente página pendiente
     */
    public function getSiguientePagina($examen_conductor_id)
    {
        return $this->where('examen_conductor_id', $examen_conductor_id)
                   ->where('estado', 'pendiente')
                   ->orderBy('orden', 'ASC')
                   ->first();
    }

    /**
     * Verificar si todas las páginas están completadas
     */
    public function todasCompletadas($examen_conductor_id)
    {
        $total = $this->where('examen_conductor_id', $examen_conductor_id)->countAllResults();
        $completadas = $this->where('examen_conductor_id', $examen_conductor_id)
                          ->where('estado', 'completada')
                          ->countAllResults();

        return $total > 0 && $total === $completadas;
    }

    /**
     * Obtener estadísticas de respuestas
     */
    public function getEstadisticas($examen_conductor_id)
    {
        $paginas = $this->where('examen_conductor_id', $examen_conductor_id)
                       ->where('estado', 'completada')
                       ->findAll();

        $correctas = 0;
        foreach ($paginas as $pagina) {
            if ($this->pagina->verificarRespuesta($pagina['pagina_id'], $pagina['respuesta_seleccionada'])) {
                $correctas++;
            }
        }

        return [
            'total' => count($paginas),
            'correctas' => $correctas,
            'incorrectas' => count($paginas) - $correctas
        ];
    }
} 