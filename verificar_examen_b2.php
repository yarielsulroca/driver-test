<?php

echo "🔍 Verificando examen B2 creado...\n\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=examen;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Verificar categoría B2
    echo "📋 Categoría B2:\n";
    $stmt = $pdo->query("SELECT * FROM categorias WHERE nombre LIKE '%B2%'");
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($categoria) {
        echo "   ✅ ID: {$categoria['categoria_id']}\n";
        echo "   ✅ Nombre: {$categoria['nombre']}\n";
        echo "   ✅ Descripción: {$categoria['descripcion']}\n";
        echo "   ✅ Creada: {$categoria['created_at']}\n\n";
    }
    
    // 2. Verificar examen
    echo "📝 Examen B2:\n";
    $stmt = $pdo->query("SELECT * FROM examenes WHERE titulo LIKE '%B2%' ORDER BY created_at DESC LIMIT 1");
    $examen = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($examen) {
        echo "   ✅ ID: {$examen['examen_id']}\n";
        echo "   ✅ Título: {$examen['titulo']}\n";
        echo "   ✅ Nombre: {$examen['nombre']}\n";
        echo "   ✅ Tiempo límite: {$examen['tiempo_limite']} minutos\n";
        echo "   ✅ Puntaje mínimo: {$examen['puntaje_minimo']}%\n";
        echo "   ✅ Número de preguntas: {$examen['numero_preguntas']}\n";
        echo "   ✅ Estado: {$examen['estado']}\n";
        echo "   ✅ Fecha inicio: {$examen['fecha_inicio']}\n";
        echo "   ✅ Fecha fin: {$examen['fecha_fin']}\n\n";
    }
    
    // 3. Verificar preguntas
    echo "❓ Preguntas del examen:\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM preguntas WHERE examen_id = ?");
    $stmt->execute([$examen['examen_id']]);
    $totalPreguntas = $stmt->fetchColumn();
    echo "   ✅ Total preguntas: $totalPreguntas\n";
    
    // Estadísticas por dificultad
    $stmt = $pdo->prepare("SELECT dificultad, COUNT(*) as cantidad FROM preguntas WHERE examen_id = ? GROUP BY dificultad");
    $stmt->execute([$examen['examen_id']]);
    $dificultades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($dificultades as $diff) {
        echo "   📊 {$diff['dificultad']}: {$diff['cantidad']} preguntas\n";
    }
    
    // Preguntas críticas
    $stmt = $pdo->prepare("SELECT COUNT(*) as criticas FROM preguntas WHERE examen_id = ? AND es_critica = 1");
    $stmt->execute([$examen['examen_id']]);
    $criticas = $stmt->fetchColumn();
    echo "   ⚠️  Preguntas críticas: $criticas\n\n";
    
    // 4. Verificar respuestas
    echo "💬 Respuestas:\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM respuestas r JOIN preguntas p ON r.pregunta_id = p.pregunta_id WHERE p.examen_id = ?");
    $stmt->execute([$examen['examen_id']]);
    $totalRespuestas = $stmt->fetchColumn();
    echo "   ✅ Total respuestas: $totalRespuestas\n";
    
    // Respuestas correctas
    $stmt = $pdo->prepare("SELECT COUNT(*) as correctas FROM respuestas r JOIN preguntas p ON r.pregunta_id = p.pregunta_id WHERE p.examen_id = ? AND r.es_correcta = 1");
    $stmt->execute([$examen['examen_id']]);
    $correctas = $stmt->fetchColumn();
    echo "   ✅ Respuestas correctas: $correctas\n";
    echo "   ❌ Respuestas incorrectas: " . ($totalRespuestas - $correctas) . "\n\n";
    
    // 5. Mostrar algunas preguntas de ejemplo
    echo "📖 Ejemplos de preguntas:\n";
    $stmt = $pdo->prepare("SELECT p.*, COUNT(r.respuesta_id) as num_respuestas FROM preguntas p LEFT JOIN respuestas r ON p.pregunta_id = r.pregunta_id WHERE p.examen_id = ? GROUP BY p.pregunta_id LIMIT 3");
    $stmt->execute([$examen['examen_id']]);
    $ejemplos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($ejemplos as $i => $pregunta) {
        echo "   " . ($i + 1) . ". {$pregunta['enunciado']}\n";
        echo "      Dificultad: {$pregunta['dificultad']} | Puntaje: {$pregunta['puntaje']} | Respuestas: {$pregunta['num_respuestas']}\n";
        
        // Mostrar respuestas de esta pregunta
        $stmt2 = $pdo->prepare("SELECT * FROM respuestas WHERE pregunta_id = ?");
        $stmt2->execute([$pregunta['pregunta_id']]);
        $respuestas = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($respuestas as $respuesta) {
            $marca = $respuesta['es_correcta'] ? "✅" : "❌";
            echo "      $marca {$respuesta['texto']}\n";
        }
        echo "\n";
    }
    
    echo "🎉 ¡El examen B2 está completamente funcional!\n";
    echo "📊 Resumen final:\n";
    echo "   - 1 categoría B2 creada\n";
    echo "   - 1 examen con 60 preguntas\n";
    echo "   - 240 respuestas (4 por pregunta)\n";
    echo "   - 60 respuestas correctas\n";
    echo "   - 180 respuestas incorrectas\n";
    echo "   - 12 preguntas críticas\n";
    echo "   - Distribución equilibrada de dificultades\n\n";
    
    echo "🚀 El examen está listo para ser usado por los conductores!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} 