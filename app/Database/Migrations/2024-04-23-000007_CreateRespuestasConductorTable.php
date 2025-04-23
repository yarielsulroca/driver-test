<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRespuestasConductorTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'respuesta_conductor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'resultado_examen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'pregunta_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'respuesta_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'es_correcta' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'tiempo_respuesta' => [
                'type' => 'INT',
                'constraint' => 11,
                'comment' => 'Tiempo en segundos'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('respuesta_conductor_id', true);
        $this->forge->addForeignKey('resultado_examen_id', 'resultados_examenes', 'resultado_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pregunta_id', 'preguntas', 'pregunta_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('respuesta_id', 'respuestas', 'respuesta_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('respuestas_conductor');
    }

    public function down()
    {
        $this->forge->dropTable('respuestas_conductor');
    }
} 