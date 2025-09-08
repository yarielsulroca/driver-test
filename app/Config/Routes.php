<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/docs', 'DocsController::index');

// Rutas para la API de resultados
$routes->group('api', function($routes) {
    // Ruta global para manejar peticiones OPTIONS en toda la API
    $routes->options('(:any)', function() {
        return service('response')
            ->setStatusCode(200)
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE, PATCH')
            ->setHeader('Access-Control-Max-Age', '86400');
    });
    
    // Rutas para verificar y gestionar resultados de exámenes
    $routes->get('resultados', 'ResultadoController::index'); // Lista todos los resultados
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

    // Rutas para la gestión de exámenes (TODAS PÚBLICAS TEMPORALMENTE)
    $routes->get('examenes', 'ExamenController::index'); // Lista todos los exámenes
    $routes->get('examenes/(:num)', 'ExamenController::show/$1'); // Muestra un examen específico (requiere auth)
    $routes->post('examenes', 'ExamenController::create'); // Crea un nuevo examen
    $routes->put('examenes/(:num)', 'ExamenController::update/$1'); // Actualiza un examen existente
    $routes->delete('examenes/(:num)', 'ExamenController::delete/$1'); // Elimina un examen
    $routes->get('examenes/(:num)/estadisticas', 'ExamenController::estadisticas/$1'); // Estadísticas del examen
    
    // Ruta de prueba
    $routes->get('test/examen', 'TestController::testExamen');
    
    // Ruta para estadísticas del dashboard
    $routes->get('dashboard/stats', 'DashboardController::getStats');

    // Rutas para la gestión de preguntas (TODAS PÚBLICAS TEMPORALMENTE)
    $routes->get('preguntas', 'PreguntaController::index'); // Lista todas las preguntas
    $routes->get('preguntas/(:num)', 'PreguntaController::show/$1'); // Muestra una pregunta específica
    $routes->get('preguntas/examen/(:num)', 'PreguntaController::porExamen/$1'); // Lista preguntas por examen
    $routes->get('preguntas/categoria/(:num)', 'PreguntaController::porCategoria/$1'); // Lista preguntas por categoría
    $routes->get('preguntas/criticas', 'PreguntaController::criticas'); // Lista preguntas críticas
    
    // Rutas para la gestión de preguntas (ESCRITURA - TAMBIÉN PÚBLICAS TEMPORALMENTE)
    $routes->post('preguntas', 'PreguntaController::create'); // Crea una nueva pregunta
    $routes->put('preguntas/(:num)', 'PreguntaController::update/$1'); // Actualiza una pregunta existente
    $routes->delete('preguntas/(:num)', 'PreguntaController::delete/$1'); // Elimina una pregunta
    
    // Rutas para gestión de archivos
    $routes->post('files/upload-image', 'FileController::uploadImage'); // Subir imagen
    $routes->post('files/delete-image', 'FileController::deleteImage'); // Eliminar imagen
    $routes->get('files/image/(:segment)', 'FileController::getImage/$1'); // Obtener imagen
    
    // Rutas para imágenes de respuestas (legacy)
    $routes->post('preguntas/subir-imagen', 'PreguntaController::subirImagenRespuesta'); // Subir imagen para respuesta
    $routes->get('preguntas/imagen/(:segment)', 'PreguntaController::obtenerImagenRespuesta/$1'); // Obtener imagen de respuesta

    // Rutas para la gestión de respuestas
    $routes->get('respuestas', 'RespuestaController::index'); // Lista todas las respuestas
    $routes->get('respuestas/(:num)', 'RespuestaController::show/$1'); // Muestra una respuesta específica
    $routes->post('respuestas', 'RespuestaController::create'); // Crea una nueva respuesta
    $routes->put('respuestas/(:num)', 'RespuestaController::update/$1'); // Actualiza una respuesta existente
    $routes->delete('respuestas/(:num)', 'RespuestaController::delete/$1'); // Elimina una respuesta
    $routes->get('respuestas/pregunta/(:num)', 'RespuestaController::porPregunta/$1'); // Lista respuestas por pregunta
    $routes->get('respuestas/correctas/(:num)', 'RespuestaController::correctas/$1'); // Lista respuestas correctas por pregunta

    // Rutas de categorías (TODAS PÚBLICAS TEMPORALMENTE)
    $routes->group('categorias', ['namespace' => 'App\Controllers'], function($routes) {
        $routes->get('/', 'CategoriaController::index');
        $routes->get('(:num)', 'CategoriaController::show/$1');
        $routes->post('/', 'CategoriaController::create');
        $routes->put('(:num)', 'CategoriaController::update/$1');
        $routes->delete('(:num)', 'CategoriaController::delete/$1');
    });

    // Rutas de escuelas (oficinas de tránsito) - TODAS PÚBLICAS TEMPORALMENTE
    $routes->group('escuelas', ['namespace' => 'App\Controllers'], function($routes) {
        $routes->get('/', 'EscuelaController::index');
        $routes->get('(:num)', 'EscuelaController::show/$1');
        $routes->post('/', 'EscuelaController::create');
        $routes->put('(:num)', 'EscuelaController::update/$1');
        $routes->patch('(:num)', 'EscuelaController::update/$1'); // Agregar PATCH
        $routes->delete('(:num)', 'EscuelaController::delete/$1');
    });

    // Rutas para perfiles de usuarios - TODAS PÚBLICAS TEMPORALMENTE
    $routes->group('perfiles', ['namespace' => 'App\Controllers'], function($routes) {
        $routes->get('/', 'PerfilController::index');
        $routes->get('estadisticas', 'PerfilController::estadisticas');
        $routes->get('usuario/(:num)', 'PerfilController::getPerfilUsuario/$1');
        $routes->get('(:num)', 'PerfilController::show/$1');
        $routes->post('/', 'PerfilController::create');
        $routes->put('(:num)', 'PerfilController::update/$1');
        $routes->delete('(:num)', 'PerfilController::delete/$1');
    });

    // Rutas para categorías aprobadas (conductor-examen) - TODAS PÚBLICAS TEMPORALMENTE
    $routes->group('categorias-aprobadas', ['namespace' => 'App\Controllers'], function($routes) {
        $routes->get('/', 'CategoriaAprobadaController::index');
        $routes->get('estadisticas', 'CategoriaAprobadaController::estadisticas');
        $routes->get('conductor/(:num)', 'CategoriaAprobadaController::porConductor/$1');
        $routes->get('categoria/(:num)', 'CategoriaAprobadaController::porCategoria/$1');
        $routes->get('(:num)', 'CategoriaAprobadaController::show/$1');
        $routes->post('/', 'CategoriaAprobadaController::create');
        $routes->put('(:num)', 'CategoriaAprobadaController::update/$1');
        $routes->delete('(:num)', 'CategoriaAprobadaController::delete/$1');
    });

    // Rutas para la gestión de imágenes
    $routes->resource('imagenes');
    $routes->get('imagenes/get/(:num)', 'ImagenesController::getImagen/$1');
    $routes->post('imagenes/upload', 'ImagenController::upload');
    $routes->delete('imagenes/(:segment)', 'ImagenController::delete/$1');

    // Rutas del conductor (para tomar exámenes) - TODAS PÚBLICAS TEMPORALMENTE
    $routes->group('conductor', function($routes) {
        $routes->get('perfil', 'Conductores::perfil');
        $routes->get('examenes', 'Conductores::examenes');
        $routes->get('examenes/(:num)', 'Conductores::examen/$1');
        $routes->get('categorias', 'Conductores::categorias');
        $routes->get('historial', 'Conductores::historial');
    });
    
    // Rutas para gestión administrativa de conductores (CRUD) - TODAS PÚBLICAS TEMPORALMENTE
    $routes->resource('conductores');
    
});

