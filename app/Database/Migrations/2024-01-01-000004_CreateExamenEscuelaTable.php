<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamenEscuelaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'examen_escuela_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'examen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'escuela_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['activo', 'inactivo'],
                'default' => 'activo',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('examen_escuela_id', true);
        $this->forge->addKey(['examen_id', 'escuela_id'], false, true); // Clave única compuesta
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('escuela_id', 'escuelas', 'escuela_id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('examen_escuela');
    }

    public function down()
    {
        $this->forge->dropTable('examen_escuela');
    }
}
