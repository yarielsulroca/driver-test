<?php

// Script para probar la creación de exámenes desde el backend
$host = 'localhost';
$dbname = 'examen';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🧪 Probando creación de examen desde el backend...\n\n";
    
    // Verificar que tenemos categorías disponibles
    $stmt = $pdo->query("SELECT categoria_id, codigo, nombre FROM categorias WHERE estado = 'activo' LIMIT 3");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($categorias)) {
        echo "❌ No hay categorías disponibles para crear el examen\n";
        exit(1);
    }
    
    echo "📋 Categorías disponibles:\n";
    foreach ($categorias as $cat) {
        echo "- ID: {$cat['categoria_id']}, Código: {$cat['codigo']}, Nombre: {$cat['nombre']}\n";
    }
    
    // Crear un examen de prueba directamente en la base de datos
    echo "\n🔄 Creando examen de prueba...\n";
    
    // Insertar el examen
    $sql = "INSERT INTO examenes (titulo, nombre, descripcion, tiempo_limite, duracion_minutos, puntaje_minimo, fecha_inicio, fecha_fin, numero_preguntas, estado, created_at, updated_at) 
            VALUES (:titulo, :nombre, :descripcion, :tiempo_limite, :duracion_minutos, :puntaje_minimo, :fecha_inicio, :fecha_fin, :numero_preguntas, :estado, NOW(), NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo' => 'Examen de Prueba B2',
        ':nombre' => 'Examen de Prueba B2',
        ':descripcion' => 'Examen de prueba para la categoría B2',
        ':tiempo_limite' => 30,
        ':duracion_minutos' => 30,
        ':puntaje_minimo' => 70.00,
        ':fecha_inicio' => date('Y-m-d H:i:s'),
        ':fecha_fin' => date('Y-m-d H:i:s', strtotime('+1 year')),
        ':numero_preguntas' => 2,
        ':estado' => 'activo'
    ]);
    
    $examen_id = $pdo->lastInsertId();
    echo "✅ Examen creado con ID: $examen_id\n";
    
    // Asignar categoría al examen
    $categoria_id = $categorias[0]['categoria_id'];
    $sql = "INSERT INTO examen_categoria (examen_id, categoria_id, created_at, updated_at) VALUES (:examen_id, :categoria_id, NOW(), NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':examen_id' => $examen_id,
        ':categoria_id' => $categoria_id
    ]);
    echo "✅ Categoría asignada al examen\n";
    
    // Crear preguntas de prueba
    $preguntas = [
        [
            'enunciado' => '¿Cuál es la velocidad máxima en zona urbana?',
            'tipo_pregunta' => 'multiple',
            'dificultad' => 'medio',
            'puntaje' => 10,
            'es_critica' => 0
        ],
        [
            'enunciado' => '¿Es obligatorio usar cinturón de seguridad?',
            'tipo_pregunta' => 'verdadero_falso',
            'dificultad' => 'facil',
            'puntaje' => 5,
            'es_critica' => 1
        ]
    ];
    
    foreach ($preguntas as $index => $pregunta) {
        $sql = "INSERT INTO preguntas (examen_id, categoria_id, enunciado, tipo_pregunta, puntaje, dificultad, es_critica, created_at, updated_at) 
                VALUES (:examen_id, :categoria_id, :enunciado, :tipo_pregunta, :puntaje, :dificultad, :es_critica, NOW(), NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':examen_id' => $examen_id,
            ':categoria_id' => $categoria_id,
            ':enunciado' => $pregunta['enunciado'],
            ':tipo_pregunta' => $pregunta['tipo_pregunta'],
            ':puntaje' => $pregunta['puntaje'],
            ':dificultad' => $pregunta['dificultad'],
            ':es_critica' => $pregunta['es_critica']
        ]);
        
        $pregunta_id = $pdo->lastInsertId();
        echo "✅ Pregunta " . ($index + 1) . " creada con ID: $pregunta_id\n";
        
        // Crear respuestas para la pregunta
        if ($pregunta['tipo_pregunta'] === 'multiple') {
            $respuestas = [
                ['texto' => '30 km/h', 'es_correcta' => 0],
                ['texto' => '40 km/h', 'es_correcta' => 1],
                ['texto' => '50 km/h', 'es_correcta' => 0]
            ];
        } else {
            $respuestas = [
                ['texto' => 'Verdadero', 'es_correcta' => 1],
                ['texto' => 'Falso', 'es_correcta' => 0]
            ];
        }
        
        foreach ($respuestas as $respuesta) {
            $sql = "INSERT INTO respuestas (pregunta_id, texto, es_correcta, created_at, updated_at) 
                    VALUES (:pregunta_id, :texto, :es_correcta, NOW(), NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':pregunta_id' => $pregunta_id,
                ':texto' => $respuesta['texto'],
                ':es_correcta' => $respuesta['es_correcta']
            ]);
        }
        echo "✅ Respuestas creadas para la pregunta\n";
    }
    
    echo "\n🎉 Examen de prueba creado exitosamente!\n";
    echo "📊 Resumen:\n";
    echo "- Examen ID: $examen_id\n";
    echo "- Categoría: {$categorias[0]['codigo']} - {$categorias[0]['nombre']}\n";
    echo "- Preguntas: " . count($preguntas) . "\n";
    echo "- Tiempo límite: 30 minutos\n";
    echo "- Puntaje mínimo: 70%\n";
    
    // Verificar que el examen se puede recuperar
    $stmt = $pdo->query("SELECT * FROM examenes WHERE examen_id = $examen_id");
    $examen = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($examen) {
        echo "\n✅ Verificación: El examen se puede recuperar correctamente\n";
        echo "- Título: {$examen['titulo']}\n";
        echo "- Estado: {$examen['estado']}\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
