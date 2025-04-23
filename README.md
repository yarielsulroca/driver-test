# API de Sistema de Gestión de Exámenes para Licencias de Conducir

## Descripción del Sistema
API REST desarrollada en CodeIgniter 4 para la gestión de exámenes teóricos de licencias de conducir. Proporciona endpoints para administrar diferentes categorías de licencias, escuelas de conducción, conductores y técnicos evaluadores.

## Endpoints de la API

### Autenticación
```
POST /api/auth/login
POST /api/auth/logout
POST /api/auth/refresh-token
```

### Técnicos
```
GET    /api/tecnicos
POST   /api/tecnicos
GET    /api/tecnicos/{id}
PUT    /api/tecnicos/{id}
DELETE /api/tecnicos/{id}
```

### Conductores
```
GET    /api/conductores
POST   /api/conductores/registro
GET    /api/conductores/{id}
PUT    /api/conductores/{id}
DELETE /api/conductores/{id}
GET    /api/conductores/{id}/examenes
POST   /api/conductores/{id}/verificar-elegibilidad
```

### Categorías
```
GET    /api/categorias
POST   /api/categorias
GET    /api/categorias/{id}
PUT    /api/categorias/{id}
DELETE /api/categorias/{id}
GET    /api/categorias/{id}/examenes
GET    /api/categorias/{id}/preguntas
```

### Exámenes
```
GET    /api/examenes
POST   /api/examenes
GET    /api/examenes/{id}
PUT    /api/examenes/{id}
DELETE /api/examenes/{id}
GET    /api/examenes/{id}/preguntas
POST   /api/examenes/{id}/asignar
```

### Preguntas y Respuestas
```
GET    /api/preguntas
POST   /api/preguntas
GET    /api/preguntas/{id}
PUT    /api/preguntas/{id}
DELETE /api/preguntas/{id}
GET    /api/preguntas/{id}/respuestas
```

### Resultados
```
GET    /api/resultados
POST   /api/resultados
GET    /api/resultados/{id}
GET    /api/resultados/conductor/{id}
GET    /api/resultados/examen/{id}
```

## Roles y Permisos

### 1. Técnico
- Autenticación mediante JWT
- CRUD completo de exámenes y categorías
- Gestión de solicitudes de conductores
- Asignación de exámenes
- Acceso a reportes y estadísticas

### 2. Conductor
- Autenticación mediante JWT
- Registro en el sistema
- Consulta de categorías disponibles
- Realización de exámenes asignados
- Consulta de resultados propios

## Modelos de Datos

### Respuestas de la API
Todas las respuestas siguen el siguiente formato:
```json
{
    "status": "success|error",
    "message": "Mensaje descriptivo",
    "data": {
        // Datos de la respuesta
    }
}
```

### Estructura de Datos Principales

1. **Usuario (Técnico)**
```json
{
    "usuario_id": integer,
    "nombre": string,
    "apellido": string,
    "email": string,
    "rol": "tecnico",
    "estado": "activo|inactivo"
}
```

2. **Conductor**
```json
{
    "conductor_id": integer,
    "nombre": string,
    "apellido": string,
    "dni": string,
    "fecha_nacimiento": date,
    "direccion": string,
    "telefono": string,
    "email": string,
    "categoria_id": integer,
    "estado_registro": "pendiente|aprobado|rechazado",
    "fecha_registro": datetime
}
```

3. **Examen**
```json
{
    "examen_id": integer,
    "categoria_id": integer,
    "escuela_id": integer,
    "nombre": string,
    "descripcion": string,
    "duracion_minutos": integer,
    "puntaje_minimo": float,
    "numero_preguntas": integer,
    "fecha_inicio": datetime,
    "fecha_fin": datetime
}
```

4. **Resultado Examen**
```json
{
    "resultado_id": integer,
    "conductor_id": integer,
    "examen_id": integer,
    "puntaje_total": float,
    "preguntas_correctas": integer,
    "preguntas_incorrectas": integer,
    "tiempo_empleado": integer,
    "estado": "aprobado|reprobado",
    "bloqueado": boolean,
    "fecha_bloqueo": datetime,
    "respuestas": [
        {
            "pregunta_id": integer,
            "respuesta_id": integer,
            "es_correcta": boolean,
            "tiempo_respuesta": integer
        }
    ]
}
```

## Seguridad

### Autenticación
- Implementación de JWT (JSON Web Tokens)
- Tokens de acceso y renovación
- Expiración configurable de tokens

### Autorización
- Middleware de verificación de roles
- Validación de permisos por endpoint
- Protección contra CSRF en endpoints sensibles

### Validaciones
- Sanitización de datos de entrada
- Validación de tipos y formatos
- Protección contra inyección SQL
- Rate limiting en endpoints críticos

## Requisitos Técnicos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- CodeIgniter 4.x
- Extensiones PHP requeridas:
  - JSON
  - MySQLi
  - intl
  - mbstring

## Instalación
1. Clonar el repositorio
2. Configurar el archivo .env:
   ```env
   CI_ENVIRONMENT = production
   app.baseURL = 'http://tu-dominio.com/'
   
   database.default.hostname = localhost
   database.default.database = nombre_db
   database.default.username = usuario
   database.default.password = contraseña
   
   jwt.secretKey = 'tu_clave_secreta'
   jwt.timeToLive = 3600
   ```
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
```json
{
    "email": "admin@sistema.com",
    "password": "admin123"
}
```

### Técnico Evaluador
```json
{
    "email": "tecnico@sistema.com",
    "password": "tecnico123"
}
```

## Documentación Adicional
La documentación completa de la API está disponible en:
```
/api/docs
```

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
