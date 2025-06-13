<?php

namespace App\Controllers;

use App\Models\ConductorModel;
use App\Models\ExamenConductorModel;
use App\Models\CategoriaModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ConductorController extends ResourceController
{
    use ResponseTrait;

    protected $conductorModel;
    protected $examenConductorModel;
    protected $categoriaModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->conductorModel = new ConductorModel();
        $this->examenConductorModel = new ExamenConductorModel();
        $this->categoriaModel = new CategoriaModel();
    }

    /**
     * Listar todos los conductores
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            
            $conductores = $this->conductorModel->paginate($perPage, 'default', $page);
            $pager = $this->conductorModel->pager;

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'conductores' => $conductores,
                    'pagination' => [
                        'current_page' => $pager->getCurrentPage(),
                        'total_pages' => $pager->getPageCount(),
                        'total_items' => $pager->getTotal(),
                        'per_page' => $perPage
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtiene el perfil del conductor
     */
    public function perfil()
    {
        try {
            $conductor_id = $this->request->getHeaderLine('X-Conductor-ID');
            $conductor = $this->conductorModel->find($conductor_id);

            if (!$conductor) {
                return $this->failNotFound('Conductor no encontrado');
            }

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'conductor' => $conductor,
                    'escuela' => $conductor['escuela_id'] ? $this->conductorModel->escuela->find($conductor['escuela_id']) : null
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtiene los exámenes disponibles y asignados al conductor
     */
    public function examenes()
    {
        try {
            $conductor_id = $this->request->getHeaderLine('X-Conductor-ID');
            
            // Exámenes asignados
            $examenesAsignados = $this->examenConductorModel
                ->select('examen_conductor.*, examenes.titulo, examenes.descripcion, examenes.fecha_inicio, examenes.fecha_fin')
                ->join('examenes', 'examenes.examen_id = examen_conductor.examen_id')
                ->where('conductor_id', $conductor_id)
                ->findAll();

            // Exámenes disponibles
            $examenesDisponibles = $this->examenConductorModel
                ->select('examenes.*')
                ->join('examenes', 'examenes.examen_id = examen_conductor.examen_id')
                ->where('examenes.fecha_inicio <=', date('Y-m-d H:i:s'))
                ->where('examenes.fecha_fin >=', date('Y-m-d H:i:s'))
                ->whereNotIn('examenes.examen_id', function($builder) use ($conductor_id) {
                    return $builder->select('examen_id')
                                 ->from('examen_conductor')
                                 ->where('conductor_id', $conductor_id);
                })
                ->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'asignados' => $examenesAsignados,
                    'disponibles' => $examenesDisponibles
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtiene detalles de un examen específico
     */
    public function examen($examen_id)
    {
        try {
            $conductor_id = $this->request->getHeaderLine('X-Conductor-ID');
            
            $examen = $this->examenConductorModel
                ->select('examen_conductor.*, examenes.titulo, examenes.descripcion, examenes.fecha_inicio, examenes.fecha_fin')
                ->join('examenes', 'examenes.examen_id = examen_conductor.examen_id')
                ->where('conductor_id', $conductor_id)
                ->where('examen_id', $examen_id)
                ->first();

            if (!$examen) {
                return $this->failNotFound('Examen no encontrado o no asignado');
            }

            return $this->respond([
                'status' => 'success',
                'data' => $examen
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtiene las categorías aprobadas del conductor
     */
    public function categorias()
    {
        try {
            $conductor_id = $this->request->getHeaderLine('X-Conductor-ID');
            
            $categorias = $this->categoriaModel
                ->select('categorias.*, categorias_aprobadas.fecha_aprobacion, categorias_aprobadas.puntaje_obtenido')
                ->join('categorias_aprobadas', 'categorias_aprobadas.categoria_id = categorias.categoria_id')
                ->where('categorias_aprobadas.conductor_id', $conductor_id)
                ->where('categorias_aprobadas.estado', 'aprobado')
                ->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $categorias
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtiene el historial de exámenes del conductor
     */
    public function historial()
    {
        try {
            $conductor_id = $this->request->getHeaderLine('X-Conductor-ID');
            
            $historial = $this->examenConductorModel
                ->select('examen_conductor.*, examenes.titulo, examenes.descripcion')
                ->join('examenes', 'examenes.examen_id = examen_conductor.examen_id')
                ->where('conductor_id', $conductor_id)
                ->orderBy('examen_conductor.created_at', 'DESC')
                ->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $historial
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
} 