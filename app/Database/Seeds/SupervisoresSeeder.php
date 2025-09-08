<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SupervisoresSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'supervisor_id' => 1,
                'usuario_id' => 1, // Admin del sistema
                'escuela_id' => 1, // Primera escuela
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'supervisor_id' => 2,
                'usuario_id' => 2, // Técnico evaluador
                'escuela_id' => 1, // Primera escuela
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('supervisores')->insertBatch($data);
    }
} 