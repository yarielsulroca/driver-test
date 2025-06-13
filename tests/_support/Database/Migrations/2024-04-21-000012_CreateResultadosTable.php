<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResultadosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'resultado_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'conductor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'examen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'puntuacion' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['aprobado', 'reprobado', 'pendiente'],
                'default' => 'pendiente'
            ],
            'fecha_inicio' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'fecha_fin' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('resultado_id', true);
        $this->forge->addForeignKey('conductor_id', 'conductores', 'conductor_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('resultados');
    }

    public function down()
    {
        $this->forge->dropTable('resultados');
    }
} 