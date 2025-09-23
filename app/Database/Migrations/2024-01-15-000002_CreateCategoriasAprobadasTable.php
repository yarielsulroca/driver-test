<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriasAprobadasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'categoria_aprobada_id' => [
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
                'constraint' => ['aprobado'],
                'default' => 'aprobado',
            ],
            'fecha_aprobacion' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'puntaje_obtenido' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => false,
            ],
            'fecha_vencimiento' => [
                'type' => 'DATETIME',
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

        $this->forge->addKey('categoria_aprobada_id', true);
        $this->forge->addForeignKey('conductor_id', 'conductores', 'conductor_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('categoria_id', 'categorias', 'categoria_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('categorias_aprobadas');
    }

    public function down()
    {
        $this->forge->dropTable('categorias_aprobadas');
    }
}
