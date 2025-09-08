<?php

// Script para verificar la tabla categorías
$host = 'localhost';
$dbname = 'examen';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== VERIFICACIÓN DE TABLA CATEGORÍAS ===\n";
    
    // Verificar si la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'categorias'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'categorias' existe\n";
        
        // Mostrar estructura de la tabla
        echo "\n📋 Estructura de la tabla:\n";
        $stmt = $pdo->query("DESCRIBE categorias");
        $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($fields as $field) {
            echo "- {$field['Field']}: {$field['Type']} " . 
                 ($field['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . 
                 ($field['Default'] ? " DEFAULT '{$field['Default']}'" : '') . "\n";
        }
        
        // Contar registros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "\n📊 Total de registros: $count\n";
        
        // Mostrar algunos registros de ejemplo
        if ($count > 0) {
            echo "\n📝 Registros de ejemplo:\n";
            $stmt = $pdo->query("SELECT * FROM categorias LIMIT 3");
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($records as $record) {
                echo "- ID: {$record['categoria_id']}, Código: {$record['codigo']}, Nombre: {$record['nombre']}\n";
            }
        }
        
    } else {
        echo "❌ Tabla 'categorias' NO existe\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . "\n";
} 