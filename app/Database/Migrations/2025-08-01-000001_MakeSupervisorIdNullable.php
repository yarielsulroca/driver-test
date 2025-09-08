<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeSupervisorIdNullable extends Migration
{
    public function up()
    {
        // Hacer supervisor_id nullable temporalmente
        $this->forge->modifyColumn('examenes', [
            'supervisor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ]
        ]);
    }

    public function down()
    {
        // Revertir el cambio
        $this->forge->modifyColumn('examenes', [
            'supervisor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ]
        ]);
    }
} 