<?php

// Script para actualizar la tabla categorías usando MySQL directamente
$host = 'localhost';
$dbname = 'examen';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si los campos ya existen
    $stmt = $pdo->query("DESCRIBE categorias");
    $fields = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('codigo', $fields)) {
        // Agregar campo código
        $pdo->exec("ALTER TABLE categorias ADD COLUMN codigo VARCHAR(10) NOT NULL AFTER categoria_id");
        echo "✅ Campo 'codigo' agregado\n";
    } else {
        echo "ℹ️ Campo 'codigo' ya existe\n";
    }
    
    if (!in_array('requisitos', $fields)) {
        // Agregar campo requisitos
        $pdo->exec("ALTER TABLE categorias ADD COLUMN requisitos TEXT NULL AFTER descripcion");
        echo "✅ Campo 'requisitos' agregado\n";
    } else {
        echo "ℹ️ Campo 'requisitos' ya existe\n";
    }
    
    if (!in_array('estado', $fields)) {
        // Agregar campo estado
        $pdo->exec("ALTER TABLE categorias ADD COLUMN estado ENUM('activo', 'inactivo') DEFAULT 'activo' AFTER requisitos");
        echo "✅ Campo 'estado' agregado\n";
    } else {
        echo "ℹ️ Campo 'estado' ya existe\n";
    }
    
    // Agregar índices únicos si no existen
    try {
        $pdo->exec("ALTER TABLE categorias ADD UNIQUE KEY unique_codigo (codigo)");
        echo "✅ Índice único 'unique_codigo' agregado\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "ℹ️ Índice único 'unique_codigo' ya existe\n";
        } else {
            throw $e;
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE categorias ADD UNIQUE KEY unique_nombre (nombre)");
        echo "✅ Índice único 'unique_nombre' agregado\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "ℹ️ Índice único 'unique_nombre' ya existe\n";
        } else {
            throw $e;
        }
    }
    
    // Verificar la estructura final
    echo "\n📋 Estructura final de la tabla categorías:\n";
    $stmt = $pdo->query("DESCRIBE categorias");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']}\n";
    }
    
    echo "\n✅ Tabla categorías actualizada exitosamente\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?> 