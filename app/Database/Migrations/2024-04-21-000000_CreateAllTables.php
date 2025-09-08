<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAllTables extends Migration
{
    public function up()
    {
        // Tabla roles
        if (!$this->db->tableExists('roles')) {
            $this->forge->addField([
                'rol_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'nombre' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'descripcion' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
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
            $this->forge->addKey('rol_id', true);
            $this->forge->createTable('roles');
        }

        // Tabla usuarios
        if (!$this->db->tableExists('usuarios')) {
            $this->forge->addField([
                'usuario_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'rol_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'dni' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'unique' => true,
                ],
                'nombre' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'apellido' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'email' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'unique' => true,
                ],
                'password' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
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
            $this->forge->addKey('usuario_id', true);
            $this->forge->addForeignKey('rol_id', 'roles', 'rol_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('usuarios');
        }

        // Tabla perfiles
        if (!$this->db->tableExists('perfiles')) {
            $this->forge->addField([
                'perfil_id' => [
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
                'nombre' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'apellido' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'telefono' => [
                    'type' => 'VARCHAR',
                    'constraint' => 15,
                    'null' => true,
                ],
                'direccion' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                ],
                'fecha_nacimiento' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'genero' => [
                    'type' => 'ENUM',
                    'constraint' => ['M', 'F', 'O'],
                    'null' => true,
                ],
                'foto' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
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
            $this->forge->addKey('perfil_id', true);
            $this->forge->addForeignKey('usuario_id', 'usuarios', 'usuario_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('perfiles');
        }

        // Tabla escuelas
        if (!$this->db->tableExists('escuelas')) {
            $this->forge->addField([
                'escuela_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'nombre' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'direccion' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ],
                'telefono' => [
                    'type' => 'VARCHAR',
                    'constraint' => 15,
                ],
                'email' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
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

        // Tabla categorias
        if (!$this->db->tableExists('categorias')) {
            $this->forge->addField([
                'categoria_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'nombre' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'descripcion' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
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
            $this->forge->addKey('categoria_id', true);
            $this->forge->createTable('categorias');
        }

        // Tabla supervisores
        if (!$this->db->tableExists('supervisores')) {
            $this->forge->addField([
                'supervisor_id' => [
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
                'escuela_id' => [
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
            $this->forge->addKey('supervisor_id', true);
            $this->forge->addForeignKey('usuario_id', 'usuarios', 'usuario_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('escuela_id', 'escuelas', 'escuela_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('supervisores');
        }

        // Tabla examenes
        if (!$this->db->tableExists('examenes')) {
            $this->forge->addField([
                'examen_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'supervisor_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'titulo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'descripcion' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'tiempo_limite' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'comment' => 'Tiempo en minutos',
                ],
                'puntaje_minimo' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
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
            $this->forge->addKey('examen_id', true);
            $this->forge->addForeignKey('supervisor_id', 'supervisores', 'supervisor_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('examenes');
        }

        // Tabla examen_categoria
        if (!$this->db->tableExists('examen_categoria')) {
            $this->forge->addField([
                'examen_categoria_id' => [
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
                'categoria_id' => [
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
            $this->forge->addKey('examen_categoria_id', true);
            $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('categoria_id', 'categorias', 'categoria_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('examen_categoria');
        }

        // Tabla preguntas
        if (!$this->db->tableExists('preguntas')) {
            $this->forge->addField([
                'pregunta_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'categoria_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'pregunta' => [
                    'type' => 'TEXT',
                ],
                'tipo_pregunta' => [
                    'type' => 'ENUM',
                    'constraint' => ['multiple', 'unica', 'verdadero_falso'],
                ],
                'dificultad' => [
                    'type' => 'ENUM',
                    'constraint' => ['baja', 'media', 'alta'],
                ],
                'puntaje' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
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
            $this->forge->addKey('pregunta_id', true);
            $this->forge->addForeignKey('categoria_id', 'categorias', 'categoria_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('preguntas');
        }

        // Tabla examen_pregunta
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

        // Tabla conductores
        if (!$this->db->tableExists('conductores')) {
            $this->forge->addField([
                'conductor_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'licencia' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ],
                'fecha_vencimiento' => [
                    'type' => 'DATE',
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
            $this->forge->addKey('conductor_id', true);
            $this->forge->createTable('conductores');
        }

        // Tabla respuestas
        if (!$this->db->tableExists('respuestas')) {
            $this->forge->addField([
                'respuesta_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'pregunta_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'respuesta' => [
                    'type' => 'TEXT',
                ],
                'es_correcta' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
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
            $this->forge->addKey('respuesta_id', true);
            $this->forge->addForeignKey('pregunta_id', 'preguntas', 'pregunta_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('respuestas');
        }

        // Tabla estados_examen
        if (!$this->db->tableExists('estados_examen')) {
            $this->forge->addField([
                'estado_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'nombre' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'descripcion' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
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
            $this->forge->addKey('estado_id', true);
            $this->forge->createTable('estados_examen');
        }

        // Tabla examen_conductor
        if (!$this->db->tableExists('examen_conductor')) {
            $this->forge->addField([
                'examen_conductor_id' => [
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
                'conductor_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'estado_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'puntaje_total' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                ],
                'intentos_realizados' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                ],
                'ultimo_intento' => [
                    'type' => 'DATETIME',
                    'null' => true,
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
            $this->forge->addKey('examen_conductor_id', true);
            $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('conductor_id', 'conductores', 'conductor_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('estado_id', 'estados_examen', 'estado_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('examen_conductor');
        }

        // Tabla sesiones_examen
        if (!$this->db->tableExists('sesiones_examen')) {
            $this->forge->addField([
                'sesion_id' => [
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
                'fecha_inicio' => [
                    'type' => 'DATETIME',
                ],
                'fecha_fin' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'tiempo_total' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'comment' => 'Tiempo en segundos',
                ],
                'ip_address' => [
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                    'null' => true,
                ],
                'user_agent' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
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
            $this->forge->addKey('sesion_id', true);
            $this->forge->addForeignKey('examen_conductor_id', 'examen_conductor', 'examen_conductor_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('sesiones_examen');
        }

        // Tabla respuestas_conductor
        if (!$this->db->tableExists('respuestas_conductor')) {
            $this->forge->addField([
                'respuesta_conductor_id' => [
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
                'pregunta_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'respuesta_id' => [
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
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('respuesta_conductor_id', true);
            $this->forge->addForeignKey('examen_conductor_id', 'examen_conductor', 'examen_conductor_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('pregunta_id', 'preguntas', 'pregunta_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('respuesta_id', 'respuestas', 'respuesta_id', 'SET NULL', 'CASCADE');
            $this->forge->createTable('respuestas_conductor');
        }

        // Tabla categorias_aprobadas
        if (!$this->db->tableExists('categorias_aprobadas')) {
            $this->forge->addField([
                'categoria_aprobada_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'conductor_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'categoria_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'examen_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'estado' => [
                    'type' => 'ENUM',
                    'constraint' => ['aprobado', 'reprobado'],
                ],
                'puntaje_obtenido' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                ],
                'fecha_aprobacion' => [
                    'type' => 'DATETIME',
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
            $this->forge->addKey('categoria_aprobada_id', true);
            $this->forge->addForeignKey('conductor_id', 'conductores', 'conductor_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('categoria_id', 'categorias', 'categoria_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('categorias_aprobadas');
        }
    }

    public function down()
    {
        // Eliminar tablas en orden inverso para respetar las claves foráneas
        $this->forge->dropTable('categorias_aprobadas', true);
        $this->forge->dropTable('respuestas_conductor', true);
        $this->forge->dropTable('sesiones_examen', true);
        $this->forge->dropTable('examen_conductor', true);
        $this->forge->dropTable('estados_examen', true);
        $this->forge->dropTable('respuestas', true);
        $this->forge->dropTable('conductores', true);
        $this->forge->dropTable('examen_pregunta', true);
        $this->forge->dropTable('preguntas', true);
        $this->forge->dropTable('examen_categoria', true);
        $this->forge->dropTable('examenes', true);
        $this->forge->dropTable('supervisores', true);
        $this->forge->dropTable('categorias', true);
        $this->forge->dropTable('escuelas', true);
        $this->forge->dropTable('perfiles', true);
        $this->forge->dropTable('usuarios', true);
        $this->forge->dropTable('roles', true);
    }
} 