<?php

namespace Tests\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\PreguntaModel;
use App\Models\ExamenModel;
use App\Models\RespuestaModel;

class PreguntaModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refreshDatabase = true;
    protected $pregunta;
    protected $examen;
    protected $respuesta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pregunta = new PreguntaModel();
        $this->examen = new ExamenModel();
        $this->respuesta = new RespuestaModel();
        
        // Crear examen
        $this->examen->insert([
            'titulo' => 'Examen Test',
            'categoria_id' => 1,
            'tiempo_limite' => 30
        ]);
        
        // Crear pregunta
        $this->pregunta->insert([
            'examen_id' => 1,
            'texto' => '¿Cuál es el límite de velocidad en autopista?',
            'es_critica' => true
        ]);
        
        // Crear respuestas
        $this->respuesta->insertBatch([
            [
                'pregunta_id' => 1,
                'texto' => '120 km/h',
                'es_correcta' => true
            ],
            [
                'pregunta_id' => 1,
                'texto' => '140 km/h',
                'es_correcta' => false
            ]
        ]);
    }

    public function testFind()
    {
        $pregunta = $this->pregunta->find(1);
        $this->assertEquals('¿Cuál es el límite de velocidad en autopista?', $pregunta['texto']);
        $this->assertTrue((bool)$pregunta['es_critica']);
    }

    public function testInsert()
    {
        $data = [
            'examen_id' => 1,
            'texto' => '¿Qué significa un semáforo en rojo?',
            'es_critica' => true
        ];

        $preguntaId = $this->pregunta->insert($data);
        $this->assertIsNumeric($preguntaId);
        
        $pregunta = $this->pregunta->find($preguntaId);
        $this->assertEquals('¿Qué significa un semáforo en rojo?', $pregunta['texto']);
    }

    public function testUpdate()
    {
        $data = [
            'texto' => 'Pregunta actualizada',
            'es_critica' => false
        ];
        
        $result = $this->pregunta->update(1, $data);
        $this->assertTrue($result);
        
        $pregunta = $this->pregunta->find(1);
        $this->assertEquals('Pregunta actualizada', $pregunta['texto']);
        $this->assertFalse((bool)$pregunta['es_critica']);
    }

    public function testDelete()
    {
        $result = $this->pregunta->delete(1);
        $this->assertTrue($result);
        
        $pregunta = $this->pregunta->find(1);
        $this->assertNull($pregunta);
    }
}