<?php
/**
 * Script simple para probar el endpoint de escuelas
 */

echo "🧪 PROBANDO ENDPOINT DE ESCUELAS\n";
echo "================================\n\n";

// URL del endpoint
$url = 'https://examen.test/api/escuelas';

echo "📡 URL: $url\n\n";

// Configurar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Ejecutar la petición
echo "🔄 Ejecutando petición GET...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

echo "📊 Código de respuesta HTTP: $httpCode\n";

if ($error) {
    echo "❌ Error de cURL: $error\n";
} else {
    echo "✅ Respuesta recibida\n";
    
    // Separar headers del body
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    echo "\n📋 Headers:\n";
    echo $headers;
    
    echo "\n📄 Body:\n";
    echo $body;
    
    // Intentar decodificar JSON
    $jsonData = json_decode($body, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "\n✅ JSON válido\n";
        echo "📊 Estructura de datos:\n";
        print_r($jsonData);
    } else {
        echo "\n❌ Error al decodificar JSON: " . json_last_error_msg() . "\n";
    }
}

curl_close($ch);

echo "\n🏁 Prueba completada\n";
?>
