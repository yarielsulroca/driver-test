<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCiudadToEscuelas extends Migration
{
    public function up()
    {
        // Verificar si la columna ciudad ya existe
        if ($this->db->fieldExists('ciudad', 'escuelas')) {
            echo "La columna 'ciudad' ya existe en la tabla 'escuelas'. Saltando migración.\n";
            return;
        }

        // Agregar la columna ciudad
        $this->forge->addColumn('escuelas', [
            'ciudad' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'direccion'
            ]
        ]);

        echo "Columna 'ciudad' agregada exitosamente a la tabla 'escuelas'.\n";
    }

    public function down()
    {
        // Verificar si la columna existe antes de eliminarla
        if ($this->db->fieldExists('ciudad', 'escuelas')) {
            $this->forge->dropColumn('escuelas', 'ciudad');
            echo "Columna 'ciudad' eliminada de la tabla 'escuelas'.\n";
        } else {
            echo "La columna 'ciudad' no existe en la tabla 'escuelas'.\n";
        }
    }
} 