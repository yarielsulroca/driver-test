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
