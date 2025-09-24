<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriaExamenTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'categoria_examen_id' => [
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
            'examen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('categoria_examen_id');
        $this->forge->addForeignKey('categoria_id', 'categorias', 'categoria_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('examen_id', 'examenes', 'examen_id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['categoria_id', 'examen_id', 'deleted_at'], 'unique_categoria_examen');
        $this->forge->createTable('categoria_examen');
    }

    public function down()
    {
        $this->forge->dropTable('categoria_examen');
    }
}
