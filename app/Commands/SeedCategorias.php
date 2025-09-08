<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;
use Config\Database;

class SeedCategorias extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'seed:categorias';
    protected $description = 'Ejecuta el seeder de categorías de licencias de conducción';

    public function run(array $params)
    {
        CLI::write('🌱 Ejecutando seeder de categorías...', 'green');
        
        try {
            $seeder = new Seeder(new Database());
            $seeder->call('CategoriasSeeder');
            
            CLI::write('✅ Seeder ejecutado exitosamente!', 'green');
            CLI::write('📊 Las categorías han sido insertadas en la base de datos.', 'blue');
            
        } catch (\Exception $e) {
            CLI::error('❌ Error al ejecutar el seeder: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
