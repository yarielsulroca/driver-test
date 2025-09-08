<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveSupervisorConstraint extends Migration
{
    public function up()
    {
        // Eliminar la restricción de clave foránea
        $this->db->query('ALTER TABLE examenes DROP FOREIGN KEY examenes_supervisor_id_foreign');
        
        // Hacer supervisor_id nullable
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
        
        // Recrear la restricción de clave foránea
        $this->db->query('ALTER TABLE examenes ADD CONSTRAINT examenes_supervisor_id_foreign FOREIGN KEY (supervisor_id) REFERENCES supervisores(supervisor_id) ON DELETE CASCADE ON UPDATE CASCADE');
    }
} 