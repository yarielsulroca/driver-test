<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriasAsignadasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'categoria_asignada_id' => [
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
            'categoria_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'examen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['pendiente', 'iniciado', 'aprobado'],
                'default' => 'pendiente',
            ],
            'intentos_realizados' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 0,
            ],
            'intentos_maximos' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 3,
            ],
            'fecha_asignacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_ultimo_intento' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_aprobacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'puntaje_obtenido' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
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

        $this->forge->addKey('categoria_asignada_id', true);
        $this->forge->addForeignKey('conductor_id', 'conductores', 'conductor_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('categoria_id', 'categorias', 'categoria_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('categorias_asignadas');
    }

    public function down()
    {
        $this->forge->dropTable('categorias_asignadas');
    }
}
