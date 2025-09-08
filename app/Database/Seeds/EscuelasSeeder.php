<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EscuelasSeeder extends Seeder
{
    public function run()
    {
        $escuelas = [
            [
                'nombre' => 'Escuela de Manejo Central',
                'direccion' => 'Av. Principal 123',
                'ciudad' => 'Buenos Aires',
                'telefono' => '11-1234-5678',
                'email' => 'central@escuela.com',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'Instituto de Conducción Norte',
                'direccion' => 'Calle Norte 456',
                'ciudad' => 'Buenos Aires',
                'telefono' => '11-2345-6789',
                'email' => 'norte@instituto.com',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'Centro de Formación Sur',
                'direccion' => 'Ruta Sur 789',
                'ciudad' => 'Buenos Aires',
                'telefono' => '11-3456-7890',
                'email' => 'sur@centro.com',
                'estado' => 'activo'
            ]
        ];

        foreach ($escuelas as $escuela) {
            $this->db->table('escuelas')->insert($escuela);
        }

        echo "✅ Escuelas creadas exitosamente\n";
    }
} 