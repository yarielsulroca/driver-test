<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MigrateUsuarioRolesSeeder extends Seeder
{
    public function run()
    {
        // NOTA: Este seeder debe ejecutarse DESPUÉS de crear la tabla usuario_roles
        // y ANTES de eliminar la columna rol_id de la tabla usuarios
        
        $this->db = \Config\Database::connect();
        
        try {
            // Verificar que la tabla usuario_roles existe
            if (!$this->db->tableExists('usuario_roles')) {
                echo "ERROR: La tabla usuario_roles no existe. Ejecuta primero la migración.\n";
                return;
            }

            // Verificar que la columna rol_id existe en usuarios
            if (!$this->db->fieldExists('rol_id', 'usuarios')) {
                echo "ADVERTENCIA: La columna rol_id ya no existe en usuarios. Los datos ya fueron migrados.\n";
                return;
            }

            echo "Iniciando migración de datos de usuario-roles...\n";

            // Obtener todos los usuarios que tienen rol_id
            $usuarios = $this->db->query("SELECT usuario_id, rol_id FROM usuarios WHERE rol_id IS NOT NULL")->getResultArray();

            $migrados = 0;
            $errores = 0;

            foreach ($usuarios as $usuario) {
                try {
                    // Verificar que el rol existe
                    $rol = $this->db->query("SELECT rol_id FROM roles WHERE rol_id = ?", [$usuario['rol_id']])->getRow();
                    
                    if (!$rol) {
                        echo "ADVERTENCIA: Rol ID {$usuario['rol_id']} no existe para usuario {$usuario['usuario_id']}\n";
                        $errores++;
                        continue;
                    }

                    // Verificar si ya existe la asignación
                    $existe = $this->db->query(
                        "SELECT id FROM usuario_roles WHERE usuario_id = ? AND rol_id = ?", 
                        [$usuario['usuario_id'], $usuario['rol_id']]
                    )->getRow();

                    if ($existe) {
                        echo "INFO: Usuario {$usuario['usuario_id']} ya tiene el rol {$usuario['rol_id']} asignado\n";
                        continue;
                    }

                    // Insertar en la tabla usuario_roles
                    $this->db->query(
                        "INSERT INTO usuario_roles (usuario_id, rol_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())",
                        [$usuario['usuario_id'], $usuario['rol_id']]
                    );

                    $migrados++;
                    echo "Migrado: Usuario {$usuario['usuario_id']} -> Rol {$usuario['rol_id']}\n";

                } catch (\Exception $e) {
                    echo "ERROR: No se pudo migrar usuario {$usuario['usuario_id']}: " . $e->getMessage() . "\n";
                    $errores++;
                }
            }

            echo "\nMigración completada:\n";
            echo "- Registros migrados: $migrados\n";
            echo "- Errores: $errores\n";

            // Verificar integridad
            $totalUsuarios = $this->db->query("SELECT COUNT(*) as total FROM usuarios WHERE rol_id IS NOT NULL")->getRow()->total;
            $totalAsignaciones = $this->db->query("SELECT COUNT(*) as total FROM usuario_roles")->getRow()->total;

            echo "- Total usuarios con rol_id: $totalUsuarios\n";
            echo "- Total asignaciones en usuario_roles: $totalAsignaciones\n";

            if ($totalUsuarios == $totalAsignaciones) {
                echo "✅ Migración exitosa: Todos los datos fueron migrados correctamente.\n";
            } else {
                echo "⚠️  ADVERTENCIA: Hay diferencias en los conteos. Revisa los datos.\n";
            }

        } catch (\Exception $e) {
            echo "ERROR CRÍTICO: " . $e->getMessage() . "\n";
        }
    }
}
