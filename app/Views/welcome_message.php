<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>API de Exámenes de Conducción - Lomas de Zamora</title>
    <meta name="description" content="API para el sistema de exámenes de conducción de Lomas de Zamora, Buenos Aires">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">

    <!-- STYLES -->
    <style {csp-style-nonce}>
        * {
            transition: background-color 300ms ease, color 300ms ease;
        }
        *:focus {
            background-color: rgba(0, 123, 255, .2);
            outline: none;
        }
        html, body {
            color: rgba(33, 37, 41, 1);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
            font-size: 16px;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
        header {
            background-color: rgba(247, 248, 249, 1);
            padding: .4rem 0 0;
        }
        .menu {
            padding: .4rem 2rem;
        }
        header ul {
            border-bottom: 1px solid rgba(242, 242, 242, 1);
            list-style-type: none;
            margin: 0;
            overflow: hidden;
            padding: 0;
            text-align: right;
        }
        header li {
            display: inline-block;
        }
        header li a {
            border-radius: 5px;
            color: rgba(0, 0, 0, .5);
            display: block;
            height: 44px;
            text-decoration: none;
        }
        header li.menu-item a {
            border-radius: 5px;
            margin: 5px 0;
            height: 38px;
            line-height: 36px;
            padding: .4rem .65rem;
            text-align: center;
        }
        header li.menu-item a:hover,
        header li.menu-item a:focus {
            background-color: rgba(0, 123, 255, .2);
            color: rgba(0, 123, 255, 1);
        }
        header .logo {
            float: left;
            height: 44px;
            padding: .4rem .5rem;
        }
        header .menu-toggle {
            display: none;
            float: right;
            font-size: 2rem;
            font-weight: bold;
        }
        header .menu-toggle button {
            background-color: rgba(0, 123, 255, .6);
            border: none;
            border-radius: 3px;
            color: rgba(255, 255, 255, 1);
            cursor: pointer;
            font: inherit;
            font-size: 1.3rem;
            height: 36px;
            padding: 0;
            margin: 11px 0;
            overflow: visible;
            width: 40px;
        }
        header .menu-toggle button:hover,
        header .menu-toggle button:focus {
            background-color: rgba(0, 123, 255, .8);
            color: rgba(255, 255, 255, .8);
        }
        header .heroe {
            margin: 0 auto;
            max-width: 1100px;
            padding: 1rem 1.75rem 1.75rem 1.75rem;
        }
        header .heroe h1 {
            font-size: 2.5rem;
            font-weight: 500;
        }
        header .heroe h2 {
            font-size: 1.5rem;
            font-weight: 300;
        }
        section {
            margin: 0 auto;
            max-width: 1100px;
            padding: 2.5rem 1.75rem 3.5rem 1.75rem;
        }
        section h1 {
            margin-bottom: 2.5rem;
        }
        section h2 {
            font-size: 120%;
            line-height: 2.5rem;
            padding-top: 1.5rem;
        }
        section pre {
            background-color: rgba(247, 248, 249, 1);
            border: 1px solid rgba(242, 242, 242, 1);
            display: block;
            font-size: .9rem;
            margin: 2rem 0;
            padding: 1rem 1.5rem;
            white-space: pre-wrap;
            word-break: break-all;
        }
        section code {
            display: block;
        }
        section a {
            color: rgba(0, 123, 255, 1);
        }
        .further {
            background-color: rgba(247, 248, 249, 1);
            border-bottom: 1px solid rgba(242, 242, 242, 1);
            border-top: 1px solid rgba(242, 242, 242, 1);
        }
        footer {
            background-color: rgba(0, 123, 255, .8);
            text-align: center;
        }
        footer .environment {
            color: rgba(255, 255, 255, 1);
            padding: 2rem 1.75rem;
        }
        footer .copyrights {
            background-color: rgba(62, 62, 62, 1);
            color: rgba(200, 200, 200, 1);
            padding: .25rem 1.75rem;
        }
        @media (max-width: 629px) {
            header ul {
                padding: 0;
            }
            header .menu-toggle {
                padding: 0 1rem;
            }
            header .menu-item {
                background-color: rgba(244, 245, 246, 1);
                border-top: 1px solid rgba(242, 242, 242, 1);
                margin: 0 15px;
                width: calc(100% - 30px);
            }
            header .menu-toggle {
                display: block;
            }
            header .hidden {
                display: none;
            }
            header li.menu-item a {
                background-color: rgba(0, 123, 255, .1);
            }
            header li.menu-item a:hover,
            header li.menu-item a:focus {
                background-color: rgba(0, 123, 255, .7);
                color: rgba(255, 255, 255, .8);
            }
        }
    </style>
</head>
<body>

<!-- HEADER: MENU + HEROE SECTION -->
<header>
    <div class="menu">
        <ul>
            <li class="logo">
                <a href="/">
                    <h2>API Exámenes de Conducción</h2>
                </a>
            </li>
            <li class="menu-toggle">
                <button id="menuToggle">&#9776;</button>
            </li>
            <li class="menu-item hidden"><a href="/">Inicio</a></li>
            <li class="menu-item hidden"><a href="/docs">Documentación</a></li>
        </ul>
    </div>

    <div class="heroe">
        <h1>API de Exámenes de Conducción</h1>
        <h2>Municipalidad de Lomas de Zamora, Buenos Aires</h2>
    </div>
</header>

<!-- CONTENT -->
<section>
    <h1>Bienvenido a la API</h1>

    <p>Esta es la API backend para el sistema de exámenes de conducción de la Municipalidad de Lomas de Zamora.</p>

    <p>La API proporciona endpoints para:</p>
    <ul>
        <li>Registro y autenticación de conductores</li>
        <li>Gestión de exámenes teóricos</li>
        <li>Administración de preguntas y respuestas</li>
        <li>Control de resultados y certificaciones</li>
    </ul>

    <p>Para acceder a la documentación completa de la API, consulte el archivo README.md del proyecto que contiene todos los endpoints disponibles y sus especificaciones.</p>

    <p>El controlador principal de esta página se encuentra en:</p>
    <pre><code>app/Controllers/Home.php</code></pre>
</section>

<footer>
    <div class="environment">
        <p>Municipalidad de Lomas de Zamora - Sistema de Exámenes de Conducción</p>
    </div>
    <div class="copyrights">
        <p>&copy; <?= date('Y') ?> Todos los derechos reservados</p>
    </div>
</footer>

<script>
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.querySelectorAll('.menu-item').forEach(function(item) {
            item.classList.toggle('hidden');
        });
    });
</script>

</body>
</html>
