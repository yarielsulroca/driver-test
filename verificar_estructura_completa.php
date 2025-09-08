<?php
/**
 * Script para verificar la estructura completa de la base de datos
 * relacionada con oficinas/escuelas y sus relaciones
 */

try {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=examen', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🔍 VERIFICANDO ESTRUCTURA COMPLETA DE LA BASE DE DATOS\n";
    echo "====================================================\n\n";

    // PASO 1: Verificar tablas principales
    echo "📋 PASO 1: Verificando tablas principales\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tablas existentes:\n";
    foreach ($tablas as $tabla) {
        echo "  - $tabla\n";
    }
    echo "\n";

    // PASO 2: Verificar tabla escuelas (oficinas de tránsito)
    echo "🏢 PASO 2: Verificando tabla 'escuelas'\n";
    if (in_array('escuelas', $tablas)) {
        echo "✅ Tabla 'escuelas' existe\n";
        
        // Verificar estructura
        $stmt = $pdo->query("DESCRIBE escuelas");
        $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Estructura de la tabla 'escuelas':\n";
        foreach ($campos as $campo) {
            echo "  - {$campo['Field']} ({$campo['Type']}) - Null: {$campo['Null']}\n";
        }
        
        // Verificar datos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM escuelas");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "Total de escuelas registradas: $total\n";
        
        if ($total > 0) {
            $stmt = $pdo->query("SELECT * FROM escuelas LIMIT 3");
            $ejemplos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "Ejemplos de escuelas:\n";
            foreach ($ejemplos as $ejemplo) {
                echo "  - ID: {$ejemplo['escuela_id']}, Nombre: {$ejemplo['nombre']}, Estado: {$ejemplo['estado']}\n";
            }
        }
    } else {
        echo "❌ Tabla 'escuelas' NO existe\n";
    }
    echo "\n";

    // PASO 3: Verificar tabla examen_escuela (relación muchos a muchos)
    echo "🔗 PASO 3: Verificando tabla 'examen_escuela'\n";
    if (in_array('examen_escuela', $tablas)) {
        echo "✅ Tabla 'examen_escuela' existe\n";
        
        $stmt = $pdo->query("DESCRIBE examen_escuela");
        $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Estructura de la tabla 'examen_escuela':\n";
        foreach ($campos as $campo) {
            echo "  - {$campo['Field']} ({$campo['Type']}) - Null: {$campo['Null']}\n";
        }
        
        // Verificar datos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM examen_escuela");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "Total de relaciones examen-escuela: $total\n";
    } else {
        echo "❌ Tabla 'examen_escuela' NO existe\n";
    }
    echo "\n";

    // PASO 4: Verificar tabla conductor_escuela (relación muchos a muchos)
    echo "👤 PASO 4: Verificando tabla 'conductor_escuela'\n";
    if (in_array('conductor_escuela', $tablas)) {
        echo "✅ Tabla 'conductor_escuela' existe\n";
        
        $stmt = $pdo->query("DESCRIBE conductor_escuela");
        $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Estructura de la tabla 'conductor_escuela':\n";
        foreach ($campos as $campo) {
            echo "  - {$campo['Field']} ({$campo['Type']}) - Null: {$campo['Null']}\n";
        }
        
        // Verificar datos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM conductor_escuela");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "Total de relaciones conductor-escuela: $total\n";
    } else {
        echo "❌ Tabla 'conductor_escuela' NO existe\n";
    }
    echo "\n";

    // PASO 5: Verificar tablas relacionadas
    echo "📚 PASO 5: Verificando tablas relacionadas\n";
    
    // Tabla examenes
    if (in_array('examenes', $tablas)) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM examenes");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "✅ Tabla 'examenes' existe con $total registros\n";
    } else {
        echo "❌ Tabla 'examenes' NO existe\n";
    }
    
    // Tabla conductores
    if (in_array('conductores', $tablas)) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM conductores");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "✅ Tabla 'conductores' existe con $total registros\n";
    } else {
        echo "❌ Tabla 'conductores' NO existe\n";
    }
    
    // Tabla categorias
    if (in_array('categorias', $tablas)) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "✅ Tabla 'categorias' existe con $total registros\n";
    } else {
        echo "❌ Tabla 'categorias' NO existe\n";
    }

    echo "\n🏁 Verificación completada\n";

} catch (PDOException $e) {
    echo "❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "\n";
    echo "Código de error: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
}
?>
