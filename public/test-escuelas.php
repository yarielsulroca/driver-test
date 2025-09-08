<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

// Simular datos de entrada
$input = '{"nombre":"Oficina de Tránsito Central","direccion":"Av. Principal 123","telefono":"123456789","email":"central@transito.com"}';

echo "Input JSON: " . $input . "\n\n";

// Decodificar JSON
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error decodificando JSON: " . json_last_error_msg() . "\n";
    exit;
}

echo "Datos decodificados:\n";
print_r($data);

// Verificar que los campos requeridos estén presentes
$required_fields = ['nombre', 'direccion', 'telefono', 'email'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    echo "Campos faltantes: " . implode(', ', $missing_fields) . "\n";
    exit;
}

echo "Todos los campos requeridos están presentes.\n";

// Validar email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo "Email inválido: " . $data['email'] . "\n";
    exit;
}

echo "Email válido.\n";

// Validar longitud de campos
$validations = [
    'nombre' => ['min' => 3, 'max' => 100],
    'direccion' => ['min' => 5, 'max' => 255],
    'telefono' => ['min' => 8, 'max' => 15],
    'email' => ['min' => 1, 'max' => 100]
];

foreach ($validations as $field => $rules) {
    $length = strlen($data[$field]);
    if ($length < $rules['min'] || $length > $rules['max']) {
        echo "Campo '$field' tiene longitud inválida: $length (debe estar entre {$rules['min']} y {$rules['max']})\n";
        exit;
    }
}

echo "Todas las validaciones pasaron.\n";

// Simular inserción en base de datos
$escuela_data = [
    'nombre' => $data['nombre'],
    'direccion' => $data['direccion'],
    'telefono' => $data['telefono'],
    'email' => $data['email'],
    'estado' => 'activo',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

echo "Datos preparados para inserción:\n";
print_r($escuela_data);

echo "\nSimulación completada exitosamente.\n";
?> 