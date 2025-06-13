<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Controllers\AuthController;
use App\Models\ConductorModel;
use App\Models\ExamenConductorModel;

class AuthControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait, DatabaseTestTrait;

    protected $refreshDatabase = true;
    protected $conductor;
    protected $examenConductor;

    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->conductor = new ConductorModel();
        $this->examenConductor = new ExamenConductorModel();
    }

    protected function extractJsonFromHtml($html)
    {
        // Buscar el JSON dentro de las etiquetas <p>
        if (preg_match('/<p>(.*?)<\/p>/s', $html, $matches)) {
            return json_decode($matches[1], true);
        }
        return null;
    }

    public function testRegistroExitoso()
    {
        $data = [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '123456780',
            'email' => 'juan.perez@example.com',
            'estado_registro' => 'aprobado',
            'telefono' => '600123456'
        ];

        $jsonData = json_encode($data);

        $result = $this->withBody($jsonData)
            ->controller(AuthController::class)
            ->execute('registro', [], 'POST');

        $response = $this->extractJsonFromHtml($result->getBody());

        $this->assertEquals('success', $response['status']);
        $this->assertArrayHasKey('token', $response['data']);
        $this->assertArrayHasKey('conductor', $response['data']);
        $this->assertEquals('Juan', $response['data']['conductor']['nombre']);
        $this->assertEquals('aprobado', $response['data']['conductor']['estado_registro']);
    }

    public function testLoginExitoso()
    {
        // Primero creamos un conductor
        $this->conductor->insert([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '123456780',
            'email' => 'juan.perez@example.com',
            'estado_registro' => 'aprobado',
            'telefono' => '600123456'
        ]);

        // Intentamos hacer login
        $data = [
            'dni' => '123456780'
        ];

        $jsonData = json_encode($data);
        $result = $this->withBody($jsonData)
            ->controller(AuthController::class)
            ->execute('login', [], 'POST');

        $response = $this->extractJsonFromHtml($result->getBody());

        $this->assertEquals('success', $response['status']);
        $this->assertArrayHasKey('token', $response['data']);
        $this->assertArrayHasKey('conductor', $response['data']);
        $this->assertEquals('Juan', $response['data']['conductor']['nombre']);
        $this->assertEquals('aprobado', $response['data']['conductor']['estado_registro']);
        $this->assertArrayHasKey('examenes', $response['data']);
    }

    public function testLoginConductorNoExistente()
    {
        $data = [
            'dni' => '999999999'
        ];

        $jsonData = json_encode($data);
        $result = $this->withBody($jsonData)
            ->controller(AuthController::class)
            ->execute('login', [], 'POST');

        $response = $this->extractJsonFromHtml($result->getBody());

        $this->assertEquals('error', $response['status']);
        $this->assertEquals('Conductor no encontrado o cuenta rechazada', $response['message']);
    }

    public function testRegistroConDNIDuplicado()
    {
        // Primero creamos un conductor
        $this->conductor->insert([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '123456780',
            'email' => 'juan.perez@example.com',
            'estado_registro' => 'aprobado',
            'telefono' => '600123456'
        ]);

        // Intentamos registrar otro con el mismo DNI
        $data = [
            'nombre' => 'Pedro',
            'apellido' => 'García',
            'dni' => '123456780',
            'email' => 'pedro.garcia@example.com',
            'estado_registro' => 'aprobado',
            'telefono' => '600123457'
        ];

        $jsonData = json_encode($data);
        $result = $this->withBody($jsonData)
            ->controller(AuthController::class)
            ->execute('registro', [], 'POST');

        $response = $this->extractJsonFromHtml($result->getBody());

        $this->assertEquals('error', $response['status']);
        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('dni', $response['errors']);
    }
} 