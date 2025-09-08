<?php
// Script para probar CORS desde el servidor

$url = 'http://examen.test/api/auth/login';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Origin: http://localhost:4200',
    'Access-Control-Request-Method: POST',
]);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

header('Content-Type: text/plain; charset=utf-8');
echo "--- HEADERS DE RESPUESTA ---\n";
echo $response;
echo "\n--- INFO CURL ---\n";
print_r($info); 