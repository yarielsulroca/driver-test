<?php
/**
 * Script para generar contraseñas encriptadas
 * Ejecutar desde la línea de comandos: php GENERAR_CONTRASEÑAS.php
 */

echo "🔐 GENERADOR DE CONTRASEÑAS ENCRIPTADAS\n";
echo "=====================================\n\n";

// Contraseñas comunes que podrías querer usar
$contraseñas = [
    'admin123' => 'Contraseña para administradores',
    'password' => 'Contraseña por defecto',
    '12345678' => 'Contraseña numérica simple',
    'secret' => 'Contraseña secreta',
    'conductor123' => 'Contraseña para conductores',
    'supervisor123' => 'Contraseña para supervisores',
    'test123' => 'Contraseña de prueba'
];

echo "Contraseñas encriptadas para usar en la base de datos:\n";
echo "=====================================================\n\n";

foreach ($contraseñas as $contraseña => $descripcion) {
    $hash = password_hash($contraseña, PASSWORD_DEFAULT);
    echo "Contraseña: '{$contraseña}' ({$descripcion})\n";
    echo "Hash: {$hash}\n";
    echo "Comando SQL: UPDATE usuarios SET password = '{$hash}' WHERE email = 'usuario@ejemplo.com';\n";
    echo "---\n\n";
}

echo "📝 INSTRUCCIONES DE USO:\n";
echo "=======================\n";
echo "1. Copia el hash que necesites\n";
echo "2. Ejecuta el comando SQL correspondiente en tu base de datos\n";
echo "3. Los usuarios podrán iniciar sesión con la contraseña original\n\n";

echo "🔍 VERIFICACIÓN:\n";
echo "===============\n";
echo "Para verificar que el hash funciona, usa este código PHP:\n";
echo "if (password_verify('contraseña_original', 'hash_generado')) {\n";
echo "    echo 'Contraseña correcta';\n";
echo "} else {\n";
echo "    echo 'Contraseña incorrecta';\n";
echo "}\n\n";

echo "✅ Script completado!\n";
?>
