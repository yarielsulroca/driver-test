<?php

namespace Tests\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\RespuestaModel;
use App\Models\PreguntaModel;

class RespuestaModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refreshDatabase = true;
    protected $respuesta;
    protected $pregunta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->respuesta = new RespuestaModel();
        $this->pregunta = new PreguntaModel();
        
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
        $respuesta = $this->respuesta->find(1);
        $this->assertEquals('120 km/h', $respuesta['texto']);
        $this->assertTrue((bool)$respuesta['es_correcta']);
    }

    public function testInsert()
    {
        $data = [
            'pregunta_id' => 1,
            'texto' => '100 km/h',
            'es_correcta' => false
        ];

        $respuestaId = $this->respuesta->insert($data);
        $this->assertIsNumeric($respuestaId);
        
        $respuesta = $this->respuesta->find($respuestaId);
        $this->assertEquals('100 km/h', $respuesta['texto']);
    }

    public function testUpdate()
    {
        $data = [
            'texto' => 'Respuesta actualizada',
            'es_correcta' => false
        ];
        
        $result = $this->respuesta->update(1, $data);
        $this->assertTrue($result);
        
        $respuesta = $this->respuesta->find(1);
        $this->assertEquals('Respuesta actualizada', $respuesta['texto']);
        $this->assertFalse((bool)$respuesta['es_correcta']);
    }

    public function testDelete()
    {
        $result = $this->respuesta->delete(1);
        $this->assertTrue($result);
        
        $respuesta = $this->respuesta->find(1);
        $this->assertNull($respuesta);
    }
}