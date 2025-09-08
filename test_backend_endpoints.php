<?php
/**
 * Script para probar todos los endpoints del backend
 * Verifica que las rutas, controladores y modelos estén funcionando
 */

echo "🧪 PROBANDO ENDPOINTS DEL BACKEND\n";
echo "==================================\n\n";

// Configuración
$baseUrl = 'http://localhost:8080/index.php/api';
$endpoints = [
    // Endpoints básicos
    'GET /' => '/',
    'GET /docs' => '/docs',
    
    // Endpoints de autenticación
    'POST /auth/registro' => '/auth/registro',
    'POST /auth/login' => '/auth/login',
    
    // Endpoints de categorías
    'GET /categorias' => '/categorias',
    'GET /categorias/1' => '/categorias/1',
    
    // Endpoints de escuelas
    'GET /escuelas' => '/escuelas',
    'GET /escuelas/1' => '/escuelas/1',
    
    // Endpoints de exámenes
    'GET /examenes' => '/examenes',
    'GET /examenes/1' => '/examenes/1',
    
    // Endpoints de preguntas
    'GET /preguntas' => '/preguntas',
    'GET /preguntas/1' => '/preguntas/1',
    
    // Endpoints de respuestas
    'GET /respuestas' => '/respuestas',
    'GET /respuestas/1' => '/respuestas/1',
    
    // Endpoints de perfiles
    'GET /perfiles' => '/perfiles',
    'GET /perfiles/estadisticas' => '/perfiles/estadisticas',
    
    // Endpoints de conductores
    'GET /conductores' => '/conductores',
    'GET /conductores/1' => '/conductores/1',
    
    // Endpoints de categorías aprobadas
    'GET /categorias-aprobadas' => '/categorias-aprobadas',
    'GET /categorias-aprobadas/estadisticas' => '/categorias-aprobadas/estadisticas',
    
    // Endpoints de resultados
    'GET /resultados' => '/resultados',
];

// Función para hacer peticiones HTTP
function testEndpoint($method, $url, $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error
    ];
}

// Función para analizar la respuesta
function analyzeResponse($method, $endpoint, $result) {
    $status = '';
    $message = '';
    
    if ($result['error']) {
        $status = '❌ ERROR DE CONEXIÓN';
        $message = $result['error'];
    } elseif ($result['http_code'] >= 200 && $result['http_code'] < 300) {
        $status = '✅ EXITOSO';
        $message = "HTTP {$result['http_code']} - Respuesta válida";
    } elseif ($result['http_code'] >= 400 && $result['http_code'] < 500) {
        $status = '⚠️ CLIENTE ERROR';
        $message = "HTTP {$result['http_code']} - Error del cliente (normal para endpoints que requieren auth)";
    } elseif ($result['http_code'] >= 500) {
        $status = '❌ SERVER ERROR';
        $message = "HTTP {$result['http_code']} - Error del servidor";
    } else {
        $status = '❓ DESCONOCIDO';
        $message = "HTTP {$result['http_code']} - Respuesta inesperada";
    }
    
    return [$status, $message];
}

// Probar endpoints
$results = [];
foreach ($endpoints as $description => $endpoint) {
    $method = explode(' ', $description)[0];
    $fullUrl = $baseUrl . $endpoint;
    
    echo "🔍 Probando: {$description}\n";
    echo "   URL: {$fullUrl}\n";
    
    $result = testEndpoint($method, $fullUrl);
    list($status, $message) = analyzeResponse($method, $endpoint, $result);
    
    echo "   Estado: {$status}\n";
    echo "   Mensaje: {$message}\n";
    
    if ($result['response']) {
        $responseData = json_decode($result['response'], true);
        if ($responseData && isset($responseData['data'])) {
            if (is_array($responseData['data'])) {
                echo "   Datos: " . count($responseData['data']) . " elementos\n";
            } else {
                echo "   Datos: " . substr($result['response'], 0, 100) . "...\n";
            }
        }
    }
    
    echo "\n";
    
    $results[] = [
        'endpoint' => $description,
        'url' => $fullUrl,
        'status' => $status,
        'message' => $message,
        'http_code' => $result['http_code']
    ];
}

// Resumen de resultados
echo "📊 RESUMEN DE PRUEBAS\n";
echo "=====================\n";

$exitosos = 0;
$errores = 0;
$warnings = 0;

foreach ($results as $result) {
    if (strpos($result['status'], '✅') !== false) {
        $exitosos++;
    } elseif (strpos($result['status'], '❌') !== false) {
        $errores++;
    } elseif (strpos($result['status'], '⚠️') !== false) {
        $warnings++;
    }
}

echo "✅ Exitosos: {$exitosos}\n";
echo "⚠️ Warnings: {$warnings}\n";
echo "❌ Errores: {$errores}\n";
echo "📊 Total: " . count($results) . "\n\n";

if ($errores === 0) {
    echo "🎉 ¡Todos los endpoints están funcionando correctamente!\n";
} else {
    echo "⚠️ Hay algunos problemas que necesitan atención.\n";
}

echo "\n🔧 RECOMENDACIONES:\n";
echo "- Los endpoints que devuelven 401/403 son normales si requieren autenticación\n";
echo "- Los endpoints que devuelven 404 pueden no tener datos de prueba\n";
echo "- Los endpoints que devuelven 500 indican errores del servidor\n";
?>
