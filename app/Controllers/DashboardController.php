<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends ResourceController
{
    protected $format = 'json';

    public function __construct()
    {
        // Configurar CORS si es necesario
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        
        // Manejar peticiones OPTIONS (preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /**
     * Obtener estadísticas del dashboard
     */
    public function getStats()
    {
        try {
            $db = \Config\Database::connect();
            
            // Contar oficinas (escuelas)
            $oficinasCount = $db->table('escuelas')->countAllResults();
            
            // Contar categorías
            $categoriasCount = $db->table('categorias')->countAllResults();
            
            // Contar exámenes
            $examenesCount = $db->table('examenes')->countAllResults();
            
            // Contar preguntas
            $preguntasCount = $db->table('preguntas')->countAllResults();
            
            // Contar conductores
            $conductoresCount = $db->table('conductores')->countAllResults();
            
            // Contar categorías aprobadas
            $categoriasAprobadasCount = $db->table('categorias_aprobadas')->countAllResults();
            
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'oficinas' => $oficinasCount,
                    'categorias' => $categoriasCount,
                    'examenes' => $examenesCount,
                    'preguntas' => $preguntasCount,
                    'conductores' => $conductoresCount,
                    'categorias_aprobadas' => $categoriasAprobadasCount
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo estadísticas del dashboard: ' . $e->getMessage());
            return $this->failServerError('Error al obtener las estadísticas: ' . $e->getMessage());
        }
    }
}
