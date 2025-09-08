<?php
// Script temporal para verificar la base de datos
require_once 'vendor/autoload.php';

$db = \Config\Database::connect();

echo "=== Verificando usuarios ===\n";
$usuarios = $db->table('usuarios')->get()->getResultArray();
echo "Usuarios encontrados: " . count($usuarios) . "\n";
if (!empty($usuarios)) {
    echo "Primer usuario: " . json_encode($usuarios[0]) . "\n";
}

echo "\n=== Verificando conductores ===\n";
$conductores = $db->table('conductores')->get()->getResultArray();
echo "Conductores encontrados: " . count($conductores) . "\n";
if (!empty($conductores)) {
    echo "Primer conductor: " . json_encode($conductores[0]) . "\n";
}

echo "\n=== Verificando escuelas ===\n";
$escuelas = $db->table('escuelas')->get()->getResultArray();
echo "Escuelas encontradas: " . count($escuelas) . "\n";
if (!empty($escuelas)) {
    echo "Primera escuela: " . json_encode($escuelas[0]) . "\n";
} 