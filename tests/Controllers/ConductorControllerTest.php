<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Controllers\ConductorController;
use App\Models\ConductorModel;

class ConductorControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait, DatabaseTestTrait;

    protected $refreshDatabase = true;
    protected $conductor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->conductor = new ConductorModel();
        
        // Crear múltiples conductores para probar paginación
        for ($i = 1; $i <= 15; $i++) {
            $this->conductor->insert([
                'nombre' => "Conductor {$i}",
                'dni' => "1234567{$i}A",
                'email' => "conductor{$i}@test.com",
                'fecha_nacimiento' => '1990-01-01',
                'categoria_id' => 1
            ]);
        }
    }

    public function testIndexPagination()
    {
        // Probar primera página con 10 items por página
        $result = $this->controller(ConductorController::class)
                       ->execute('index');
        
        $response = json_decode($result->getBody(), true);
        
        $this->assertTrue($result->isOK());
        $this->assertEquals('success', $response['status']);
        $this->assertCount(10, $response['data']['conductores']);
        $this->assertEquals(1, $response['data']['pagination']['current_page']);
        $this->assertEquals(10, $response['data']['pagination']['per_page']);
        $this->assertEquals(2, $response['data']['pagination']['total_pages']);
        $this->assertEquals(15, $response['data']['pagination']['total_items']);

        // Probar segunda página
        $result = $this->controller(ConductorController::class)
                       ->execute('index', ['page' => 2]);
        
        $response = json_decode($result->getBody(), true);
        
        $this->assertTrue($result->isOK());
        $this->assertEquals('success', $response['status']);
        $this->assertCount(5, $response['data']['conductores']);
        $this->assertEquals(2, $response['data']['pagination']['current_page']);
    }
} 