<?php

namespace App\Controllers;

use App\Models\CategoriaAprobadaModel;
use App\Models\ConductorModel;
use App\Models\ExamenModel;
use App\Models\CategoriaModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class CategoriaAprobadaController extends ResourceController
{
    use ResponseTrait;

    protected $categoriaAprobadaModel;
    protected $conductorModel;
    protected $examenModel;
    protected $categoriaModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->categoriaAprobadaModel = new CategoriaAprobadaModel();
        $this->conductorModel = new ConductorModel();
        $this->examenModel = new ExamenModel();
        $this->categoriaModel = new CategoriaModel();
    }

    /**
     * Listar todas las categorías aprobadas
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            $conductor_id = $this->request->getGet('conductor_id');
            $categoria_id = $this->request->getGet('categoria_id');
            $estado = $this->request->getGet('estado');

            $builder = $this->categoriaAprobadaModel;

            // Aplicar filtros
            if ($conductor_id) {
                $builder->where('conductor_id', $conductor_id);
            }
            if ($categoria_id) {
                $builder->where('categoria_id', $categoria_id);
            }
            if ($estado) {
                $builder->where('estado', $estado);
            }

            $categoriasAprobadas = $builder->paginate($perPage, 'default', $page);
            $pager = $builder->pager;

            // Obtener información adicional para cada registro
            foreach ($categoriasAprobadas as &$categoriaAprobada) {
                $categoriaAprobada['conductor'] = $this->conductorModel->find($categoriaAprobada['conductor_id']);
                $categoriaAprobada['categoria'] = $this->categoriaModel->find($categoriaAprobada['categoria_id']);
                if ($categoriaAprobada['examen_id']) {
                    $categoriaAprobada['examen'] = $this->examenModel->find($categoriaAprobada['examen_id']);
                }
            }

            return $this->respond([
                'status' => 'success',
                'data' => $categoriasAprobadas,
                'pager' => $pager
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener categorías aprobadas: ' . $e->getMessage());
        }
    }

    /**
     * Obtener una categoría aprobada específica
     */
    public function show($id = null)
    {
        try {
            $categoriaAprobada = $this->categoriaAprobadaModel->find($id);

            if (!$categoriaAprobada) {
                return $this->failNotFound('Categoría aprobada no encontrada');
            }

            // Obtener información adicional
            $categoriaAprobada['conductor'] = $this->conductorModel->find($categoriaAprobada['conductor_id']);
            $categoriaAprobada['categoria'] = $this->categoriaModel->find($categoriaAprobada['categoria_id']);
            if ($categoriaAprobada['examen_id']) {
                $categoriaAprobada['examen'] = $this->examenModel->find($categoriaAprobada['examen_id']);
            }

            return $this->respond([
                'status' => 'success',
                'data' => $categoriaAprobada
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener categoría aprobada: ' . $e->getMessage());
        }
    }

    /**
     * Crear una nueva categoría aprobada
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);

            // Validar datos requeridos
            if (!isset($data['conductor_id']) || !isset($data['categoria_id'])) {
                return $this->fail('Faltan datos requeridos: conductor_id y categoria_id', 400);
            }

            // Verificar que el conductor existe
            if (!$this->conductorModel->find($data['conductor_id'])) {
                return $this->fail('El conductor especificado no existe', 400);
            }

            // Verificar que la categoría existe
            if (!$this->categoriaModel->find($data['categoria_id'])) {
                return $this->fail('La categoría especificada no existe', 400);
            }

            // Verificar que el examen existe si se proporciona
            if (isset($data['examen_id']) && !$this->examenModel->find($data['examen_id'])) {
                return $this->fail('El examen especificado no existe', 400);
            }

            // Verificar si ya existe una categoría aprobada para este conductor y categoría
            $existe = $this->categoriaAprobadaModel
                ->where('conductor_id', $data['conductor_id'])
                ->where('categoria_id', $data['categoria_id'])
                ->first();

            if ($existe) {
                return $this->fail('Ya existe una categoría aprobada para este conductor y categoría', 400);
            }

            // Preparar datos para inserción
            $categoriaAprobadaData = [
                'conductor_id' => $data['conductor_id'],
                'categoria_id' => $data['categoria_id'],
                'examen_id' => $data['examen_id'] ?? null,
                'estado' => $data['estado'] ?? 'pendiente',
                'puntaje_obtenido' => $data['puntaje_obtenido'] ?? null,
                'fecha_aprobacion' => $data['fecha_aprobacion'] ?? null,
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
                'observaciones' => $data['observaciones'] ?? null
            ];

            if (!$this->categoriaAprobadaModel->insert($categoriaAprobadaData)) {
                return $this->fail($this->categoriaAprobadaModel->errors());
            }

            $categoriaAprobadaId = $this->categoriaAprobadaModel->getInsertID();

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Categoría aprobada creada exitosamente',
                'categoria_aprobada_id' => $categoriaAprobadaId
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al crear categoría aprobada: ' . $e->getMessage());
        }
    }

    /**
     * Método store para compatibilidad con ResourceController
     */
    public function store()
    {
        return $this->create();
    }

    /**
     * Actualizar una categoría aprobada
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);

            $categoriaAprobada = $this->categoriaAprobadaModel->find($id);
            if (!$categoriaAprobada) {
                return $this->failNotFound('Categoría aprobada no encontrada');
            }

            // Preparar datos para actualización
            $updateData = [];
            
            if (isset($data['estado'])) {
                $updateData['estado'] = $data['estado'];
            }
            if (isset($data['puntaje_obtenido'])) {
                $updateData['puntaje_obtenido'] = $data['puntaje_obtenido'];
            }
            if (isset($data['fecha_aprobacion'])) {
                $updateData['fecha_aprobacion'] = $data['fecha_aprobacion'];
            }
            if (isset($data['fecha_vencimiento'])) {
                $updateData['fecha_vencimiento'] = $data['fecha_vencimiento'];
            }
            if (isset($data['observaciones'])) {
                $updateData['observaciones'] = $data['observaciones'];
            }

            if (empty($updateData)) {
                return $this->fail('No se proporcionaron datos para actualizar', 400);
            }

            if (!$this->categoriaAprobadaModel->update($id, $updateData)) {
                return $this->fail($this->categoriaAprobadaModel->errors());
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Categoría aprobada actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al actualizar categoría aprobada: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar una categoría aprobada
     */
    public function delete($id = null)
    {
        try {
            $categoriaAprobada = $this->categoriaAprobadaModel->find($id);
            if (!$categoriaAprobada) {
                return $this->failNotFound('Categoría aprobada no encontrada');
            }

            if (!$this->categoriaAprobadaModel->delete($id)) {
                return $this->failServerError('Error al eliminar la categoría aprobada');
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Categoría aprobada eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al eliminar categoría aprobada: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas de categorías aprobadas
     */
    public function estadisticas()
    {
        try {
            $conductor_id = $this->request->getGet('conductor_id');
            $categoria_id = $this->request->getGet('categoria_id');

            $builder = $this->categoriaAprobadaModel;

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

            return $this->respond([
                'status' => 'success',
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener estadísticas: ' . $e->getMessage());
        }
    }

    /**
     * Obtener categorías aprobadas por conductor
     */
    public function porConductor($conductor_id)
    {
        try {
            $categoriasAprobadas = $this->categoriaAprobadaModel
                ->where('conductor_id', $conductor_id)
                ->findAll();

            // Obtener información adicional
            foreach ($categoriasAprobadas as &$categoriaAprobada) {
                $categoriaAprobada['categoria'] = $this->categoriaModel->find($categoriaAprobada['categoria_id']);
                if ($categoriaAprobada['examen_id']) {
                    $categoriaAprobada['examen'] = $this->examenModel->find($categoriaAprobada['examen_id']);
                }
            }

            return $this->respond([
                'status' => 'success',
                'data' => $categoriasAprobadas
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener categorías del conductor: ' . $e->getMessage());
        }
    }

    /**
     * Obtener conductores por categoría
     */
    public function porCategoria($categoria_id)
    {
        try {
            $categoriasAprobadas = $this->categoriaAprobadaModel
                ->where('categoria_id', $categoria_id)
                ->findAll();

            // Obtener información adicional
            foreach ($categoriasAprobadas as &$categoriaAprobada) {
                $categoriaAprobada['conductor'] = $this->conductorModel->find($categoriaAprobada['conductor_id']);
                if ($categoriaAprobada['examen_id']) {
                    $categoriaAprobada['examen'] = $this->examenModel->find($categoriaAprobada['examen_id']);
                }
            }

            return $this->respond([
                'status' => 'success',
                'data' => $categoriasAprobadas
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener conductores de la categoría: ' . $e->getMessage());
        }
    }
}
