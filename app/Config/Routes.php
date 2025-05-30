<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Rutas para la API de resultados
$routes->group('api', function($routes) {
    // Rutas para verificar y gestionar resultados de exámenes
    $routes->get('resultados/verificar/(:num)', 'ResultadoController::verificarEstado/$1'); // Verifica el estado de un resultado específico
    $routes->post('resultados/registrar', 'ResultadoController::registrar'); // Registra un nuevo resultado
    $routes->get('resultados/historial/(:num)', 'ResultadoController::historial/$1'); // Obtiene el historial de resultados de un conductor
    $routes->get('resultados/ultimo/(:num)', 'ResultadoController::ultimoResultado/$1'); // Obtiene el último resultado de un conductor
    $routes->get('resultados/evaluar/(:num)', 'ResultadoController::evaluarExamen/$1');

    // Rutas de autenticación y gestión de usuarios
    $routes->post('auth/registro', 'AuthController::registro'); // Registra un nuevo usuario
    $routes->post('auth/login', 'AuthController::login'); // Inicia sesión de usuario
    $routes->post('auth/logout', 'AuthController::logout'); // Cierra sesión de usuario
    $routes->post('auth/refresh-token', 'AuthController::refreshToken'); // Renueva el token de autenticación

    // Rutas para la gestión de exámenes
    $routes->get('examenes', 'ExamenController::index'); // Lista todos los exámenes
    $routes->get('examenes/(:num)', 'ExamenController::show/$1'); // Muestra un examen específico
    $routes->post('examenes', 'ExamenController::create'); // Crea un nuevo examen
    $routes->put('examenes/(:num)', 'ExamenController::update/$1'); // Actualiza un examen existente
    $routes->delete('examenes/(:num)', 'ExamenController::delete/$1'); // Elimina un examen
    $routes->get('examenes/categoria/(:num)', 'ExamenController::porCategoria/$1'); // Lista exámenes por categoría
    $routes->get('examenes/activos', 'ExamenController::activos'); // Lista exámenes activos

    // Rutas para la gestión de preguntas
    $routes->get('preguntas', 'PreguntaController::index'); // Lista todas las preguntas
    $routes->get('preguntas/(:num)', 'PreguntaController::show/$1'); // Muestra una pregunta específica
    $routes->post('preguntas', 'PreguntaController::create'); // Crea una nueva pregunta
    $routes->put('preguntas/(:num)', 'PreguntaController::update/$1'); // Actualiza una pregunta existente
    $routes->delete('preguntas/(:num)', 'PreguntaController::delete/$1'); // Elimina una pregunta
    $routes->get('preguntas/examen/(:num)', 'PreguntaController::porExamen/$1'); // Lista preguntas por examen
    $routes->get('preguntas/categoria/(:num)', 'PreguntaController::porCategoria/$1'); // Lista preguntas por categoría
    $routes->get('preguntas/criticas', 'PreguntaController::criticas'); // Lista preguntas críticas
});

// Rutas para imágenes
$routes->post('imagenes/upload', 'ImagenController::upload');
$routes->delete('imagenes/(:segment)', 'ImagenController::delete/$1');
