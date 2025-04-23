<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamenesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'examen_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'escuela_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => false,
            ],
            'categoria_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'      => false,
            ],
            'descripcion' => [
                'type'       => 'TEXT',
                'null'      => true,
            ],
            'fecha_inicio' => [
                'type'       => 'DATETIME',
                'null'      => false,
            ],
            'fecha_fin' => [
                'type'       => 'DATETIME',
                'null'      => false,
            ],
            'duracion_minutos' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'      => false,
            ],
            'puntaje_minimo' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'      => false,
            ],
            'numero_preguntas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'      => false,
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

        $this->forge->addKey('examen_id', true);
        $this->forge->addKey(['escuela_id', 'categoria_id']);
        
        $this->forge->addForeignKey('escuela_id', 'escuelas', 'escuela_id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('examenes');
    }

    public function down()
    {
        $this->forge->dropTable('examenes');
    }
} 