<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AnalyzeDatabase extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'db:analyze';
    protected $description = 'Analiza la estructura de la base de datos y genera modelos y controladores';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('🔍 Analizando estructura de la base de datos...', 'yellow');
        
        // Obtener todas las tablas
        $tables = $db->listTables();
        
        CLI::write("\n📋 Tablas encontradas:", 'green');
        foreach ($tables as $table) {
            CLI::write("  - $table");
        }
        
        // Analizar cada tabla
        foreach ($tables as $table) {
            CLI::write("\n🔍 Analizando tabla: $table", 'blue');
            
            // Obtener estructura de la tabla
            $fields = $db->getFieldData($table);
            $indexes = $db->getIndexData($table);
            $foreignKeys = $db->getForeignKeyData($table);
            
            CLI::write("  Campos:");
            foreach ($fields as $field) {
                $type = $field->type;
                $null = $field->nullable ? 'NULL' : 'NOT NULL';
                $default = $field->default ? "DEFAULT '{$field->default}'" : '';
                $key = $field->primary_key ? 'PRIMARY KEY' : ($field->type === 'int' && strpos($field->name, '_id') !== false ? 'FOREIGN KEY' : '');
                
                CLI::write("    - {$field->name}: $type $null $default $key");
            }
            
            // Mostrar relaciones
            if (!empty($foreignKeys)) {
                CLI::write("  Relaciones:");
                foreach ($foreignKeys as $fk) {
                    CLI::write("    - {$fk->column_name} -> {$fk->foreign_table_name}.{$fk->foreign_column_name}");
                }
            }
            
            // Mostrar índices
            if (!empty($indexes)) {
                CLI::write("  Índices:");
                foreach ($indexes as $index) {
                    $type = $index->type === 'PRIMARY' ? 'PRIMARY KEY' : $index->type;
                    $fields = implode(', ', $index->fields);
                    CLI::write("    - $type ($fields)");
                }
            }
        }
        
        CLI::write("\n✅ Análisis completado!", 'green');
    }
}
