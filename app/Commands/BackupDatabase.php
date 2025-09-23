<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BackupDatabase extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'db:backup';
    protected $description = 'Crea un respaldo de la base de datos antes de las migraciones';

    public function run(array $params)
    {
        CLI::write('💾 Creando respaldo de la base de datos...', 'yellow');
        
        $config = config('Database');
        $dbConfig = $config->default;
        
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        $hostname = $dbConfig['hostname'];
        
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = FCPATH . "../backups/backup_{$database}_{$timestamp}.sql";
        
        // Crear directorio de backups si no existe
        $backupDir = dirname($backupFile);
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        // Comando mysqldump
        $command = "mysqldump -h {$hostname} -u {$username}";
        if (!empty($password)) {
            $command .= " -p{$password}";
        }
        $command .= " {$database} > \"{$backupFile}\"";
        
        CLI::write("Ejecutando: {$command}", 'blue');
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            CLI::write("✅ Respaldo creado exitosamente: {$backupFile}", 'green');
            CLI::write("📁 Tamaño del archivo: " . $this->formatBytes(filesize($backupFile)), 'blue');
            return $backupFile;
        } else {
            CLI::write("❌ Error al crear respaldo: " . implode("\n", $output), 'red');
            return false;
        }
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }
}
