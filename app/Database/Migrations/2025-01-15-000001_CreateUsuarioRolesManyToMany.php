<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsuarioRolesManyToMany extends Migration
{
    public function up()
    {
        // Crear tabla pivot usuario_roles
        if (!$this->db->tableExists('usuario_roles')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'usuario_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'rol_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('usuario_id', 'usuarios', 'usuario_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('rol_id', 'roles', 'rol_id', 'CASCADE', 'CASCADE');
            $this->forge->addUniqueKey(['usuario_id', 'rol_id']);
            $this->forge->createTable('usuario_roles');
        }
    }

    public function down()
    {
        // Eliminar tabla pivot
        if ($this->db->tableExists('usuario_roles')) {
            $this->forge->dropTable('usuario_roles');
        }
    }
}
