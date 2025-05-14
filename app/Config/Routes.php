<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Rutas para la API de resultados
$routes->group('api', function($routes) {
    $routes->get('resultados/verificar/(:num)', 'ResultadoController::verificarEstado/$1');
    $routes->post('resultados/registrar', 'ResultadoController::registrar');
    $routes->get('resultados/historial/(:num)', 'ResultadoController::historial/$1');
    $routes->get('resultados/ultimo/(:num)', 'ResultadoController::ultimoResultado/$1');

    // Rutas de autenticación
    $routes->post('auth/registro', 'AuthController::registro');
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/logout', 'AuthController::logout');
    $routes->post('auth/refresh-token', 'AuthController::refreshToken');

    // Rutas de exámenes
    $routes->get('examenes', 'ExamenController::index');
    $routes->get('examenes/(:num)', 'ExamenController::show/$1');
    $routes->post('examenes', 'ExamenController::create');
    $routes->put('examenes/(:num)', 'ExamenController::update/$1');
    $routes->delete('examenes/(:num)', 'ExamenController::delete/$1');
    $routes->get('examenes/categoria/(:num)', 'ExamenController::porCategoria/$1');
    $routes->get('examenes/activos', 'ExamenController::activos');

    // Rutas de preguntas
    $routes->get('preguntas', 'PreguntaController::index');
    $routes->get('preguntas/(:num)', 'PreguntaController::show/$1');
    $routes->post('preguntas', 'PreguntaController::create');
    $routes->put('preguntas/(:num)', 'PreguntaController::update/$1');
    $routes->delete('preguntas/(:num)', 'PreguntaController::delete/$1');
    $routes->get('preguntas/examen/(:num)', 'PreguntaController::porExamen/$1');
    $routes->get('preguntas/categoria/(:num)', 'PreguntaController::porCategoria/$1');
    $routes->get('preguntas/criticas', 'PreguntaController::criticas');
});
