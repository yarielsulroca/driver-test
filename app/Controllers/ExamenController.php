<?php

namespace App\Controllers;

use App\Models\ExamenModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\BaseConnection;

class ExamenController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->model = new ExamenModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Listar todos los exámenes
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            
            $examenes = $this->model->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'examenes' => $examenes,
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
     * Obtener un examen específico
     */
    public function show($id = null)
    {
        try {
            $examen = $this->model->find($id);
            
            if (!$examen) {
                return $this->failNotFound('Examen no encontrado');
            }

            // Convertir el JSON de paginas_preguntas a array
            $examen['paginas_preguntas'] = json_decode($examen['paginas_preguntas'], true);
            
            // Obtener las categorías
            $examen['categorias'] = $this->model->getCategorias($id);

            return $this->respond([
                'status' => 'success',
                'data' => $examen
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Crear un nuevo examen
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data) {
                return $this->fail('No se recibieron datos', 400);
            }

            // Validar la estructura de paginas_preguntas
            if (!isset($data['paginas_preguntas']) || !is_array($data['paginas_preguntas'])) {
                return $this->fail('El formato de páginas de preguntas es inválido', 400);
            }

            // Validar que cada página tenga preguntas y una respuesta correcta
            foreach ($data['paginas_preguntas'] as $index => $pagina) {
                if (!is_array($pagina) || empty($pagina)) {
                    return $this->fail("La página {$index} no tiene un formato válido", 400);
                }
            }

            // Validar categorías
            if (!isset($data['categorias']) || !is_array($data['categorias']) || empty($data['categorias'])) {
                return $this->fail('Debe especificar al menos una categoría', 400);
            }

            // Convertir el array a JSON para almacenamiento
            $data['paginas_preguntas'] = json_encode($data['paginas_preguntas']);

            // Guardar las categorías temporalmente
            $categorias = $data['categorias'];
            unset($data['categorias']);

            // Iniciar transacción
            $this->db->transStart();

            // Insertar el examen
            if (!$this->model->insert($data)) {
                return $this->fail($this->model->errors());
            }

            $examen_id = $this->model->getInsertID();

            // Asignar categorías
            if (!$this->model->asignarCategorias($examen_id, $categorias)) {
                return $this->fail('Error al asignar categorías');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->fail('Error al crear el examen');
            }

            $examen = $this->model->find($examen_id);
            $examen['paginas_preguntas'] = json_decode($examen['paginas_preguntas'], true);
            $examen['categorias'] = $this->model->getCategorias($examen_id);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Examen creado exitosamente',
                'data' => $examen
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Actualizar un examen existente
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$data || !$id) {
                return $this->fail('Datos inválidos', 400);
            }

            $examenExiste = $this->model->find($id);
            if (!$examenExiste) {
                return $this->failNotFound('Examen no encontrado');
            }

            // Validar categorías si se proporcionan
            if (isset($data['categorias'])) {
                if (!is_array($data['categorias']) || empty($data['categorias'])) {
                    return $this->fail('Debe especificar al menos una categoría', 400);
                }
                $categorias = $data['categorias'];
                unset($data['categorias']);
            }

            // Iniciar transacción
            $this->db->transStart();

            if (!$this->model->update($id, $data)) {
                return $this->fail($this->model->errors());
            }

            // Actualizar categorías si se proporcionaron
            if (isset($categorias)) {
                if (!$this->model->asignarCategorias($id, $categorias)) {
                    return $this->fail('Error al actualizar categorías');
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->fail('Error al actualizar el examen');
            }

            $examen = $this->model->find($id);
            $examen['paginas_preguntas'] = json_decode($examen['paginas_preguntas'], true);
            $examen['categorias'] = $this->model->getCategorias($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Examen actualizado exitosamente',
                'data' => $examen
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Eliminar un examen
     */
    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('ID no proporcionado', 400);
            }

            $examenExiste = $this->model->find($id);
            if (!$examenExiste) {
                return $this->failNotFound('Examen no encontrado');
            }

            if (!$this->model->delete($id)) {
                return $this->fail('No se pudo eliminar el examen');
            }

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Examen eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Obtener exámenes por categoría
     */
    public function porCategoria($categoria_id)
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            
            $examenes = $this->model->where('categoria_id', $categoria_id)
                                  ->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;
            
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'examenes' => $examenes,
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
     * Obtener exámenes activos
     */
    public function activos()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            
            $fechaActual = date('Y-m-d H:i:s');
            $examenes = $this->model->where('fecha_inicio <=', $fechaActual)
                                  ->where('fecha_fin >=', $fechaActual)
                                  ->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;
            
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'examenes' => $examenes,
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
} 