<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InsertTestUsers extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'db:insert-test-users';
    protected $description = 'Inserta usuarios de prueba en la base de datos';

    public function run(array $params)
    {
        CLI::write('👥 Insertando usuarios de prueba...', 'yellow');
        
        try {
            $db = \Config\Database::connect();
            
            // Datos de usuarios de prueba
            $usuarios = [
                [
                    'dni' => '12345678',
                    'nombre' => 'Juan',
                    'apellido' => 'Pérez',
                    'email' => 'juan.perez@ejemplo.com',
                    'password' => password_hash('conductor123', PASSWORD_DEFAULT),
                    'estado' => 'activo',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'dni' => '87654321',
                    'nombre' => 'María',
                    'apellido' => 'González',
                    'email' => 'maria.gonzalez@ejemplo.com',
                    'password' => password_hash('conductor123', PASSWORD_DEFAULT),
                    'estado' => 'activo',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'dni' => '11223344',
                    'nombre' => 'Carlos',
                    'apellido' => 'López',
                    'email' => 'carlos.lopez@ejemplo.com',
                    'password' => password_hash('conductor123', PASSWORD_DEFAULT),
                    'estado' => 'activo',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'dni' => '55667788',
                    'nombre' => 'Ana',
                    'apellido' => 'Martínez',
                    'email' => 'ana.martinez@ejemplo.com',
                    'password' => password_hash('conductor123', PASSWORD_DEFAULT),
                    'estado' => 'activo',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'dni' => '99887766',
                    'nombre' => 'Luis',
                    'apellido' => 'Rodríguez',
                    'email' => 'luis.rodriguez@ejemplo.com',
                    'password' => password_hash('conductor123', PASSWORD_DEFAULT),
                    'estado' => 'activo',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            $insertados = 0;
            $existentes = 0;
            
            foreach ($usuarios as $usuario) {
                // Verificar si el usuario ya existe
                $existe = $db->table('usuarios')
                    ->where('dni', $usuario['dni'])
                    ->orWhere('email', $usuario['email'])
                    ->countAllResults();
                
                if ($existe > 0) {
                    $existentes++;
                    CLI::write("  ⚠️ Usuario ya existe: {$usuario['nombre']} {$usuario['apellido']} ({$usuario['email']})", 'yellow');
                } else {
                    if ($db->table('usuarios')->insert($usuario)) {
                        $insertados++;
                        CLI::write("  ✅ Usuario insertado: {$usuario['nombre']} {$usuario['apellido']} ({$usuario['email']})", 'green');
                    } else {
                        CLI::write("  ❌ Error al insertar: {$usuario['nombre']} {$usuario['apellido']}", 'red');
                    }
                }
            }
            
            CLI::write("\n📊 Resumen:", 'blue');
            CLI::write("  - Usuarios insertados: {$insertados}", 'green');
            CLI::write("  - Usuarios existentes: {$existentes}", 'yellow');
            CLI::write("  - Total procesados: " . count($usuarios), 'blue');
            
            // Verificar el total de usuarios en la BD
            $totalUsuarios = $db->table('usuarios')->countAllResults();
            CLI::write("  - Total usuarios en BD: {$totalUsuarios}", 'blue');
            
            CLI::write("\n🔐 Credenciales de prueba:", 'blue');
            CLI::write("  - Email: juan.perez@ejemplo.com");
            CLI::write("  - Contraseña: conductor123");
            CLI::write("  - (Usar las mismas credenciales para todos los usuarios)");
            
        } catch (\Exception $e) {
            CLI::write("❌ Error: " . $e->getMessage(), 'red');
        }
    }
}
