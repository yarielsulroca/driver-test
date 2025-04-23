# Sistema de Gestión de Exámenes para Licencias de Conducir

## Descripción del Sistema
Sistema web desarrollado en CodeIgniter 4 para la gestión de exámenes teóricos de licencias de conducir, que permite administrar diferentes categorías de licencias, escuelas de conducción, conductores y técnicos evaluadores.

## Roles del Sistema

### 1. Técnico
- Acceso al panel administrativo
- Gestión de exámenes y categorías
- Evaluación de solicitudes de conductores
- Asignación de exámenes
- Revisión de resultados
- Gestión de escuelas de conducción

### 2. Conductor
- Registro en el sistema
- Selección de categoría de licencia
- Realización de exámenes asignados
- Consulta de resultados y estado

## Funcionalidades Principales

### Gestión de Usuarios
1. **Técnicos**
   - Registro con datos personales
   - Autenticación segura
   - Gestión de perfiles
   - Estado activo/inactivo

2. **Conductores**
   - Registro con validación de datos
   - Selección de categoría de licencia
   - Estado de registro (pendiente, aprobado, rechazado)
   - Historial de exámenes
   - Bloqueo temporal por reprobación

### Gestión de Categorías
- Clasificación por tipo de vehículo
- Requisitos específicos por categoría
- Edad mínima requerida
- Experiencia previa necesaria
- Documentación requerida

### Gestión de Exámenes
1. **Configuración**
   - Asignación por categoría
   - Número de preguntas
   - Tiempo límite
   - Puntaje mínimo de aprobación
   - Preguntas críticas

2. **Tipos de Preguntas**
   - Opción múltiple
   - Verdadero/Falso
   - Con/Sin imágenes
   - Preguntas críticas

3. **Evaluación**
   - Registro de respuestas
   - Control de tiempo
   - Cálculo automático de puntaje
   - Identificación de errores críticos

### Sistema de Bloqueo
- Bloqueo automático por reprobación
- Período de espera de 7 días laborales
- Desbloqueo automático al cumplir el período
- Registro de fechas de bloqueo/desbloqueo

## Estructura de la Base de Datos

### Tablas Principales
1. **usuarios**
   - usuario_id (PK)
   - nombre, apellido
   - email, password
   - rol (técnico)
   - estado

2. **conductores**
   - conductor_id (PK)
   - datos personales
   - categoria_id (FK)
   - estado_registro
   - fecha_registro

3. **categorias**
   - categoria_id (PK)
   - sigla, nombre
   - descripción
   - requisitos
   - edad_mínima
   - experiencia_requerida

4. **examenes**
   - examen_id (PK)
   - categoria_id (FK)
   - escuela_id (FK)
   - configuración
   - fechas
   - requisitos

5. **preguntas**
   - pregunta_id (PK)
   - examen_id (FK)
   - categoria_id (FK)
   - tipo, dificultad
   - es_critica

6. **respuestas**
   - respuesta_id (PK)
   - pregunta_id (FK)
   - texto
   - es_correcta

7. **resultados_examenes**
   - resultado_id (PK)
   - conductor_id (FK)
   - examen_id (FK)
   - estadísticas
   - estado
   - bloqueo

8. **respuestas_conductor**
   - respuesta_conductor_id (PK)
   - resultado_examen_id (FK)
   - pregunta_id (FK)
   - respuesta_id (FK)
   - tiempo_respuesta
   - es_correcta

### Relaciones Principales
- Conductor -> Categoría (N:1)
- Conductor -> Resultados (1:N)
- Examen -> Categoría (N:1)
- Examen -> Preguntas (1:N)
- Pregunta -> Respuestas (1:N)
- ResultadoExamen -> RespuestasConductor (1:N)

## Flujo del Sistema

1. **Registro de Conductor**
   - Ingreso de datos personales
   - Selección de categoría
   - Estado inicial: pendiente

2. **Aprobación de Registro**
   - Técnico revisa solicitud
   - Verifica requisitos
   - Aprueba/Rechaza registro

3. **Asignación de Examen**
   - Técnico asigna examen
   - Sistema verifica elegibilidad
   - Notificación al conductor

4. **Realización del Examen**
   - Verificación de identidad
   - Control de tiempo
   - Registro de respuestas
   - Cálculo de resultado

5. **Gestión de Resultados**
   - Registro detallado de respuestas
   - Cálculo de estadísticas
   - Aplicación de bloqueos
   - Generación de reportes

## Requisitos Técnicos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- CodeIgniter 4.x
- Servidor web Apache/Nginx

## Instalación
1. Clonar el repositorio
2. Configurar el archivo .env
3. Ejecutar migraciones:
   ```bash
   php spark migrate
   ```
4. Ejecutar seeders:
   ```bash
   php spark db:seed DatabaseSeeder
   ```

## Credenciales por Defecto
### Técnico Administrador
- Email: admin@sistema.com
- Contraseña: admin123

### Técnico Evaluador
- Email: tecnico@sistema.com
- Contraseña: tecnico123

# CodeIgniter 4 Framework

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds the distributable version of the framework.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Contributing

We welcome contributions from the community.

Please read the [*Contributing to CodeIgniter*](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the development repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
