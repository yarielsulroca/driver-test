<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Controllers\ResultadoController;
use App\Models\ResultadoExamenModel;
use App\Models\ConductorModel;
use App\Models\ExamenModel;

class ResultadoControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait, DatabaseTestTrait;

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
        
        // Crear resultados
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

    public function testVerificarEstado()
    {
        $result = $this->controller(ResultadoController::class)
                       ->execute('verificarEstado', 1);
        
        $this->assertTrue($result->isOK());
    }

    public function testHistorial()
    {
        $result = $this->controller(ResultadoController::class)
                       ->execute('historial', 1);
        
        $this->assertTrue($result->isOK());
    }
}