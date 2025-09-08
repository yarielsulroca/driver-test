<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateConductoresTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('conductores', [
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'after' => 'conductor_id'
            ],
            'apellido' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'after' => 'nombre'
            ],
            'dni' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'after' => 'apellido',
                'unique' => true
            ],
            'fecha_nacimiento' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'dni'
            ],
            'direccion' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'fecha_nacimiento'
            ],
            'telefono' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'direccion'
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'telefono',
                'unique' => true
            ],
            'estado_registro' => [
                'type' => 'ENUM',
                'constraint' => ['pendiente', 'aprobado', 'rechazado'],
                'default' => 'pendiente',
                'after' => 'email'
            ],
            'fecha_registro' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'estado_registro'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('conductores', [
            'nombre',
            'apellido',
            'dni',
            'fecha_nacimiento',
            'direccion',
            'telefono',
            'email',
            'estado_registro',
            'fecha_registro'
        ]);
    }
} 