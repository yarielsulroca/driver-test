<?php

echo "🔍 Verificando estructura de tablas...\n\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=examen;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar tabla categorias
    echo "📋 Tabla categorias:\n";
    $stmt = $pdo->query("DESCRIBE categorias");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fields as $field) {
        echo "   - {$field['Field']} ({$field['Type']})\n";
    }
    
    echo "\n📝 Tabla examenes:\n";
    $stmt = $pdo->query("DESCRIBE examenes");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fields as $field) {
        echo "   - {$field['Field']} ({$field['Type']})\n";
    }
    
    echo "\n❓ Tabla preguntas:\n";
    $stmt = $pdo->query("DESCRIBE preguntas");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fields as $field) {
        echo "   - {$field['Field']} ({$field['Type']})\n";
    }
    
    echo "\n💬 Tabla respuestas:\n";
    $stmt = $pdo->query("DESCRIBE respuestas");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fields as $field) {
        echo "   - {$field['Field']} ({$field['Type']})\n";
    }
    
    echo "\n📊 Datos existentes:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias");
    $categorias = $stmt->fetchColumn();
    echo "   - Categorías: $categorias\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM examenes");
    $examenes = $stmt->fetchColumn();
    echo "   - Exámenes: $examenes\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM preguntas");
    $preguntas = $stmt->fetchColumn();
    echo "   - Preguntas: $preguntas\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM respuestas");
    $respuestas = $stmt->fetchColumn();
    echo "   - Respuestas: $respuestas\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} 