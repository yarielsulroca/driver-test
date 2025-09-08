<?php

// Script para eliminar y recrear la categoría problemática
$host = 'localhost';
$dbname = 'examen';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🗑️  Eliminando categoría problemática con ID 1...\n";
    
    // Eliminar la categoría con ID 1
    $pdo->exec("DELETE FROM categorias WHERE categoria_id = 1");
    echo "✅ Categoría eliminada\n";
    
    // Recrear la categoría B2 correctamente
    echo "🔄 Creando nueva categoría B2...\n";
    
    $sql = "INSERT INTO categorias (codigo, nombre, descripcion, requisitos, estado, created_at, updated_at) 
            VALUES (:codigo, :nombre, :descripcion, :requisitos, :estado, NOW(), NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':codigo' => 'B2',
        ':nombre' => 'Automóviles y camiones livianos',
        ':descripcion' => 'Licencia para conducir automóviles particulares y camiones de hasta 3500 kg de peso total',
        ':requisitos' => 'Edad mínima 18 años. Examen teórico y práctico. Certificado médico. Licencia B1 por al menos 1 año.',
        ':estado' => 'activo'
    ]);
    
    $nuevoId = $pdo->lastInsertId();
    echo "✅ Nueva categoría B2 creada con ID: $nuevoId\n";
    
    // Verificar el resultado
    $stmt = $pdo->query("SELECT * FROM categorias WHERE codigo = 'B2'");
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "📋 Categoría recreada:\n";
    echo "- ID: " . $categoria['categoria_id'] . "\n";
    echo "- Código: " . $categoria['codigo'] . "\n";
    echo "- Nombre: " . $categoria['nombre'] . "\n";
    echo "- Estado: " . $categoria['estado'] . "\n";
    
    // Verificar total de categorías
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "\n📊 Total de categorías en la base de datos: $total\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
