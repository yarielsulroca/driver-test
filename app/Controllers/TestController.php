<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class TestController extends ResourceController
{
    use ResponseTrait;

    public function index()
    {
        return $this->respond([
            'status' => 'success',
            'message' => 'Backend funcionando correctamente',
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => [
                'servidor' => 'CodeIgniter 4',
                'version' => '4.6.0',
                'php_version' => PHP_VERSION
            ]
        ]);
    }

    public function usuarios()
    {
        try {
            // Intentar obtener usuarios reales de la base de datos
            $db = \Config\Database::connect();
            
            $usuarios = $db->table('usuarios')
                ->select('usuario_id, nombre, apellido, dni, email, estado')
                ->where('estado', 'activo')
                ->get()
                ->getResultArray();
                
            if (empty($usuarios)) {
                // Si no hay usuarios en la BD, crear algunos de prueba
                $usuarios = [
                    [
                        'usuario_id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Pérez',
                        'dni' => '12345678',
                        'email' => 'juan.perez@ejemplo.com',
                        'estado' => 'activo'
                    ],
                    [
                        'usuario_id' => 2,
                        'nombre' => 'María',
                        'apellido' => 'González',
                        'dni' => '87654321',
                        'email' => 'maria.gonzalez@ejemplo.com',
                        'estado' => 'activo'
                    ],
                    [
                        'usuario_id' => 3,
                        'nombre' => 'Carlos',
                        'apellido' => 'López',
                        'dni' => '11223344',
                        'email' => 'carlos.lopez@ejemplo.com',
                        'estado' => 'activo'
                    ]
                ];
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Usuarios obtenidos exitosamente',
                'data' => $usuarios
            ]);
            
        } catch (\Exception $e) {
            // En caso de error de BD, devolver datos de prueba
            $usuarios = [
                [
                    'usuario_id' => 1,
                    'nombre' => 'Juan',
                    'apellido' => 'Pérez',
                    'dni' => '12345678',
                    'email' => 'juan.perez@ejemplo.com',
                    'estado' => 'activo'
                ],
                [
                    'usuario_id' => 2,
                    'nombre' => 'María',
                    'apellido' => 'González',
                    'dni' => '87654321',
                    'email' => 'maria.gonzalez@ejemplo.com',
                    'estado' => 'activo'
                ]
            ];

            return $this->respond([
                'status' => 'success',
                'message' => 'Usuarios de prueba (BD no disponible)',
                'data' => $usuarios
            ]);
        }
    }

    public function conductores()
    {
        // Datos de prueba para conductores
        $conductores = [
            [
                'conductor_id' => 1,
                'usuario_id' => 1,
                'licencia' => 'LIC001',
                'fecha_vencimiento' => '2025-12-31',
                'estado' => 'activo',
                'categoria_principal' => 'A',
                'fecha_registro' => '2024-01-15',
                'created_at' => '2024-01-15 10:00:00',
                'updated_at' => '2024-01-15 10:00:00'
            ],
            [
                'conductor_id' => 2,
                'usuario_id' => 2,
                'licencia' => 'LIC002',
                'fecha_vencimiento' => '2025-11-30',
                'estado' => 'activo',
                'categoria_principal' => 'B',
                'fecha_registro' => '2024-02-01',
                'created_at' => '2024-02-01 14:30:00',
                'updated_at' => '2024-02-01 14:30:00'
            ]
        ];

        return $this->respond([
            'status' => 'success',
            'message' => 'Conductores de prueba',
            'data' => $conductores
        ]);
    }

    public function crearConductor()
    {
        $data = $this->request->getJSON(true);
        
        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Conductor creado exitosamente (modo prueba)',
            'data' => array_merge($data, [
                'conductor_id' => rand(100, 999),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])
        ]);
    }
}