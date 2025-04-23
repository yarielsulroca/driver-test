<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEsCriticaToPreguntas extends Migration
{
    public function up()
    {
        $this->forge->addColumn('preguntas', [
            'es_critica' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'dificultad'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('preguntas', 'es_critica');
    }
} 