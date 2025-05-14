# Sistema de Exámenes para Conductores

## Descripción
Sistema de gestión de exámenes para conductores con autenticación JWT y roles diferenciados (conductores y técnicos).

## Estructura de Endpoints

### 1. Endpoints Públicos (No requieren autenticación)
- `POST /api/auth/registro`: Registro de nuevos conductores
- `POST /api/auth/login`: Inicio de sesión de conductores

### 2. Endpoints para Conductores (Requieren autenticación de conductor)
- `POST /api/auth/logout`: Cierre de sesión
- `POST /api/auth/refresh-token`: Renovación de token JWT
- `GET /api/resultados/verificar/:id`: Verificar estado de resultados
- `GET /api/resultados/historial/:id`: Ver historial de resultados
- `GET /api/resultados/ultimo/:id`: Ver último resultado

### 3. Endpoints para Técnicos (Requieren autenticación de técnico)

#### Gestión de Exámenes
- `GET /api/examenes`: Listar todos los exámenes
- `GET /api/examenes/:id`: Ver examen específico
- `POST /api/examenes`: Crear nuevo examen
- `PUT /api/examenes/:id`: Actualizar examen
- `DELETE /api/examenes/:id`: Eliminar examen
- `GET /api/examenes/categoria/:id`: Exámenes por categoría
- `GET /api/examenes/activos`: Exámenes activos

#### Gestión de Preguntas
- `GET /api/preguntas`: Listar todas las preguntas
- `GET /api/preguntas/:id`: Ver pregunta específica
- `POST /api/preguntas`: Crear nueva pregunta
- `PUT /api/preguntas/:id`: Actualizar pregunta
- `DELETE /api/preguntas/:id`: Eliminar pregunta
- `GET /api/preguntas/examen/:id`: Preguntas por examen
- `GET /api/preguntas/categoria/:id`: Preguntas por categoría
- `GET /api/preguntas/criticas`: Preguntas críticas

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

## Seguridad
- Validación de DNI y email únicos
- Protección de rutas por rol
- Manejo de sesiones con Redis (opcional)
- Tokens JWT con expiración configurable

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
- Es crítica: 0|1


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
```

### 2. Inicio de Sesión

**Endpoint:** `POST /api/auth/login`

**Headers:**
- Content-Type: application/json

**Body (raw JSON):**
```json
{
"dni": "123456780"
}
```

**Respuesta esperada (200 OK):**
```json
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
```

### 3. Obtener Puntuación de un Examen

Primero, necesitarás obtener la lista de exámenes asignados a un conductor:

**Endpoint:** `GET /api/resultados/historial/1` (donde 1 es el ID del conductor)

**Headers:**
- Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI...

**Respuesta esperada (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "examen_id": 1,
      "conductor_id": 1,
      "fecha_realizacion": "2023-05-14 15:30:22",
      "puntuacion": 85,
      "estado": "aprobado",
      "aciertos": 17,
      "errores": 3,
      "comentario": "Examen completado satisfactoriamente",
      "examen": {
        "titulo": "Examen Categoría E1",
        "categoria": "E1"
      }
    }
  ]
}
```

Para obtener el último resultado de un conductor específico:

**Endpoint:** `GET /api/resultados/ultimo/1` (donde 1 es el ID del conductor)

**Headers:**
- Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI...

**Respuesta esperada (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "examen_id": 1,
    "conductor_id": 1,
    "fecha_realizacion": "2023-05-14 15:30:22",
    "puntuacion": 85,
    "estado": "aprobado",
    "aciertos": 17,
    "errores": 3,
    "comentario": "Examen completado satisfactoriamente"
  }
}
```

### 4. Realizar un Examen y Registrar Respuestas

**Endpoint:** `POST /api/resultados/registrar`

**Headers:**
- Content-Type: application/json
- Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI...

**Body (raw JSON):**
```json
{
  "conductor_id": 1,
  "examen_id": 1,
  "respuestas": {
    "1": 2,
    "2": 1,
    "3": 3,
    "4": 1,
    "5": 2
  }
}
```

> Donde las claves en "respuestas" son los IDs de las preguntas y los valores son los IDs de las respuestas seleccionadas.

**Respuesta esperada (201 Created):**
```json
{
  "status": "success",
  "message": "Resultado registrado correctamente",
  "data": {
    "resultado_id": 2,
    "puntuacion": 80,
    "aciertos": 4,
    "errores": 1,
    "estado": "aprobado"
  }
}
```

### 5. Verificar si un Conductor Puede Presentar un Examen

**Endpoint:** `GET /api/resultados/verificar/1` (donde 1 es el ID del conductor)

**Headers:**
- Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI...

**Respuesta esperada (200 OK):**
```json
{
  "status": "success",
  "data": {
    "puede_presentar": true,
    "mensaje": "El conductor puede presentar el examen",
    "examenes_disponibles": [
      {
        "examen_id": 2,
        "titulo": "Examen Categoría E2",
        "categoria": "E2",
        "tiempo_limite": 30
      }
    ]
  }
}
```

## Notas importantes:

1. Asegúrate de guardar el token JWT recibido en el registro o inicio de sesión, ya que lo necesitarás para todas las solicitudes autenticadas.

2. El token tiene un tiempo de expiración (generalmente 1 hora). Si expira, debes obtener uno nuevo mediante el endpoint de inicio de sesión o refresh token.

3. Para preguntas críticas, ten en cuenta que una respuesta incorrecta resultará en la suspensión automática del examen, independientemente de las otras respuestas.

4. El sistema maneja diferentes estados para los conductores: "pendiente", "activo" y "rechazado".

## Códigos de estado HTTP

- 200: Operación exitosa
- 201: Recurso creado correctamente
- 400: Error de validación o datos incorrectos
- 401: No autorizado (token inválido o expirado)
- 404: Recurso no encontrado
- 500: Error interno del servidor
