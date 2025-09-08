<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Primero las escuelas
        $this->call('EscuelasSeeder');

        // Luego las categorías
        $this->call('CategoriasSeeder');

        // Usuarios técnicos
        $this->call('UsuariosTecnicosSeeder');

        // Supervisores
        $this->call('SupervisoresSeeder');

        // Después los exámenes
        $this->call('ExamenesE1Seeder');

        // Finalmente las preguntas y respuestas
        $this->call('PreguntasE1Seeder');
    }
} 