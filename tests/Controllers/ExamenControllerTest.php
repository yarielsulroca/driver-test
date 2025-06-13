<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Controllers\ExamenController;
use App\Models\ExamenModel;
use App\Models\CategoriaModel;

class ExamenControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait, DatabaseTestTrait;

    protected $refreshDatabase = true;
    protected $examen;
    protected $categoria;

    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->examen = new ExamenModel();
        $this->categoria = new CategoriaModel();
        
        // Crear categorías de prueba
        $this->categoria->insert([
            'nombre' => 'Cat1',
            'descripcion' => 'Categoría 1'
        ]);
        $this->categoria->insert([
            'nombre' => 'Cat2',
            'descripcion' => 'Categoría 2'
        ]);
        
        // Crear múltiples exámenes para probar paginación
        for ($i = 1; $i <= 15; $i++) {
            $this->examen->insert([
                'titulo' => "Examen E1 #{$i}",
                'categoria_id' => 1,
                'tiempo_limite' => 30,
                'activo' => 1
            ]);
        }
    }

    protected function extractJsonFromHtml($html)
    {
        if (preg_match('/<p>(.*?)<\/p>/s', $html, $matches)) {
            return json_decode($matches[1], true);
        }
        return null;
    }

    public function testIndex()
    {
        // Crear algunos exámenes de prueba
        $this->examen->insert([
            'titulo' => 'Examen 1',
            'descripcion' => 'Descripción del examen 1',
            'paginas_preguntas' => json_encode([
                [
                    'pregunta' => '¿Pregunta 1?',
                    'opciones' => ['A', 'B', 'C'],
                    'respuesta_correcta' => 'A'
                ]
            ]),
            'estado' => 'activo',
            'categoria_id' => 1
        ]);

        $this->examen->insert([
            'titulo' => 'Examen 2',
            'descripcion' => 'Descripción del examen 2',
            'paginas_preguntas' => json_encode([
                [
                    'pregunta' => '¿Pregunta 2?',
                    'opciones' => ['A', 'B', 'C'],
                    'respuesta_correcta' => 'B'
                ]
            ]),
            'estado' => 'activo',
            'categoria_id' => 2
        ]);

        $result = $this->controller(ExamenController::class)
            ->execute('index');

        $response = $this->extractJsonFromHtml($result->getBody());

        $this->assertEquals('success', $response['status']);
        $this->assertArrayHasKey('examenes', $response['data']);
        $this->assertArrayHasKey('pagination', $response['data']);
        $this->assertCount(2, $response['data']['examenes']);
    }

    public function testShow()
    {
        // Crear un examen de prueba
        $examenId = $this->examen->insert([
            'titulo' => 'Examen Test',
            'descripcion' => 'Descripción del examen test',
            'paginas_preguntas' => json_encode([
                [
                    'pregunta' => '¿Pregunta 1?',
                    'opciones' => ['A', 'B', 'C'],
                    'respuesta_correcta' => 'A'
                ]
            ]),
            'estado' => 'activo',
            'categoria_id' => 1
        ]);

        $result = $this->controller(ExamenController::class)
            ->execute('show', [$examenId]);

        $response = $this->extractJsonFromHtml($result->getBody());

        $this->assertEquals('success', $response['status']);
        $this->assertEquals('Examen Test', $response['data']['titulo']);
        $this->assertEquals('Descripción del examen test', $response['data']['descripcion']);
    }

    public function testCreate()
    {
        $data = [
            'titulo' => 'Nuevo Examen',
            'descripcion' => 'Descripción del nuevo examen',
            'paginas_preguntas' => [
                [
                    'pregunta' => '¿Pregunta 1?',
                    'opciones' => ['A', 'B', 'C'],
                    'respuesta_correcta' => 'A'
                ]
            ],
            'categorias' => [1, 2],
            'estado' => 'activo',
            'categoria_id' => 1
        ];

        $jsonData = json_encode($data);
        $result = $this->withBody($jsonData)
            ->controller(ExamenController::class)
            ->execute('create');

        $response = $this->extractJsonFromHtml($result->getBody());

        $this->assertEquals('success', $response['status']);
        $this->assertEquals('Nuevo Examen', $response['data']['titulo']);
        $this->assertEquals('Descripción del nuevo examen', $response['data']['descripcion']);
    }

    public function testUpdate()
    {
        // Crear un examen de prueba
        $examenId = $this->examen->insert([
            'titulo' => 'Examen Original',
            'descripcion' => 'Descripción original',
            'paginas_preguntas' => json_encode([
                [
                    'pregunta' => '¿Pregunta 1?',
                    'opciones' => ['A', 'B', 'C'],
                    'respuesta_correcta' => 'A'
                ]
            ]),
            'estado' => 'activo',
            'categoria_id' => 1
        ]);

        $data = [
            'titulo' => 'Examen Actualizado',
            'descripcion' => 'Descripción actualizada',
            'categorias' => [1, 2],
            'categoria_id' => 1
        ];

        $jsonData = json_encode($data);
        $result = $this->withBody($jsonData)
            ->controller(ExamenController::class)
            ->execute('update', [$examenId]);

        $response = $this->extractJsonFromHtml($result->getBody());

        $this->assertEquals('success', $response['status']);
        $this->assertEquals('Examen Actualizado', $response['data']['titulo']);
        $this->assertEquals('Descripción actualizada', $response['data']['descripcion']);
    }

    public function testDelete()
    {
        // Crear un examen de prueba
        $examenId = $this->examen->insert([
            'titulo' => 'Examen a Eliminar',
            'descripcion' => 'Descripción del examen a eliminar',
            'paginas_preguntas' => json_encode([
                [
                    'pregunta' => '¿Pregunta 1?',
                    'opciones' => ['A', 'B', 'C'],
                    'respuesta_correcta' => 'A'
                ]
            ]),
            'estado' => 'activo',
            'categoria_id' => 1
        ]);

        $result = $this->controller(ExamenController::class)
            ->execute('delete', [$examenId]);

        $response = $this->extractJsonFromHtml($result->getBody());

        $this->assertEquals('success', $response['status']);
        $this->assertEquals('Examen eliminado exitosamente', $response['message']);

        // Verificar que el examen ya no existe
        $examenEliminado = $this->examen->find($examenId);
        $this->assertNull($examenEliminado);
    }
}