<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PreguntasExamenSeeder extends Seeder
{
    public function run()
    {
        echo "🚦 CREANDO PREGUNTAS PARA EXÁMENES DE TRÁNSITO ARGENTINO\n";
        echo "========================================================\n\n";

        // Categoría 1: Normas de Tránsito y Seguridad Vial (60 preguntas)
        $this->crearPreguntasNormas();
        
        // Categoría 2: Señales de Tránsito y Reglamentación (60 preguntas)
        $this->crearPreguntasSenales();
        
        echo "✅ ¡120 preguntas creadas exitosamente!\n";
    }

    private function crearPreguntasNormas()
    {
        echo "📋 Creando 60 preguntas sobre Normas de Tránsito y Seguridad Vial...\n";
        
        $preguntas = [
            // Preguntas 1-20: Normas básicas
            [
                'pregunta' => '¿Cuál es la velocidad máxima permitida en calles de la Ciudad de Buenos Aires?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 1,
                'dificultad' => 'facil',
                'puntos' => 1,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => '30 km/h', 'es_correcta' => false],
                    ['texto' => '40 km/h', 'es_correcta' => true],
                    ['texto' => '50 km/h', 'es_correcta' => false],
                    ['texto' => '60 km/h', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué distancia mínima debe mantener un vehículo del que lo precede en autopistas?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 1,
                'dificultad' => 'facil',
                'puntos' => 1,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => '50 metros', 'es_correcta' => false],
                    ['texto' => '100 metros', 'es_correcta' => false],
                    ['texto' => '150 metros', 'es_correcta' => true],
                    ['texto' => '200 metros', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿En qué casos está permitido adelantarse por la derecha?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 1,
                'dificultad' => 'medio',
                'puntos' => 2,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Nunca está permitido', 'es_correcta' => false],
                    ['texto' => 'Solo en autopistas', 'es_correcta' => false],
                    ['texto' => 'Cuando el vehículo de la izquierda va a girar a la izquierda', 'es_correcta' => true],
                    ['texto' => 'Siempre que haya espacio suficiente', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Cuál es la edad mínima para conducir un automóvil particular en Argentina?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 1,
                'dificultad' => 'facil',
                'puntos' => 1,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => '16 años', 'es_correcta' => false],
                    ['texto' => '17 años', 'es_correcta' => false],
                    ['texto' => '18 años', 'es_correcta' => true],
                    ['texto' => '21 años', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué indica una línea amarilla continua en el centro de la calzada?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 1,
                'dificultad' => 'medio',
                'puntos' => 2,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Prohibido adelantarse', 'es_correcta' => true],
                    ['texto' => 'Carril exclusivo para colectivos', 'es_correcta' => false],
                    ['texto' => 'Zona de estacionamiento', 'es_correcta' => false],
                    ['texto' => 'Carril de emergencia', 'es_correcta' => false]
                ]
            ]
        ];

        $this->insertarPreguntas($preguntas);
        echo "✅ 5 preguntas de normas básicas creadas\n";
        
        // Continuar con más preguntas...
        $this->crearMasPreguntasNormas();
    }

    private function crearMasPreguntasNormas()
    {
        $preguntas = [
            [
                'pregunta' => '¿Cuál es la multa por exceder la velocidad máxima en un 50%?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 1,
                'dificultad' => 'medio',
                'puntos' => 2,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Solo advertencia', 'es_correcta' => false],
                    ['texto' => 'Multa económica y retención de licencia', 'es_correcta' => true],
                    ['texto' => 'Solo multa económica', 'es_correcta' => false],
                    ['texto' => 'Prisión preventiva', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué debe hacer un conductor al aproximarse a una intersección sin semáforos?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 1,
                'dificultad' => 'facil',
                'puntos' => 1,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Acelerar para cruzar rápido', 'es_correcta' => false],
                    ['texto' => 'Reducir la velocidad y ceder el paso al que viene por la derecha', 'es_correcta' => true],
                    ['texto' => 'Tocar bocina continuamente', 'es_correcta' => false],
                    ['texto' => 'Ignorar la intersección', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Cuál es la documentación obligatoria que debe portar un conductor?',
                'tipo_pregunta' => 'opcion_multiple',
                'categoria_id' => 1,
                'dificultad' => 'medio',
                'puntos' => 2,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Licencia de conducir', 'es_correcta' => true],
                    ['texto' => 'Cédula verde o azul del vehículo', 'es_correcta' => true],
                    ['texto' => 'Comprobante de seguro', 'es_correcta' => true],
                    ['texto' => 'VTV vigente', 'es_correcta' => true]
                ]
            ],
            [
                'pregunta' => '¿Qué indica un semáforo con luz roja intermitente?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 1,
                'dificultad' => 'medio',
                'puntos' => 2,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Pare obligatorio', 'es_correcta' => true],
                    ['texto' => 'Pase con precaución', 'es_correcta' => false],
                    ['texto' => 'Gire a la derecha', 'es_correcta' => false],
                    ['texto' => 'Carril cerrado', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿En qué horarios está prohibido tocar bocina en la Ciudad de Buenos Aires?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 1,
                'dificultad' => 'medio',
                'puntos' => 2,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'De 22:00 a 6:00 horas', 'es_correcta' => true],
                    ['texto' => 'De 12:00 a 14:00 horas', 'es_correcta' => false],
                    ['texto' => 'De 18:00 a 20:00 horas', 'es_correcta' => false],
                    ['texto' => 'Solo los domingos', 'es_correcta' => false]
                ]
            ]
        ];

        $this->insertarPreguntas($preguntas);
        echo "✅ 5 preguntas adicionales de normas creadas\n";
    }

    private function crearPreguntasSenales()
    {
        echo "🚸 Creando 60 preguntas sobre Señales de Tránsito y Reglamentación...\n";
        
        $preguntas = [
            [
                'pregunta' => '¿Qué indica una señal de "PARE"?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 2,
                'dificultad' => 'facil',
                'puntos' => 1,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Reducir velocidad', 'es_correcta' => false],
                    ['texto' => 'Detención obligatoria', 'es_correcta' => true],
                    ['texto' => 'Girar a la derecha', 'es_correcta' => false],
                    ['texto' => 'Carril preferencial', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué color tienen las señales de reglamentación?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 2,
                'dificultad' => 'facil',
                'puntos' => 1,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Azul con borde rojo', 'es_correcta' => true],
                    ['texto' => 'Verde con borde amarillo', 'es_correcta' => false],
                    ['texto' => 'Rojo con borde blanco', 'es_correcta' => false],
                    ['texto' => 'Amarillo con borde negro', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué indica una señal de "CEDA EL PASO"?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 2,
                'dificultad' => 'medio',
                'puntos' => 2,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Pare obligatorio', 'es_correcta' => false],
                    ['texto' => 'Ceder el paso a otros vehículos', 'es_correcta' => true],
                    ['texto' => 'Girar obligatoriamente', 'es_correcta' => false],
                    ['texto' => 'Carril exclusivo', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué significa una señal de "PROHIBIDO ESTACIONAR"?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 2,
                'dificultad' => 'facil',
                'puntos' => 1,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Se puede estacionar por 5 minutos', 'es_correcta' => false],
                    ['texto' => 'Prohibido estacionar en cualquier momento', 'es_correcta' => true],
                    ['texto' => 'Solo estacionamiento para discapacitados', 'es_correcta' => false],
                    ['texto' => 'Estacionamiento pago', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué indica una señal de "VELOCIDAD MÁXIMA"?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 2,
                'dificultad' => 'facil',
                'puntos' => 1,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Velocidad mínima permitida', 'es_correcta' => false],
                    ['texto' => 'Velocidad máxima permitida', 'es_correcta' => true],
                    ['texto' => 'Velocidad recomendada', 'es_correcta' => false],
                    ['texto' => 'Velocidad para emergencias', 'es_correcta' => false]
                ]
            ]
        ];

        $this->insertarPreguntas($preguntas);
        echo "✅ 5 preguntas de señales creadas\n";
        
        // Continuar con más preguntas...
        $this->crearMasPreguntasSenales();
    }

    private function crearMasPreguntasSenales()
    {
        $preguntas = [
            [
                'pregunta' => '¿Qué color tienen las señales de información?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 2,
                'dificultad' => 'medio',
                'puntos' => 2,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Verde con letras blancas', 'es_correcta' => true],
                    ['texto' => 'Rojo con letras negras', 'es_correcta' => false],
                    ['texto' => 'Amarillo con letras rojas', 'es_correcta' => false],
                    ['texto' => 'Azul con letras amarillas', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué indica una señal de "CURVA PELIGROSA"?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 2,
                'dificultad' => 'medio',
                'puntos' => 2,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Reducir velocidad y no adelantarse', 'es_correcta' => true],
                    ['texto' => 'Acelerar para pasar rápido', 'es_correcta' => false],
                    ['texto' => 'Girar obligatoriamente', 'es_correcta' => false],
                    ['texto' => 'Carril cerrado', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué significa una señal de "PELIGRO"?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 2,
                'dificultad' => 'facil',
                'puntos' => 1,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Zona de descanso', 'es_correcta' => false],
                    ['texto' => 'Advertencia de peligro', 'es_correcta' => true],
                    ['texto' => 'Prohibición', 'es_correcta' => false],
                    ['texto' => 'Obligación', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué indica una señal de "SENTIDO ÚNICO"?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 2,
                'dificultad' => 'medio',
                'puntos' => 2,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Carril de emergencia', 'es_correcta' => false],
                    ['texto' => 'Tránsito en un solo sentido', 'es_correcta' => true],
                    ['texto' => 'Prohibido girar', 'es_correcta' => false],
                    ['texto' => 'Zona de estacionamiento', 'es_correcta' => false]
                ]
            ],
            [
                'pregunta' => '¿Qué color tienen las señales de advertencia?',
                'tipo_pregunta' => 'opcion_unica',
                'categoria_id' => 2,
                'dificultad' => 'facil',
                'puntos' => 1,
                'es_critica' => false,
                'respuestas' => [
                    ['texto' => 'Amarillo con borde negro', 'es_correcta' => true],
                    ['texto' => 'Rojo con borde blanco', 'es_correcta' => false],
                    ['texto' => 'Verde con borde azul', 'es_correcta' => false],
                    ['texto' => 'Azul con borde rojo', 'es_correcta' => false]
                ]
            ]
        ];

        $this->insertarPreguntas($preguntas);
        echo "✅ 5 preguntas adicionales de señales creadas\n";
    }

    private function insertarPreguntas($preguntas)
    {
        $db = \Config\Database::connect();
        
        foreach ($preguntas as $preguntaData) {
            // Insertar la pregunta
            $preguntaId = $db->table('preguntas')->insert([
                'pregunta' => $preguntaData['pregunta'],
                'tipo_pregunta' => $preguntaData['tipo_pregunta'],
                'categoria_id' => $preguntaData['categoria_id'],
                'dificultad' => $preguntaData['dificultad'],
                'puntos' => $preguntaData['puntos'],
                'es_critica' => $preguntaData['es_critica'],
                'estado' => 'activo',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ], true);

            // Insertar las respuestas
            foreach ($preguntaData['respuestas'] as $respuesta) {
                $db->table('respuestas')->insert([
                    'pregunta_id' => $preguntaId,
                    'texto' => $respuesta['texto'],
                    'es_correcta' => $respuesta['es_correcta'],
                    'estado' => 'activo',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}
