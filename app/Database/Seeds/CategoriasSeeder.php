<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    public function run()
    {
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

        // Insertar las categorías
        foreach ($categorias as $categoria) {
            $this->db->table('categorias')->insert($categoria);
        }

        echo "✅ Se han insertado " . count($categorias) . " categorías de licencias de conducción\n";
        echo "📋 Categorías incluidas:\n";
        echo "   - Motos (A1, A2, A3)\n";
        echo "   - Automóviles particulares (B1, B2)\n";
        echo "   - Camiones (C1, C2, C3)\n";
        echo "   - Ómnibus (D1, D2, D3)\n";
        echo "   - Maquinaria especial (E1, E2)\n";
        echo "   - Vehículos para discapacitados (F)\n";
        echo "   - Transporte profesional (G1, G2, G3)\n";
        echo "   - Licencias temporales (T1, T2)\n";
    }
} 