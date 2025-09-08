<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateExamenesTable extends Migration
{
    public function up()
    {
        // Agregar campos faltantes a la tabla examenes
        $fields = [
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'titulo'
            ],
            'duracion_minutos' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'tiempo_limite'
            ],
            'fecha_inicio' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'puntaje_minimo'
            ],
            'fecha_fin' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'fecha_inicio'
            ],
            'numero_preguntas' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'fecha_fin'
            ],
            'dificultad' => [
                'type' => 'ENUM',
                'constraint' => ['facil', 'medio', 'dificil'],
                'default' => 'medio',
                'after' => 'numero_preguntas'
            ]
        ];

        foreach ($fields as $fieldName => $fieldDefinition) {
            $this->forge->addColumn('examenes', [$fieldName => $fieldDefinition]);
        }

        // Crear tabla escuelas
        $this->forge->addField([
            'escuela_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'direccion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ciudad' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'telefono' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['activo', 'inactivo'],
                'default' => 'activo',
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
        $this->forge->addKey('escuela_id', true);
        $this->forge->createTable('escuelas');
    }

    public function down()
    {
        // Revertir cambios
        $fieldsToRemove = ['nombre', 'duracion_minutos', 'fecha_inicio', 'fecha_fin', 'numero_preguntas', 'dificultad'];
        
        foreach ($fieldsToRemove as $fieldName) {
            $this->forge->dropColumn('examenes', $fieldName);
        }

        // Eliminar tabla escuelas
        $this->forge->dropTable('escuelas', true);
    }
}
