<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=examen', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 VERIFICANDO ESTRUCTURA COMPLETA DE LA TABLA PREGUNTAS\n";
    echo "========================================================\n\n";
    
    $stmt = $pdo->query('DESCRIBE preguntas');
    $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Campos de la tabla 'preguntas':\n";
    foreach($campos as $campo) {
        echo "  - {$campo['Field']} ({$campo['Type']}) - Null: {$campo['Null']} - Default: {$campo['Default']}\n";
    }
    
    echo "\n📊 Verificando si hay preguntas existentes:\n";
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM preguntas');
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total de preguntas: $total\n";
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
