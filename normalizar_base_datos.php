<?php

// Script para normalizar la base de datos
$host = 'localhost';
$dbname = 'examen';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔧 NORMALIZANDO BASE DE DATOS\n";
    echo "==============================\n\n";
    
    // 1. Verificar estructura de tablas principales
    echo "📋 1. VERIFICANDO ESTRUCTURA DE TABLAS...\n";
    
    $tablas = ['categorias', 'examenes', 'preguntas', 'respuestas', 'conductores', 'categorias_aprobadas'];
    foreach ($tablas as $tabla) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabla '$tabla' existe\n";
        } else {
            echo "❌ Tabla '$tabla' NO existe\n";
        }
    }
    
    // 2. Verificar campos de la tabla categorias
    echo "\n📋 2. VERIFICANDO CAMPOS DE CATEGORIAS...\n";
    $stmt = $pdo->query("DESCRIBE categorias");
    $campos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $camposRequeridos = ['categoria_id', 'codigo', 'nombre', 'descripcion', 'requisitos', 'estado'];
    foreach ($camposRequeridos as $campo) {
        if (in_array($campo, $campos)) {
            echo "✅ Campo '$campo' existe\n";
        } else {
            echo "❌ Campo '$campo' NO existe\n";
        }
    }
    
    // 3. Verificar campos de la tabla examenes
    echo "\n📋 3. VERIFICANDO CAMPOS DE EXAMENES...\n";
    $stmt = $pdo->query("DESCRIBE examenes");
    $campos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $camposRequeridos = ['examen_id', 'titulo', 'nombre', 'descripcion', 'tiempo_limite', 'duracion_minutos', 'puntaje_minimo', 'estado'];
    foreach ($camposRequeridos as $campo) {
        if (in_array($campo, $campos)) {
            echo "✅ Campo '$campo' existe\n";
        } else {
            echo "❌ Campo '$campo' NO existe\n";
        }
    }
    
    // 4. Verificar si existe campo supervisor_id (debe eliminarse)
    if (in_array('supervisor_id', $campos)) {
        echo "⚠️ Campo 'supervisor_id' existe - DEBE ELIMINARSE\n";
        
        // Eliminar el campo supervisor_id
        try {
            $pdo->exec("ALTER TABLE examenes DROP COLUMN supervisor_id");
            echo "✅ Campo 'supervisor_id' eliminado\n";
        } catch (PDOException $e) {
            echo "❌ Error eliminando supervisor_id: " . $e->getMessage() . "\n";
        }
    }
    
    // 5. Verificar tabla examen_escuela
    echo "\n📋 4. VERIFICANDO TABLA EXAMEN_ESCUELA...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'examen_escuela'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'examen_escuela' existe\n";
    } else {
        echo "❌ Tabla 'examen_escuela' NO existe - CREANDO...\n";
        
        try {
            $pdo->exec("CREATE TABLE examen_escuela (
                examen_escuela_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                examen_id INT(11) UNSIGNED NOT NULL,
                escuela_id INT(11) UNSIGNED NOT NULL,
                estado ENUM('activo', 'inactivo') DEFAULT 'activo',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE KEY unique_examen_escuela (examen_id, escuela_id),
                FOREIGN KEY (examen_id) REFERENCES examenes(examen_id) ON DELETE CASCADE,
                FOREIGN KEY (escuela_id) REFERENCES escuelas(escuela_id) ON DELETE CASCADE
            )");
            echo "✅ Tabla 'examen_escuela' creada\n";
        } catch (PDOException $e) {
            echo "❌ Error creando tabla: " . $e->getMessage() . "\n";
        }
    }
    
    // 6. Verificar tabla examen_categoria
    echo "\n📋 5. VERIFICANDO TABLA EXAMEN_CATEGORIA...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'examen_categoria'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'examen_categoria' existe\n";
    } else {
        echo "❌ Tabla 'examen_categoria' NO existe - CREANDO...\n";
        
        try {
            $pdo->exec("CREATE TABLE examen_categoria (
                examen_categoria_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                examen_id INT(11) UNSIGNED NOT NULL,
                categoria_id INT(11) UNSIGNED NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE KEY unique_examen_categoria (examen_id, categoria_id),
                FOREIGN KEY (examen_id) REFERENCES examenes(examen_id) ON DELETE CASCADE,
                FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id) ON DELETE CASCADE
            )");
            echo "✅ Tabla 'examen_categoria' creada\n";
        } catch (PDOException $e) {
            echo "❌ Error creando tabla: " . $e->getMessage() . "\n";
        }
    }
    
    // 7. Verificar tabla examen_conductor
    echo "\n📋 6. VERIFICANDO TABLA EXAMEN_CONDUCTOR...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'examen_conductor'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'examen_conductor' existe\n";
    } else {
        echo "❌ Tabla 'examen_conductor' NO existe - CREANDO...\n";
        
        try {
            $pdo->exec("CREATE TABLE examen_conductor (
                examen_conductor_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                examen_id INT(11) UNSIGNED NOT NULL,
                conductor_id INT(11) UNSIGNED NOT NULL,
                estado ENUM('pendiente', 'en_progreso', 'completado', 'aprobado', 'reprobado') DEFAULT 'pendiente',
                fecha_inicio DATETIME NULL,
                fecha_fin DATETIME NULL,
                puntaje_obtenido DECIMAL(5,2) NULL,
                tiempo_utilizado INT(11) NULL,
                intentos_restantes INT(11) DEFAULT 3,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE KEY unique_examen_conductor (examen_id, conductor_id),
                FOREIGN KEY (examen_id) REFERENCES examenes(examen_id) ON DELETE CASCADE,
                FOREIGN KEY (conductor_id) REFERENCES conductores(conductor_id) ON DELETE CASCADE
            )");
            echo "✅ Tabla 'examen_conductor' creada\n";
        } catch (PDOException $e) {
            echo "❌ Error creando tabla: " . $e->getMessage() . "\n";
        }
    }
    
    // 8. Verificar tabla respuestas_conductor
    echo "\n📋 7. VERIFICANDO TABLA RESPUESTAS_CONDUCTOR...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'respuestas_conductor'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'respuestas_conductor' existe\n";
    } else {
        echo "❌ Tabla 'respuestas_conductor' NO existe - CREANDO...\n";
        
        try {
            $pdo->exec("CREATE TABLE respuestas_conductor (
                respuesta_conductor_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                examen_conductor_id INT(11) UNSIGNED NOT NULL,
                pregunta_id INT(11) UNSIGNED NOT NULL,
                respuesta_id INT(11) UNSIGNED NULL,
                puntaje_obtenido DECIMAL(5,2) NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (examen_conductor_id) REFERENCES examen_conductor(examen_conductor_id) ON DELETE CASCADE,
                FOREIGN KEY (pregunta_id) REFERENCES preguntas(pregunta_id) ON DELETE CASCADE,
                FOREIGN KEY (respuesta_id) REFERENCES respuestas(respuesta_id) ON DELETE SET NULL
            )");
            echo "✅ Tabla 'respuestas_conductor' creada\n";
        } catch (PDOException $e) {
            echo "❌ Error creando tabla: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🎉 NORMALIZACIÓN COMPLETADA\n";
    echo "==========================\n";
    echo "✅ Base de datos normalizada y lista para usar\n";
    echo "📊 Todas las tablas y relaciones están configuradas correctamente\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
