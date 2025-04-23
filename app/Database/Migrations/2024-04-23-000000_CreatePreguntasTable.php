<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePreguntasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pregunta_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'examen_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => true,
            ],
            'categoria_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => true,
            ],
            'enunciado' => [
                'type'       => 'TEXT',
                'null'      => false,
            ],
            'tipo_pregunta' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'      => false,
            ],
            'puntaje' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'      => false,
            ],
            'dificultad' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'      => false,
            ],
            'es_critica' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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

        $this->forge->addKey('pregunta_id', true);
        $this->forge->addKey('examen_id');
        $this->forge->addKey('categoria_id');
        
        $this->forge->createTable('preguntas');
    }

    public function down()
    {
        $this->forge->dropTable('preguntas');
    }
} 