<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveRolIdFromUsuarios extends Migration
{
    public function up()
    {
        // Eliminar foreign key constraint primero
        if ($this->db->fieldExists('rol_id', 'usuarios')) {
            $this->forge->dropForeignKey('usuarios', 'usuarios_rol_id_foreign');
        }
        
        // Eliminar columna rol_id de la tabla usuarios
        if ($this->db->fieldExists('rol_id', 'usuarios')) {
            $this->forge->dropColumn('usuarios', 'rol_id');
        }
    }

    public function down()
    {
        // Restaurar columna rol_id
        if (!$this->db->fieldExists('rol_id', 'usuarios')) {
            $fields = [
                'rol_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true, // Permitir null temporalmente
                ],
            ];
            
            $this->forge->addColumn('usuarios', $fields);
            $this->forge->addForeignKey('rol_id', 'roles', 'rol_id', 'CASCADE', 'CASCADE');
        }
    }
}
