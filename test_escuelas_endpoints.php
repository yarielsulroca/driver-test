<?php

// Script para probar todos los endpoints de escuelas
$baseUrl = 'https://examen.test/api';

echo "🧪 Probando endpoints de escuelas...\n\n";

// 1. Probar GET /api/escuelas (listar)
echo "1️⃣ Probando GET /api/escuelas (listar escuelas):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/escuelas');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status Code: $httpCode\n";
echo "   Response: " . $response . "\n\n";

// 2. Probar POST /api/escuelas (crear)
echo "2️⃣ Probando POST /api/escuelas (crear escuela):\n";
$testData = [
    'nombre' => 'Oficina Central Rivadavia 375',
    'direccion' => 'Rivadavia 375 Buenos Aires',
    'ciudad' => 'Buenos Aires',
    'telefono' => '0230 15-435-1043',
    'email' => 'of1@gmail.com'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/escuelas');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status Code: $httpCode\n";
echo "   Request Data: " . json_encode($testData) . "\n";
echo "   Response: " . $response . "\n\n";

// 3. Probar GET /api/escuelas/1 (mostrar específica)
echo "3️⃣ Probando GET /api/escuelas/1 (mostrar escuela específica):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/escuelas/1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status Code: $httpCode\n";
echo "   Response: " . $response . "\n\n";

// 4. Probar PUT /api/escuelas/1 (actualizar)
echo "4️⃣ Probando PUT /api/escuelas/1 (actualizar escuela):\n";
$updateData = [
    'nombre' => 'Oficina Central Actualizada',
    'direccion' => 'Rivadavia 375 Buenos Aires',
    'telefono' => '0230 15-435-1043',
    'email' => 'of1_updated@gmail.com'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/escuelas/1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status Code: $httpCode\n";
echo "   Request Data: " . json_encode($updateData) . "\n";
echo "   Response: " . $response . "\n\n";

// 5. Probar DELETE /api/escuelas/1 (eliminar)
echo "5️⃣ Probando DELETE /api/escuelas/1 (eliminar escuela):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/escuelas/1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status Code: $httpCode\n";
echo "   Response: " . $response . "\n\n";

echo "✅ Pruebas completadas.\n";
?>