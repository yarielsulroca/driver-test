<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateConductoresTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('conductores', [
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'after' => 'email'
            ],
            'categoria_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'password'
            ],
            'estado_registro' => [
                'type' => 'ENUM',
                'constraint' => ['pendiente', 'aprobado', 'rechazado'],
                'default' => 'pendiente',
                'after' => 'categoria_id'
            ],
            'fecha_registro' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'estado_registro'
            ]
        ]);

        // Agregar la llave foránea para categoria_id
        $this->forge->addForeignKey('categoria_id', 'categorias', 'categoria_id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        // Eliminar la llave foránea primero
        $this->forge->dropForeignKey('conductores', 'conductores_categoria_id_foreign');
        
        // Eliminar las columnas
        $this->forge->dropColumn('conductores', ['password', 'categoria_id', 'estado_registro', 'fecha_registro']);
    }
} 