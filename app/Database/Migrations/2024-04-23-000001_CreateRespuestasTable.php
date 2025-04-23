<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRespuestasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'respuesta_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'pregunta_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'texto' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'es_correcta' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('respuesta_id', true);
        $this->forge->addForeignKey('pregunta_id', 'preguntas', 'pregunta_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('respuestas');
    }

    public function down()
    {
        $this->forge->dropTable('respuestas');
    }
} 