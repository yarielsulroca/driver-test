<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaginasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pagina_id' => [
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
            'orden' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'          => false,
                'default'       => 0,
            ],
            'preguntas' => [
                'type'           => 'JSON',
                'null'          => false,
                'comment'       => 'Array de IDs de preguntas'
            ],
            'respuesta_correcta' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'          => false,
                'comment'       => 'ID de la respuesta correcta'
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

        $this->forge->addKey('pagina_id', true);
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('paginas');
    }

    public function down()
    {
        $this->forge->dropTable('paginas');
    }
} 