<?php
// Archivo de prueba para verificar CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, PATCH');
header('Access-Control-Max-Age: 86400');

// Manejar peticiones OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Log de la petición
file_put_contents(__DIR__.'/../writable/cors_test.log', 'Test CORS ejecutado: ' . date('c') . ' - URI: ' . $_SERVER['REQUEST_URI'] . ' - Method: ' . $_SERVER['REQUEST_METHOD'] . PHP_EOL, FILE_APPEND);

// Respuesta de prueba
echo json_encode([
    'status' => 'success',
    'message' => 'Test CORS funcionando',
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI'],
    'timestamp' => date('c')
]); 