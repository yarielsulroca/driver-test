<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaConductorEscuela extends Migration
{
    public function up()
    {
        // Verificar si la tabla ya existe
        if ($this->db->tableExists('conductor_escuela')) {
            echo "La tabla conductor_escuela ya existe. Saltando migración.\n";
            return;
        }

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'conductor_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'escuela_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('conductor_id', 'conductores', 'conductor_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('escuela_id', 'escuelas', 'escuela_id', 'CASCADE', 'CASCADE');
        
        // Agregar índice único para evitar duplicados
        $this->forge->addUniqueKey(['conductor_id', 'escuela_id']);
        
        $this->forge->createTable('conductor_escuela');
        
        echo "Tabla conductor_escuela creada exitosamente.\n";
    }

    public function down()
    {
        if ($this->db->tableExists('conductor_escuela')) {
            $this->forge->dropTable('conductor_escuela');
            echo "Tabla conductor_escuela eliminada.\n";
        }
    }
} 