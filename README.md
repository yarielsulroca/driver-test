# Sistema de Exámenes para Conductores - Lomas de Zamora

## Descripción
Sistema de gestión de exámenes para conductores de la Municipalidad de Lomas de Zamora, Buenos Aires. El sistema permite la gestión completa del proceso de evaluación de conductores, desde el registro hasta la obtención de la licencia.

## Características Principales
- Registro y autenticación de conductores
- Gestión de exámenes teóricos
- Administración de preguntas y respuestas
- Control de resultados y certificaciones
- Sistema de roles (conductores y técnicos)
- Autenticación JWT
- Validación de DNI y email únicos

## Requisitos del Sistema
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Composer
- Servidor web (Apache/Nginx)

## Instalación

1. Clonar el repositorio:
```bash
git clone [URL_DEL_REPOSITORIO]
cd examen
```

2. Instalar dependencias:
```bash
composer install
```

3. Configurar el archivo .env:
```bash
cp env .env
```
Editar el archivo .env con los datos de conexión a la base de datos y otras configuraciones.

4. Ejecutar migraciones:
```bash
php spark migrate
```

5. Ejecutar seeders (opcional):
```bash
php spark db:seed
```

## Estructura de Endpoints

### 1. Endpoints Públicos (No requieren autenticación)

#### Registro de Conductores
- **Endpoint:** `POST /api/auth/registro`
- **Descripción:** Registro de nuevos conductores
- **Body:**
```json
{
    "nombre": "string",
    "apellido": "string",
    "dni": "string",
    "email": "string",
    "telefono": "string"
}
```
- **Respuesta (201):**
```json
{
    "status": "success",
    "message": "¡Registro exitoso!",
    "data": {
        "token": "string",
        "conductor": {
            "id": "string",
            "nombre": "string",
            "apellido": "string",
            "dni": "string",
            "telefono": "string",
            "email": "string",
            "estado_registro": "string"
        }
    }
}
```

#### Inicio de Sesión
- **Endpoint:** `POST /api/auth/login`
- **Descripción:** Inicio de sesión de conductores
- **Body:**
```json
{
    "dni": "string"
}
```
- **Respuesta (200):**
```json
{
    "status": "success",
    "message": "¡Inicio de sesión exitoso!",
    "data": {
        "token": "string",
        "conductor": {
            "id": "string",
            "nombre": "string",
            "apellido": "string",
            "dni": "string",
            "estado_registro": "string"
        }
    }
}
```

### 2. Endpoints para Conductores (Requieren autenticación)

#### Cierre de Sesión
- **Endpoint:** `POST /api/auth/logout`
- **Headers:** `Authorization: Bearer {token}`
- **Descripción:** Cierre de sesión del conductor

#### Renovación de Token
- **Endpoint:** `POST /api/auth/refresh-token`
- **Headers:** `Authorization: Bearer {token}`
- **Descripción:** Renovación del token JWT

#### Verificar Estado de Resultados
- **Endpoint:** `GET /api/resultados/verificar/:id`
- **Headers:** `Authorization: Bearer {token}`
- **Descripción:** Verifica el estado de los resultados de un examen

#### Ver Historial de Resultados
- **Endpoint:** `GET /api/resultados/historial/:id`
- **Headers:** `Authorization: Bearer {token}`
- **Descripción:** Obtiene el historial completo de resultados

#### Ver Último Resultado
- **Endpoint:** `GET /api/resultados/ultimo/:id`
- **Headers:** `Authorization: Bearer {token}`
- **Descripción:** Obtiene el último resultado del conductor

### 3. Endpoints para Técnicos (Requieren autenticación)

#### Gestión de Exámenes

##### Listar Exámenes
- **Endpoint:** `GET /api/examenes`
- **Headers:** `Authorization: Bearer {token}`
- **Descripción:** Lista todos los exámenes disponibles

##### Ver Examen Específico
- **Endpoint:** `GET /api/examenes/:id`
- **Headers:** `Authorization: Bearer {token}`
- **Descripción:** Obtiene los detalles de un examen específico

##### Crear Examen
- **Endpoint:** `POST /api/examenes`
- **Headers:** `Authorization: Bearer {token}`
- **Body:**
```json
{
    "nombre": "string",
    "descripcion": "string",
    "duracion": "integer",
    "puntaje_minimo": "integer",
    "numero_preguntas": "integer"
}
```

##### Actualizar Examen
- **Endpoint:** `PUT /api/examenes/:id`
- **Headers:** `Authorization: Bearer {token}`
- **Body:** Mismo formato que crear examen

##### Eliminar Examen
- **Endpoint:** `DELETE /api/examenes/:id`
- **Headers:** `Authorization: Bearer {token}`

##### Exámenes por Categoría
- **Endpoint:** `GET /api/examenes/categoria/:id`
- **Headers:** `Authorization: Bearer {token}`

##### Exámenes Activos
- **Endpoint:** `GET /api/examenes/activos`
- **Headers:** `Authorization: Bearer {token}`

#### Gestión de Preguntas

##### Listar Preguntas
- **Endpoint:** `GET /api/preguntas`
- **Headers:** `Authorization: Bearer {token}`

##### Ver Pregunta Específica
- **Endpoint:** `GET /api/preguntas/:id`
- **Headers:** `Authorization: Bearer {token}`

##### Crear Pregunta
- **Endpoint:** `POST /api/preguntas`
- **Headers:** `Authorization: Bearer {token}`
- **Body:**
```json
{
    "enunciado": "string",
    "tipo": "multiple|verdadero_falso",
    "puntaje": "integer",
    "dificultad": "baja|media|alta",
    "es_critica": "boolean"
}
```

##### Actualizar Pregunta
- **Endpoint:** `PUT /api/preguntas/:id`
- **Headers:** `Authorization: Bearer {token}`
- **Body:** Mismo formato que crear pregunta

##### Eliminar Pregunta
- **Endpoint:** `DELETE /api/preguntas/:id`
- **Headers:** `Authorization: Bearer {token}`

##### Preguntas por Examen
- **Endpoint:** `GET /api/preguntas/examen/:id`
- **Headers:** `Authorization: Bearer {token}`

##### Preguntas por Categoría
- **Endpoint:** `GET /api/preguntas/categoria/:id`
- **Headers:** `Authorization: Bearer {token}`

##### Preguntas Críticas
- **Endpoint:** `GET /api/preguntas/criticas`
- **Headers:** `Authorization: Bearer {token}`

## Autenticación

### JWT (JSON Web Tokens)
El sistema utiliza JWT para la autenticación. Los tokens deben incluirse en el header:
```
Authorization: Bearer <token>
```

### Estados de Conductor
- **pendiente**: Estado inicial al registrarse
- **activo**: Se actualiza automáticamente en el primer login
- **rechazado**: No puede acceder al sistema

### Roles
1. **conductor**: Acceso a gestión de su perfil y resultados
2. **tecnico**: Acceso completo a gestión de exámenes y preguntas

## Validaciones

### Conductor
- Nombre: 3-50 caracteres
- DNI: 8-20 caracteres, único
- Email: Formato válido, único (opcional)

### Examen
- Nombre: 3-100 caracteres
- Descripción: Mínimo 10 caracteres
- Duración: Mayor a 0 minutos
- Puntaje mínimo: Mayor a 0
- Número de preguntas: Mayor a 0

### Pregunta
- Enunciado: Mínimo 10 caracteres
- Tipo: multiple|verdadero_falso
- Puntaje: Mayor a 0
- Dificultad: baja|media|alta
- Es crítica: boolean

## Respuestas API
Todas las respuestas siguen el formato:
```json
{
    "status": "success|error",
    "message": "Mensaje descriptivo",
    "data": {
        // Datos de la respuesta
    }
}
```

## Códigos de Estado HTTP
- 200: Éxito
- 201: Creado exitosamente
- 400: Error de validación
- 401: No autorizado
- 403: Prohibido (rol incorrecto)
- 404: No encontrado
- 500: Error del servidor

## Configuración
- JWT_SECRET_KEY: Clave para firmar tokens
- JWT_TIME_TO_LIVE: Tiempo de vida del token (default: 3600s)

## Soporte
Para soporte técnico o consultas, contactar a:
- Email: [EMAIL_SOPORTE]
- Teléfono: [TELEFONO_SOPORTE]

## Licencia
Este proyecto es propiedad de la Municipalidad de Lomas de Zamora. Todos los derechos reservados.

----------------------------------------------------- PRUEBAS EN POSTMAN -----------------------------------
# Sistema de Exámenes para Conductores - API Documentation

Este sistema permite gestionar exámenes para conductores, con funcionalidades para registro, autenticación, realización de exámenes y gestión de resultados.

## Ejemplos de uso con Postman

A continuación se muestran ejemplos de cómo interactuar con la API utilizando Postman.

### 1. Registro de un Conductor

**Endpoint:** `POST /api/auth/registro`

**Headers:**
- Content-Type: application/json

**Body (raw JSON):**
```json
{
  "nombre": "Juan",
  "apellido": "Pérez",
  "dni": "123456780",
  "email": "juan.perez@example.com",
  "telefono": "600123456"

}
```

**Respuesta esperada (201 Created):**
```json
{
    "status": "success",
    "message": "¡Registro exitoso! Bienvenido al sistema de exámenes.",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJTaXN0ZW1hIGRlIEV4XHUwMGUxbWVuZXMiLCJzdWIiOiIxIiwiaWF0IjoxNzQ3MjQ5MTUzLCJleHAiOjE3NDcyNTI3NTMsInJvbCI6ImNvbmR1Y3RvciIsImRuaSI6IjEyMzQ1Njc4MCJ9.g9R7lbpr_s10fUBMEuFfEk3keuMrKnCo0Z_7LUkgDLE",
        "conductor": {
            "id": "1",
            "nombre": "Juan",
            "apellido": "Pérez",
            "dni": "123456780",
            "telefono": "600123456",
            "email": "juan.perez@example.com",
            "estado_registro": "pendiente",
            "tiene_examenes": false,
            "examenes": []
        },
        "token_expira_en": "2025-05-14 19:59:13",
        "instrucciones": "Por favor, guarde este token de forma segura. Lo necesitará para futuras autenticaciones."
    }
}
### 2. Inicio de Sesión

**Endpoint:** `POST /api/auth/login`

**Headers:**
- Content-Type: application/json

**Body (raw JSON):**
```json
{
"dni": "123456780"
}

**Respuesta esperada (200 OK):**
json
{
    "status": "success",
    "message": "¡Inicio de sesión exitoso!",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJTaXN0ZW1hIGRlIEV4XHUwMGUxbWVuZXMiLCJzdWIiOiIxIiwiaWF0IjoxNzQ3MjQ5Mjk4LCJleHAiOjE3NDcyNTI4OTgsInJvbCI6ImNvbmR1Y3RvciIsImRuaSI6IjEyMzQ1Njc4MCJ9.c-vfac3fYcJZ6MjXa8YIrCB98yfcwHuA9UgbXi5HDiE",
        "token_expira_en": "2025-05-14 20:01:38",
        "conductor": {
            "id": "1",
            "nombre": "Juan",
            "apellido": "Pérez",
            "dni": "123456780",
            "estado_registro": ""
        },
        "examenes": {
            "estado": "Sin exámenes asociados",
            "detalle": []
        }
    }
}

categoria:POST https://examen.test/api/categorias
{
    "codigo": "B",
    "nombre": "Automóviles",
    "descripcion": "Licencia para conducir automóviles y camionetas",
    "requisitos": "[\"Edad mínima: 17 años\", \"Examen teórico aprobado\", \"Examen práctico aprobado\"]",
    "estado": "activo"
}
escuela: lulgar_del examen. POST https://examen.test/api/escuelas

{
    "codigo": "ESC002",
    "nombre": "Centro de Formación Vial",
    "direccion": "Calle Secundaria 456, Ciudad",
    "telefono": "987-654-3210",
    "email": "info@formacionvial.com",
    "horario": "Lunes a Sábado 7:00 - 20:00",
    "estado": "activo"
}