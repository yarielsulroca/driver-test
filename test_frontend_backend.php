<?php

// Script para probar la conexión entre frontend y backend
echo "🧪 Probando conexión entre frontend y backend...\n\n";

// Simular una petición POST al endpoint de exámenes
$url = 'http://examen.test/api/examenes';
$data = [
    'nombre' => 'Examen de Prueba Frontend',
    'descripcion' => 'Examen de prueba desde el frontend',
    'categorias' => [2], // Categoría A1
    'preguntas' => [
        [
            'categoria_id' => 2,
            'enunciado' => '¿Cuál es la velocidad máxima en zona urbana?',
            'tipo' => 'multiple',
            'dificultad' => 'medio',
            'puntaje' => 10,
            'es_critica' => false,
            'respuestas' => [
                ['texto' => '30 km/h', 'es_correcta' => false],
                ['texto' => '40 km/h', 'es_correcta' => true],
                ['texto' => '50 km/h', 'es_correcta' => false]
            ]
        ]
    ],
    'duracion_minutos' => 30,
    'puntaje_minimo' => 70
];

echo "📡 Enviando petición POST a: $url\n";
echo "📋 Datos a enviar:\n";
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

// Configurar la petición cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Ejecutar la petición
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "📊 Respuesta del servidor:\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ Error cURL: $error\n";
} else {
    echo "✅ Respuesta recibida:\n";
    $responseData = json_decode($response, true);
    if ($responseData) {
        echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Respuesta no válida JSON: $response\n";
    }
}

// Probar también el endpoint GET
echo "\n🔍 Probando endpoint GET...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://examen.test/api/examenes');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "GET /examenes - HTTP Code: $httpCode\n";
if ($response) {
    $data = json_decode($response, true);
    if ($data && isset($data['status']) && $data['status'] === 'success') {
        echo "✅ GET exitoso - Exámenes encontrados: " . count($data['data']['examenes'] ?? []) . "\n";
    } else {
        echo "⚠️ GET con problemas: " . substr($response, 0, 200) . "...\n";
    }
}
