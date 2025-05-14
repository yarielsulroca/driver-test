<?php

namespace Tests\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ResultadoExamenModel;
use App\Models\ConductorModel;
use App\Models\ExamenModel;

class ResultadoExamenModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refreshDatabase = true;
    protected $resultado;
    protected $conductor;
    protected $examen;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resultado = new ResultadoExamenModel();
        $this->conductor = new ConductorModel();
        $this->examen = new ExamenModel();
        
        // Crear conductor
        $this->conductor->insert([
            'nombre' => 'Juan Pérez',
            'dni' => '12345678A',
            'email' => 'juan@test.com',
            'fecha_nacimiento' => '1990-01-01',
            'categoria_id' => 1
        ]);
        
        // Crear examen
        $this->examen->insert([
            'titulo' => 'Examen Test',
            'categoria_id' => 1,
            'tiempo_limite' => 30
        ]);
        
        // Crear resultado
        $this->resultado->insert([
            'conductor_id' => 1,
            'examen_id' => 1,
            'fecha' => date('Y-m-d H:i:s'),
            'aciertos' => 8,
            'errores' => 2,
            'estado' => 'aprobado',
            'comentario' => 'Buen examen'
        ]);
    }

    public function testFind()
    {
        $resultado = $this->resultado->find(1);
        $this->assertEquals(1, $resultado['conductor_id']);
        $this->assertEquals(1, $resultado['examen_id']);
        $this->assertEquals(8, $resultado['aciertos']);
        $this->assertEquals('aprobado', $resultado['estado']);
    }

    public function testInsert()
    {
        $data = [
            'conductor_id' => 1,
            'examen_id' => 1,
            'fecha' => date('Y-m-d H:i:s'),
            'aciertos' => 5,
            'errores' => 5,
            'estado' => 'suspendido',
            'comentario' => 'Necesita mejorar'
        ];

        $resultadoId = $this->resultado->insert($data);
        $this->assertIsNumeric($resultadoId);
        
        $resultado = $this->resultado->find($resultadoId);
        $this->assertEquals('suspendido', $resultado['estado']);
    }

    public function testUpdate()
    {
        $data = [
            'aciertos' => 9,
            'errores' => 1,
            'comentario' => 'Excelente examen'
        ];
        
        $result = $this->resultado->update(1, $data);
        $this->assertTrue($result);
        
        $resultado = $this->resultado->find(1);
        $this->assertEquals(9, $resultado['aciertos']);
        $this->assertEquals('Excelente examen', $resultado['comentario']);
    }

    public function testDelete()
    {
        $result = $this->resultado->delete(1);
        $this->assertTrue($result);
        
        $resultado = $this->resultado->find(1);
        $this->assertNull($resultado);
    }
}