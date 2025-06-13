<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaginaConductorTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pagina_conductor_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'examen_conductor_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => false,
            ],
            'pagina_id' => [
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
                'comment'       => 'Orden aleatorio asignado a esta página para este conductor'
            ],
            'estado' => [
                'type'           => 'ENUM',
                'constraint'     => ['pendiente', 'vista', 'completada'],
                'default'        => 'pendiente',
                'null'          => false,
            ],
            'fecha_vista' => [
                'type'           => 'DATETIME',
                'null'          => true,
            ],
            'respuesta_seleccionada' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'          => true,
                'comment'       => 'ID de la respuesta seleccionada por el conductor'
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

        $this->forge->addKey('pagina_conductor_id', true);
        $this->forge->addForeignKey('examen_conductor_id', 'examen_conductor', 'examen_conductor_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pagina_id', 'paginas', 'pagina_id', 'CASCADE', 'CASCADE');
        
        // Índice único para evitar duplicados
        $this->forge->addUniqueKey(['examen_conductor_id', 'pagina_id']);
        
        $this->forge->createTable('pagina_conductor');
    }

    public function down()
    {
        $this->forge->dropTable('pagina_conductor');
    }
} 