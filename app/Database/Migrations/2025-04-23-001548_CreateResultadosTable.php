<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResultadosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'resultado_id' => [
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
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'puntuacion' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'fecha_realizacion' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['aprobado', 'reprobado'],
                'default'    => 'reprobado',
            ],
            'fecha_bloqueo' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'bloqueado' => [
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
        ]);

        $this->forge->addKey('resultado_id', true);
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'usuario_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('resultados');
    }

    public function down()
    {
        $this->forge->dropTable('resultados');
    }
}
