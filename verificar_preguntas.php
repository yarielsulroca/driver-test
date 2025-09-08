<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=examen', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 VERIFICANDO PREGUNTAS INSERTADAS\n";
    echo "==================================\n\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM preguntas');
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total de preguntas: $total\n\n";
    
    if ($total > 0) {
        $stmt = $pdo->query('SELECT pregunta_id, enunciado, tipo_pregunta, categoria_id, dificultad, puntaje, es_critica FROM preguntas LIMIT 3');
        $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Primeras 3 preguntas:\n";
        foreach($preguntas as $pregunta) {
            echo "  - ID: {$pregunta['pregunta_id']}, Tipo: {$pregunta['tipo_pregunta']}, Categoría: {$pregunta['categoria_id']}, Crítica: " . ($pregunta['es_critica'] ? 'Sí' : 'No') . "\n";
            echo "    Enunciado: " . substr($pregunta['enunciado'], 0, 50) . "...\n\n";
        }
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
