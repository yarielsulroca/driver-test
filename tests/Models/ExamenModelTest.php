<?php

namespace Tests\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ExamenModel;
use App\Models\CategoriaModel;

class ExamenModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refreshDatabase = true;
    protected $examen;
    protected $categoria;

    protected function setUp(): void
    {
        parent::setUp();
        $this->examen = new ExamenModel();
        $this->categoria = new CategoriaModel();
        
        // Crear categoría
        $this->categoria->insert([
            'nombre' => 'E1',
            'descripcion' => 'Categoría para vehículos pesados'
        ]);
        
        // Crear examen
        $this->examen->insert([
            'titulo' => 'Examen E1',
            'categoria_id' => 1,
            'tiempo_limite' => 30
        ]);
    }

    public function testFind()
    {
        $examen = $this->examen->find(1);
        $this->assertEquals('Examen E1', $examen['titulo']);
        $this->assertEquals(1, $examen['categoria_id']);
    }

    public function testInsert()
    {
        $data = [
            'titulo' => 'Nuevo Examen',
            'categoria_id' => 1,
            'tiempo_limite' => 45
        ];

        $examenId = $this->examen->insert($data);
        $this->assertIsNumeric($examenId);
        
        $examen = $this->examen->find($examenId);
        $this->assertEquals('Nuevo Examen', $examen['titulo']);
    }

    public function testUpdate()
    {
        $data = [
            'titulo' => 'Examen E1 Actualizado',
            'tiempo_limite' => 60
        ];
        
        $result = $this->examen->update(1, $data);
        $this->assertTrue($result);
        
        $examen = $this->examen->find(1);
        $this->assertEquals('Examen E1 Actualizado', $examen['titulo']);
        $this->assertEquals(60, $examen['tiempo_limite']);
    }

    public function testDelete()
    {
        $result = $this->examen->delete(1);
        $this->assertTrue($result);
        
        $examen = $this->examen->find(1);
        $this->assertNull($examen);
    }
}
