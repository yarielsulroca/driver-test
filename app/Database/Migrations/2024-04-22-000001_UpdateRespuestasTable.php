<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateRespuestasTable extends Migration
{
    public function up()
    {
        // Actualizar tabla respuestas para incluir campo imagen
        if ($this->db->tableExists('respuestas')) {
            // Agregar campo imagen
            $this->forge->addColumn('respuestas', [
                'imagen' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'texto'
                ]
            ]);

            // Eliminar campo valor si existe
            if ($this->db->fieldExists('valor', 'respuestas')) {
                $this->forge->dropColumn('respuestas', 'valor');
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('respuestas')) {
            // Eliminar campo imagen
            $this->forge->dropColumn('respuestas', 'imagen');

            // Recrear campo valor
            $this->forge->addColumn('respuestas', [
                'valor' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                    'after' => 'texto'
                ]
            ]);
        }
    }
} 