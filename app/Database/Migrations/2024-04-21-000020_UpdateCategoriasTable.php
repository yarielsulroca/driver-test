<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCategoriasTable extends Migration
{
    public function up()
    {
        // Agregar campo código si no existe
        if (!$this->db->fieldExists('codigo', 'categorias')) {
            $this->forge->addColumn('categorias', [
                'codigo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                    'null' => false,
                    'after' => 'categoria_id'
                ]
            ]);
        }

        // Agregar campo requisitos si no existe
        if (!$this->db->fieldExists('requisitos', 'categorias')) {
            $this->forge->addColumn('categorias', [
                'requisitos' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'descripcion'
                ]
            ]);
        }

        // Agregar campo estado si no existe
        if (!$this->db->fieldExists('estado', 'categorias')) {
            $this->forge->addColumn('categorias', [
                'estado' => [
                    'type' => 'ENUM',
                    'constraint' => ['activo', 'inactivo'],
                    'default' => 'activo',
                    'after' => 'requisitos'
                ]
            ]);
        }

        // Agregar índices únicos
        $this->db->query("ALTER TABLE categorias ADD UNIQUE KEY unique_codigo (codigo)");
        $this->db->query("ALTER TABLE categorias ADD UNIQUE KEY unique_nombre (nombre)");
    }

    public function down()
    {
        // Eliminar índices únicos
        $this->db->query("ALTER TABLE categorias DROP INDEX unique_codigo");
        $this->db->query("ALTER TABLE categorias DROP INDEX unique_nombre");

        // Eliminar campos agregados
        $this->forge->dropColumn('categorias', ['codigo', 'requisitos', 'estado']);
    }
} 