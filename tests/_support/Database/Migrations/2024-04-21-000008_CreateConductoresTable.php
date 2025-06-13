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
            'dni' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'apellido' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'      => true,
            ],
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'      => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'      => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'categoria_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => true,
            ],
            'estado_registro' => [
                'type'       => 'ENUM',
                'constraint' => ['pendiente', 'aprobado', 'rechazado'],
                'default'    => 'pendiente',
            ],
            'fecha_registro' => [
                'type'       => 'DATETIME',
                'null'      => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'      => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'      => true,
            ],
        ]);

        $this->forge->addKey('conductor_id', true);
        $this->forge->addUniqueKey(['dni']);
        $this->forge->addUniqueKey(['email']);
        
        // Agregamos la clave foránea para categoria_id
        $this->forge->addForeignKey('categoria_id', 'categorias', 'categoria_id', 'CASCADE', 'SET NULL');
        
        $this->forge->createTable('conductores');
    }

    public function down()
    {
        $this->forge->dropTable('conductores', true);
    }
} 