<?php

// Script para crear examen B2 completo con 60 preguntas
// Ejecutar desde la línea de comandos: php crear_examen_b2.php

// Inicializar CodeIgniter
require_once 'vendor/autoload.php';

// Configurar el entorno
putenv('CI_ENVIRONMENT=development');

// Inicializar CodeIgniter
$pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
require_once $pathsPath;

$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

echo "🚗 Iniciando creación de examen B2 completo...\n\n";

try {
    // Conectar a la base de datos
    $db = \Config\Database::connect();
    
    // 1. Verificar/Crear categoría B2
    echo "📋 Paso 1: Verificando categoría B2...\n";
    
    // Buscar categoría B2
    $categoriaB2 = $db->table('categorias')
                      ->where('codigo', 'B2')
                      ->orWhere('sigla', 'B2')
                      ->orWhere('nombre LIKE', '%B2%')
                      ->get()
                      ->getRowArray();
    
    if (!$categoriaB2) {
        echo "   ⚠️  Categoría B2 no encontrada, creando...\n";
        
        // Verificar estructura de tabla
        $fields = $db->getFieldNames('categorias');
        
        $categoriaData = [
            'codigo' => 'B2',
            'sigla' => 'B2',
            'nombre' => 'Vehículos de Carga',
            'descripcion' => 'Licencia para conducir vehículos de carga y transporte de mercancías',
            'estado' => 'activo',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Agregar requisitos si la columna existe
        if (in_array('requisitos', $fields)) {
            $categoriaData['requisitos'] = "1. Tener 21 años cumplidos.\n2. Poseer licencia B2 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de antecedentes penales.";
        }
        
        $db->table('categorias')->insert($categoriaData);
        $categoriaId = $db->insertID();
        
        $categoriaB2 = $db->table('categorias')->where('categoria_id', $categoriaId)->get()->getRowArray();
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
        'puntaje_minimo' => 70,
        'fecha_inicio' => date('Y-m-d'),
        'fecha_fin' => date('Y-m-d', strtotime('+1 year')),
        'numero_preguntas' => 60,
        'estado' => 'activo',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Agregar categoria_id si la columna existe
    $fields = $db->getFieldNames('examenes');
    if (in_array('categoria_id', $fields)) {
        $examenData['categoria_id'] = $categoriaId;
    }
    
    $db->table('examenes')->insert($examenData);
    $examenId = $db->insertID();
    echo "   ✅ Examen creado con ID: $examenId\n";
    
    // 3. Generar preguntas
    echo "\n❓ Paso 3: Generando 60 preguntas...\n";
    
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
        
        $db->table('preguntas')->insert($preguntaData);
        $preguntaId = $db->insertID();
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
            
            $db->table('respuestas')->insert($respuestaData);
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
    $examenCreado = $db->table('examenes')->where('examen_id', $examenId)->get()->getRowArray();
    $preguntasCount = $db->table('preguntas')->where('examen_id', $examenId)->countAllResults();
    $respuestasCount = $db->table('respuestas')
                          ->join('preguntas', 'preguntas.pregunta_id = respuestas.pregunta_id')
                          ->where('preguntas.examen_id', $examenId)
                          ->countAllResults();
    
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