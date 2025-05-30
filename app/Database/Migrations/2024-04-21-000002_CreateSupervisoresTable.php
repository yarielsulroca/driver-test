<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupervisoresTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'supervisor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'usuario_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'apellido' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'unique' => true
            ],
            'telefono' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['activo', 'inactivo'],
                'default' => 'activo'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('supervisor_id', true);
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'usuario_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('supervisores');
    }

    public function down()
    {
        $this->forge->dropTable('supervisores');
    }
} 