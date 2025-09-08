<?php

// Script para corregir la categoría con ID 1
$host = 'localhost';
$dbname = 'examen';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔧 Corrigiendo categoría con ID 1...\n";
    
    // Actualizar la categoría con ID 1 para que sea B2 completa
    $sql = "UPDATE categorias SET 
            codigo = :codigo,
            nombre = :nombre,
            descripcion = :descripcion,
            requisitos = :requisitos,
            estado = :estado,
            updated_at = NOW()
            WHERE categoria_id = 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':codigo' => 'B2',
        ':nombre' => 'Automóviles y camiones livianos',
        ':descripcion' => 'Licencia para conducir automóviles particulares y camiones de hasta 3500 kg de peso total',
        ':requisitos' => 'Edad mínima 18 años. Examen teórico y práctico. Certificado médico. Licencia B1 por al menos 1 año.',
        ':estado' => 'activo'
    ]);
    
    echo "✅ Categoría B2 corregida exitosamente\n";
    
    // Verificar el resultado
    $stmt = $pdo->query("SELECT * FROM categorias WHERE categoria_id = 1");
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "📋 Categoría corregida:\n";
    echo "- ID: " . $categoria['categoria_id'] . "\n";
    echo "- Código: " . $categoria['codigo'] . "\n";
    echo "- Nombre: " . $categoria['nombre'] . "\n";
    echo "- Estado: " . $categoria['estado'] . "\n";
    
    // Verificar que no hay más categorías con campos vacíos
    echo "\n🔍 Verificando que no hay más campos vacíos...\n";
    $stmt = $pdo->query("SELECT categoria_id, codigo, nombre FROM categorias WHERE codigo = '' OR nombre = '' OR descripcion = ''");
    $vacios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($vacios) == 0) {
        echo "✅ No hay más categorías con campos vacíos\n";
    } else {
        echo "⚠️  Categorías con campos vacíos encontradas:\n";
        foreach ($vacios as $vacio) {
            echo "- ID: " . $vacio['categoria_id'] . ", Código: '" . $vacio['codigo'] . "', Nombre: '" . $vacio['nombre'] . "'\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
