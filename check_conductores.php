<?php

// Cargar CodeIgniter 4
require_once 'vendor/autoload.php';

// Inicializar la aplicación
$app = \Config\Services::codeigniter();
$app->initialize();

// Conectar a la base de datos
$db = \Config\Database::connect();

// Verificar estructura de la tabla conductores
echo "=== ESTRUCTURA DE LA TABLA CONDUCTORES ===\n";
$fields = $db->getFieldNames('conductores');
foreach ($fields as $field) {
    $fieldData = $db->getFieldData('conductores', $field);
    echo "Campo: {$field}\n";
    if (!empty($fieldData)) {
        foreach ($fieldData as $data) {
            echo "  - Tipo: {$data->type}\n";
            echo "  - Null: " . ($data->nullable ? 'YES' : 'NO') . "\n";
            echo "  - Default: " . ($data->default ?? 'NULL') . "\n";
        }
    }
    echo "\n";
}

// Verificar si hay usuarios
echo "=== USUARIOS EN LA BASE DE DATOS ===\n";
$usuarios = $db->table('usuarios')->get()->getResultArray();
echo "Total usuarios: " . count($usuarios) . "\n";
if (!empty($usuarios)) {
    foreach ($usuarios as $usuario) {
        echo "ID: {$usuario['usuario_id']}, Nombre: {$usuario['nombre']}\n";
    }
}

// Verificar conductores existentes
echo "\n=== CONDUCTORES EXISTENTES ===\n";
$conductores = $db->table('conductores')->get()->getResultArray();
echo "Total conductores: " . count($conductores) . "\n";
if (!empty($conductores)) {
    foreach ($conductores as $conductor) {
        echo "ID: {$conductor['conductor_id']}, Nombre: {$conductor['nombre']}, DNI: {$conductor['dni']}\n";
    }
} 