<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema. Puede gestionar usuarios, roles, escuelas y exámenes.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nombre' => 'Supervisor',
                'descripcion' => 'Puede gestionar exámenes y conductores de su escuela asignada.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nombre' => 'Conductor',
                'descripcion' => 'Puede presentar exámenes y ver su historial.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('roles')->insertBatch($data);
    }
} 