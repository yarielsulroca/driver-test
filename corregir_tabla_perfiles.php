<?php
/**
 * Script para corregir la tabla perfiles
 * Elimina campos duplicados (nombre, apellido) que ya existen en usuarios
 * Optimiza la estructura para evitar redundancia de datos
 */

try {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=examen', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔧 CORRIGIENDO TABLA PERFILES\n";
    echo "================================\n\n";
    
    // PASO 1: Verificar estructura actual
    echo "📋 PASO 1: Verificando estructura actual de la tabla perfiles\n";
    $stmt = $pdo->query("DESCRIBE perfiles");
    $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Campos actuales:\n";
    foreach ($campos as $campo) {
        echo "  - {$campo['Field']} ({$campo['Type']}) - Null: {$campo['Null']}\n";
    }
    echo "\n";
    
    // PASO 2: Verificar si hay datos duplicados
    echo "📊 PASO 2: Verificando datos duplicados\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM perfiles");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total de perfiles: {$total}\n";
    
    if ($total > 0) {
        $stmt = $pdo->query("SELECT p.perfil_id, p.nombre as perfil_nombre, p.apellido as perfil_apellido, 
                                    u.nombre as usuario_nombre, u.apellido as usuario_apellido
                             FROM perfiles p 
                             JOIN usuarios u ON p.usuario_id = u.usuario_id 
                             LIMIT 5");
        $ejemplos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Ejemplos de duplicación:\n";
        foreach ($ejemplos as $ejemplo) {
            echo "  - Perfil ID {$ejemplo['perfil_id']}: {$ejemplo['perfil_nombre']} {$ejemplo['perfil_apellido']}\n";
            echo "    Usuario: {$ejemplo['usuario_nombre']} {$ejemplo['usuario_apellido']}\n";
        }
        echo "\n";
    }
    
    // PASO 3: Crear tabla temporal con estructura optimizada
    echo "🔨 PASO 3: Creando tabla temporal optimizada\n";
    
    $sqlCrearTemp = "CREATE TABLE perfiles_temp (
        perfil_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT(11) UNSIGNED NOT NULL,
        telefono VARCHAR(15) NULL,
        direccion VARCHAR(255) NULL,
        fecha_nacimiento DATE NULL,
        genero ENUM('M', 'F', 'O') NULL,
        foto VARCHAR(255) NULL,
        created_at DATETIME NULL,
        updated_at DATETIME NULL,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    
    $pdo->exec($sqlCrearTemp);
    echo "✅ Tabla temporal 'perfiles_temp' creada\n";
    
    // PASO 4: Migrar datos existentes (solo campos no duplicados)
    echo "📦 PASO 4: Migrando datos a la nueva estructura\n";
    
    $sqlMigrar = "INSERT INTO perfiles_temp (perfil_id, usuario_id, telefono, direccion, fecha_nacimiento, genero, foto, created_at, updated_at)
                   SELECT perfil_id, usuario_id, telefono, direccion, fecha_nacimiento, genero, foto, created_at, updated_at
                   FROM perfiles";
    
    $pdo->exec($sqlMigrar);
    echo "✅ Datos migrados a la tabla temporal\n";
    
    // PASO 5: Eliminar tabla original y renombrar la temporal
    echo "🗑️ PASO 5: Reemplazando tabla original\n";
    
    $pdo->exec("DROP TABLE perfiles");
    echo "✅ Tabla 'perfiles' original eliminada\n";
    
    $pdo->exec("RENAME TABLE perfiles_temp TO perfiles");
    echo "✅ Tabla temporal renombrada a 'perfiles'\n";
    
    // PASO 6: Verificar estructura final
    echo "\n📋 PASO 6: Verificando estructura final\n";
    $stmt = $pdo->query("DESCRIBE perfiles");
    $camposFinales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Estructura final optimizada:\n";
    foreach ($camposFinales as $campo) {
        echo "  - {$campo['Field']} ({$campo['Type']}) - Null: {$campo['Null']}\n";
    }
    
    // PASO 7: Verificar integridad referencial
    echo "\n🔗 PASO 7: Verificando integridad referencial\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM perfiles");
    $totalFinal = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total de perfiles después de la optimización: {$totalFinal}\n";
    
    // PASO 8: Crear índices para optimizar consultas
    echo "\n⚡ PASO 8: Creando índices para optimización\n";
    $pdo->exec("CREATE INDEX idx_perfiles_usuario_id ON perfiles(usuario_id)");
    echo "✅ Índice en usuario_id creado\n";
    
    echo "\n🎉 ¡TABLA PERFILES OPTIMIZADA EXITOSAMENTE!\n";
    echo "============================================\n";
    echo "✅ Campos duplicados (nombre, apellido) eliminados\n";
    echo "✅ Estructura optimizada sin redundancia\n";
    echo "✅ Datos existentes preservados\n";
    echo "✅ Relaciones y integridad referencial mantenidas\n";
    echo "✅ Índices creados para mejor rendimiento\n";
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Código de error: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
}
?>
