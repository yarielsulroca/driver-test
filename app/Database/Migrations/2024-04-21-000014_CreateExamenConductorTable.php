<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamenConductorTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'examen_conductor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'examen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'conductor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['pendiente', 'en_progreso', 'completado', 'aprobado', 'reprobado'],
                'default' => 'pendiente',
            ],
            'fecha_inicio' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_fin' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'puntaje_obtenido' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'tiempo_utilizado' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Tiempo en segundos',
            ],
            'intentos_restantes' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 3,
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

        $this->forge->addKey('examen_conductor_id', true);
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('conductor_id', 'conductores', 'conductor_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('examen_conductor');
    }

    public function down()
    {
        $this->forge->dropTable('examen_conductor');
    }
} 