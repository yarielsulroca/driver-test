<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ExamenModel;
use App\Models\CategoriaModel;
use App\Models\CategoriaAsignadaModel;
use CodeIgniter\HTTP\ResponseInterface;

class ExamenController extends BaseController
{
    protected $examenModel;
    protected $categoriaModel;
    protected $categoriaAsignadaModel;

    public function __construct()
    {
        $this->examenModel = new ExamenModel();
        $this->categoriaModel = new CategoriaModel();
        $this->categoriaAsignadaModel = new CategoriaAsignadaModel();
    }

    /**
     * Listar todos los exámenes
     */
    public function index()
    {
        try {
            // Obtener solo los exámenes reales de la tabla
            $examenes = $this->examenModel
                ->where('estado', 'activo')
                ->findAll();

            // Agregar información de categorías si existe
            foreach ($examenes as &$examen) {
                // Buscar categorías asociadas a este examen
                $categorias = $this->categoriaModel
                    ->select('c.categoria_id, c.codigo, c.nombre')
                    ->from('categorias c')
                    ->join('examen_categoria ec', 'ec.categoria_id = c.categoria_id')
                    ->where('ec.examen_id', $examen['examen_id'])
                    ->findAll();
                
                if (!empty($categorias)) {
                    // Eliminar duplicados por categoria_id
                    $categoriasUnicas = [];
                    $categoriaIds = [];
                    foreach ($categorias as $categoria) {
                        if (!in_array($categoria['categoria_id'], $categoriaIds)) {
                            $categoriasUnicas[] = $categoria;
                            $categoriaIds[] = $categoria['categoria_id'];
                        }
                    }
                    
                    // Si hay múltiples categorías, mostrar la primera o concatenar los códigos
                    if (count($categoriasUnicas) == 1) {
                        $examen['categoria_id'] = $categoriasUnicas[0]['categoria_id'];
                        $examen['categoria_codigo'] = $categoriasUnicas[0]['codigo'];
                        $examen['categoria_nombre'] = $categoriasUnicas[0]['nombre'];
                    } else {
                        // Múltiples categorías - mostrar códigos concatenados únicos
                        $codigos = array_column($categoriasUnicas, 'codigo');
                        $nombres = array_column($categoriasUnicas, 'nombre');
                        $examen['categoria_id'] = $categoriasUnicas[0]['categoria_id']; // Primera categoría
                        $examen['categoria_codigo'] = implode(', ', $codigos);
                        $examen['categoria_nombre'] = implode(', ', $nombres);
                    }
                } else {
                    $examen['categoria_id'] = null;
                    $examen['categoria_codigo'] = 'Sin categoría';
                    $examen['categoria_nombre'] = 'Sin categoría asignada';
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $examenes,
                'total' => count($examenes)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en ExamenController::index: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Mostrar un examen específico
     */
    public function show($id = null)
    {
        try {
            if (!$id) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID de examen requerido'
                ])->setStatusCode(400);
            }

            $examen = $this->examenModel
                ->where('examen_id', $id)
                ->where('estado', 'activo')
                ->first();

            if (!$examen) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Examen no encontrado'
                ])->setStatusCode(404);
            }

            // Agregar información de categorías si existe
            $categorias = $this->categoriaModel
                ->select('c.categoria_id, c.codigo, c.nombre')
                ->from('categorias c')
                ->join('examen_categoria ec', 'ec.categoria_id = c.categoria_id')
                ->where('ec.examen_id', $examen['examen_id'])
                ->findAll();
            
            if (!empty($categorias)) {
                $examen['categoria_id'] = $categorias[0]['categoria_id'];
                $examen['categoria_codigo'] = $categorias[0]['codigo'];
                $examen['categoria_nombre'] = $categorias[0]['nombre'];
            } else {
                $examen['categoria_id'] = null;
                $examen['categoria_codigo'] = 'Sin categoría';
                $examen['categoria_nombre'] = 'Sin categoría asignada';
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $examen
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en ExamenController::show: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Actualizar un examen existente
     */
    public function update($id = null)
    {
        try {
            if (!$id) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID de examen requerido'
                ])->setStatusCode(400);
            }

            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Datos de examen requeridos'
                ])->setStatusCode(400);
            }

            // Verificar que el examen existe
            $examen = $this->examenModel->where('examen_id', $id)->first();
            
            if (!$examen) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Examen no encontrado'
                ])->setStatusCode(404);
            }

            // Actualizar el examen
            $examenActualizado = $this->examenModel->update($id, $data);

            if ($examenActualizado) {
                // Obtener el examen actualizado con información de categorías
                $examenActualizado = $this->examenModel->where('examen_id', $id)->first();
                
                // Agregar información de categorías si existe
                $categorias = $this->categoriaModel
                    ->select('c.categoria_id, c.codigo, c.nombre')
                    ->from('categorias c')
                    ->join('examen_categoria ec', 'ec.categoria_id = c.categoria_id')
                    ->where('ec.examen_id', $examenActualizado['examen_id'])
                    ->findAll();
                
                if (!empty($categorias)) {
                    $examenActualizado['categoria_id'] = $categorias[0]['categoria_id'];
                    $examenActualizado['categoria_codigo'] = $categorias[0]['codigo'];
                    $examenActualizado['categoria_nombre'] = $categorias[0]['nombre'];
                } else {
                    $examenActualizado['categoria_id'] = null;
                    $examenActualizado['categoria_codigo'] = 'Sin categoría';
                    $examenActualizado['categoria_nombre'] = 'Sin categoría asignada';
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Examen actualizado correctamente',
                    'data' => $examenActualizado
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Error al actualizar el examen'
                ])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en ExamenController::update: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Método de prueba para verificar datos de categorías
     */
    public function testCategorias()
    {
        try {
            // Verificar si hay datos en examen_categoria usando el modelo
            $examenCategoriaModel = new \App\Models\ExamenCategoriaModel();
            $examenCategoria = $examenCategoriaModel->findAll();
            
            // Verificar si hay datos en categorias
            $categorias = $this->categoriaModel->findAll();
            
            // Verificar join entre las tablas usando el modelo
            $join = $this->examenModel
                ->select('e.examen_id, e.nombre, ec.categoria_id, c.codigo, c.nombre as categoria_nombre')
                ->from('examenes e')
                ->join('examen_categoria ec', 'ec.examen_id = e.examen_id', 'left')
                ->join('categorias c', 'c.categoria_id = ec.categoria_id', 'left')
                ->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'examen_categoria' => $examenCategoria,
                    'categorias' => $categorias,
                    'join_result' => $join
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener exámenes disponibles para un conductor
     */
    public function disponibles()
    {
        try {
            $conductorId = $this->request->getGet('conductor_id');
            
            if (!$conductorId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID de conductor requerido'
            ])->setStatusCode(400);
            }

            // Obtener solo los exámenes reales de la tabla
            $examenesDisponibles = $this->examenModel
                ->where('estado', 'activo')
                ->findAll();

            // Agregar información de categorías si existe
            foreach ($examenesDisponibles as &$examen) {
                // Buscar categorías asociadas a este examen
                $categorias = $this->categoriaModel
                    ->select('c.categoria_id, c.codigo, c.nombre')
                    ->from('categorias c')
                    ->join('examen_categoria ec', 'ec.categoria_id = c.categoria_id')
                    ->where('ec.examen_id', $examen['examen_id'])
                    ->findAll();
                
                if (!empty($categorias)) {
                    // Eliminar duplicados por categoria_id
                    $categoriasUnicas = [];
                    $categoriaIds = [];
                    foreach ($categorias as $categoria) {
                        if (!in_array($categoria['categoria_id'], $categoriaIds)) {
                            $categoriasUnicas[] = $categoria;
                            $categoriaIds[] = $categoria['categoria_id'];
                        }
                    }
                    
                    // Si hay múltiples categorías, mostrar la primera o concatenar los códigos
                    if (count($categoriasUnicas) == 1) {
                        $examen['categoria_id'] = $categoriasUnicas[0]['categoria_id'];
                        $examen['categoria_codigo'] = $categoriasUnicas[0]['codigo'];
                        $examen['categoria_nombre'] = $categoriasUnicas[0]['nombre'];
                    } else {
                        // Múltiples categorías - mostrar códigos concatenados únicos
                        $codigos = array_column($categoriasUnicas, 'codigo');
                        $nombres = array_column($categoriasUnicas, 'nombre');
                        $examen['categoria_id'] = $categoriasUnicas[0]['categoria_id']; // Primera categoría
                        $examen['categoria_codigo'] = implode(', ', $codigos);
                        $examen['categoria_nombre'] = implode(', ', $nombres);
                    }
                } else {
                    $examen['categoria_id'] = null;
                    $examen['categoria_codigo'] = 'Sin categoría';
                    $examen['categoria_nombre'] = 'Sin categoría asignada';
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $examenesDisponibles,
                'total' => count($examenesDisponibles)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en ExamenController::disponibles: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Obtener exámenes reprobados por un conductor
     */
    public function reprobados()
    {
        try {
            $conductorId = $this->request->getGet('conductor_id');
            
            if (!$conductorId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID de conductor requerido'
                ])->setStatusCode(400);
            }

            $examenesReprobados = $this->examenModel
                ->select('
                    e.examen_id,
                    e.titulo,
                    e.descripcion,
                    e.dificultad,
                    e.puntaje_minimo,
                    e.tiempo_limite,
                    e.duracion_minutos,
                    e.estado,
                    c.categoria_id,
                    c.codigo as categoria_codigo,
                    c.nombre as categoria_nombre,
                    ea.intentos_disponibles,
                    ea.fecha_asignacion,
                    ea.puntaje_final
                ')
                ->from('examenes e')
                ->join('examen_categoria ec', 'ec.examen_id = e.examen_id')
                ->join('categorias c', 'c.categoria_id = ec.categoria_id')
                ->join('examen_asignado ea', 'ea.examen_id = e.examen_id')
                ->where('ea.conductor_id', $conductorId)
                ->where('ea.intentos_disponibles', 0)
                ->where('ea.aprobado', 0)
                ->where('e.deleted_at IS NULL')
                ->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $examenesReprobados,
                'total' => count($examenesReprobados)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en ExamenController::reprobados: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ])->setStatusCode(500);
        }
    }

    /**
     * Asignar un examen a un conductor
     */
    public function asignar()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Datos JSON requeridos'
                ])->setStatusCode(400);
            }

            $conductorId = $data['conductor_id'] ?? null;
            $examenId = $data['examen_id'] ?? null;
            $categoriaId = $data['categoria_id'] ?? null;

            if (!$conductorId || !$examenId || !$categoriaId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'conductor_id, examen_id y categoria_id son requeridos'
                ])->setStatusCode(400);
            }

            // Verificar que el examen existe y está activo
            $examen = $this->examenModel->where('examen_id', $examenId)
                ->where('estado', 'activo')
                ->first();

            if (!$examen) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Examen no encontrado o inactivo'
                ])->setStatusCode(404);
            }

            // Crear nueva asignación
            $this->categoriaAsignadaModel->insert([
                'conductor_id' => $conductorId,
                'categoria_id' => $categoriaId,
                'examen_id' => $examenId,
                'estado' => 'Iniciado',
                'intentos_realizados' => 0,
                'intentos_maximos' => 3,
                'fecha_asignacion' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Examen asignado exitosamente',
                'data' => [
                    'conductor_id' => $conductorId,
                    'examen_id' => $examenId,
                    'categoria_id' => $categoriaId,
                    'estado' => 'Iniciado'
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en ExamenController::asignar: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}