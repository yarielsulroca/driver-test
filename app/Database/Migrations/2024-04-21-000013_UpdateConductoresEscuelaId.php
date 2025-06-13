<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateConductoresEscuelaId extends Migration
{
    public function up()
    {
        // Primero eliminamos la clave foránea existente
        $this->forge->dropForeignKey('conductores', 'conductores_escuela_id_foreign');

        // Modificamos la columna escuela_id para permitir NULL
        $this->forge->modifyColumn('conductores', [
            'escuela_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ]
        ]);

        // Agregamos la nueva clave foránea que permite NULL
        $this->forge->addForeignKey('escuela_id', 'escuelas', 'escuela_id', 'CASCADE', 'SET NULL', 'conductores_escuela_id_foreign');
    }

    public function down()
    {
        // Eliminamos la clave foránea
        $this->forge->dropForeignKey('conductores', 'conductores_escuela_id_foreign');

        // Modificamos la columna escuela_id para no permitir NULL
        $this->forge->modifyColumn('conductores', [
            'escuela_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ]
        ]);

        // Restauramos la clave foránea original
        $this->forge->addForeignKey('escuela_id', 'escuelas', 'escuela_id', 'CASCADE', 'CASCADE', 'conductores_escuela_id_foreign');
    }
} 