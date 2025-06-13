<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/docs', 'DocsController::index');

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
    $routes->post('registro', 'AuthController::registro'); // Alias para registro
    $routes->post('auth/login', 'AuthController::login'); // Inicia sesión de usuario
    $routes->post('login', 'AuthController::login'); // Alias para login
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

    // Rutas de categorías
    $routes->group('categorias', ['namespace' => 'App\Controllers'], function($routes) {
        $routes->get('/', 'CategoriaController::index');
        $routes->get('(:num)', 'CategoriaController::show/$1');
        $routes->post('/', 'CategoriaController::create');
        $routes->put('(:num)', 'CategoriaController::update/$1');
        $routes->delete('(:num)', 'CategoriaController::delete/$1');
    });

    // Rutas de escuelas
    $routes->group('escuelas', ['namespace' => 'App\Controllers'], function($routes) {
        $routes->get('/', 'EscuelaController::index');
        $routes->get('(:num)', 'EscuelaController::show/$1');
        $routes->post('/', 'EscuelaController::create');
        $routes->put('(:num)', 'EscuelaController::update/$1');
        $routes->delete('(:num)', 'EscuelaController::delete/$1');
    });

    // Rutas para las páginas
    $routes->get('paginas', 'PaginasController::index');
    $routes->get('paginas/(:num)', 'PaginasController::show/$1');

    // Rutas para la gestión de imágenes
    $routes->resource('imagenes');
    $routes->get('imagenes/get/(:num)', 'ImagenesController::getImagen/$1');

    // Rutas de autenticación
    $routes->post('api/registro', 'AuthController::registro');
    $routes->post('api/login', 'AuthController::login');
    $routes->post('api/logout', 'AuthController::logout', ['filter' => 'auth']);
    $routes->post('api/refresh-token', 'AuthController::refreshToken', ['filter' => 'auth']);

    // Rutas del conductor
    $routes->group('api/conductor', ['filter' => 'auth'], function($routes) {
        $routes->get('perfil', 'ConductorController::perfil');
        $routes->get('examenes', 'ConductorController::examenes');
        $routes->get('examenes/(:num)', 'ConductorController::examen/$1');
        $routes->get('categorias', 'ConductorController::categorias');
        $routes->get('historial', 'ConductorController::historial');
    });
});

// Rutas para imágenes
$routes->post('imagenes/upload', 'ImagenController::upload');
$routes->delete('imagenes/(:segment)', 'ImagenController::delete/$1');
