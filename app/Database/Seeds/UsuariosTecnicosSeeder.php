<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UsuarioModel;

class UsuariosTecnicosSeeder extends Seeder
{
    public function run()
    {
        $model = new UsuarioModel();

        $usuarios = [
            [
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'email' => 'admin@sistema.com',
                'password' => 'admin123',
                'rol' => 'tecnico',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'Técnico',
                'apellido' => 'Evaluador',
                'email' => 'tecnico@sistema.com',
                'password' => 'tecnico123',
                'rol' => 'tecnico',
                'estado' => 'activo'
            ]
        ];

        foreach ($usuarios as $usuario) {
            $model->insert($usuario);
        }
    }
} 