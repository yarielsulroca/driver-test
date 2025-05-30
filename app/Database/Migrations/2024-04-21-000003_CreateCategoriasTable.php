<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'categoria_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'codigo' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'unique' => true
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'requisitos' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('categoria_id', true);
        $this->forge->createTable('categorias');

        // Insertar las categorías internacionales
        $categorias = [
            [
                'codigo' => 'A1',
                'nombre' => 'Motocicletas hasta 50cc',
                'descripcion' => 'Licencia para conducir motocicletas con cilindrada hasta 50cc',
                'requisitos' => json_encode([
                    'Edad mínima: 16 años',
                    'Examen teórico y práctico específico'
                ])
            ],
            [
                'codigo' => 'A',
                'nombre' => 'Motocicletas',
                'descripcion' => 'Licencia para conducir motocicletas de cualquier cilindrada',
                'requisitos' => json_encode([
                    'Edad mínima: 18 años',
                    'Examen teórico y práctico específico'
                ])
            ],
            [
                'codigo' => 'B',
                'nombre' => 'Automóviles',
                'descripcion' => 'Licencia para conducir vehículos de hasta 3.500 kg',
                'requisitos' => json_encode([
                    'Edad mínima: 18 años',
                    'Examen teórico y práctico'
                ])
            ],
            [
                'codigo' => 'C',
                'nombre' => 'Camiones',
                'descripcion' => 'Licencia para conducir vehículos de más de 3.500 kg',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Tener licencia B',
                    'Examen teórico y práctico específico'
                ])
            ],
            [
                'codigo' => 'D',
                'nombre' => 'Autobuses',
                'descripcion' => 'Licencia para conducir vehículos de transporte de pasajeros',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Tener licencia B',
                    'Examen teórico y práctico específico'
                ])
            ],
            [
                'codigo' => 'E',
                'nombre' => 'Remolques',
                'descripcion' => 'Licencia para conducir vehículos con remolque pesado',
                'requisitos' => json_encode([
                    'Tener licencia B, C o D',
                    'Examen teórico y práctico específico'
                ])
            ]
        ];

        $this->db->table('categorias')->insertBatch($categorias);
    }

    public function down()
    {
        $this->forge->dropTable('categorias');
    }
} 