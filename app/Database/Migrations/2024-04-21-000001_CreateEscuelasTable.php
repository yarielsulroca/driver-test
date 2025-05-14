<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEscuelasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'escuela_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'      => false,
            ],
            'direccion' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'      => false,
            ],
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'      => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'      => false,
            ],
            'sitio_web' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'      => true,
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

        $this->forge->addKey('escuela_id', true);
        $this->forge->addUniqueKey('email');
        
        $this->forge->createTable('escuelas');
    }

    public function down()
    {
        $this->forge->dropTable('escuelas');
    }
} 