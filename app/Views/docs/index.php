<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <meta name="description" content="<?= $description ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Highlight.js para resaltado de código -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    
    <style>
        .endpoint {
            border-left: 4px solid #3b82f6;
            padding-left: 1rem;
            margin: 1rem 0;
        }
        .method {
            font-weight: bold;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
        .get { background-color: #dbeafe; color: #1e40af; }
        .post { background-color: #dcfce7; color: #166534; }
        .put { background-color: #fef3c7; color: #92400e; }
        .delete { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    Documentación API
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Sistema de Exámenes de Conducción - Lomas de Zamora
                </p>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Sidebar -->
            <div class="flex">
                <div class="w-64 bg-white shadow rounded-lg p-4 mr-6">
                    <nav>
                        <h2 class="text-lg font-semibold mb-4">Contenido</h2>
                        <ul class="space-y-2">
                            <li><a href="#introduccion" class="text-blue-600 hover:text-blue-800">Introducción</a></li>
                            <li><a href="#autenticacion" class="text-blue-600 hover:text-blue-800">Autenticación</a></li>
                            <li><a href="#endpoints-publicos" class="text-blue-600 hover:text-blue-800">Endpoints Públicos</a></li>
                            <li><a href="#endpoints-conductores" class="text-blue-600 hover:text-blue-800">Endpoints Conductores</a></li>
                            <li><a href="#endpoints-tecnicos" class="text-blue-600 hover:text-blue-800">Endpoints Técnicos</a></li>
                            <li><a href="#validaciones" class="text-blue-600 hover:text-blue-800">Validaciones</a></li>
                            <li><a href="#respuestas" class="text-blue-600 hover:text-blue-800">Respuestas API</a></li>
                        </ul>
                    </nav>
                </div>

                <!-- Content -->
                <div class="flex-1 bg-white shadow rounded-lg p-6">
                    <section id="introduccion" class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Introducción</h2>
                        <p class="mb-4">
                            Bienvenido a la documentación de la API del Sistema de Exámenes de Conducción de Lomas de Zamora.
                            Esta API proporciona endpoints para la gestión completa del proceso de evaluación de conductores.
                        </p>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                            <p class="text-blue-700">
                                <strong>Base URL:</strong> http://examen.test/api
                            </p>
                        </div>
                    </section>

                    <section id="autenticacion" class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Autenticación</h2>
                        <p class="mb-4">
                            La API utiliza JWT (JSON Web Tokens) para la autenticación. Los tokens deben incluirse en el header de las peticiones:
                        </p>
                        <pre><code class="language-bash">Authorization: Bearer &lt;token&gt;</code></pre>
                    </section>

                    <section id="endpoints-publicos" class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Endpoints Públicos</h2>
                        
                        <div class="endpoint">
                            <div class="flex items-center mb-2">
                                <span class="method post mr-2">POST</span>
                                <code class="text-sm">/api/auth/registro</code>
                            </div>
                            <p class="text-gray-600 mb-2">Registro de nuevos conductores</p>
                            <div class="bg-gray-50 p-4 rounded">
                                <h4 class="font-semibold mb-2">Request Body:</h4>
                                <pre><code class="language-json">{
    "nombre": "string",
    "apellido": "string",
    "dni": "string",
    "email": "string",
    "telefono": "string"
}</code></pre>
                            </div>
                        </div>

                        <div class="endpoint">
                            <div class="flex items-center mb-2">
                                <span class="method post mr-2">POST</span>
                                <code class="text-sm">/api/auth/login</code>
                            </div>
                            <p class="text-gray-600 mb-2">Inicio de sesión de conductores</p>
                            <div class="bg-gray-50 p-4 rounded">
                                <h4 class="font-semibold mb-2">Request Body:</h4>
                                <pre><code class="language-json">{
    "dni": "string"
}</code></pre>
                            </div>
                        </div>
                    </section>

                    <section id="endpoints-conductores" class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Endpoints para Conductores</h2>
                        <p class="mb-4 text-gray-600">Requieren autenticación con token JWT</p>
                        
                        <div class="endpoint">
                            <div class="flex items-center mb-2">
                                <span class="method get mr-2">GET</span>
                                <code class="text-sm">/api/resultados/verificar/:id</code>
                            </div>
                            <p class="text-gray-600 mb-2">Verifica el estado de los resultados de un examen</p>
                        </div>

                        <div class="endpoint">
                            <div class="flex items-center mb-2">
                                <span class="method get mr-2">GET</span>
                                <code class="text-sm">/api/resultados/historial/:id</code>
                            </div>
                            <p class="text-gray-600 mb-2">Obtiene el historial completo de resultados</p>
                        </div>
                    </section>

                    <section id="endpoints-tecnicos" class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Endpoints para Técnicos</h2>
                        <p class="mb-4 text-gray-600">Requieren autenticación con token JWT y rol de técnico</p>
                        
                        <div class="endpoint">
                            <div class="flex items-center mb-2">
                                <span class="method get mr-2">GET</span>
                                <code class="text-sm">/api/examenes</code>
                            </div>
                            <p class="text-gray-600 mb-2">Lista todos los exámenes disponibles</p>
                        </div>

                        <div class="endpoint">
                            <div class="flex items-center mb-2">
                                <span class="method post mr-2">POST</span>
                                <code class="text-sm">/api/examenes</code>
                            </div>
                            <p class="text-gray-600 mb-2">Crea un nuevo examen</p>
                            <div class="bg-gray-50 p-4 rounded">
                                <h4 class="font-semibold mb-2">Request Body:</h4>
                                <pre><code class="language-json">{
    "nombre": "string",
    "descripcion": "string",
    "duracion": "integer",
    "puntaje_minimo": "integer",
    "numero_preguntas": "integer"
}</code></pre>
                            </div>
                        </div>
                    </section>

                    <section id="validaciones" class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Validaciones</h2>
                        
                        <div class="bg-gray-50 p-4 rounded mb-4">
                            <h3 class="font-semibold mb-2">Conductor</h3>
                            <ul class="list-disc list-inside text-gray-600">
                                <li>Nombre: 3-50 caracteres</li>
                                <li>DNI: 8-20 caracteres, único</li>
                                <li>Email: Formato válido, único (opcional)</li>
                            </ul>
                        </div>

                        <div class="bg-gray-50 p-4 rounded">
                            <h3 class="font-semibold mb-2">Examen</h3>
                            <ul class="list-disc list-inside text-gray-600">
                                <li>Nombre: 3-100 caracteres</li>
                                <li>Descripción: Mínimo 10 caracteres</li>
                                <li>Duración: Mayor a 0 minutos</li>
                                <li>Puntaje mínimo: Mayor a 0</li>
                                <li>Número de preguntas: Mayor a 0</li>
                            </ul>
                        </div>
                    </section>

                    <section id="respuestas" class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Respuestas API</h2>
                        <p class="mb-4">Todas las respuestas siguen el formato:</p>
                        <pre><code class="language-json">{
    "status": "success|error",
    "message": "Mensaje descriptivo",
    "data": {
        // Datos de la respuesta
    }
}</code></pre>
                    </section>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white shadow mt-8">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500 text-sm">
                    &copy; <?= date('Y') ?> Municipalidad de Lomas de Zamora - Sistema de Exámenes de Conducción
                </p>
            </div>
        </footer>
    </div>

    <script>
        // Inicializar highlight.js
        hljs.highlightAll();

        // Smooth scroll para los enlaces del menú
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 