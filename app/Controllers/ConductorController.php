<?php

namespace App\Controllers;

use App\Models\ConductorModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ConductorController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new ConductorModel();
    }

    /**
     * Listar todos los conductores
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            
            $conductores = $this->model->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;

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

    // ... existing code ...
} 