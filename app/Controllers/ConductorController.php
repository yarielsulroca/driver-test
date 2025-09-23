<?php

namespace App\Controllers;

use App\Models\ConductorModel;
use App\Models\CategoriaAsignadaModel;
use App\Models\CategoriaAprobadaModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ConductorController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $categoriaAsignadaModel;
    protected $categoriaAprobadaModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new ConductorModel();
        $this->categoriaAsignadaModel = new CategoriaAsignadaModel();
        $this->categoriaAprobadaModel = new CategoriaAprobadaModel();
    }

    /**
     * Listar todos los registros
     */
    public function index()
    {
        try {
            // Obtener conductores con información básica
            $data = $this->model->select('
                conductores.*,
                usuarios.nombre,
                usuarios.apellido,
                usuarios.email,
                usuarios.dni
            ')
            ->join('usuarios', 'usuarios.usuario_id = conductores.usuario_id')
            ->findAll();

            // Agregar información completa de exámenes y categorías para cada conductor
            foreach ($data as &$conductor) {
                $conductorId = $conductor['conductor_id'];
                
                // Información del usuario
                $conductor['usuario'] = [
                    'usuario_id' => $conductor['usuario_id'],
                    'nombre' => $conductor['nombre'],
                    'apellido' => $conductor['apellido'],
                    'email' => $conductor['email'],
                    'dni' => $conductor['dni']
                ];

                // Categorías asignadas (todas las categorías del conductor)
                $categoriasAsignadas = $this->categoriaAsignadaModel->getCategoriasAsignadasCompletas($conductorId);
                $conductor['categorias_asignadas'] = $categoriasAsignadas;

                // Categorías aprobadas (solo las que aprobó)
                $categoriasAprobadas = $this->categoriaAsignadaModel->getCategoriasAprobadasCompletas($conductorId);
                $conductor['categorias_aprobadas'] = $categoriasAprobadas;

                // Categorías pendientes (no aprobadas: Reprobada, Iniciado)
                $categoriasPendientes = $this->categoriaAsignadaModel->getCategoriasPendientesCompletas($conductorId);
                $conductor['categorias_pendientes'] = $categoriasPendientes;

                // Resumen de categorías
                $conductor['resumen_categorias'] = $this->getResumenCategorias($categoriasAsignadas, $categoriasAprobadas);

                // Obtener información completa de exámenes asignados
                $examenesAsignados = $this->getExamenesAsignadosCompletos($conductorId);
                $conductor['examenes_asignados'] = $examenesAsignados;
                $conductor['total_examenes_asignados'] = count($examenesAsignados);
                $conductor['examenes_aprobados'] = count(array_filter($examenesAsignados, fn($e) => $e['aprobado'] == 1));
                $conductor['examenes_pendientes'] = count(array_filter($examenesAsignados, fn($e) => $e['aprobado'] == 0));
                $conductor['examenes_habilitados_no_realizados'] = count(array_filter($examenesAsignados, fn($e) => $e['aprobado'] == 0 && $e['intentos_disponibles'] > 0));

                // Las categorías aprobadas ya se obtuvieron arriba con el nuevo modelo
                // $categoriasAprobadas = $this->getCategoriasAprobadas($conductorId);
                // $conductor['categorias_aprobadas'] = $categoriasAprobadas;

                // Estadísticas de por vida
                $estadisticasVida = $this->getEstadisticasVida($conductorId);
                $conductor['estadisticas_vida'] = $estadisticasVida;

                // Historial de exámenes realizados
                $historialExamenes = $this->getHistorialExamenes($conductorId);
                $conductor['historial_examenes'] = $historialExamenes;
            }

            return $this->respond([
                'status' => 'success',
                'data' => $data,
                'total' => count($data)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener conductores: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Error al obtener conductores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un registro específico
     */
    public function show($id = null)
    {
        try {
            $data = $this->model->find($id);
            if (!$data) {
                return $this->failNotFound('Registro no encontrado');
            }
            return $this->respond([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error al obtener registro: ' . $e->getMessage());
        }
    }

    /**
     * Crear un nuevo registro
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                log_message('error', 'No se recibieron datos válidos');
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'No se recibieron datos válidos'
                ]);
            }

            // Validar que el usuario existe
            $usuarioModel = new \App\Models\UsuarioModel();
            
            if (!isset($data['usuario_id'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'usuario_id es requerido'
                ]);
            }
            
            $usuario = $usuarioModel->find($data['usuario_id']);
            
            if (!$usuario) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Usuario no válido'
                ]);
            }

            // Verificar que no exista ya un conductor para este usuario
            $conductorExistente = $this->model->where('usuario_id', $data['usuario_id'])->first();
            if ($conductorExistente) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Ya existe un conductor para este usuario'
                ]);
            }

            if (!$this->model->insert($data)) {
                $errors = $this->model->errors();
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $errors
                ]);
            }

            $id = $this->model->getInsertID();
            
            // Obtener el conductor creado con información del usuario
            $conductor = $this->model->select('
                conductores.*,
                usuarios.nombre,
                usuarios.apellido,
                usuarios.email,
                usuarios.dni
            ')
            ->join('usuarios', 'usuarios.usuario_id = conductores.usuario_id')
            ->where('conductores.conductor_id', $id)
            ->first();

            $conductor['usuario'] = [
                'usuario_id' => $conductor['usuario_id'],
                'nombre' => $conductor['nombre'],
                'apellido' => $conductor['apellido'],
                'email' => $conductor['email'],
                'dni' => $conductor['dni']
            ];

            return $this->response->setStatusCode(201)->setJSON([
                'status' => 'success',
                'message' => 'Conductor creado exitosamente',
                'data' => $conductor
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al crear conductor: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar un registro
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            if (!$data) {
                return $this->fail('No se recibieron datos válidos');
            }

            if (!$this->model->find($id)) {
                return $this->failNotFound('Registro no encontrado');
            }

            if (!$this->model->update($id, $data)) {
                $errors = $this->model->errors();
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $errors
                ]);
            }

            $data = $this->model->find($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Registro actualizado exitosamente',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error al actualizar registro: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un registro
     */
    public function delete($id = null)
    {
        try {
            if (!$this->model->find($id)) {
                return $this->failNotFound('Registro no encontrado');
            }

            $this->model->delete($id);

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Registro eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error al eliminar registro: ' . $e->getMessage());
        }
    }

    /**
     * Obtener exámenes asignados completos con información detallada
     */
    private function getExamenesAsignadosCompletos($conductorId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                ea.id,
                ea.conductor_id,
                ea.examen_id,
                ea.intentos_disponibles,
                ea.aprobado,
                ea.fecha_asignacion,
                ea.fecha_aprobacion,
                ea.puntaje_final,
                e.titulo,
                e.descripcion,
                e.tiempo_limite,
                e.duracion_minutos,
                e.puntaje_minimo,
                e.dificultad,
                e.estado as examen_estado,
                CASE 
                    WHEN ea.aprobado = 1 THEN 'Aprobado'
                    WHEN ea.intentos_disponibles = 0 AND ea.aprobado = 0 THEN 'Reprobado'
                    WHEN ea.intentos_disponibles > 0 AND ea.aprobado = 0 THEN 'Pendiente'
                    ELSE 'Sin estado'
                END as estado_texto
            FROM examen_asignado ea
            LEFT JOIN examenes e ON e.examen_id = ea.examen_id
            WHERE ea.conductor_id = ?
            ORDER BY ea.fecha_asignacion DESC
        ", [$conductorId]);
        
        return $query->getResultArray();
    }

    /**
     * Obtener categorías aprobadas por el conductor
     */
    private function getCategoriasAprobadas($conductorId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT DISTINCT
                e.dificultad as categoria,
                COUNT(*) as total_examenes_categoria,
                SUM(CASE WHEN ea.aprobado = 1 THEN 1 ELSE 0 END) as examenes_aprobados_categoria,
                AVG(CASE WHEN ea.aprobado = 1 THEN ea.puntaje_final ELSE NULL END) as promedio_puntaje_categoria,
                MAX(ea.fecha_aprobacion) as ultima_aprobacion
            FROM examen_asignado ea
            LEFT JOIN examenes e ON e.examen_id = ea.examen_id
            WHERE ea.conductor_id = ? AND ea.aprobado = 1
            GROUP BY e.dificultad
            ORDER BY e.dificultad
        ", [$conductorId]);
        
        return $query->getResultArray();
    }

    /**
     * Obtener estadísticas de por vida del conductor
     */
    private function getEstadisticasVida($conductorId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                COUNT(*) as total_examenes_tomados,
                SUM(CASE WHEN ea.aprobado = 1 THEN 1 ELSE 0 END) as total_aprobados,
                SUM(CASE WHEN ea.aprobado = 0 THEN 1 ELSE 0 END) as total_reprobados,
                AVG(CASE WHEN ea.aprobado = 1 THEN ea.puntaje_final ELSE NULL END) as promedio_puntaje_vida,
                MIN(ea.fecha_asignacion) as primer_examen,
                MAX(CASE WHEN ea.aprobado = 1 THEN ea.fecha_aprobacion ELSE NULL END) as ultimo_aprobado,
                SUM(ea.intentos_disponibles) as intentos_restantes_totales
            FROM examen_asignado ea
            WHERE ea.conductor_id = ?
        ", [$conductorId]);
        
        $result = $query->getRowArray();
        
        // Calcular porcentaje de aprobación
        if ($result['total_examenes_tomados'] > 0) {
            $result['porcentaje_aprobacion'] = round(($result['total_aprobados'] / $result['total_examenes_tomados']) * 100, 2);
        } else {
            $result['porcentaje_aprobacion'] = 0;
        }
        
        return $result;
    }

    /**
     * Obtener historial detallado de exámenes realizados
     */
    private function getHistorialExamenes($conductorId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                ea.id,
                ea.examen_id,
                ea.aprobado,
                ea.puntaje_final,
                ea.fecha_asignacion,
                ea.fecha_aprobacion,
                ea.intentos_disponibles,
                e.titulo,
                e.dificultad,
                e.puntaje_minimo,
                CASE 
                    WHEN ea.aprobado = 1 THEN 'Aprobado'
                    WHEN ea.intentos_disponibles = 0 AND ea.aprobado = 0 THEN 'Reprobado'
                    WHEN ea.intentos_disponibles > 0 AND ea.aprobado = 0 THEN 'Pendiente'
                    ELSE 'Sin estado'
                END as estado_texto,
                CASE 
                    WHEN ea.puntaje_final >= e.puntaje_minimo THEN 'Cumple requisitos'
                    WHEN ea.puntaje_final IS NOT NULL THEN 'No cumple requisitos'
                    ELSE 'Sin calificar'
                END as cumple_requisitos
            FROM examen_asignado ea
            LEFT JOIN examenes e ON e.examen_id = ea.examen_id
            WHERE ea.conductor_id = ?
            ORDER BY ea.fecha_asignacion DESC, ea.fecha_aprobacion DESC
        ", [$conductorId]);
        
        return $query->getResultArray();
    }

    /**
     * Obtener resumen de categorías para un conductor
     */
    private function getResumenCategorias($categoriasAsignadas, $categoriasAprobadas)
    {
        $resumen = [
            'total_categorias_asignadas' => count($categoriasAsignadas),
            'categorias_pendientes' => 0,
            'categorias_iniciadas' => 0,
            'categorias_aprobadas' => count($categoriasAprobadas),
            'total_intentos_realizados' => 0,
            'total_intentos_maximos' => 0,
            'categorias_con_examen_asignado' => 0,
            'puede_asignar_examen' => false
        ];

        foreach ($categoriasAsignadas as $categoria) {
            $resumen['total_intentos_realizados'] += $categoria['intentos_realizados'];
            $resumen['total_intentos_maximos'] += $categoria['intentos_maximos'];
            
            if ($categoria['examen_id'] !== null) {
                $resumen['categorias_con_examen_asignado']++;
            }

            switch ($categoria['estado']) {
                case 'pendiente':
                    $resumen['categorias_pendientes']++;
                    if ($categoria['examen_id'] !== null) {
                        $resumen['puede_asignar_examen'] = true;
                    }
                    break;
                case 'iniciado':
                    $resumen['categorias_iniciadas']++;
                    $resumen['puede_asignar_examen'] = true;
                    break;
                case 'aprobado':
                    // Ya contado en categorias_aprobadas
                    break;
            }
        }

        return $resumen;
    }
}
