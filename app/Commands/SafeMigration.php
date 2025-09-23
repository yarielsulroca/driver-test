<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SafeMigration extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'migrate:safe';
    protected $description = 'Ejecuta migraciones de forma segura preservando datos';

    public function run(array $params)
    {
        CLI::write('🛡️ Iniciando migración segura...', 'yellow');
        
        // Paso 1: Crear respaldo
        CLI::write("\n📦 Paso 1: Creando respaldo de seguridad...", 'blue');
        $backupCommand = new \App\Commands\BackupDatabase();
        $backupFile = $backupCommand->run($params);
        
        if (!$backupFile) {
            CLI::write("❌ No se pudo crear el respaldo. Abortando migración.", 'red');
            return;
        }
        
        // Paso 2: Verificar datos existentes
        CLI::write("\n🔍 Paso 2: Verificando datos existentes...", 'blue');
        $this->verifyExistingData();
        
        // Paso 3: Ejecutar migración de tabla pivot
        CLI::write("\n🔧 Paso 3: Creando tabla usuario_roles...", 'blue');
        $this->runMigration('2025-01-15-000001');
        
        // Paso 4: Migrar datos existentes
        CLI::write("\n📋 Paso 4: Migrando datos existentes...", 'blue');
        $this->migrateExistingData();
        
        // Paso 5: Eliminar columna rol_id
        CLI::write("\n🗑️ Paso 5: Eliminando columna rol_id...", 'blue');
        $this->runMigration('2025-01-15-000002');
        
        // Paso 6: Verificar migración
        CLI::write("\n✅ Paso 6: Verificando migración...", 'blue');
        $this->verifyMigration();
        
        CLI::write("\n🎉 ¡Migración completada exitosamente!", 'green');
        CLI::write("💾 Respaldo guardado en: {$backupFile}", 'blue');
    }
    
    private function verifyExistingData()
    {
        $db = \Config\Database::connect();
        
        // Verificar usuarios con roles
        $usuariosConRoles = $db->table('usuarios')
            ->where('rol_id IS NOT NULL')
            ->countAllResults();
            
        CLI::write("  📊 Usuarios con roles asignados: {$usuariosConRoles}");
        
        // Mostrar algunos ejemplos
        $ejemplos = $db->table('usuarios u')
            ->select('u.usuario_id, u.nombre, u.apellido, u.rol_id, r.nombre as rol_nombre')
            ->join('roles r', 'r.rol_id = u.rol_id', 'left')
            ->where('u.rol_id IS NOT NULL')
            ->limit(5)
            ->get()
            ->getResultArray();
            
        CLI::write("  📝 Ejemplos de datos a migrar:");
        foreach ($ejemplos as $ejemplo) {
            CLI::write("    - {$ejemplo['nombre']} {$ejemplo['apellido']} (Rol: {$ejemplo['rol_nombre']})");
        }
    }
    
    private function runMigration($migration)
    {
        $command = "php spark migrate -g default {$migration}";
        CLI::write("  🔄 Ejecutando: {$command}");
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            CLI::write("  ✅ Migración {$migration} ejecutada exitosamente", 'green');
        } else {
            CLI::write("  ❌ Error en migración {$migration}: " . implode("\n", $output), 'red');
            throw new \Exception("Error en migración {$migration}");
        }
    }
    
    private function migrateExistingData()
    {
        $command = "php spark db:seed MigrateUsuarioRolesSeeder";
        CLI::write("  🔄 Ejecutando: {$command}");
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            CLI::write("  ✅ Datos migrados exitosamente", 'green');
            foreach ($output as $line) {
                if (!empty(trim($line))) {
                    CLI::write("    📝 {$line}");
                }
            }
        } else {
            CLI::write("  ❌ Error al migrar datos: " . implode("\n", $output), 'red');
            throw new \Exception("Error al migrar datos existentes");
        }
    }
    
    private function verifyMigration()
    {
        $db = \Config\Database::connect();
        
        // Verificar que la tabla usuario_roles existe y tiene datos
        if (!$db->tableExists('usuario_roles')) {
            throw new \Exception("La tabla usuario_roles no existe");
        }
        
        $totalRelaciones = $db->table('usuario_roles')->countAllResults();
        CLI::write("  📊 Total de relaciones usuario-rol creadas: {$totalRelaciones}");
        
        // Verificar que la columna rol_id fue eliminada
        $fields = $db->getFieldData('usuarios');
        $tieneRolId = false;
        foreach ($fields as $field) {
            if ($field->name === 'rol_id') {
                $tieneRolId = true;
                break;
            }
        }
        
        if ($tieneRolId) {
            CLI::write("  ⚠️ La columna rol_id aún existe en la tabla usuarios", 'yellow');
        } else {
            CLI::write("  ✅ La columna rol_id fue eliminada correctamente", 'green');
        }
        
        // Mostrar algunos ejemplos de la nueva estructura
        $ejemplos = $db->table('usuario_roles ur')
            ->select('ur.usuario_id, u.nombre, u.apellido, r.nombre as rol_nombre')
            ->join('usuarios u', 'u.usuario_id = ur.usuario_id')
            ->join('roles r', 'r.rol_id = ur.rol_id')
            ->limit(5)
            ->get()
            ->getResultArray();
            
        CLI::write("  📝 Ejemplos de la nueva estructura:");
        foreach ($ejemplos as $ejemplo) {
            CLI::write("    - {$ejemplo['nombre']} {$ejemplo['apellido']} (Rol: {$ejemplo['rol_nombre']})");
        }
    }
}
