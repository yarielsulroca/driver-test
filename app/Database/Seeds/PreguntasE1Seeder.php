<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\PreguntaModel;
use App\Models\RespuestaModel;

class PreguntasE1Seeder extends Seeder
{
    public function run()
    {
        $preguntaModel = new PreguntaModel();
        $respuestaModel = new RespuestaModel();

        $preguntas = [
            [
                'categoria_id' => 1, // ID de la categoría E1
                'enunciado' => '¿Cuál es la distancia mínima de seguridad que debe mantener con el vehículo de adelante al conducir un vehículo con remolque?',
                'tipo_pregunta' => 'multiple',
                'puntaje' => 10,
                'dificultad' => 'media',
                'respuestas' => [
                    ['texto' => '50 metros', 'es_correcta' => true],
                    ['texto' => '30 metros', 'es_correcta' => false],
                    ['texto' => '20 metros', 'es_correcta' => false],
                    ['texto' => '10 metros', 'es_correcta' => false]
                ]
            ],
            [
                'categoria_id' => 1,
                'enunciado' => '¿Qué documentación adicional se requiere para conducir un vehículo con remolque?',
                'tipo_pregunta' => 'multiple',
                'puntaje' => 10,
                'dificultad' => 'media',
                'respuestas' => [
                    ['texto' => 'Permiso de circulación del remolque y seguro específico', 'es_correcta' => true],
                    ['texto' => 'Solo el permiso de conducir E1', 'es_correcta' => false],
                    ['texto' => 'No se requiere documentación adicional', 'es_correcta' => false],
                    ['texto' => 'Solo el seguro del vehículo principal', 'es_correcta' => false]
                ]
            ],
            [
                'categoria_id' => 1,
                'enunciado' => '¿Cuál es la velocidad máxima permitida para vehículos con remolque en autopista?',
                'tipo_pregunta' => 'multiple',
                'puntaje' => 10,
                'dificultad' => 'media',
                'respuestas' => [
                    ['texto' => '80 km/h', 'es_correcta' => true],
                    ['texto' => '100 km/h', 'es_correcta' => false],
                    ['texto' => '120 km/h', 'es_correcta' => false],
                    ['texto' => '90 km/h', 'es_correcta' => false]
                ]
            ],
            [
                'categoria_id' => 1,
                'enunciado' => '¿Qué precauciones especiales debe tomar al estacionar un vehículo con remolque?',
                'tipo_pregunta' => 'multiple',
                'puntaje' => 10,
                'dificultad' => 'alta',
                'respuestas' => [
                    ['texto' => 'Asegurar el remolque con calzos y activar el freno de mano del remolque', 'es_correcta' => true],
                    ['texto' => 'Solo activar el freno de mano del vehículo', 'es_correcta' => false],
                    ['texto' => 'Desconectar el remolque', 'es_correcta' => false],
                    ['texto' => 'No se requieren precauciones especiales', 'es_correcta' => false]
                ]
            ],
            [
                'categoria_id' => 1,
                'enunciado' => '¿Qué verificaciones debe realizar antes de iniciar un viaje con remolque?',
                'tipo_pregunta' => 'multiple',
                'puntaje' => 10,
                'dificultad' => 'alta',
                'respuestas' => [
                    ['texto' => 'Estado de luces, neumáticos, conexiones y sistema de enganche', 'es_correcta' => true],
                    ['texto' => 'Solo revisar el combustible', 'es_correcta' => false],
                    ['texto' => 'Verificar únicamente las luces', 'es_correcta' => false],
                    ['texto' => 'No es necesario realizar verificaciones especiales', 'es_correcta' => false]
                ]
            ]
        ];

        foreach ($preguntas as $pregunta) {
            $respuestas = $pregunta['respuestas'];
            unset($pregunta['respuestas']);
            
            $preguntaId = $preguntaModel->insert($pregunta);
            
            foreach ($respuestas as $respuesta) {
                $respuesta['pregunta_id'] = $preguntaId;
                $respuestaModel->insert($respuesta);
            }
        }
    }
} 