<?php

namespace App\Controllers;

use App\Models\ResultadoExamenModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ResultadoController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new ResultadoExamenModel();
    }

    /**
     * Listar todos los resultados
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            $search = $this->request->getGet('search');

            $db = \Config\Database::connect();
            $builder = $db->table('resultados_examenes re')
                ->select('re.resultado_id, re.estado, re.fecha_realizacion, re.puntaje_total, c.dni, c.nombre, c.apellido')
                ->join('conductores c', 'c.conductor_id = re.conductor_id');

            if ($search) {
                $builder->groupStart()
                    ->like('c.dni', $search)
                    ->orLike('c.nombre', $search)
                    ->orLike('c.apellido', $search)
                    ->groupEnd();
            }

            $total = $builder->countAllResults(false);
            $resultados = $builder
                ->orderBy('re.fecha_realizacion', 'DESC')
                ->limit($perPage, ($page - 1) * $perPage)
                ->get()->getResultArray();

            // Formatear resultados
            $rows = array_map(function($r) {
                $estado = $r['estado'] === 'aprobado' ? 'aprobado' : ($r['estado'] === 'reprobado' ? 'desaprobado' : 'pendiente');
                $fecha = $r['fecha_realizacion'] ? date('d/m/Y H:i', strtotime($r['fecha_realizacion'])) : null;
                $puntaje = $r['puntaje_total'] !== null ? $r['puntaje_total'] . '/60' : null;
                return [
                    'dni' => $r['dni'],
                    'usuario' => trim($r['nombre'] . ' ' . $r['apellido']),
                    'estado' => $estado,
                    'fecha_realizado' => $fecha,
                    'puntaje' => $puntaje
                ];
            }, $resultados);

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'resultados' => $rows,
                    'pagination' => [
                        'current_page' => (int)$page,
                        'total_pages' => ceil($total / $perPage),
                        'total_items' => $total,
                        'per_page' => (int)$perPage
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Verifica si un usuario puede presentar un examen
     */
    public function verificarEstado($usuario_id)
    {
        try {
            $resultado = $this->model->puedePresentarExamen($usuario_id);
            
            return $this->respond([
                'status' => 'success',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Registra un nuevo resultado de examen
     */
    public function registrar()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!$data) {
                return $this->fail('No se recibieron datos', 400);
            }

            // Verificar si el usuario puede presentar el examen
            $verificacion = $this->model->puedePresentarExamen($data['usuario_id']);
            
            if (!$verificacion['puede_presentar']) {
                return $this->fail([
                    'status' => 'error',
                    'message' => $verificacion['mensaje']
                ], 400);
            }

            // Registrar el resultado
            $resultado_id = $this->model->registrarResultado($data);

            if ($resultado_id) {
                return $this->respondCreated([
                    'status' => 'success',
                    'message' => 'Resultado registrado correctamente',
                    'data' => [
                        'resultado_id' => $resultado_id
                    ]
                ]);
            }

            return $this->fail('Error al registrar el resultado', 500);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtiene el historial de resultados de un usuario
     */
    public function historial($usuario_id)
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            
            $resultados = $this->model->where('conductor_id', $usuario_id)
                                    ->orderBy('fecha_realizacion', 'DESC')
                                    ->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'resultados' => $resultados,
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
     * Obtiene el último resultado de un usuario
     */
    public function ultimoResultado($usuario_id)
    {
        try {
            $resultado = $this->model->where('conductor_id', $usuario_id)
                                   ->orderBy('fecha_realizacion', 'DESC')
                                   ->first();

            if (!$resultado) {
                return $this->failNotFound([
                    'status' => 'error',
                    'message' => 'No se encontraron resultados para este usuario'
                ]);
            }

            return $this->respond([
                'status' => 'success',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Evalúa un examen y retorna el puntaje final
     */
    public function evaluarExamen($resultado_id)
    {
        try {
            // Obtener el resultado del examen
            $resultado = $this->model->find($resultado_id);
            
            if (!$resultado) {
                return $this->failNotFound([
                    'status' => 'error',
                    'message' => 'Resultado de examen no encontrado'
                ]);
            }

            // Obtener el examen
            $examenModel = new \App\Models\ExamenModel();
            $examen = $examenModel->find($resultado['examen_id']);

            if (!$examen) {
                return $this->failNotFound([
                    'status' => 'error',
                    'message' => 'Examen no encontrado'
                ]);
            }

            // Obtener las respuestas del conductor
            $respuestaModel = new \App\Models\RespuestaConductorModel();
            $respuestas = $respuestaModel->where('resultado_examen_id', $resultado_id)->findAll();

            // Calcular puntaje total (suma de puntos por respuestas correctas)
            $puntajeTotal = 0;
            $preguntasCorrectas = 0;
            $preguntasIncorrectas = 0;
            $preguntaObligatoriaCorrecta = true;

            foreach ($respuestas as $respuesta) {
                // Obtener la pregunta
                $preguntaModel = new \App\Models\PreguntaModel();
                $pregunta = $preguntaModel->find($respuesta['pregunta_id']);

                if ($pregunta) {
                    if ($respuesta['es_correcta']) {
                        $puntajeTotal += $pregunta['puntaje'];
                        $preguntasCorrectas++;
                    } else {
                        $preguntasIncorrectas++;
                        // Si es una pregunta obligatoria y está incorrecta
                        if ($pregunta['es_critica']) {
                            $preguntaObligatoriaCorrecta = false;
                        }
                    }
                }
            }

            // Calcular puntaje máximo y porcentaje obtenido
            $puntajeMaximo = $examen['puntaje_minimo'] * 100 / 70; // Convertir el 70% al puntaje máximo
            $porcentajeObtenido = ($puntajeTotal / $puntajeMaximo) * 100;

            // Determinar si está aprobado (70% o más y pregunta obligatoria correcta)
            $aprobado = $porcentajeObtenido >= 70 && $preguntaObligatoriaCorrecta;

            // Actualizar el resultado en la tabla resultados_examenes
            $this->model->update($resultado_id, [
                'puntaje_total' => $puntajeTotal,
                'preguntas_correctas' => $preguntasCorrectas,
                'preguntas_incorrectas' => $preguntasIncorrectas,
                'estado' => $aprobado ? 'aprobado' : 'reprobado'
            ]);

            // Actualizar la tabla examen_conductor
            $examenConductorModel = new \App\Models\ExamenConductorModel();
            $examenConductor = $examenConductorModel->where('examen_id', $examen['examen_id'])
                                                  ->where('conductor_id', $resultado['conductor_id'])
                                                  ->first();

            if ($examenConductor) {
                $examenConductorModel->update($examenConductor['examen_conductor_id'], [
                    'aprobado' => $aprobado,
                    'puntuacion_final' => $porcentajeObtenido, // Guardamos el porcentaje obtenido
                    'estado' => $aprobado ? 'aprobado' : 'reprobado',
                    'fecha_fin' => date('Y-m-d H:i:s')
                ]);
            }

            // Manejar lógica de intentos para examen_asignado
            $examenAsignadoModel = new \App\Models\ExamenAsignadoModel();
            $asignacion = $examenAsignadoModel->where('conductor_id', $resultado['conductor_id'])
                                            ->where('examen_id', $examen['examen_id'])
                                            ->first();

            if ($asignacion) {
                if ($aprobado) {
                    // Si está aprobado, marcar como aprobado
                    $examenAsignadoModel->update($asignacion['id'], [
                        'aprobado' => 1,
                        'puntaje_final' => $porcentajeObtenido,
                        'fecha_aprobacion' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    // Si no está aprobado, reducir intentos
                    $intentosRestantes = $asignacion['intentos_disponibles'] - 1;
                    $examenAsignadoModel->update($asignacion['id'], [
                        'intentos_disponibles' => $intentosRestantes
                    ]);
                    
                    // Si se agotaron los intentos, marcar como reprobado
                    if ($intentosRestantes <= 0) {
                        $examenAsignadoModel->update($asignacion['id'], [
                            'aprobado' => 0,
                            'puntaje_final' => $porcentajeObtenido
                        ]);
                    }
                }
            }

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'resultado_id' => $resultado_id,
                    'examen_id' => $examen['examen_id'],
                    'puntaje_total' => $puntajeTotal, // Puntos obtenidos
                    'puntaje_maximo' => $puntajeMaximo, // Puntos posibles
                    'porcentaje_obtenido' => $porcentajeObtenido, // Porcentaje final
                    'preguntas_correctas' => $preguntasCorrectas,
                    'preguntas_incorrectas' => $preguntasIncorrectas,
                    'pregunta_obligatoria_correcta' => $preguntaObligatoriaCorrecta,
                    'aprobado' => $aprobado,
                    'estado' => $aprobado ? 'aprobado' : 'reprobado'
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
