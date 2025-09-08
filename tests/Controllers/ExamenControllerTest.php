<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use App\Controllers\ExamenController;

class ExamenControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    }

    protected function extractJsonFromHtml($html)
    {
        if (preg_match('/<p>(.*?)<\/p>/s', $html, $matches)) {
            return json_decode($matches[1], true);
        }
        return null;
    }

    public function testCreateExamen()
    {
        $data = [
            'nombre' => 'Examen de Prueba',
            'descripcion' => 'Descripción del examen de prueba',
            'categorias' => [1],
            'preguntas' => [
                [
                    'categoria_id' => 1,
                    'enunciado' => '¿Cuál es la velocidad máxima en zona urbana?',
                    'tipo' => 'multiple',
                    'dificultad' => 'media',
                    'puntaje' => 10,
                    'es_critica' => false,
                    'respuestas' => [
                        ['texto' => '30 km/h', 'es_correcta' => false],
                        ['texto' => '40 km/h', 'es_correcta' => true],
                        ['texto' => '50 km/h', 'es_correcta' => false]
                    ]
                ]
            ],
            'duracion_minutos' => 30,
            'puntaje_minimo' => 70
        ];

        $jsonData = json_encode($data);
        $result = $this->withBody($jsonData)
            ->controller(ExamenController::class)
            ->execute('create');

        // Debug: imprimir información para diagnóstico
        echo "Status Code: " . $result->getStatusCode() . "\n";
        echo "Response Body: " . $result->getBody() . "\n";

        $response = $this->extractJsonFromHtml($result->getBody());
        
        if ($response) {
            echo "Parsed Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
        }

        // Por ahora solo verificamos que no sea un error 500
        $this->assertNotEquals(500, $result->getStatusCode(), 'El servidor no debería devolver error 500');
    }
}