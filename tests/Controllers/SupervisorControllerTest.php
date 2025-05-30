<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Controllers\SupervisorController;
use App\Models\SupervisorModel;

class SupervisorControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait, DatabaseTestTrait;

    protected $refreshDatabase = true;
    protected $supervisor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->supervisor = new SupervisorModel();
        
        // Crear múltiples supervisores para probar paginación
        for ($i = 1; $i <= 15; $i++) {
            $this->supervisor->insert([
                'nombre' => "Supervisor {$i}",
                'apellido' => "Apellido {$i}",
                'email' => "supervisor{$i}@test.com",
                'telefono' => "12345678{$i}",
                'estado' => 'activo'
            ]);
        }
    }

    public function testIndexPagination()
    {
        // Probar primera página con 10 items por página
        $result = $this->controller(SupervisorController::class)
                       ->execute('index');
        
        $response = json_decode($result->getBody(), true);
        
        $this->assertTrue($result->isOK());
        $this->assertEquals('success', $response['status']);
        $this->assertCount(10, $response['data']['supervisores']);
        $this->assertEquals(1, $response['data']['pagination']['current_page']);
        $this->assertEquals(10, $response['data']['pagination']['per_page']);
        $this->assertEquals(2, $response['data']['pagination']['total_pages']);
        $this->assertEquals(15, $response['data']['pagination']['total_items']);

        // Probar segunda página
        $result = $this->controller(SupervisorController::class)
                       ->execute('index', ['page' => 2]);
        
        $response = json_decode($result->getBody(), true);
        
        $this->assertTrue($result->isOK());
        $this->assertEquals('success', $response['status']);
        $this->assertCount(5, $response['data']['supervisores']);
        $this->assertEquals(2, $response['data']['pagination']['current_page']);
    }
} 