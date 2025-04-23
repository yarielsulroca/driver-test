# Sistema de Exámenes para Conductores

Este proyecto es un sistema de gestión de exámenes para conductores, desarrollado con CodeIgniter 4. Permite la administración de conductores, exámenes, preguntas y el seguimiento de resultados.

## Funcionalidades Principales

### Gestión de Conductores
- Registro y administración de conductores
- Información personal (nombre, apellido, DNI, etc.)
- Historial de exámenes realizados

### Gestión de Exámenes
- Creación y administración de exámenes
- Configuración de duración y puntaje mínimo
- Asignación a escuelas de manejo
- Categorización por tipo de vehículo

### Categorías de Exámenes
El sistema maneja diferentes categorías de exámenes, identificadas por siglas que representan tipos específicos de vehículos:

- **A**: Motocicletas y similares
  - A1: Motocicletas hasta 125cc
  - A2: Motocicletas hasta 35kW
  - A: Motocicletas sin restricción de potencia

- **B**: Automóviles
  - B1: Automóviles particulares
  - B2: Automóviles de servicio público

- **C**: Vehículos de carga
  - C1: Camiones ligeros
  - C2: Camiones pesados
  - C3: Camiones articulados

- **D**: Vehículos de pasajeros
  - D1: Microbuses
  - D2: Buses
  - D3: Buses articulados

- **E**: Vehículos especiales
  - E1: Maquinaria agrícola
  - E2: Vehículos de emergencia
  - E3: Vehículos de transporte especial

Cada categoría tiene:
- Sigla única identificadora
- Descripción detallada del tipo de vehículo
- Requisitos específicos para la licencia
- Preguntas especializadas en el examen

### Sistema de Preguntas
- Creación de preguntas con múltiples opciones
- Cuatro respuestas por pregunta
- Una única respuesta correcta por pregunta
- Asignación de puntaje por pregunta
- Preguntas específicas por categoría

### Resultados y Análisis
- Registro detallado de resultados
- Tiempo empleado en cada examen
- Estadísticas de rendimiento
- Historial de intentos
- Seguimiento por categoría

## Estructura de la Base de Datos

### Tablas Principales

1. **conductores**
   - Información personal de los conductores
   - Datos de contacto y documentación

2. **categorias**
   - Sigla de la categoría
   - Descripción del tipo de vehículo
   - Requisitos específicos

3. **examenes**
   - Configuración de exámenes
   - Duración y requisitos
   - Relación con escuelas
   - Categoría asignada

4. **preguntas**
   - Enunciados de preguntas
   - Tipo de pregunta
   - Puntaje asignado
   - Relación con exámenes
   - Categoría asociada

5. **respuestas**
   - Opciones de respuesta
   - Indicador de respuesta correcta
   - Relación con preguntas

6. **resultados_examenes**
   - Registro de intentos de examen
   - Puntaje obtenido
   - Tiempo empleado
   - Estado (aprobado/reprobado)
   - Categoría del examen

## Relaciones entre Modelos

### ConductorModel
- Tiene muchos `ResultadoExamenModel`
- Almacena información personal del conductor

### CategoriaModel
- Tiene muchos `ExamenModel`
- Define los tipos de vehículos y requisitos

### ExamenModel
- Pertenece a una `EscuelaModel`
- Pertenece a una `CategoriaModel`
- Tiene muchos `PreguntaModel`
- Tiene muchos `ResultadoExamenModel`

### PreguntaModel
- Pertenece a un `ExamenModel`
- Pertenece a una `CategoriaModel`
- Tiene muchos `RespuestaModel`

### RespuestaModel
- Pertenece a una `PreguntaModel`
- Almacena las opciones de respuesta

### ResultadoExamenModel
- Pertenece a un `ConductorModel`
- Pertenece a un `ExamenModel`
- Registra el desempeño en el examen

## Requisitos del Sistema
- PHP 7.4 o superior
- MySQL 5.7 o superior
- CodeIgniter 4
- Composer (para gestión de dependencias)

## Instalación
1. Clonar el repositorio
2. Ejecutar `composer install`
3. Configurar el archivo `.env`
4. Ejecutar las migraciones de la base de datos
5. Configurar el servidor web

## Uso
1. Acceder al sistema con credenciales de administrador
2. Crear conductores y exámenes
3. Configurar preguntas y respuestas por categoría
4. Asignar exámenes a conductores según la categoría deseada
5. Revisar resultados y estadísticas por categoría

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
