<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamenConductorTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'examen_conductor_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'examen_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'conductor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'fecha_inicio' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_fin' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['pendiente', 'en_progreso', 'completado', 'cancelado'],
                'default'    => 'pendiente',
            ],
            'puntuacion' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'      => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('examen_conductor_id', true);
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('conductor_id', 'conductores', 'conductor_id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['examen_id', 'conductor_id']);
        
        $this->forge->createTable('examen_conductor');
    }

    public function down()
    {
        $this->forge->dropTable('examen_conductor');
    }
} 