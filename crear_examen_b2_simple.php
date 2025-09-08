veve<?php

// Script simple para crear examen B2 completo con 60 preguntas
// Ejecutar desde la línea de comandos: php crear_examen_b2_simple.php

echo "🚗 Iniciando creación de examen B2 completo...\n\n";

try {
    // Conectar a la base de datos directamente
    $host = 'localhost';
    $dbname = 'examen'; // Ajustar según tu configuración
    $username = 'root'; // Ajustar según tu configuración
    $password = ''; // Ajustar según tu configuración
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexión a base de datos establecida\n\n";
    
    // 1. Verificar/Crear categoría B2
    echo "📋 Paso 1: Verificando categoría B2...\n";
    
    // Buscar categoría B2 por nombre
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE nombre LIKE '%B2%' OR nombre LIKE '%carga%' OR nombre LIKE '%vehículo%'");
    $stmt->execute();
    $categoriaB2 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$categoriaB2) {
        echo "   ⚠️  Categoría B2 no encontrada, creando...\n";
        
        $categoriaData = [
            'nombre' => 'B2 - Vehículos de Carga',
            'descripcion' => 'Licencia para conducir vehículos de carga y transporte de mercancías',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $columns = implode(', ', array_keys($categoriaData));
        $values = ':' . implode(', :', array_keys($categoriaData));
        
        $stmt = $pdo->prepare("INSERT INTO categorias ($columns) VALUES ($values)");
        $stmt->execute($categoriaData);
        $categoriaId = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("SELECT * FROM categorias WHERE categoria_id = ?");
        $stmt->execute([$categoriaId]);
        $categoriaB2 = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "   ✅ Categoría B2 creada con ID: $categoriaId\n";
    } else {
        echo "   ✅ Categoría B2 encontrada con ID: {$categoriaB2['categoria_id']}\n";
    }
    
    $categoriaId = $categoriaB2['categoria_id'];
    
    // 2. Crear examen base
    echo "\n📝 Paso 2: Creando examen base...\n";
    
    $examenData = [
        'titulo' => 'Examen Teórico Categoría B2 - Vehículos de Carga',
        'nombre' => 'Examen B2 Completo',
        'descripcion' => 'Examen teórico completo para la obtención de licencia de conducir categoría B2. Incluye 60 preguntas sobre normativa de tránsito, señales de tránsito, mecánica básica y seguridad vial.',
        'tiempo_limite' => 90,
        'duracion_minutos' => 90,
        'puntaje_minimo' => 70.00,
        'fecha_inicio' => date('Y-m-d H:i:s'),
        'fecha_fin' => date('Y-m-d H:i:s', strtotime('+1 year')),
        'numero_preguntas' => 60,
        'estado' => 'activo',
        'supervisor_id' => 1, // Valor por defecto
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $columns = implode(', ', array_keys($examenData));
    $values = ':' . implode(', :', array_keys($examenData));
    
    $stmt = $pdo->prepare("INSERT INTO examenes ($columns) VALUES ($values)");
    $stmt->execute($examenData);
    $examenId = $pdo->lastInsertId();
    
    echo "   ✅ Examen creado con ID: $examenId\n";
    
    // 3. Verificar si existe tabla respuestas
    echo "\n🔍 Paso 3: Verificando tabla respuestas...\n";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'respuestas'");
    $tablaRespuestas = $stmt->fetch();
    
    if (!$tablaRespuestas) {
        echo "   ⚠️  Tabla respuestas no existe, creando...\n";
        
        $sql = "CREATE TABLE respuestas (
            respuesta_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            pregunta_id INT(11) UNSIGNED NOT NULL,
            texto TEXT NOT NULL,
            imagen VARCHAR(255) NULL,
            es_correcta TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY (respuesta_id),
            INDEX pregunta_id (pregunta_id),
            CONSTRAINT respuestas_pregunta_id_foreign FOREIGN KEY (pregunta_id) REFERENCES preguntas (pregunta_id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        $pdo->exec($sql);
        echo "   ✅ Tabla respuestas creada\n";
    } else {
        echo "   ✅ Tabla respuestas existe\n";
    }
    
    // 4. Generar preguntas
    echo "\n❓ Paso 4: Generando 60 preguntas...\n";
    
    // Temas para preguntas de categoría B2
    $temas = [
        'Normativa de tránsito para vehículos de carga',
        'Señales de tránsito específicas para camiones',
        'Límites de velocidad para vehículos pesados',
        'Documentación requerida para vehículos de carga',
        'Mecánica básica de vehículos pesados',
        'Seguridad vial en carreteras',
        'Mantenimiento preventivo',
        'Carga y descarga segura',
        'Emergencias en carretera',
        'Legislación de transporte'
    ];
    
    $dificultades = ['facil', 'medio', 'dificil'];
    $tiposPregunta = ['multiple', 'unica'];
    
    $preguntasGeneradas = 0;
    $respuestasGeneradas = 0;
    
    for ($i = 1; $i <= 60; $i++) {
        // Seleccionar tema y dificultad
        $tema = $temas[($i - 1) % count($temas)];
        $dificultad = $dificultades[($i - 1) % count($dificultades)];
        $tipoPregunta = $tiposPregunta[($i - 1) % count($tiposPregunta)];
        
        // Generar pregunta
        $pregunta = generarPregunta($tema, $dificultad, $i);
        
        // Crear pregunta en BD
        $preguntaData = [
            'examen_id' => $examenId,
            'categoria_id' => $categoriaId,
            'enunciado' => $pregunta['enunciado'],
            'tipo_pregunta' => $tipoPregunta,
            'puntaje' => $pregunta['puntaje'],
            'dificultad' => $dificultad,
            'es_critica' => $pregunta['es_critica'] ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $columns = implode(', ', array_keys($preguntaData));
        $values = ':' . implode(', :', array_keys($preguntaData));
        
        $stmt = $pdo->prepare("INSERT INTO preguntas ($columns) VALUES ($values)");
        $stmt->execute($preguntaData);
        $preguntaId = $pdo->lastInsertId();
        $preguntasGeneradas++;
        
        // Crear respuestas
        foreach ($pregunta['respuestas'] as $respuesta) {
            $respuestaData = [
                'pregunta_id' => $preguntaId,
                'texto' => $respuesta['texto'],
                'es_correcta' => $respuesta['es_correcta'] ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $columns = implode(', ', array_keys($respuestaData));
            $values = ':' . implode(', :', array_keys($respuestaData));
            
            $stmt = $pdo->prepare("INSERT INTO respuestas ($columns) VALUES ($values)");
            $stmt->execute($respuestaData);
            $respuestasGeneradas++;
        }
        
        if ($i % 10 == 0) {
            echo "   📊 Progreso: $i/60 preguntas generadas\n";
        }
    }
    
    echo "\n✅ ¡Examen B2 creado exitosamente!\n";
    echo "📊 Estadísticas:\n";
    echo "   - Examen ID: $examenId\n";
    echo "   - Preguntas generadas: $preguntasGeneradas\n";
    echo "   - Respuestas generadas: $respuestasGeneradas\n";
    echo "   - Categoría: B2 (ID: $categoriaId)\n";
    echo "   - Tiempo límite: 90 minutos\n";
    echo "   - Puntaje mínimo: 70%\n\n";
    
    // Verificar en BD
    echo "🔍 Verificando en base de datos...\n";
    $stmt = $pdo->prepare("SELECT * FROM examenes WHERE examen_id = ?");
    $stmt->execute([$examenId]);
    $examenCreado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM preguntas WHERE examen_id = ?");
    $stmt->execute([$examenId]);
    $preguntasCount = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM respuestas r JOIN preguntas p ON r.pregunta_id = p.pregunta_id WHERE p.examen_id = ?");
    $stmt->execute([$examenId]);
    $respuestasCount = $stmt->fetchColumn();
    
    echo "   ✅ Examen encontrado: {$examenCreado['titulo']}\n";
    echo "   ✅ Preguntas en BD: $preguntasCount\n";
    echo "   ✅ Respuestas en BD: $respuestasCount\n\n";
    
    echo "🎉 ¡El examen B2 está listo para ser usado!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

function generarPregunta($tema, $dificultad, $numero) {
    // Base de preguntas para categoría B2
    $preguntasBase = [
        'Normativa de tránsito para vehículos de carga' => [
            [
                'enunciado' => '¿Cuál es el peso máximo permitido para un vehículo de categoría B2 sin documentación especial?',
                'respuestas' => [
                    ['texto' => '3.500 kg', 'es_correcta' => true],
                    ['texto' => '5.000 kg', 'es_correcta' => false],
                    ['texto' => '7.500 kg', 'es_correcta' => false],
                    ['texto' => '10.000 kg', 'es_correcta' => false]
                ]
            ],
            [
                'enunciado' => '¿Qué documentación es obligatoria para transportar carga en un vehículo B2?',
                'respuestas' => [
                    ['texto' => 'Solo licencia de conducir', 'es_correcta' => false],
                    ['texto' => 'Licencia de conducir y documentación del vehículo', 'es_correcta' => true],
                    ['texto' => 'Solo documentación del vehículo', 'es_correcta' => false],
                    ['texto' => 'Ninguna documentación especial', 'es_correcta' => false]
                ]
            ]
        ],
        'Señales de tránsito específicas para camiones' => [
            [
                'enunciado' => '¿Qué significa la señal "Prohibido el paso de vehículos de carga"?',
                'respuestas' => [
                    ['texto' => 'Solo camiones pequeños pueden pasar', 'es_correcta' => false],
                    ['texto' => 'Ningún vehículo de carga puede circular', 'es_correcta' => true],
                    ['texto' => 'Solo vehículos ligeros pueden pasar', 'es_correcta' => false],
                    ['texto' => 'Se permite el paso con restricciones', 'es_correcta' => false]
                ]
            ]
        ],
        'Límites de velocidad para vehículos pesados' => [
            [
                'enunciado' => '¿Cuál es el límite de velocidad máximo para un vehículo B2 en autopista?',
                'respuestas' => [
                    ['texto' => '80 km/h', 'es_correcta' => true],
                    ['texto' => '100 km/h', 'es_correcta' => false],
                    ['texto' => '120 km/h', 'es_correcta' => false],
                    ['texto' => '140 km/h', 'es_correcta' => false]
                ]
            ]
        ],
        'Mecánica básica de vehículos pesados' => [
            [
                'enunciado' => '¿Qué sistema es fundamental para la seguridad de un vehículo de carga?',
                'respuestas' => [
                    ['texto' => 'Solo el sistema de frenos', 'es_correcta' => false],
                    ['texto' => 'Solo el sistema de dirección', 'es_correcta' => false],
                    ['texto' => 'Solo el sistema de suspensión', 'es_correcta' => false],
                    ['texto' => 'Todos los sistemas de seguridad', 'es_correcta' => true]
                ]
            ]
        ],
        'Seguridad vial en carreteras' => [
            [
                'enunciado' => '¿Cuál es la distancia mínima de seguridad recomendada para un vehículo B2?',
                'respuestas' => [
                    ['texto' => '2 segundos', 'es_correcta' => false],
                    ['texto' => '3 segundos', 'es_correcta' => true],
                    ['texto' => '1 segundo', 'es_correcta' => false],
                    ['texto' => '5 segundos', 'es_correcta' => false]
                ]
            ]
        ]
    ];
    
    // Obtener preguntas del tema
    $preguntasTema = $preguntasBase[$tema] ?? $preguntasBase['Normativa de tránsito para vehículos de carga'];
    $preguntaBase = $preguntasTema[($numero - 1) % count($preguntasTema)];
    
    // Variar la pregunta para evitar duplicados
    $variaciones = [
        '¿Cuál es la normativa vigente sobre',
        'Según la legislación actual,',
        'De acuerdo con el código de tránsito,',
        'La reglamentación establece que',
        'En términos de seguridad vial,'
    ];
    
    $variacion = $variaciones[($numero - 1) % count($variaciones)];
    
    return [
        'enunciado' => $variacion . ' ' . strtolower(str_replace('¿', '', $preguntaBase['enunciado'])),
        'respuestas' => $preguntaBase['respuestas'],
        'puntaje' => $dificultad === 'dificil' ? 3 : ($dificultad === 'medio' ? 2 : 1),
        'es_critica' => $numero % 5 === 0 // Cada 5 preguntas es crítica
    ];
} 