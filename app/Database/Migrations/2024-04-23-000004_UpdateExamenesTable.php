<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateExamenesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('examenes', [
            'categoria_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'escuela_id'
            ],
            'numero_preguntas' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'puntaje_minimo'
            ]
        ]);

        // Agregar la llave foránea para categoria_id
        $this->forge->addForeignKey('categoria_id', 'categorias', 'categoria_id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        // Eliminar la llave foránea primero
        $this->forge->dropForeignKey('examenes', 'examenes_categoria_id_foreign');
        
        // Eliminar las columnas
        $this->forge->dropColumn('examenes', ['categoria_id', 'numero_preguntas']);
    }
} 