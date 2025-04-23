<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConductoresTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'conductor_id' => [
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
            'apellido' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'      => false,
            ],
            'dni' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'      => false,
            ],
            'fecha_nacimiento' => [
                'type'       => 'DATE',
                'null'      => false,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'      => false,
            ],
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'      => true,
            ],
            'direccion' => [
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

        $this->forge->addKey('conductor_id', true);
        $this->forge->addUniqueKey('dni');
        $this->forge->addUniqueKey('email');
        
        $this->forge->createTable('conductores');
    }

    public function down()
    {
        $this->forge->dropTable('conductores');
    }
} 