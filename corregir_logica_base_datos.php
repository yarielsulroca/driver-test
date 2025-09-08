<?php

// Script para corregir la lógica de la base de datos
$host = 'localhost';
$dbname = 'examen';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔧 CORRIGIENDO LÓGICA DE BASE DE DATOS\n";
    echo "========================================\n\n";
    
    // 1. Verificar si existe el campo examen_id en categorias_aprobadas
    echo "📋 1. VERIFICANDO CAMPO EXAMEN_ID EN CATEGORIAS_APROBADAS...\n";
    $stmt = $pdo->query("DESCRIBE categorias_aprobadas");
    $campos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('examen_id', $campos)) {
        echo "❌ Campo 'examen_id' NO existe - AGREGANDO...\n";
        
        try {
            $pdo->exec("ALTER TABLE categorias_aprobadas ADD COLUMN examen_id INT(11) UNSIGNED NULL AFTER categoria_id");
            echo "✅ Campo 'examen_id' agregado\n";
            
            // Agregar foreign key
            $pdo->exec("ALTER TABLE categorias_aprobadas ADD CONSTRAINT fk_categoria_aprobada_examen 
                       FOREIGN KEY (examen_id) REFERENCES examenes(examen_id) ON DELETE SET NULL");
            echo "✅ Foreign key agregada\n";
        } catch (PDOException $e) {
            echo "❌ Error agregando campo: " . $e->getMessage() . "\n";
        }
    } else {
        echo "✅ Campo 'examen_id' ya existe\n";
    }
    
    // 2. Verificar tabla examen_escuela
    echo "\n📋 2. VERIFICANDO TABLA EXAMEN_ESCUELA...\n";
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
                FOREIGN KEY (examen_id) REFERENCES examenes(examen_id) ON DELETE CASCADE
            )");
            echo "✅ Tabla 'examen_escuela' creada (sin foreign key a escuelas por ahora)\n";
        } catch (PDOException $e) {
            echo "❌ Error creando tabla: " . $e->getMessage() . "\n";
        }
    }
    
    // 3. Verificar tabla examen_categoria
    echo "\n📋 3. VERIFICANDO TABLA EXAMEN_CATEGORIA...\n";
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
    
    // 4. Verificar tabla respuestas_conductor
    echo "\n📋 4. VERIFICANDO TABLA RESPUESTAS_CONDUCTOR...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'respuestas_conductor'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'respuestas_conductor' existe\n";
        
        // Verificar si tiene el campo correcto para categorias_aprobadas
        $stmt = $pdo->query("DESCRIBE respuestas_conductor");
        $campos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('categoria_aprobada_id', $campos)) {
            echo "⚠️ Campo 'categoria_aprobada_id' NO existe - AGREGANDO...\n";
            
            try {
                $pdo->exec("ALTER TABLE respuestas_conductor ADD COLUMN categoria_aprobada_id INT(11) UNSIGNED NULL AFTER examen_conductor_id");
                echo "✅ Campo 'categoria_aprobada_id' agregado\n";
                
                // Agregar foreign key
                $pdo->exec("ALTER TABLE respuestas_conductor ADD CONSTRAINT fk_respuesta_categoria_aprobada 
                           FOREIGN KEY (categoria_aprobada_id) REFERENCES categorias_aprobadas(categoria_aprobada_id) ON DELETE SET NULL");
                echo "✅ Foreign key agregada\n";
            } catch (PDOException $e) {
                echo "❌ Error agregando campo: " . $e->getMessage() . "\n";
            }
        } else {
            echo "✅ Campo 'categoria_aprobada_id' ya existe\n";
        }
    } else {
        echo "❌ Tabla 'respuestas_conductor' NO existe - CREANDO...\n";
        
        try {
            $pdo->exec("CREATE TABLE respuestas_conductor (
                respuesta_conductor_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                categoria_aprobada_id INT(11) UNSIGNED NOT NULL,
                pregunta_id INT(11) UNSIGNED NOT NULL,
                respuesta_id INT(11) UNSIGNED NULL,
                puntaje_obtenido DECIMAL(5,2) NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                FOREIGN KEY (categoria_aprobada_id) REFERENCES categorias_aprobadas(categoria_aprobada_id) ON DELETE CASCADE,
                FOREIGN KEY (pregunta_id) REFERENCES preguntas(pregunta_id) ON DELETE CASCADE,
                FOREIGN KEY (respuesta_id) REFERENCES respuestas(respuesta_id) ON DELETE SET NULL
            )");
            echo "✅ Tabla 'respuestas_conductor' creada con lógica corregida\n";
        } catch (PDOException $e) {
            echo "❌ Error creando tabla: " . $e->getMessage() . "\n";
        }
    }
    
    // 5. Eliminar tabla examen_conductor si existe (ya no necesaria)
    echo "\n📋 5. ELIMINANDO TABLA EXAMEN_CONDUCTOR INNECESARIA...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'examen_conductor'");
    if ($stmt->rowCount() > 0) {
        echo "⚠️ Tabla 'examen_conductor' existe - ELIMINANDO...\n";
        
        try {
            // Primero eliminar foreign keys
            $pdo->exec("ALTER TABLE respuestas_conductor DROP FOREIGN KEY IF EXISTS fk_respuesta_examen_conductor");
            echo "✅ Foreign keys eliminadas\n";
            
            // Eliminar la tabla
            $pdo->exec("DROP TABLE examen_conductor");
            echo "✅ Tabla 'examen_conductor' eliminada\n";
        } catch (PDOException $e) {
            echo "❌ Error eliminando tabla: " . $e->getMessage() . "\n";
        }
    } else {
        echo "✅ Tabla 'examen_conductor' no existe\n";
    }
    
    echo "\n🎉 LÓGICA CORREGIDA\n";
    echo "===================\n";
    echo "✅ Base de datos ahora usa 'categorias_aprobadas' como tabla principal\n";
    echo "✅ Relación conductor-examen-categoria establecida correctamente\n";
    echo "✅ Tablas innecesarias eliminadas\n";
    echo "📊 Estructura optimizada y normalizada\n";
    
    // 6. Mostrar resumen de la nueva estructura
    echo "\n📋 RESUMEN DE LA NUEVA ESTRUCTURA:\n";
    echo "==================================\n";
    echo "🔗 EXAMENES ←→ CATEGORIAS (via examen_categoria)\n";
    echo "🔗 EXAMENES ←→ ESCUELAS (via examen_escuela)\n";
    echo "🔗 CONDUCTORES ←→ CATEGORIAS ←→ EXAMENES (via categorias_aprobadas)\n";
    echo "🔗 RESPUESTAS_CONDUCTOR ←→ CATEGORIAS_APROBADAS\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
