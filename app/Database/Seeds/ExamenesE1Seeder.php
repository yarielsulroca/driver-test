<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\ExamenModel;

class ExamenesE1Seeder extends Seeder
{
    public function run()
    {
        $examenModel = new ExamenModel();

        $examenes = [
            [
                'escuela_id' => 1,
                'categoria_id' => 1, // Categoría E1
                'nombre' => 'Examen Teórico Categoría E1 - Vehículos con Remolque',
                'descripcion' => 'Evaluación teórica para la obtención de licencia E1 que habilita a conducir vehículos de clase C o D con remolque.',
                'fecha_inicio' => date('Y-m-d H:i:s'),
                'fecha_fin' => date('Y-m-d H:i:s', strtotime('+6 months')),
                'duracion_minutos' => 60,
                'puntaje_minimo' => 75,
                'numero_preguntas' => 20
            ],
            [
                'escuela_id' => 2,
                'categoria_id' => 1, // Categoría E1
                'nombre' => 'Evaluación Práctica E1 - Maniobras con Remolque',
                'descripcion' => 'Examen práctico de maniobras y conducción de vehículos con remolque para licencia E1.',
                'fecha_inicio' => date('Y-m-d H:i:s'),
                'fecha_fin' => date('Y-m-d H:i:s', strtotime('+6 months')),
                'duracion_minutos' => 90,
                'puntaje_minimo' => 80,
                'numero_preguntas' => 15
            ],
            [
                'escuela_id' => 3,
                'categoria_id' => 1, // Categoría E1
                'nombre' => 'Certificación Final E1 - Teórico-Práctico',
                'descripcion' => 'Evaluación final que combina aspectos teóricos y prácticos para la certificación de licencia E1.',
                'fecha_inicio' => date('Y-m-d H:i:s'),
                'fecha_fin' => date('Y-m-d H:i:s', strtotime('+6 months')),
                'duracion_minutos' => 120,
                'puntaje_minimo' => 85,
                'numero_preguntas' => 25
            ]
        ];

        foreach ($examenes as $examen) {
            $examenModel->insert($examen);
        }
    }
} 