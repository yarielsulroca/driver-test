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
});
