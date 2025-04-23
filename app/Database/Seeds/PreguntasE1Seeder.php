<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\PreguntaModel;
use App\Models\RespuestaModel;

class PreguntasE1Seeder extends Seeder
{
    public function run()
    {
        $preguntas = [
            // Pregunta Crítica 1
            [
                'enunciado' => '¿Qué debe hacer si el remolque comienza a hacer "serpenteo" (movimiento lateral incontrolado) durante la marcha?',
                'tipo_pregunta' => 'multiple',
                'puntaje' => 4,
                'dificultad' => 'alta',
                'es_critica' => 1,
                'respuestas' => [
                    [
                        'texto' => 'Acelerar para estabilizar el remolque',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => 'Frenar bruscamente',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => 'Soltar el volante y dejar que el vehículo se estabilice solo',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => 'Reducir suavemente la velocidad y mantener el volante firme',
                        'es_correcta' => 1
                    ]
                ]
            ],
            // Pregunta Normal
            [
                'enunciado' => '¿Cuál es la distancia mínima de seguridad que debe mantener un vehículo con remolque en autopista?',
                'tipo_pregunta' => 'multiple',
                'puntaje' => 4,
                'dificultad' => 'media',
                'es_critica' => 0,
                'respuestas' => [
                    [
                        'texto' => '50 metros',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => '100 metros',
                        'es_correcta' => 1
                    ],
                    [
                        'texto' => '150 metros',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => '200 metros',
                        'es_correcta' => 0
                    ]
                ]
            ],
            // Pregunta Crítica 2
            [
                'enunciado' => '¿Qué debe hacer si nota que el remolque se está desacoplando durante la marcha?',
                'tipo_pregunta' => 'multiple',
                'puntaje' => 4,
                'dificultad' => 'alta',
                'es_critica' => 1,
                'respuestas' => [
                    [
                        'texto' => 'Continuar conduciendo hasta encontrar un lugar seguro',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => 'Detenerse inmediatamente en el lugar más seguro posible',
                        'es_correcta' => 1
                    ],
                    [
                        'texto' => 'Acelerar para llegar más rápido a destino',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => 'Intentar reacoplar el remolque mientras conduce',
                        'es_correcta' => 0
                    ]
                ]
            ],
            // Pregunta Normal
            [
                'enunciado' => '¿Qué documentación específica debe portar un conductor de vehículo con remolque?',
                'tipo_pregunta' => 'multiple',
                'puntaje' => 4,
                'dificultad' => 'alta',
                'es_critica' => 0,
                'respuestas' => [
                    [
                        'texto' => 'Solo la licencia de conducir',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => 'Licencia de conducir y cédula verde del remolque',
                        'es_correcta' => 1
                    ],
                    [
                        'texto' => 'Solo la cédula verde del remolque',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => 'Ninguna documentación adicional',
                        'es_correcta' => 0
                    ]
                ]
            ],
            // Pregunta Crítica 3
            [
                'enunciado' => '¿Qué debe hacer si el sistema de frenos del remolque falla durante la marcha?',
                'tipo_pregunta' => 'multiple',
                'puntaje' => 4,
                'dificultad' => 'alta',
                'es_critica' => 1,
                'respuestas' => [
                    [
                        'texto' => 'Usar solo el freno de mano del vehículo tractor',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => 'Reducir la velocidad gradualmente usando el freno motor y buscar un lugar seguro para detenerse',
                        'es_correcta' => 1
                    ],
                    [
                        'texto' => 'Frenar bruscamente para detener el vehículo lo antes posible',
                        'es_correcta' => 0
                    ],
                    [
                        'texto' => 'Continuar conduciendo hasta el próximo taller',
                        'es_correcta' => 0
                    ]
                ]
            ]
        ];

        $preguntaModel = new PreguntaModel();
        $respuestaModel = new RespuestaModel();

        foreach ($preguntas as $pregunta) {
            $respuestas = $pregunta['respuestas'];
            unset($pregunta['respuestas']);

            $pregunta['categoria_id'] = 1; // ID de la categoría E1
            $preguntaId = $preguntaModel->insert($pregunta);

            foreach ($respuestas as $respuesta) {
                $respuesta['pregunta_id'] = $preguntaId;
                $respuestaModel->insert($respuesta);
            }
        }
    }
} 