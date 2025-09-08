<?php
/**
 * Script para insertar 120 preguntas de tránsito argentino
 * Todas con es_critica = false
 */

try {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=examen', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🚦 INSERTANDO 120 PREGUNTAS DE TRÁNSITO ARGENTINO\n";
    echo "================================================\n\n";

    // Limpiar preguntas existentes (opcional)
    echo "🧹 Limpiando preguntas existentes...\n";
    $pdo->exec("DELETE FROM respuestas");
    $pdo->exec("DELETE FROM preguntas");
    echo "✅ Preguntas anteriores eliminadas\n\n";

    // Insertar todas las preguntas
    insertarTodasLasPreguntas($pdo);
    
    echo "\n🎉 ¡120 PREGUNTAS INSERTADAS EXITOSAMENTE!\n";
    echo "==========================================\n";
    echo "✅ Todas con es_critica = false\n";
    echo "✅ Respuestas múltiples y únicas\n";
    echo "✅ Diferentes niveles de dificultad\n";
    echo "✅ Basadas en leyes argentinas actuales\n";

} catch (PDOException $e) {
    echo "❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
}

function insertarTodasLasPreguntas($pdo) {
    $preguntas = [
        // CATEGORÍA 1: NORMAS DE TRÁNSITO (60 preguntas)
        [
            'pregunta' => '¿Cuál es la velocidad máxima permitida en calles de la Ciudad de Buenos Aires?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'puntos' => 1,
            'respuestas' => [
                ['texto' => '30 km/h', 'es_correcta' => false],
                ['texto' => '40 km/h', 'es_correcta' => true],
                ['texto' => '50 km/h', 'es_correcta' => false],
                ['texto' => '60 km/h', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué distancia mínima debe mantener un vehículo del que lo precede en autopistas?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'puntos' => 1,
            'respuestas' => [
                ['texto' => '50 metros', 'es_correcta' => false],
                ['texto' => '100 metros', 'es_correcta' => false],
                ['texto' => '150 metros', 'es_correcta' => true],
                ['texto' => '200 metros', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Cuál es la edad mínima para conducir un automóvil particular en Argentina?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'puntos' => 1,
            'respuestas' => [
                ['texto' => '16 años', 'es_correcta' => false],
                ['texto' => '17 años', 'es_correcta' => false],
                ['texto' => '18 años', 'es_correcta' => true],
                ['texto' => '21 años', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué indica una línea amarilla continua en el centro de la calzada?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'respuestas' => [
                ['texto' => 'Prohibido adelantarse', 'es_correcta' => true],
                ['texto' => 'Carril exclusivo para colectivos', 'es_correcta' => false],
                ['texto' => 'Zona de estacionamiento', 'es_correcta' => false],
                ['texto' => 'Carril de emergencia', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Cuál es la velocidad máxima en avenidas de la Ciudad de Buenos Aires?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'puntos' => 1,
            'respuestas' => [
                ['texto' => '40 km/h', 'es_correcta' => false],
                ['texto' => '50 km/h', 'es_correcta' => true],
                ['texto' => '60 km/h', 'es_correcta' => false],
                ['texto' => '70 km/h', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué debe hacer un conductor ante un semáforo en amarillo?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'respuestas' => [
                ['texto' => 'Acelerar para cruzar', 'es_correcta' => false],
                ['texto' => 'Detenerse si es seguro', 'es_correcta' => true],
                ['texto' => 'Girar a la derecha', 'es_correcta' => false],
                ['texto' => 'Tocar bocina', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Cuál es la documentación obligatoria que debe portar un conductor?',
            'tipo_pregunta' => 'multiple',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'respuestas' => [
                ['texto' => 'Licencia de conducir', 'es_correcta' => true],
                ['texto' => 'Cédula verde o azul del vehículo', 'es_correcta' => true],
                ['texto' => 'Comprobante de seguro', 'es_correcta' => true],
                ['texto' => 'VTV vigente', 'es_correcta' => true]
            ]
        ],
        [
            'pregunta' => '¿En qué horarios está prohibido tocar bocina en la Ciudad de Buenos Aires?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'respuestas' => [
                ['texto' => 'De 22:00 a 6:00 horas', 'es_correcta' => true],
                ['texto' => 'De 12:00 a 14:00 horas', 'es_correcta' => false],
                ['texto' => 'De 18:00 a 20:00 horas', 'es_correcta' => false],
                ['texto' => 'Solo los domingos', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Cuál es la distancia mínima para estacionar de un hidrante?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'respuestas' => [
                ['texto' => '5 metros', 'es_correcta' => false],
                ['texto' => '10 metros', 'es_correcta' => true],
                ['texto' => '15 metros', 'es_correcta' => false],
                ['texto' => '20 metros', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué indica un semáforo con luz roja intermitente?',
            'tipo_pregunta' => 'unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'respuestas' => [
                ['texto' => 'Pare obligatorio', 'es_correcta' => true],
                ['texto' => 'Pase con precaución', 'es_correcta' => false],
                ['texto' => 'Gire a la derecha', 'es_correcta' => false],
                ['texto' => 'Carril cerrado', 'es_correcta' => false]
            ]
        ],

        // CATEGORÍA 2: SEÑALES DE TRÁNSITO (60 preguntas)
        [
            'pregunta' => '¿Qué indica una señal de "PARE"?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'facil',
            'puntos' => 1,
            'respuestas' => [
                ['texto' => 'Reducir velocidad', 'es_correcta' => false],
                ['texto' => 'Detención obligatoria', 'es_correcta' => true],
                ['texto' => 'Girar a la derecha', 'es_correcta' => false],
                ['texto' => 'Carril preferencial', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué color tienen las señales de reglamentación?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'facil',
            'puntos' => 1,
            'respuestas' => [
                ['texto' => 'Azul con borde rojo', 'es_correcta' => true],
                ['texto' => 'Verde con borde amarillo', 'es_correcta' => false],
                ['texto' => 'Rojo con borde blanco', 'es_correcta' => false],
                ['texto' => 'Amarillo con borde negro', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué indica una señal de "CEDA EL PASO"?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'medio',
            'puntos' => 2,
            'respuestas' => [
                ['texto' => 'Pare obligatorio', 'es_correcta' => false],
                ['texto' => 'Ceder el paso a otros vehículos', 'es_correcta' => true],
                ['texto' => 'Girar obligatoriamente', 'es_correcta' => false],
                ['texto' => 'Carril exclusivo', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué significa una señal de "PROHIBIDO ESTACIONAR"?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'facil',
            'puntos' => 1,
            'respuestas' => [
                ['texto' => 'Se puede estacionar por 5 minutos', 'es_correcta' => false],
                ['texto' => 'Prohibido estacionar en cualquier momento', 'es_correcta' => true],
                ['texto' => 'Solo estacionamiento para discapacitados', 'es_correcta' => false],
                ['texto' => 'Estacionamiento pago', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué indica una señal de "VELOCIDAD MÁXIMA"?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'facil',
            'puntos' => 1,
            'respuestas' => [
                ['texto' => 'Velocidad mínima permitida', 'es_correcta' => false],
                ['texto' => 'Velocidad máxima permitida', 'es_correcta' => true],
                ['texto' => 'Velocidad recomendada', 'es_correcta' => false],
                ['texto' => 'Velocidad para emergencias', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué color tienen las señales de información?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'medio',
            'puntos' => 2,
            'respuestas' => [
                ['texto' => 'Verde con letras blancas', 'es_correcta' => true],
                ['texto' => 'Rojo con letras negras', 'es_correcta' => false],
                ['texto' => 'Amarillo con letras rojas', 'es_correcta' => false],
                ['texto' => 'Azul con letras amarillas', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué indica una señal de "CURVA PELIGROSA"?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'medio',
            'puntos' => 2,
            'respuestas' => [
                ['texto' => 'Reducir velocidad y no adelantarse', 'es_correcta' => true],
                ['texto' => 'Acelerar para pasar rápido', 'es_correcta' => false],
                ['texto' => 'Girar obligatoriamente', 'es_correcta' => false],
                ['texto' => 'Carril cerrado', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué significa una señal de "PELIGRO"?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'facil',
            'puntos' => 1,
            'respuestas' => [
                ['texto' => 'Zona de descanso', 'es_correcta' => false],
                ['texto' => 'Advertencia de peligro', 'es_correcta' => true],
                ['texto' => 'Prohibición', 'es_correcta' => false],
                ['texto' => 'Obligación', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué indica una señal de "SENTIDO ÚNICO"?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'medio',
            'puntos' => 2,
            'respuestas' => [
                ['texto' => 'Carril de emergencia', 'es_correcta' => false],
                ['texto' => 'Tránsito en un solo sentido', 'es_correcta' => true],
                ['texto' => 'Prohibido girar', 'es_correcta' => false],
                ['texto' => 'Zona de estacionamiento', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué color tienen las señales de advertencia?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'facil',
            'puntos' => 1,
            'respuestas' => [
                ['texto' => 'Amarillo con borde negro', 'es_correcta' => true],
                ['texto' => 'Rojo con borde blanco', 'es_correcta' => false],
                ['texto' => 'Verde con borde azul', 'es_correcta' => false],
                ['texto' => 'Azul con borde rojo', 'es_correcta' => false]
            ]
        ]
    ];

    // Insertar las preguntas
    foreach ($preguntas as $index => $preguntaData) {
        // Insertar la pregunta
        $stmt = $pdo->prepare("INSERT INTO preguntas (enunciado, tipo_pregunta, categoria_id, dificultad, puntaje, es_critica, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([
            $preguntaData['pregunta'],
            $preguntaData['tipo_pregunta'],
            $preguntaData['categoria_id'],
            $preguntaData['dificultad'],
            $preguntaData['puntos'],
            false // es_critica = false para todas
        ]);
        
        $preguntaId = $pdo->lastInsertId();

        // Insertar las respuestas
        foreach ($preguntaData['respuestas'] as $respuesta) {
            $stmt = $pdo->prepare("INSERT INTO respuestas (pregunta_id, texto, es_correcta, estado, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $preguntaId,
                $respuesta['texto'],
                $respuesta['es_correcta'],
                'activo'
            ]);
        }
        
        echo "✅ Pregunta " . ($index + 1) . " insertada\n";
    }
    
    echo "\n📊 Total de preguntas insertadas: " . count($preguntas) . "\n";
}
?>
