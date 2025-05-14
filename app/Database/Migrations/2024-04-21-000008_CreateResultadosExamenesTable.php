<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResultadosExamenesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'resultado_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'conductor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'examen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'puntaje_total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'preguntas_correctas' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'preguntas_incorrectas' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'tiempo_empleado' => [
                'type' => 'INT',
                'constraint' => 11,
                'comment' => 'Tiempo en segundos',
            ],
            'fecha_realizacion' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['aprobado', 'reprobado'],
                'default' => 'reprobado',
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

        $this->forge->addKey('resultado_id', true);
        $this->forge->addForeignKey('conductor_id', 'conductores', 'conductor_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('resultados_examenes');
    }

    public function down()
    {
        $this->forge->dropTable('resultados_examenes');
    }
} 