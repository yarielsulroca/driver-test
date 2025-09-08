<?php

echo "=== TEST ENDPOINT CATEGORÍAS ===\n\n";

// Test 1: Verificar que el servidor responde
echo "1. Probando conexión al servidor...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://examen.test/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Error de conexión: $error\n";
    exit;
} else {
    echo "✅ Servidor responde (código: $httpCode)\n";
}

// Test 2: Probar endpoint de categorías
echo "\n2. Probando endpoint /api/categorias...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://examen.test/api/categorias');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
curl_close($ch);

echo "Código HTTP: $httpCode\n";
echo "Headers de respuesta:\n$headers\n";

if ($httpCode == 200) {
    echo "✅ Endpoint funcionando correctamente\n";
    echo "Respuesta:\n$body\n";
} else {
    echo "❌ Endpoint devuelve código $httpCode\n";
    
    // Test 3: Probar otras rutas para comparar
    echo "\n3. Probando otras rutas para comparar...\n";
    
    $testUrls = [
        'http://examen.test/api/examenes' => 'Exámenes',
        'http://examen.test/api/conductores' => 'Conductores',
        'http://examen.test/api/preguntas' => 'Preguntas'
    ];
    
    foreach ($testUrls as $url => $name) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "$name: $httpCode\n";
    }
}

echo "\n=== FIN DEL TEST ===\n"; 