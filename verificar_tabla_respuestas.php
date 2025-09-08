<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=examen', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 VERIFICANDO ESTRUCTURA DE LA TABLA RESPUESTAS\n";
    echo "================================================\n\n";
    
    $stmt = $pdo->query('DESCRIBE respuestas');
    $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Campos de la tabla 'respuestas':\n";
    foreach($campos as $campo) {
        echo "  - {$campo['Field']} ({$campo['Type']}) - Null: {$campo['Null']} - Default: {$campo['Default']}\n";
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
