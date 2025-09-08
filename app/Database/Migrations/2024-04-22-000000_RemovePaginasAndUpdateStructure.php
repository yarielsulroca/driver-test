<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemovePaginasAndUpdateStructure extends Migration
{
    public function up()
    {
        // Eliminar tablas relacionadas con páginas
        if ($this->db->tableExists('pagina_conductor')) {
            $this->forge->dropTable('pagina_conductor', true);
        }
        
        if ($this->db->tableExists('paginas')) {
            $this->forge->dropTable('paginas', true);
        }

        // Actualizar la tabla preguntas para incluir examen_id
        if ($this->db->tableExists('preguntas')) {
            // Agregar campo examen_id a la tabla preguntas
            $this->forge->addColumn('preguntas', [
                'examen_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'pregunta_id'
                ]
            ]);

            // Agregar clave foránea
            $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE', 'preguntas');
        }

        // Eliminar la tabla examen_pregunta ya que ahora las preguntas tienen examen_id directamente
        if ($this->db->tableExists('examen_pregunta')) {
            $this->forge->dropTable('examen_pregunta', true);
        }

        // Actualizar la tabla respuestas_conductor para usar pregunta_id directamente
        if ($this->db->tableExists('respuestas_conductor')) {
            // La tabla ya tiene pregunta_id, así que está bien
        }
    }

    public function down()
    {
        // Recrear tabla examen_pregunta
        if (!$this->db->tableExists('examen_pregunta')) {
            $this->forge->addField([
                'examen_pregunta_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'examen_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'pregunta_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'orden' => [
                    'type' => 'INT',
                    'constraint' => 11,
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
            $this->forge->addKey('examen_pregunta_id', true);
            $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('pregunta_id', 'preguntas', 'pregunta_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('examen_pregunta');
        }

        // Recrear tabla paginas
        if (!$this->db->tableExists('paginas')) {
            $this->forge->addField([
                'pagina_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'examen_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'orden' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
                'preguntas' => [
                    'type' => 'JSON',
                    'null' => false,
                    'comment' => 'Array de IDs de preguntas'
                ],
                'respuesta_correcta' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'comment' => 'ID de la respuesta correcta'
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
            $this->forge->addKey('pagina_id', true);
            $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('paginas');
        }

        // Recrear tabla pagina_conductor
        if (!$this->db->tableExists('pagina_conductor')) {
            $this->forge->addField([
                'pagina_conductor_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'examen_conductor_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'pagina_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'respuesta_seleccionada' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                ],
                'puntaje_obtenido' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                ],
                'tiempo_respuesta' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'comment' => 'Tiempo en segundos'
                ],
                'completada' => [
                    'type' => 'BOOLEAN',
                    'default' => false,
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
            $this->forge->addKey('pagina_conductor_id', true);
            $this->forge->addForeignKey('examen_conductor_id', 'examen_conductor', 'examen_conductor_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('pagina_id', 'paginas', 'pagina_id', 'CASCADE', 'CASCADE');
            $this->forge->addUniqueKey(['examen_conductor_id', 'pagina_id']);
            $this->forge->createTable('pagina_conductor');
        }

        // Remover campo examen_id de preguntas
        if ($this->db->tableExists('preguntas')) {
            $this->forge->dropForeignKey('preguntas', 'preguntas_examen_id_fkey');
            $this->forge->dropColumn('preguntas', 'examen_id');
        }
    }
} 