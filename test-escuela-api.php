<?php
// Script para probar el endpoint de escuelas

$url = 'http://examen.test/api/escuelas';
$data = [
    'nombre' => 'Oficina de Tránsito Central',
    'direccion' => 'Av. Principal 123',
    'telefono' => '123456789',
    'email' => 'central@transito.com'
];

$json_data = json_encode($data);

echo "Enviando datos a: $url\n";
echo "Datos: " . $json_data . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($json_data)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Código de respuesta HTTP: $http_code\n";
echo "Respuesta completa:\n$response\n";
?> 