<?php
/**
 * Script para crear la tabla escuelas (oficinas de tránsito)
 * y establecer las relaciones necesarias
 */

try {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=examen', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🏗️ CREANDO TABLA ESCUELAS (OFICINAS DE TRÁNSITO)\n";
    echo "================================================\n\n";

    // PASO 1: Crear tabla escuelas
    echo "📋 PASO 1: Creando tabla 'escuelas'\n";
    
    $sqlCrearEscuelas = "CREATE TABLE IF NOT EXISTS escuelas (
        escuela_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        direccion VARCHAR(255) NOT NULL,
        ciudad VARCHAR(100) NOT NULL,
        provincia VARCHAR(100) NOT NULL,
        codigo_postal VARCHAR(10),
        telefono VARCHAR(20),
        email VARCHAR(100),
        horario_atencion VARCHAR(200),
        estado ENUM('activo', 'inactivo', 'mantenimiento') DEFAULT 'activo',
        capacidad_diaria INT DEFAULT 50,
        servicios_disponibles TEXT,
        coordenadas_lat DECIMAL(10,8),
        coordenadas_lng DECIMAL(11,8),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_estado (estado),
        INDEX idx_ciudad (ciudad),
        INDEX idx_provincia (provincia)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sqlCrearEscuelas);
    echo "✅ Tabla 'escuelas' creada exitosamente\n";

    // PASO 2: Crear tabla conductor_escuela (relación muchos a muchos)
    echo "\n👤 PASO 2: Creando tabla 'conductor_escuela'\n";
    
    $sqlCrearConductorEscuela = "CREATE TABLE IF NOT EXISTS conductor_escuela (
        conductor_escuela_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        conductor_id INT(11) UNSIGNED NOT NULL,
        escuela_id INT(11) UNSIGNED NOT NULL,
        fecha_asignacion DATE NOT NULL,
        fecha_vencimiento DATE,
        estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo',
        observaciones TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_conductor_escuela (conductor_id, escuela_id),
        INDEX idx_conductor (conductor_id),
        INDEX idx_escuela (escuela_id),
        INDEX idx_estado (estado)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sqlCrearConductorEscuela);
    echo "✅ Tabla 'conductor_escuela' creada exitosamente\n";

    // PASO 3: Crear tabla conductores
    echo "\n👤 PASO 3: Creando tabla 'conductores'\n";
    
    $sqlCrearConductores = "CREATE TABLE IF NOT EXISTS conductores (
        conductor_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT(11) UNSIGNED NOT NULL,
        licencia VARCHAR(50) NOT NULL,
        fecha_vencimiento DATE NOT NULL,
        estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo',
        categoria_principal VARCHAR(10),
        fecha_registro DATE DEFAULT (CURRENT_DATE),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_usuario (usuario_id),
        INDEX idx_estado (estado),
        INDEX idx_licencia (licencia)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sqlCrearConductores);
    echo "✅ Tabla 'conductores' creada exitosamente\n";

    // PASO 4: Crear tabla usuarios (si no existe)
    echo "\n👤 PASO 4: Verificando tabla 'usuarios'\n";
    
    $sqlCrearUsuarios = "CREATE TABLE IF NOT EXISTS usuarios (
        usuario_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        apellido VARCHAR(100) NOT NULL,
        dni VARCHAR(20) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        estado ENUM('activo', 'inactivo', 'pendiente') DEFAULT 'activo',
        rol_id INT(11) UNSIGNED,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_dni (dni),
        INDEX idx_email (email),
        INDEX idx_estado (estado)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sqlCrearUsuarios);
    echo "✅ Tabla 'usuarios' creada/verificada exitosamente\n";

    // PASO 5: Insertar datos de ejemplo para escuelas
    echo "\n📝 PASO 5: Insertando datos de ejemplo para escuelas\n";
    
    $sqlInsertarEscuelas = "INSERT IGNORE INTO escuelas (nombre, direccion, ciudad, provincia, telefono, email, horario_atencion) VALUES
        ('Oficina de Tránsito Centro', 'Av. San Martín 1234', 'Buenos Aires', 'Buenos Aires', '011-1234-5678', 'centro@transito.gob.ar', 'Lunes a Viernes 8:00-16:00'),
        ('Oficina de Tránsito Norte', 'Av. Libertador 5678', 'Buenos Aires', 'Buenos Aires', '011-8765-4321', 'norte@transito.gob.ar', 'Lunes a Viernes 8:00-16:00'),
        ('Oficina de Tránsito Sur', 'Av. 9 de Julio 9012', 'Buenos Aires', 'Buenos Aires', '011-2109-8765', 'sur@transito.gob.ar', 'Lunes a Viernes 8:00-16:00'),
        ('Oficina de Tránsito Oeste', 'Av. Rivadavia 3456', 'Buenos Aires', 'Buenos Aires', '011-6543-2109', 'oeste@transito.gob.ar', 'Lunes a Viernes 8:00-16:00'),
        ('Oficina de Tránsito Este', 'Av. Corrientes 7890', 'Buenos Aires', 'Buenos Aires', '011-0987-6543', 'este@transito.gob.ar', 'Lunes a Viernes 8:00-16:00')";

    $pdo->exec($sqlInsertarEscuelas);
    echo "✅ Datos de ejemplo insertados en escuelas\n";

    // PASO 6: Verificar estructura final
    echo "\n📋 PASO 6: Verificando estructura final\n";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'escuelas'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'escuelas' existe\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM escuelas");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "Total de escuelas: $total\n";
        
        $stmt = $pdo->query("SELECT nombre, ciudad, estado FROM escuelas LIMIT 3");
        $ejemplos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Ejemplos de escuelas:\n";
        foreach ($ejemplos as $ejemplo) {
            echo "  - {$ejemplo['nombre']} en {$ejemplo['ciudad']} (Estado: {$ejemplo['estado']})\n";
        }
    }

    echo "\n🎉 ¡TABLA ESCUELAS CREADA EXITOSAMENTE!\n";
    echo "========================================\n";
    echo "✅ Tabla 'escuelas' (oficinas de tránsito) creada\n";
    echo "✅ Tabla 'conductor_escuela' (relación) creada\n";
    echo "✅ Tabla 'conductores' creada\n";
    echo "✅ Tabla 'usuarios' verificada\n";
    echo "✅ Datos de ejemplo insertados\n";
    echo "✅ Relaciones muchos a muchos establecidas\n";

} catch (PDOException $e) {
    echo "❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "\n";
    echo "Código de error: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
}
?>
