<?php

namespace App\Database\Factories;

use App\Models\ExamenModel;
use Faker\Generator;

class ExamenFactory extends Factory
{
    protected $model = ExamenModel::class;

    public function definition()
    {
        return [
            'escuela_id' => 1,
            'categoria_id' => 1,
            'nombre' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(2),
            'fecha_inicio' => $this->faker->dateTimeBetween('now', '+1 month'),
            'fecha_fin' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'duracion_minutos' => 60,
            'puntaje_minimo' => 70,
            'numero_preguntas' => 20,
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

    public function e1()
    {
        return $this->state(function (array $attributes) {
            return [
                'nombre' => 'Examen Teórico Categoría E1 - Vehículos con Remolque',
                'descripcion' => 'Examen teórico para la obtención de la licencia categoría E1, que habilita para conducir vehículos con remolque.',
                'duracion_minutos' => 90,
                'puntaje_minimo' => 80,
                'numero_preguntas' => 25,
            ];
        });
    }
} 