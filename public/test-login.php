<?php
// Script para probar el login real

$url = 'http://examen.test/api/auth/login';

$data = json_encode([
    'dni' => '12345678' // DNI de prueba
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data),
    'Origin: http://localhost:4200'
]);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

header('Content-Type: text/plain; charset=utf-8');
echo "--- RESPUESTA COMPLETA ---\n";
echo $response;
echo "\n--- INFO CURL ---\n";
print_r($info); 