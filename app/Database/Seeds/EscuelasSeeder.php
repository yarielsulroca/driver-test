<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\EscuelaModel;

class EscuelasSeeder extends Seeder
{
    public function run()
    {
        $escuelaModel = new EscuelaModel();

        $escuelas = [
            [
                'nombre' => 'Escuela de Conductores Profesionales',
                'direccion' => 'Av. Principal 123',
                'telefono' => '011-4567-8901',
                'email' => 'info@escuelaconductores.com',
                'licencia' => 'LIC-001-2024',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'Academia de Manejo Seguro',
                'direccion' => 'Calle Secundaria 456',
                'telefono' => '011-2345-6789',
                'email' => 'contacto@academiamanejo.com',
                'licencia' => 'LIC-002-2024',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'Centro de Capacitación Vial',
                'direccion' => 'Ruta Nacional 789',
                'telefono' => '011-8901-2345',
                'email' => 'info@centrovial.com',
                'licencia' => 'LIC-003-2024',
                'estado' => 'activo'
            ]
        ];

        foreach ($escuelas as $escuela) {
            $escuelaModel->insert($escuela);
        }
    }
} 