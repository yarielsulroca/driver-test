<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\CategoriaModel;

class CategoriasSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            // Categoría E1 (Vehículos con Remolque)
            [
                'sigla' => 'E1',
                'nombre' => 'Vehículos con Remolque',
                'descripcion' => 'Habilita para conducir vehículos de clase C o D, con uno o más remolques o articulaciones. Incluye camiones con acoplados o semiacoplado.',
                'requisitos' => '1. Tener licencia clase C o D vigente por al menos 1 año.
2. Realizar curso específico de conducción con remolques.
3. Aprobar examen teórico sobre normativa de remolques.
4. Aprobar examen práctico de conducción con remolque.',
                'edad_minima' => 21,
                'experiencia_requerida' => 1
            ],
            // Categoría A (Motocicletas)
            [
                'sigla' => 'A',
                'nombre' => 'Motocicletas',
                'descripcion' => 'Habilita para conducir motocicletas sin restricción de cilindrada.',
                'requisitos' => '1. Tener 18 años cumplidos.
2. Aprobar examen teórico de normas de tránsito.
3. Aprobar examen práctico de conducción.',
                'edad_minima' => 18,
                'experiencia_requerida' => 0
            ],
            // Categoría B (Automóviles)
            [
                'sigla' => 'B',
                'nombre' => 'Automóviles',
                'descripcion' => 'Habilita para conducir automóviles, camionetas y utilitarios.',
                'requisitos' => '1. Tener 17 años cumplidos.
2. Aprobar examen teórico de normas de tránsito.
3. Aprobar examen práctico de conducción.',
                'edad_minima' => 17,
                'experiencia_requerida' => 0
            ],
            // Categoría C (Camiones)
            [
                'sigla' => 'C',
                'nombre' => 'Camiones',
                'descripcion' => 'Habilita para conducir camiones y vehículos de carga.',
                'requisitos' => '1. Tener 21 años cumplidos.
2. Tener licencia clase B por al menos 1 año.
3. Aprobar examen teórico específico.
4. Aprobar examen práctico de conducción.',
                'edad_minima' => 21,
                'experiencia_requerida' => 1
            ],
            // Categoría D (Transporte de Pasajeros)
            [
                'sigla' => 'D',
                'nombre' => 'Transporte de Pasajeros',
                'descripcion' => 'Habilita para conducir vehículos de transporte de pasajeros.',
                'requisitos' => '1. Tener 21 años cumplidos.
2. Tener licencia clase B por al menos 2 años.
3. Aprobar examen teórico específico.
4. Aprobar examen práctico de conducción.
5. Certificado de antecedentes penales.',
                'edad_minima' => 21,
                'experiencia_requerida' => 2
            ]
        ];

        $model = new CategoriaModel();
        
        foreach ($categorias as $categoria) {
            $model->insert($categoria);
        }
    }
} 