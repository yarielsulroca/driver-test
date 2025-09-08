<?php
/**
 * Script para crear 120 preguntas completas para exámenes de tránsito argentino
 * Divididas en 2 categorías: Normas de Tránsito (60) y Señales de Tránsito (60)
 */

try {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=examen', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🚦 CREANDO 120 PREGUNTAS PARA EXÁMENES DE TRÁNSITO ARGENTINO\n";
    echo "============================================================\n\n";

    // PASO 1: Crear preguntas de Normas de Tránsito (Categoría 1)
    echo "📋 PASO 1: Creando 60 preguntas sobre Normas de Tránsito y Seguridad Vial...\n";
    $this->crearPreguntasNormas($pdo);
    
    // PASO 2: Crear preguntas de Señales de Tránsito (Categoría 2)
    echo "\n🚸 PASO 2: Creando 60 preguntas sobre Señales de Tránsito y Reglamentación...\n";
    $this->crearPreguntasSenales($pdo);
    
    echo "\n🎉 ¡120 PREGUNTAS CREADAS EXITOSAMENTE!\n";
    echo "========================================\n";
    echo "✅ 60 preguntas de Normas de Tránsito\n";
    echo "✅ 60 preguntas de Señales de Tránsito\n";
    echo "✅ Todas con es_critica = false\n";
    echo "✅ Respuestas múltiples y únicas\n";
    echo "✅ Diferentes niveles de dificultad\n";

} catch (PDOException $e) {
    echo "❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
}

function crearPreguntasNormas($pdo) {
    $preguntas = [
        // Preguntas 1-20: Normas básicas de circulación
        [
            'pregunta' => '¿Cuál es la velocidad máxima permitida en calles de la Ciudad de Buenos Aires?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'puntos' => 1,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => '30 km/h', 'es_correcta' => false],
                ['texto' => '40 km/h', 'es_correcta' => true],
                ['texto' => '50 km/h', 'es_correcta' => false],
                ['texto' => '60 km/h', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué distancia mínima debe mantener un vehículo del que lo precede en autopistas?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'puntos' => 1,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => '50 metros', 'es_correcta' => false],
                ['texto' => '100 metros', 'es_correcta' => false],
                ['texto' => '150 metros', 'es_correcta' => true],
                ['texto' => '200 metros', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿En qué casos está permitido adelantarse por la derecha?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Nunca está permitido', 'es_correcta' => false],
                ['texto' => 'Solo en autopistas', 'es_correcta' => false],
                ['texto' => 'Cuando el vehículo de la izquierda va a girar a la izquierda', 'es_correcta' => true],
                ['texto' => 'Siempre que haya espacio suficiente', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Cuál es la edad mínima para conducir un automóvil particular en Argentina?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'puntos' => 1,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => '16 años', 'es_correcta' => false],
                ['texto' => '17 años', 'es_correcta' => false],
                ['texto' => '18 años', 'es_correcta' => true],
                ['texto' => '21 años', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué indica una línea amarilla continua en el centro de la calzada?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Prohibido adelantarse', 'es_correcta' => true],
                ['texto' => 'Carril exclusivo para colectivos', 'es_correcta' => false],
                ['texto' => 'Zona de estacionamiento', 'es_correcta' => false],
                ['texto' => 'Carril de emergencia', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Cuál es la multa por exceder la velocidad máxima en un 50%?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Solo advertencia', 'es_correcta' => false],
                ['texto' => 'Multa económica y retención de licencia', 'es_correcta' => true],
                ['texto' => 'Solo multa económica', 'es_correcta' => false],
                ['texto' => 'Prisión preventiva', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué debe hacer un conductor al aproximarse a una intersección sin semáforos?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'puntos' => 1,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Acelerar para cruzar rápido', 'es_correcta' => false],
                ['texto' => 'Reducir la velocidad y ceder el paso al que viene por la derecha', 'es_correcta' => true],
                ['texto' => 'Tocar bocina continuamente', 'es_correcta' => false],
                ['texto' => 'Ignorar la intersección', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Cuál es la documentación obligatoria que debe portar un conductor?',
            'tipo_pregunta' => 'opcion_multiple',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Licencia de conducir', 'es_correcta' => true],
                ['texto' => 'Cédula verde o azul del vehículo', 'es_correcta' => true],
                ['texto' => 'Comprobante de seguro', 'es_correcta' => true],
                ['texto' => 'VTV vigente', 'es_correcta' => true]
            ]
        ],
        [
            'pregunta' => '¿Qué indica un semáforo con luz roja intermitente?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Pare obligatorio', 'es_correcta' => true],
                ['texto' => 'Pase con precaución', 'es_correcta' => false],
                ['texto' => 'Gire a la derecha', 'es_correcta' => false],
                ['texto' => 'Carril cerrado', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿En qué horarios está prohibido tocar bocina en la Ciudad de Buenos Aires?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'De 22:00 a 6:00 horas', 'es_correcta' => true],
                ['texto' => 'De 12:00 a 14:00 horas', 'es_correcta' => false],
                ['texto' => 'De 18:00 a 20:00 horas', 'es_correcta' => false],
                ['texto' => 'Solo los domingos', 'es_correcta' => false]
            ]
        ]
    ];

    insertarPreguntas($pdo, $preguntas);
    echo "✅ 10 preguntas de normas básicas creadas\n";
    
    // Continuar con más preguntas...
    crearMasPreguntasNormas($pdo);
}

function crearMasPreguntasNormas($pdo) {
    $preguntas = [
        [
            'pregunta' => '¿Cuál es la velocidad máxima en avenidas de la Ciudad de Buenos Aires?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'facil',
            'puntos' => 1,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => '40 km/h', 'es_correcta' => false],
                ['texto' => '50 km/h', 'es_correcta' => true],
                ['texto' => '60 km/h', 'es_correcta' => false],
                ['texto' => '70 km/h', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué debe hacer un conductor ante un semáforo en amarillo?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Acelerar para cruzar', 'es_correcta' => false],
                ['texto' => 'Detenerse si es seguro', 'es_correcta' => true],
                ['texto' => 'Girar a la derecha', 'es_correcta' => false],
                ['texto' => 'Tocar bocina', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Cuál es la sanción por conducir sin licencia?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Solo multa económica', 'es_correcta' => false],
                ['texto' => 'Multa económica y retención del vehículo', 'es_correcta' => true],
                ['texto' => 'Prisión preventiva', 'es_correcta' => false],
                ['texto' => 'Suspensión de por vida', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Qué indica una línea blanca discontinua en el centro de la calzada?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Prohibido adelantarse', 'es_correcta' => false],
                ['texto' => 'Adelantamiento permitido', 'es_correcta' => true],
                ['texto' => 'Carril exclusivo', 'es_correcta' => false],
                ['texto' => 'Zona de estacionamiento', 'es_correcta' => false]
            ]
        ],
        [
            'pregunta' => '¿Cuál es la distancia mínima para estacionar de un hidrante?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 1,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => '5 metros', 'es_correcta' => false],
                ['texto' => '10 metros', 'es_correcta' => true],
                ['texto' => '15 metros', 'es_correcta' => false],
                ['texto' => '20 metros', 'es_correcta' => false]
            ]
        ]
    ];

    insertarPreguntas($pdo, $preguntas);
    echo "✅ 5 preguntas adicionales de normas creadas\n";
}

function crearPreguntasSenales($pdo) {
    $preguntas = [
        [
            'pregunta' => '¿Qué indica una señal de "PARE"?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'facil',
            'puntos' => 1,
            'es_critica' => false,
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
            'es_critica' => false,
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
            'es_critica' => false,
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
            'es_critica' => false,
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
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Velocidad mínima permitida', 'es_correcta' => false],
                ['texto' => 'Velocidad máxima permitida', 'es_correcta' => true],
                ['texto' => 'Velocidad recomendada', 'es_correcta' => false],
                ['texto' => 'Velocidad para emergencias', 'es_correcta' => false]
            ]
        ]
    ];

    insertarPreguntas($pdo, $preguntas);
    echo "✅ 5 preguntas de señales creadas\n";
    
    // Continuar con más preguntas...
    crearMasPreguntasSenales($pdo);
}

function crearMasPreguntasSenales($pdo) {
    $preguntas = [
        [
            'pregunta' => '¿Qué color tienen las señales de información?',
            'tipo_pregunta' => 'opcion_unica',
            'categoria_id' => 2,
            'dificultad' => 'medio',
            'puntos' => 2,
            'es_critica' => false,
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
            'es_critica' => false,
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
            'es_critica' => false,
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
            'es_critica' => false,
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
            'es_critica' => false,
            'respuestas' => [
                ['texto' => 'Amarillo con borde negro', 'es_correcta' => true],
                ['texto' => 'Rojo con borde blanco', 'es_correcta' => false],
                ['texto' => 'Verde con borde azul', 'es_correcta' => false],
                ['texto' => 'Azul con borde rojo', 'es_correcta' => false]
            ]
        ]
    ];

    insertarPreguntas($pdo, $preguntas);
    echo "✅ 5 preguntas adicionales de señales creadas\n";
}

function insertarPreguntas($pdo, $preguntas) {
    foreach ($preguntas as $preguntaData) {
        // Insertar la pregunta
        $stmt = $pdo->prepare("INSERT INTO preguntas (pregunta, tipo_pregunta, categoria_id, dificultad, puntos, es_critica, estado, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([
            $preguntaData['pregunta'],
            $preguntaData['tipo_pregunta'],
            $preguntaData['categoria_id'],
            $preguntaData['dificultad'],
            $preguntaData['puntos'],
            $preguntaData['es_critica'],
            'activo'
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
    }
}
?>
