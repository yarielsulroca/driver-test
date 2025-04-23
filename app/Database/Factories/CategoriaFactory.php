<?php

namespace App\Database\Factories;

use App\Models\CategoriaModel;
use Faker\Generator;

class CategoriaFactory extends Factory
{
    protected $model = CategoriaModel::class;

    public function definition()
    {
        return [
            'sigla' => $this->faker->unique()->randomElement(['A1', 'A2', 'A', 'B1', 'B2', 'C1', 'C2', 'C3', 'D1', 'D2', 'D3', 'E1', 'E2', 'E3']),
            'nombre' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(3),
            'requisitos' => $this->faker->paragraph(2),
            'edad_minima' => $this->faker->numberBetween(18, 21),
            'experiencia_requerida' => $this->faker->numberBetween(0, 2),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

    public function e1()
    {
        return $this->state(function (array $attributes) {
            return [
                'sigla' => 'E1',
                'nombre' => 'Vehículos con Remolque',
                'descripcion' => 'Habilita para conducir vehículos de clase C o D, con uno o más remolques o articulaciones. Incluye camiones con acoplados o semiacoplado.',
                'requisitos' => '1. Tener licencia clase C o D vigente por al menos 1 año.
2. Realizar curso específico de conducción con remolques.
3. Aprobar examen teórico sobre normativa de remolques.
4. Aprobar examen práctico de conducción con remolque.',
                'edad_minima' => 21,
                'experiencia_requerida' => 1,
            ];
        });
    }
} 