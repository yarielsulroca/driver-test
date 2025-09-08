<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePreguntasTable extends Migration
{
    public function up()
    {
        // Agregar columnas faltantes
        $this->forge->addColumn('preguntas', [
            'examen_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => true,
                'after'         => 'pregunta_id'
            ],
            'es_critica' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'     => 'puntaje'
            ]
        ]);

        // Renombrar columna 'pregunta' a 'enunciado'
        $this->db->query("ALTER TABLE preguntas CHANGE pregunta enunciado TEXT NOT NULL");

        // Actualizar valores de dificultad
        $this->db->query("UPDATE preguntas SET dificultad = 'medio' WHERE dificultad = 'media'");
        $this->db->query("UPDATE preguntas SET dificultad = 'facil' WHERE dificultad = 'baja'");
        $this->db->query("UPDATE preguntas SET dificultad = 'dificil' WHERE dificultad = 'alta'");

        // Modificar el tipo de columna dificultad
        $this->db->query("ALTER TABLE preguntas MODIFY dificultad ENUM('facil','medio','dificil') NOT NULL");

        // Agregar foreign keys
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        // Revertir cambios
        $this->forge->dropForeignKey('preguntas', 'preguntas_examen_id_foreign');
        
        $this->forge->dropColumn('preguntas', ['examen_id', 'es_critica']);
        
        $this->db->query("ALTER TABLE preguntas CHANGE enunciado pregunta TEXT NOT NULL");
        
        $this->db->query("UPDATE preguntas SET dificultad = 'media' WHERE dificultad = 'medio'");
        $this->db->query("UPDATE preguntas SET dificultad = 'baja' WHERE dificultad = 'facil'");
        $this->db->query("UPDATE preguntas SET dificultad = 'alta' WHERE dificultad = 'dificil'");
        
        $this->db->query("ALTER TABLE preguntas MODIFY dificultad ENUM('baja','media','alta') NOT NULL");
    }
} 