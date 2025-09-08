<?php

// Script para ejecutar migraciones y seeders
echo "🔧 EJECUTANDO MIGRACIONES Y SEEDERS\n";
echo "====================================\n\n";

// 1. Ejecutar migración para actualizar tabla exámenes
echo "📋 1. Ejecutando migración para tabla exámenes...\n";
$output = shell_exec('cd /c/laragon/www/examen && php spark migrate --all');
echo $output . "\n";

// 2. Ejecutar seeder de escuelas
echo "📋 2. Ejecutando seeder de escuelas...\n";
$output = shell_exec('cd /c/laragon/www/examen && php spark db:seed EscuelasSeeder');
echo $output . "\n";

// 3. Verificar estructura de la tabla exámenes
echo "📋 3. Verificando estructura de tabla exámenes...\n";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=examen;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("DESCRIBE examenes");
    $campos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $camposRequeridos = [
        'examen_id', 'titulo', 'nombre', 'descripcion', 'tiempo_limite', 
        'duracion_minutos', 'puntaje_minimo', 'fecha_inicio', 'fecha_fin', 
        'numero_preguntas', 'estado', 'dificultad'
    ];
    
    foreach ($camposRequeridos as $campo) {
        if (in_array($campo, $campos)) {
            echo "✅ Campo '$campo' existe\n";
        } else {
            echo "❌ Campo '$campo' NO existe\n";
        }
    }
    
    // Verificar tabla escuelas
    echo "\n📋 4. Verificando tabla escuelas...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'escuelas'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'escuelas' existe\n";
        
        // Contar escuelas
        $stmt = $pdo->query("SELECT COUNT(*) FROM escuelas");
        $count = $stmt->fetchColumn();
        echo "📊 Número de escuelas: $count\n";
    } else {
        echo "❌ Tabla 'escuelas' NO existe\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 VERIFICACIÓN COMPLETADA\n";
echo "==========================\n";
