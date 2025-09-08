<?php

// Script de diagnóstico completo para el problema de categorías
echo "=== DIAGNÓSTICO COMPLETO - PROBLEMA CATEGORÍAS ===\n\n";

// 1. Verificar base de datos
echo "1. VERIFICACIÓN DE BASE DE DATOS:\n";
echo "--------------------------------\n";

$host = 'localhost';
$dbname = 'examen';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar tabla categorías
    $stmt = $pdo->query("SHOW TABLES LIKE 'categorias'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'categorias' existe\n";
        
        // Contar registros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "✅ Total de registros: $count\n";
        
        // Mostrar estructura
        $stmt = $pdo->query("DESCRIBE categorias");
        $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "✅ Campos de la tabla:\n";
        foreach ($fields as $field) {
            echo "   - {$field['Field']}: {$field['Type']}\n";
        }
        
    } else {
        echo "❌ Tabla 'categorias' NO existe\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Verificar servidor web
echo "2. VERIFICACIÓN DE SERVIDOR WEB:\n";
echo "--------------------------------\n";

$url = 'http://examen.test/api/categorias';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Error de conexión: $error\n";
} else {
    echo "✅ Servidor web responde\n";
    echo "✅ Código HTTP: $httpCode\n";
    
    if ($httpCode == 200) {
        echo "✅ Endpoint funcionando correctamente\n";
    } else {
        echo "⚠️ Endpoint devuelve código: $httpCode\n";
    }
}

echo "\n";

// 3. Verificar CORS
echo "3. VERIFICACIÓN DE CORS:\n";
echo "------------------------\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Origin: http://localhost:4200',
    'Access-Control-Request-Method: GET',
    'Access-Control-Request-Headers: Content-Type, Accept'
]);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "✅ CORS configurado correctamente\n";
} else {
    echo "⚠️ CORS puede tener problemas (código: $httpCode)\n";
}

echo "\n";

// 4. Verificar archivos de configuración
echo "4. VERIFICACIÓN DE ARCHIVOS:\n";
echo "-----------------------------\n";

$files = [
    'app/Config/Cors.php' => 'Configuración CORS',
    'app/Config/Routes.php' => 'Rutas API',
    'app/Controllers/CategoriaController.php' => 'Controlador Categorías',
    'frontend-examen/proxy.conf.json' => 'Configuración Proxy Angular',
    'frontend-examen/angular.json' => 'Configuración Angular'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description: $file\n";
    } else {
        echo "❌ $description: $file (NO EXISTE)\n";
    }
}

echo "\n";

// 5. Verificar puertos
echo "5. VERIFICACIÓN DE PUERTOS:\n";
echo "---------------------------\n";

$ports = [
    80 => 'HTTP (Apache/Nginx)',
    4200 => 'Angular Dev Server',
    3306 => 'MySQL'
];

foreach ($ports as $port => $service) {
    $connection = @fsockopen('localhost', $port, $errno, $errstr, 1);
    if (is_resource($connection)) {
        echo "✅ Puerto $port ($service): ACTIVO\n";
        fclose($connection);
    } else {
        echo "❌ Puerto $port ($service): INACTIVO\n";
    }
}

echo "\n";

// 6. Recomendaciones
echo "6. RECOMENDACIONES:\n";
echo "-------------------\n";

echo "1. Si el servidor Angular no está corriendo:\n";
echo "   - Ejecutar: cd frontend-examen && npm start\n";
echo "   - Verificar que no haya errores en la consola\n\n";

echo "2. Si el proxy no funciona:\n";
echo "   - Verificar que proxy.conf.json esté en la raíz del proyecto Angular\n";
echo "   - Verificar que angular.json tenga la configuración del proxy\n";
echo "   - Reiniciar el servidor Angular después de cambios\n\n";

echo "3. Si hay problemas de CORS:\n";
echo "   - Verificar app/Config/Cors.php\n";
echo "   - Verificar app/Filters/Cors.php\n";
echo "   - Asegurar que las rutas tengan el filtro CORS aplicado\n\n";

echo "4. Para debugging:\n";
echo "   - Abrir DevTools del navegador (F12)\n";
echo "   - Ir a la pestaña Network\n";
echo "   - Intentar cargar las categorías\n";
echo "   - Verificar las peticiones y respuestas\n\n";

echo "=== FIN DEL DIAGNÓSTICO ===\n"; 