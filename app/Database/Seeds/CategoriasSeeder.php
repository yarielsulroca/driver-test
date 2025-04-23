<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\CategoriaModel;

class CategoriasSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            // Categorías de Motocicletas
            [
                'sigla' => 'A1',
                'nombre' => 'Motocicletas hasta 125cc',
                'descripcion' => 'Licencia para conducir motocicletas y similares con cilindrada hasta 125cc.',
                'requisitos' => "1. Tener 18 años cumplidos.\n2. Aprobar examen teórico.\n3. Aprobar examen práctico.\n4. No tener impedimentos físicos o mentales.",
                'edad_minima' => 18,
                'experiencia_requerida' => 0
            ],
            [
                'sigla' => 'A2',
                'nombre' => 'Motocicletas hasta 35kW',
                'descripcion' => 'Licencia para conducir motocicletas y similares con potencia hasta 35kW.',
                'requisitos' => "1. Tener 18 años cumplidos.\n2. Poseer licencia A1 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.",
                'edad_minima' => 18,
                'experiencia_requerida' => 2
            ],
            [
                'sigla' => 'A',
                'nombre' => 'Motocicletas sin restricción',
                'descripcion' => 'Licencia para conducir todo tipo de motocicletas sin restricción de potencia.',
                'requisitos' => "1. Tener 20 años cumplidos.\n2. Poseer licencia A2 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.",
                'edad_minima' => 20,
                'experiencia_requerida' => 2
            ],
            // Categorías de Automóviles
            [
                'sigla' => 'B1',
                'nombre' => 'Automóviles particulares',
                'descripcion' => 'Licencia para conducir automóviles y camionetas de uso particular.',
                'requisitos' => "1. Tener 18 años cumplidos.\n2. Aprobar examen teórico.\n3. Aprobar examen práctico.\n4. No tener impedimentos físicos o mentales.",
                'edad_minima' => 18,
                'experiencia_requerida' => 0
            ],
            [
                'sigla' => 'B2',
                'nombre' => 'Automóviles de servicio público',
                'descripcion' => 'Licencia para conducir vehículos de servicio público como taxis.',
                'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia B1 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de antecedentes penales.",
                'edad_minima' => 21,
                'experiencia_requerida' => 2
            ],
            // Categorías de Vehículos de carga
            [
                'sigla' => 'C1',
                'nombre' => 'Camiones ligeros',
                'descripcion' => 'Licencia para conducir camiones ligeros de hasta 3.5 toneladas.',
                'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia B1 por al menos 1 año.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.",
                'edad_minima' => 21,
                'experiencia_requerida' => 1
            ],
            [
                'sigla' => 'C2',
                'nombre' => 'Camiones pesados',
                'descripcion' => 'Licencia para conducir camiones rígidos de más de 3.5 toneladas.',
                'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia C1 por al menos 1 año.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.",
                'edad_minima' => 21,
                'experiencia_requerida' => 1
            ],
            [
                'sigla' => 'C3',
                'nombre' => 'Camiones articulados',
                'descripcion' => 'Licencia para conducir camiones con remolque o articulados.',
                'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia C2 por al menos 1 año.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.",
                'edad_minima' => 21,
                'experiencia_requerida' => 1
            ],
            // Categorías de Vehículos de pasajeros
            [
                'sigla' => 'D1',
                'nombre' => 'Microbuses',
                'descripcion' => 'Licencia para conducir microbuses de hasta 16 pasajeros.',
                'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia B2 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de antecedentes penales.",
                'edad_minima' => 21,
                'experiencia_requerida' => 2
            ],
            [
                'sigla' => 'D2',
                'nombre' => 'Buses',
                'descripcion' => 'Licencia para conducir buses de más de 16 pasajeros.',
                'requisitos' => "1. Tener 23 años cumplidos.\n2. Poseer licencia D1 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de antecedentes penales.",
                'edad_minima' => 23,
                'experiencia_requerida' => 2
            ],
            [
                'sigla' => 'D3',
                'nombre' => 'Buses articulados',
                'descripcion' => 'Licencia para conducir buses articulados.',
                'requisitos' => "1. Tener 23 años cumplidos.\n2. Poseer licencia D2 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de antecedentes penales.",
                'edad_minima' => 23,
                'experiencia_requerida' => 2
            ],
            // Categorías de Vehículos especiales
            [
                'sigla' => 'E1',
                'nombre' => 'Vehículos con Remolque',
                'descripcion' => 'Licencia para conducir vehículos de clase C o D con remolque.',
                'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia clase C o D.\n3. Aprobar examen teórico específico.\n4. Aprobar examen práctico.\n5. Certificado de aptitud física.",
                'edad_minima' => 21,
                'experiencia_requerida' => 1
            ],
            [
                'sigla' => 'E2',
                'nombre' => 'Vehículos de emergencia',
                'descripcion' => 'Licencia para conducir ambulancias, vehículos de bomberos y policía.',
                'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia B1 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de primeros auxilios.\n6. Certificado de antecedentes penales.",
                'edad_minima' => 21,
                'experiencia_requerida' => 2
            ],
            [
                'sigla' => 'E3',
                'nombre' => 'Vehículos de transporte especial',
                'descripcion' => 'Licencia para conducir vehículos de transporte de materiales peligrosos y especiales.',
                'requisitos' => "1. Tener 23 años cumplidos.\n2. Poseer licencia C2 o D2 por al menos 2 años.\n3. Aprobar examen teórico específico.\n4. Aprobar examen práctico.\n5. Certificado de manejo de materiales peligrosos.\n6. Certificado de antecedentes penales.",
                'edad_minima' => 23,
                'experiencia_requerida' => 2
            ]
        ];

        $model = new CategoriaModel();
        foreach ($categorias as $categoria) {
            $model->insert($categoria);
        }
    }
} 