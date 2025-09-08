<?php

namespace App\Controllers;

use App\Models\ExamenModel;
use App\Models\PreguntaModel;
use App\Models\ExamenCategoriaModel;
use App\Models\ExamenEscuelaModel;
use App\Models\RespuestaModel;
use App\Models\CategoriaAprobadaModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\BaseConnection;

class ExamenController extends ResourceController
{
    use ResponseTrait;

    protected $examenModel;
    protected $preguntaModel;
    protected $examenCategoriaModel;
    protected $examenEscuelaModel;
    protected $respuestaModel;
    protected $categoriaAprobadaModel;
    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->examenModel = new ExamenModel();
        $this->preguntaModel = new PreguntaModel();
        $this->examenCategoriaModel = new ExamenCategoriaModel();
        $this->examenEscuelaModel = new ExamenEscuelaModel();
        $this->respuestaModel = new RespuestaModel();
        $this->categoriaAprobadaModel = new CategoriaAprobadaModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Listar todos los exámenes con sus preguntas y categorías
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            $includePreguntas = $this->request->getGet('include_preguntas') ?? false;
            
            $examenes = $this->examenModel->paginate($perPage, 'default', $page);
            $pager = $this->examenModel->pager;
            
            // Obtener categorías para cada examen
            foreach ($examenes as &$examen) {
                $examen['categorias'] = $this->examenCategoriaModel->getCategoriasExamen($examen['examen_id']);
                
                if ($includePreguntas) {
                    $examen['preguntas'] = $this->preguntaModel->where('examen_id', $examen['examen_id'])->findAll();
                }
            }
            
            return $this->respond([
                'status' => 'success',
                'data' => $examenes,
                'pager' => $pager
            ]);
            
        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener exámenes: ' . $e->getMessage());
        }
    }

    /**
     * Obtener un examen específico
     */
    public function show($id = null)
    {
        try {
            $examen = $this->examenModel->find($id);
            
            if (!$examen) {
                return $this->failNotFound('Examen no encontrado');
            }
            
            // Obtener categorías del examen
            $examen['categorias'] = $this->examenCategoriaModel->getCategoriasExamen($id);
            
            // Obtener preguntas del examen
            $examen['preguntas'] = $this->preguntaModel->where('examen_id', $id)->findAll();
            
            // Obtener escuelas asociadas
            $examen['escuelas'] = $this->examenEscuelaModel->getEscuelasExamen($id);
            
            return $this->respond([
                'status' => 'success',
                'data' => $examen
            ]);
            
        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener examen: ' . $e->getMessage());
        }
    }

    /**
     * Crear un nuevo examen
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            // Log para debugging
            log_message('info', 'Datos recibidos en create examen: ' . json_encode($data));
            
            // Validar datos requeridos
            if (!isset($data['nombre']) || !isset($data['categorias'])) {
                return $this->fail('Faltan datos requeridos: nombre y categorias son obligatorios', 400);
            }

            // Validar que fecha_fin sea mayor que fecha_inicio
            if (isset($data['fecha_inicio']) && isset($data['fecha_fin'])) {
                $fecha_inicio = strtotime($data['fecha_inicio']);
                $fecha_fin = strtotime($data['fecha_fin']);
                
                if ($fecha_fin <= $fecha_inicio) {
                    return $this->fail('La fecha de fin debe ser posterior a la fecha de inicio', 400);
                }
            }

            // Iniciar transacción
            $this->db->transStart();

            // Insertar el examen
            $examenData = [
                'titulo' => $data['nombre'],
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'tiempo_limite' => $data['tiempo_limite'] ?? $data['duracion_minutos'] ?? 60,
                'duracion_minutos' => $data['duracion_minutos'] ?? $data['tiempo_limite'] ?? 60,
                'puntaje_minimo' => $data['puntaje_minimo'] ?? 70.00,
                'fecha_inicio' => $data['fecha_inicio'] ?? date('Y-m-d H:i:s'),
                'fecha_fin' => $data['fecha_fin'] ?? date('Y-m-d H:i:s', strtotime('+1 year')),
                'numero_preguntas' => $data['numero_preguntas'] ?? 1,
                'estado' => $data['estado'] ?? 'activo',
                'dificultad' => $data['dificultad'] ?? 'medio'
            ];

            if (!$this->examenModel->insert($examenData)) {
                return $this->fail($this->examenModel->errors());
            }

            $examen_id = $this->examenModel->getInsertID();

            // Asignar categorías al examen
            foreach ($data['categorias'] as $categoria_id) {
                $this->examenCategoriaModel->insert([
                    'examen_id' => $examen_id,
                    'categoria_id' => $categoria_id
                ]);
            }

            // Asignar escuelas al examen si se proporcionan
            if (isset($data['escuelas']) && is_array($data['escuelas'])) {
                foreach ($data['escuelas'] as $escuela_id) {
                    $this->examenEscuelaModel->asignarExamenEscuela($examen_id, $escuela_id);
                }
            }

            // Las preguntas se pueden asignar después de crear el examen
            // Por ahora solo creamos el examen con categorías
            if (isset($data['preguntas']) && is_array($data['preguntas']) && count($data['preguntas']) > 0) {
                log_message('info', 'Preguntas recibidas: ' . count($data['preguntas']));
                // Aquí se podría implementar la lógica para asignar preguntas existentes
            }

            // Confirmar transacción
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->failServerError('Error al crear el examen');
            }

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Examen creado exitosamente',
                'examen_id' => $examen_id
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al crear examen: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->failServerError('Error al crear examen: ' . $e->getMessage());
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
     * Actualizar un examen existente
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            
            $examen = $this->examenModel->find($id);
            if (!$examen) {
                return $this->failNotFound('Examen no encontrado');
            }

            // Actualizar datos del examen
            $examenData = [
                'titulo' => $data['nombre'] ?? $examen['titulo'],
                'nombre' => $data['nombre'] ?? $examen['nombre'],
                'descripcion' => $data['descripcion'] ?? $examen['descripcion'],
                'tiempo_limite' => $data['duracion_minutos'] ?? $examen['tiempo_limite'],
                'duracion_minutos' => $data['duracion_minutos'] ?? $examen['duracion_minutos'],
                'puntaje_minimo' => $data['puntaje_minimo'] ?? $examen['puntaje_minimo'],
                'estado' => $data['estado'] ?? $examen['estado']
            ];

            if (!$this->examenModel->update($id, $examenData)) {
                return $this->fail($this->examenModel->errors());
            }

            // Actualizar categorías si se proporcionan
            if (isset($data['categorias'])) {
                // Eliminar categorías existentes
                $this->examenCategoriaModel->where('examen_id', $id)->delete();
                
                // Agregar nuevas categorías
                foreach ($data['categorias'] as $categoria_id) {
                    $this->examenCategoriaModel->insert([
                        'examen_id' => $id,
                        'categoria_id' => $categoria_id
                    ]);
                }
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Examen actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al actualizar examen: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un examen
     */
    public function delete($id = null)
    {
        try {
            $examen = $this->examenModel->find($id);
            if (!$examen) {
                return $this->failNotFound('Examen no encontrado');
            }

            // Eliminar en cascada (las foreign keys se encargan del resto)
            if (!$this->examenModel->delete($id)) {
                return $this->failServerError('Error al eliminar el examen');
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Examen eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error al eliminar examen: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas del examen
     */
    public function estadisticas($id = null)
    {
        try {
            $examen = $this->examenModel->find($id);
            if (!$examen) {
                return $this->failNotFound('Examen no encontrado');
            }

            // Obtener categorías aprobadas para este examen
            $categoriasAprobadas = $this->categoriaAprobadaModel
                ->where('examen_id', $id)
                ->findAll();

            $estadisticas = [
                'total_presentados' => count($categoriasAprobadas),
                'aprobados' => count(array_filter($categoriasAprobadas, fn($ca) => $ca['estado'] === 'aprobado')),
                'reprobados' => count(array_filter($categoriasAprobadas, fn($ca) => $ca['estado'] === 'rechazado')),
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
} 