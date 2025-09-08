<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=examen', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 VERIFICANDO ESTRUCTURA DE LA TABLA PREGUNTAS\n";
    echo "==============================================\n\n";
    
    $stmt = $pdo->query('DESCRIBE preguntas');
    $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Campos de la tabla 'preguntas':\n";
    foreach($campos as $campo) {
        echo "  - {$campo['Field']} ({$campo['Type']}) - Null: {$campo['Null']}\n";
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
