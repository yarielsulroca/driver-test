<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\CategoriaModel;
use App\Models\ConductorModel;
use App\Models\ExamenModel;
use App\Models\PreguntaModel;
use App\Models\RespuestaModel;
use App\Models\ResultadoExamenModel;

class ExamenFlujoTest extends CIUnitTestCase
{
    use DatabaseTestTrait, FeatureTestTrait;

    protected $refreshDatabase = true;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar datos de prueba
        $categoriaModel = new CategoriaModel();
        $categoriaModel->insert([
            'nombre' => 'E1',
            'descripcion' => 'Categoría para vehículos pesados'
        ]);
        
        $conductorModel = new ConductorModel();
        $conductorModel->insert([
            'nombre' => 'Juan Pérez',
            'dni' => '12345678A',
            'email' => 'juan@test.com',
            'fecha_nacimiento' => '1990-01-01',
            'categoria_id' => 1
        ]);
        
        $examenModel = new ExamenModel();
        $examenModel->insert([
            'titulo' => 'Examen E1',
            'categoria_id' => 1,
            'tiempo_limite' => 30,
            'activo' => 1
        ]);
        
        $preguntaModel = new PreguntaModel();
        $preguntaId1 = $preguntaModel->insert([
            'examen_id' => 1,
            'texto' => 'Pregunta normal',
            'es_critica' => false
        ]);
        
        $preguntaId2 = $preguntaModel->insert([
            'examen_id' => 1,
            'texto' => 'Pregunta crítica',
            'es_critica' => true
        ]);
        
        $respuestaModel = new RespuestaModel();
        // Respuestas para pregunta 1
        $respuestaModel->insertBatch([
            [
                'pregunta_id' => $preguntaId1,
                'texto' => 'Respuesta correcta',
                'es_correcta' => true
            ],
            [
                'pregunta_id' => $preguntaId1,
                'texto' => 'Respuesta incorrecta',
                'es_correcta' => false
            ]
        ]);
        
        // Respuestas para pregunta 2 (crítica)
        $respuestaModel->insertBatch([
            [
                'pregunta_id' => $preguntaId2,
                'texto' => 'Respuesta correcta',
                'es_correcta' => true
            ],
            [
                'pregunta_id' => $preguntaId2,
                'texto' => 'Respuesta incorrecta',
                'es_correcta' => false
            ]
        ]);
    }

    public function testApiExamenesEndpoint()
    {
        $result = $this->get('api/examenes');
        $result->assertStatus(200);
    }
}