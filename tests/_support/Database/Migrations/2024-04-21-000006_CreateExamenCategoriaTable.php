<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamenCategoriaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'examen_categoria_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'examen_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => false,
            ],
            'categoria_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => false,
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

        $this->forge->addKey('examen_categoria_id', true);
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('categoria_id', 'categorias', 'categoria_id', 'CASCADE', 'CASCADE');
        
        // Índice único para evitar duplicados
        $this->forge->addUniqueKey(['examen_id', 'categoria_id']);
        
        $this->forge->createTable('examen_categoria');
    }

    public function down()
    {
        $this->forge->dropTable('examen_categoria');
    }
} 