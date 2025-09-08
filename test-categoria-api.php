<?php

// Script para probar el endpoint de categorías
$url = 'http://localhost/examen/public/api/categorias';

// Datos de prueba para crear una categoría
$data = [
    'codigo' => 'TEST1',
    'nombre' => 'Categoría de Prueba',
    'descripcion' => 'Esta es una categoría de prueba para verificar el endpoint',
    'requisitos' => '["Edad mínima: 18 años", "Licencia previa: B"]',
    'estado' => 'activo'
];

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
curl_setopt($ch, CURLOPT_HEADER, true);

// Ejecutar la petición
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Separar headers y body
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "=== PRUEBA DE ENDPOINT CATEGORÍAS ===\n";
echo "URL: $url\n";
echo "Datos enviados: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
echo "Código HTTP: $httpCode\n";
echo "Headers de respuesta:\n$headers\n";
echo "Body de respuesta:\n$body\n";

// También probar GET para listar categorías
echo "\n=== PRUEBA GET CATEGORÍAS ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Código HTTP: $httpCode\n";
echo "Respuesta:\n$response\n";
?> 