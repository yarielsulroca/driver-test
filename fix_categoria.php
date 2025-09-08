<?php
$pdo = new PDO('mysql:host=localhost;dbname=examen;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "🔧 Corrigiendo categoría ID 1...\n";

// Actualizar la categoría
$sql = "UPDATE categorias SET 
        codigo = 'B2',
        nombre = 'Automóviles y camiones livianos',
        descripcion = 'Licencia para conducir automóviles particulares y camiones de hasta 3500 kg de peso total',
        requisitos = 'Edad mínima 18 años. Examen teórico y práctico. Certificado médico. Licencia B1 por al menos 1 año.',
        estado = 'activo',
        updated_at = NOW()
        WHERE categoria_id = 1";

$pdo->exec($sql);
echo "✅ Categoría corregida\n";

// Verificar
$stmt = $pdo->query("SELECT categoria_id, codigo, nombre, estado FROM categorias WHERE categoria_id = 1");
$cat = $stmt->fetch(PDO::FETCH_ASSOC);
echo "📋 Resultado: ID {$cat['categoria_id']}, Código: {$cat['codigo']}, Nombre: {$cat['nombre']}, Estado: {$cat['estado']}\n";
