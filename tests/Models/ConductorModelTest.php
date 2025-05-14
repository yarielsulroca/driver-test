<?php

namespace Tests\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ConductorModel;

class ConductorModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refreshDatabase = true;
    protected $conductor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->conductor = new ConductorModel();
        
        // Datos de prueba
        $this->conductor->insert([
            'nombre' => 'Juan Pérez',
            'dni' => '12345678A',
            'email' => 'juan@test.com',
            'fecha_nacimiento' => '1990-01-01',
            'categoria_id' => 1
        ]);
    }

    public function testFind()
    {
        $conductor = $this->conductor->find(1);
        $this->assertEquals('Juan Pérez', $conductor['nombre']);
        $this->assertEquals('12345678A', $conductor['dni']);
    }

    public function testInsert()
    {
        $data = [
            'nombre' => 'María López',
            'dni' => '87654321B',
            'email' => 'maria@test.com',
            'fecha_nacimiento' => '1992-05-15',
            'categoria_id' => 1
        ];

        $conductorId = $this->conductor->insert($data);
        $this->assertIsNumeric($conductorId);
        $this->assertGreaterThan(0, $conductorId);
        
        $conductor = $this->conductor->find($conductorId);
        $this->assertEquals('María López', $conductor['nombre']);
    }

    public function testUpdate()
    {
        $data = [
            'nombre' => 'Juan Pérez Actualizado'
        ];
        
        $result = $this->conductor->update(1, $data);
        $this->assertTrue($result);
        
        $conductor = $this->conductor->find(1);
        $this->assertEquals('Juan Pérez Actualizado', $conductor['nombre']);
    }

    public function testDelete()
    {
        $result = $this->conductor->delete(1);
        $this->assertTrue($result);
        
        $conductor = $this->conductor->find(1);
        $this->assertNull($conductor);
    }
}