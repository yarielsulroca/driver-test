<?php

// Script para ver todas las categorías
$host = 'localhost';
$dbname = 'examen';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "📋 LISTADO COMPLETO DE CATEGORÍAS\n";
    echo "================================\n\n";
    
    // Obtener todas las categorías ordenadas por ID
    $stmt = $pdo->query("SELECT categoria_id, codigo, nombre, estado FROM categorias ORDER BY categoria_id");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categorias as $cat) {
        echo "ID: {$cat['categoria_id']} | Código: {$cat['codigo']} | Nombre: {$cat['nombre']} | Estado: {$cat['estado']}\n";
    }
    
    echo "\n🔍 Buscando categoría B2...\n";
    $stmt = $pdo->query("SELECT * FROM categorias WHERE codigo = 'B2'");
    $b2 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($b2) {
        echo "✅ Categoría B2 encontrada:\n";
        echo "- ID: {$b2['categoria_id']}\n";
        echo "- Código: {$b2['codigo']}\n";
        echo "- Nombre: {$b2['nombre']}\n";
        echo "- Estado: {$b2['estado']}\n";
    } else {
        echo "❌ No se encontró categoría B2\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
