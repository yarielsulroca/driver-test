<?php

/**
 * Script para poblar la tabla de categorías con las licencias de conducción argentinas
 * Ejecutar desde la línea de comandos: php seed_categorias.php
 */

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'examen';
$username = 'root';
$password = '';

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🌱 Conectado a la base de datos. Iniciando inserción de categorías...\n\n";
    
    // Verificar si la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'categorias'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Error: La tabla 'categorias' no existe.\n";
        echo "Por favor, ejecuta las migraciones primero.\n";
        exit(1);
    }
    
    // Verificar si ya hay categorías
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if ($count > 0) {
        echo "⚠️  Advertencia: Ya existen $count categorías en la tabla.\n";
        echo "¿Deseas continuar y agregar más? (s/n): ";
        $handle = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($response) !== 's' && strtolower($response) !== 'si' && strtolower($response) !== 'y' && strtolower($response) !== 'yes') {
            echo "Operación cancelada.\n";
            exit(0);
        }
    }
    
    // Datos de las categorías según la ley argentina
    $categorias = [
        // CATEGORÍAS PARA VEHÍCULOS PARTICULARES
        [
            'codigo' => 'A1',
            'nombre' => 'Motos hasta 150cc',
            'descripcion' => 'Licencia para conducir motocicletas y motovehículos de hasta 150 centímetros cúbicos de cilindrada',
            'requisitos' => 'Edad mínima 16 años. Examen teórico y práctico. Certificado médico. No requiere experiencia previa.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'A2',
            'nombre' => 'Motos hasta 300cc',
            'descripcion' => 'Licencia para conducir motocicletas y motovehículos de hasta 300 centímetros cúbicos de cilindrada',
            'requisitos' => 'Edad mínima 17 años. Examen teórico y práctico. Certificado médico. Licencia A1 por al menos 1 año.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'A3',
            'nombre' => 'Motos sin límite de cilindrada',
            'descripcion' => 'Licencia para conducir motocicletas y motovehículos sin límite de cilindrada',
            'requisitos' => 'Edad mínima 18 años. Examen teórico y práctico. Certificado médico. Licencia A2 por al menos 1 año.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'B1',
            'nombre' => 'Automóviles particulares',
            'descripcion' => 'Licencia para conducir automóviles particulares, camionetas y utilitarios de hasta 3500 kg de peso total',
            'requisitos' => 'Edad mínima 17 años. Examen teórico y práctico. Certificado médico. No requiere experiencia previa.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'B2',
            'nombre' => 'Automóviles y camiones livianos',
            'descripcion' => 'Licencia para conducir automóviles particulares y camiones de hasta 3500 kg de peso total',
            'requisitos' => 'Edad mínima 18 años. Examen teórico y práctico. Certificado médico. Licencia B1 por al menos 1 año.',
            'estado' => 'activo'
        ],

        // CATEGORÍAS PARA VEHÍCULOS DE CARGA
        [
            'codigo' => 'C1',
            'nombre' => 'Camiones medianos',
            'descripcion' => 'Licencia para conducir camiones de más de 3500 kg hasta 8000 kg de peso total',
            'requisitos' => 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia B2 por al menos 2 años.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'C2',
            'nombre' => 'Camiones pesados',
            'descripcion' => 'Licencia para conducir camiones de más de 8000 kg de peso total',
            'requisitos' => 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia C1 por al menos 2 años.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'C3',
            'nombre' => 'Camiones con acoplado',
            'descripcion' => 'Licencia para conducir camiones con acoplado o semirremolque',
            'requisitos' => 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia C2 por al menos 2 años.',
            'estado' => 'activo'
        ],

        // CATEGORÍAS PARA VEHÍCULOS DE PASAJEROS
        [
            'codigo' => 'D1',
            'nombre' => 'Ómnibus medianos',
            'descripcion' => 'Licencia para conducir ómnibus de hasta 20 asientos para pasajeros',
            'requisitos' => 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia B2 por al menos 2 años.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'D2',
            'nombre' => 'Ómnibus grandes',
            'descripcion' => 'Licencia para conducir ómnibus de más de 20 asientos para pasajeros',
            'requisitos' => 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia D1 por al menos 2 años.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'D3',
            'nombre' => 'Ómnibus con acoplado',
            'descripcion' => 'Licencia para conducir ómnibus con acoplado o semirremolque',
            'requisitos' => 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia D2 por al menos 2 años.',
            'estado' => 'activo'
        ],

        // CATEGORÍAS ESPECIALES
        [
            'codigo' => 'E1',
            'nombre' => 'Tractores agrícolas',
            'descripcion' => 'Licencia para conducir tractores agrícolas y maquinaria agrícola',
            'requisitos' => 'Edad mínima 16 años. Examen teórico y práctico. Certificado médico. No requiere experiencia previa.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'E2',
            'nombre' => 'Maquinaria vial',
            'descripcion' => 'Licencia para conducir maquinaria vial y de construcción',
            'requisitos' => 'Edad mínima 18 años. Examen teórico y práctico. Certificado médico. Licencia B1 por al menos 1 año.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'F',
            'nombre' => 'Vehículos para discapacitados',
            'descripcion' => 'Licencia especial para conducir vehículos adaptados para personas con discapacidad',
            'requisitos' => 'Edad mínima 17 años. Examen teórico y práctico. Certificado médico especializado. Evaluación de capacidades.',
            'estado' => 'activo'
        ],

        // CATEGORÍAS PROFESIONALES
        [
            'codigo' => 'G1',
            'nombre' => 'Transporte de carga profesional',
            'descripcion' => 'Licencia profesional para transporte de carga en general',
            'requisitos' => 'Edad mínima 21 años. Examen teórico y práctico avanzado. Certificado médico. Licencia C2 por al menos 3 años. Curso de capacitación profesional.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'G2',
            'nombre' => 'Transporte de pasajeros profesional',
            'descripcion' => 'Licencia profesional para transporte de pasajeros en general',
            'requisitos' => 'Edad mínima 21 años. Examen teórico y práctico avanzado. Certificado médico. Licencia D2 por al menos 3 años. Curso de capacitación profesional.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'G3',
            'nombre' => 'Transporte de sustancias peligrosas',
            'descripcion' => 'Licencia especial para transporte de sustancias peligrosas y materiales tóxicos',
            'requisitos' => 'Edad mínima 23 años. Examen teórico y práctico especializado. Certificado médico. Licencia G1 o G2 por al menos 2 años. Curso de manejo de sustancias peligrosas.',
            'estado' => 'activo'
        ],

        // CATEGORÍAS TEMPORARIAS
        [
            'codigo' => 'T1',
            'nombre' => 'Licencia temporal de aprendizaje',
            'descripcion' => 'Licencia temporal para aprender a conducir vehículos de categoría B1',
            'requisitos' => 'Edad mínima 16 años. Certificado médico. Debe estar acompañado por un conductor con licencia válida. Válida por 1 año.',
            'estado' => 'activo'
        ],
        [
            'codigo' => 'T2',
            'nombre' => 'Licencia temporal de prueba',
            'descripcion' => 'Licencia temporal otorgada después de aprobar examen teórico, válida para práctica',
            'requisitos' => 'Examen teórico aprobado. Certificado médico. Debe estar acompañado por un conductor con licencia válida. Válida por 6 meses.',
            'estado' => 'activo'
        ]
    ];
    
    // Preparar la consulta de inserción
    $sql = "INSERT INTO categorias (codigo, nombre, descripcion, requisitos, estado, created_at, updated_at) 
            VALUES (:codigo, :nombre, :descripcion, :requisitos, :estado, NOW(), NOW())";
    
    $stmt = $pdo->prepare($sql);
    
    // Insertar cada categoría
    $insertadas = 0;
    foreach ($categorias as $categoria) {
        try {
            $stmt->execute([
                ':codigo' => $categoria['codigo'],
                ':nombre' => $categoria['nombre'],
                ':descripcion' => $categoria['descripcion'],
                ':requisitos' => $categoria['requisitos'],
                ':estado' => $categoria['estado']
            ]);
            $insertadas++;
            echo "✅ Insertada: {$categoria['codigo']} - {$categoria['nombre']}\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "⚠️  Ya existe: {$categoria['codigo']} - {$categoria['nombre']}\n";
            } else {
                echo "❌ Error al insertar {$categoria['codigo']}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n🎉 Proceso completado!\n";
    echo "📊 Total de categorías insertadas: $insertadas\n";
    echo "📋 Categorías incluidas:\n";
    echo "   - Motos (A1, A2, A3)\n";
    echo "   - Automóviles particulares (B1, B2)\n";
    echo "   - Camiones (C1, C2, C3)\n";
    echo "   - Ómnibus (D1, D2, D3)\n";
    echo "   - Maquinaria especial (E1, E2)\n";
    echo "   - Vehículos para discapacitados (F)\n";
    echo "   - Transporte profesional (G1, G2, G3)\n";
    echo "   - Licencias temporales (T1, T2)\n";
    
    // Verificar el total final
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias");
    $totalFinal = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "\n📈 Total de categorías en la base de datos: $totalFinal\n";
    
} catch (PDOException $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
    exit(1);
}
