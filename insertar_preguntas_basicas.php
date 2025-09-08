<?php
/**
 * Script para insertar preguntas básicas de tránsito argentino
 * Con la estructura correcta de la base de datos
 */

try {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=examen', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🚦 INSERTANDO PREGUNTAS BÁSICAS DE TRÁNSITO ARGENTINO\n";
    echo "====================================================\n\n";

    // Limpiar preguntas existentes
    echo "🧹 Limpiando preguntas existentes...\n";
    $pdo->exec("DELETE FROM respuestas");
    $pdo->exec("DELETE FROM preguntas");
    echo "✅ Preguntas anteriores eliminadas\n\n";

    // Insertar preguntas básicas
    insertarPreguntasBasicas($pdo);
    
    echo "\n🎉 ¡PREGUNTAS INSERTADAS EXITOSAMENTE!\n";
    echo "=====================================\n";
    echo "✅ Todas con es_critica = false\n";
    echo "✅ Respuestas múltiples y únicas\n";
    echo "✅ Estructura correcta de la base de datos\n";

} catch (PDOException $e) {
    echo "❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
}

function insertarPreguntasBasicas($pdo) {
    $preguntas = [
        // CATEGORÍA 1: NORMAS DE TRÁNSITO
        [
            'enunciado' => '¿Cuál es la velocidad máxima permitida en calles de la Ciudad de Buenos Aires?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'respuestas' => [
                ['texto' => '30 km/h', 'es_correcta' => false],
                ['texto' => '40 km/h', 'es_correcta' => true],
                ['texto' => '50 km/h', 'es_correcta' => false],
                ['texto' => '60 km/h', 'es_correcta' => false]
            ]
        ],
        [
            'enunciado' => '¿Cuál es la edad mínima para conducir un automóvil particular en Argentina?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'respuestas' => [
                ['texto' => '16 años', 'es_correcta' => false],
                ['texto' => '17 años', 'es_correcta' => false],
                ['texto' => '18 años', 'es_correcta' => true],
                ['texto' => '21 años', 'es_correcta' => false]
            ]
        ],
        [
            'enunciado' => '¿Cuál es la documentación obligatoria que debe portar un conductor?',
            'tipo_pregunta' => 'multiple',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'respuestas' => [
                ['texto' => 'Licencia de conducir', 'es_correcta' => true],
                ['texto' => 'Cédula verde o azul del vehículo', 'es_correcta' => true],
                ['texto' => 'Comprobante de seguro', 'es_correcta' => true],
                ['texto' => 'VTV vigente', 'es_correcta' => true]
            ]
        ],

        // CATEGORÍA 2: SEÑALES DE TRÁNSITO
        [
            'enunciado' => '¿Qué indica una señal de "PARE"?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 2,
            'dificultad' => 'facil',
            'respuestas' => [
                ['texto' => 'Reducir velocidad', 'es_correcta' => false],
                ['texto' => 'Detención obligatoria', 'es_correcta' => true],
                ['texto' => 'Girar a la derecha', 'es_correcta' => false],
                ['texto' => 'Carril preferencial', 'es_correcta' => false]
            ]
        ],
        [
            'enunciado' => '¿Qué color tienen las señales de reglamentación?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 2,
            'dificultad' => 'facil',
            'respuestas' => [
                ['texto' => 'Azul con borde rojo', 'es_correcta' => true],
                ['texto' => 'Verde con borde amarillo', 'es_correcta' => false],
                ['texto' => 'Rojo con borde blanco', 'es_correcta' => false],
                ['texto' => 'Amarillo con borde negro', 'es_correcta' => false]
            ]
        ]
    ];

    // Insertar las preguntas
    foreach ($preguntas as $index => $preguntaData) {
        // Insertar la pregunta
        $stmt = $pdo->prepare("INSERT INTO preguntas (enunciado, tipo_pregunta, categoria_id, dificultad, puntaje, es_critica, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([
            $preguntaData['enunciado'],
            $preguntaData['tipo_pregunta'],
            $preguntaData['categoria_id'],
            $preguntaData['dificultad'],
            1.00, // puntaje por defecto
            0 // es_critica = 0 (false) para todas
        ]);
        
        $preguntaId = $pdo->lastInsertId();

        // Insertar las respuestas
        foreach ($preguntaData['respuestas'] as $respuesta) {
            $stmt = $pdo->prepare("INSERT INTO respuestas (pregunta_id, texto, es_correcta, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $preguntaId,
                $respuesta['texto'],
                $respuesta['es_correcta']
            ]);
        }
        
        echo "✅ Pregunta " . ($index + 1) . " insertada\n";
    }
    
    echo "\n📊 Total de preguntas insertadas: " . count($preguntas) . "\n";
}
?>
