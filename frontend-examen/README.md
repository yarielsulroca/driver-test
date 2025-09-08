# Sistema de Exámenes - Frontend Angular

Este es el frontend del Sistema de Exámenes desarrollado en Angular 20 con las mejores prácticas y una arquitectura moderna.

## 🚀 Características Implementadas

### ✅ Arquitectura y Configuración
- **Angular 20** con configuración moderna
- **Variables de entorno** para desarrollo y producción
- **Arquitectura modular** con componentes standalone
- **TypeScript** con tipado estricto
- **Angular Material** para componentes UI

### ✅ Servicios Core
- **ApiService**: Servicio HTTP centralizado con gestión de errores
- **AuthService**: Gestión de autenticación JWT con renovación automática
- **NotificationService**: Sistema de notificaciones global
- **LoadingService**: Gestión de estados de carga
- **ValidationService**: Validaciones de formularios centralizadas

### ✅ Seguridad y Autenticación
- **JWT Interceptor**: Manejo automático de tokens
- **AuthGuard**: Protección de rutas con roles y permisos
- **Renovación automática** de tokens
- **Gestión de sesiones** segura

### ✅ Componentes Compartidos
- **LoadingComponent**: Indicador de carga global
- **NotificationsComponent**: Sistema de notificaciones
- **Componentes reutilizables** para formularios y tablas

### ✅ Gestión de Estado
- **RxJS** para programación reactiva
- **BehaviorSubject** para estado local
- **Observables** para comunicación entre componentes

## 📁 Estructura del Proyecto

```
src/
├── app/
│   ├── components/
│   │   ├── shared/
│   │   │   ├── loading/
│   │   │   └── notifications/
│   │   ├── auth/
│   │   ├── admin/
│   │   ├── conductores/
│   │   ├── exams/
│   │   ├── inicio/
│   │   └── layout/
│   ├── services/
│   │   ├── api.service.ts
│   │   ├── auth.service.ts
│   │   ├── notification.service.ts
│   │   ├── loading.service.ts
│   │   └── validation.service.ts
│   ├── guards/
│   │   └── auth.guard.ts
│   ├── interceptors/
│   │   └── auth.interceptor.ts
│   ├── app.routes.ts
│   ├── app.config.ts
│   └── app.ts
├── environments/
│   ├── environment.ts
│   └── environment.prod.ts
└── styles.scss
```

## 🛠️ Configuración de Entorno

### Variables de Entorno

El proyecto utiliza variables de entorno para configurar las URLs del API y otras configuraciones. Esto permite flexibilidad entre diferentes entornos (desarrollo, staging, producción).

#### Configuración Automática

1. **Copiar el archivo de ejemplo**:
   ```bash
   cp env.example .env
   ```

2. **Editar las variables** en el archivo `.env`:
   ```env
   # Configuración del API
   API_URL=https://examen.test/api
   AUTH_URL=https://examen.test/auth
   
   # Configuración de la aplicación
   APP_NAME=Sistema de Exámenes
   APP_VERSION=1.0.0
   
   # Configuración de JWT
   JWT_KEY=exam_token
   
   # Configuración de paginación
   PAGE_SIZE=10
   
   # Configuración de timeout
   REQUEST_TIMEOUT=30000
   
   # Configuración de CORS
   WITH_CREDENTIALS=true
   ```

3. **Generar la configuración**:
   ```bash
   npm run generate-env
   ```

#### Variables Disponibles

| Variable | Descripción | Valor por Defecto |
|----------|-------------|-------------------|
| `API_URL` | URL base del API | `https://examen.test/api` |
| `AUTH_URL` | URL de autenticación | `https://examen.test/auth` |
| `APP_NAME` | Nombre de la aplicación | `Sistema de Exámenes` |
| `APP_VERSION` | Versión de la aplicación | `1.0.0` |
| `JWT_KEY` | Clave para almacenar JWT | `exam_token` |
| `PAGE_SIZE` | Tamaño de página por defecto | `10` |
| `REQUEST_TIMEOUT` | Timeout de peticiones (ms) | `30000` |
| `WITH_CREDENTIALS` | Incluir credenciales en CORS | `true` |

#### Scripts Disponibles

- `npm run generate-env`: Genera el archivo de configuración desde `.env`
- `npm start`: Ejecuta el script de generación y luego inicia el servidor
- `npm run build`: Ejecuta el script de generación y luego construye la aplicación

## 🔧 Instalación y Desarrollo

### Prerrequisitos
- Node.js 18+ 
- npm o bun
- Angular CLI 20

### Instalación
```bash
# Instalar dependencias
npm install
# o
bun install

# Servidor de desarrollo
npm start
# o
bun run start

# Construir para producción
npm run build
# o
bun run build
```

### Scripts Disponibles
- `npm start`: Servidor de desarrollo en `http://localhost:4200`
- `npm run build`: Construir para producción
- `npm run watch`: Construir en modo watch
- `npm test`: Ejecutar tests

## 🔐 Autenticación y Autorización

### Roles del Sistema
- **admin**: Acceso completo al sistema
- **conductor**: Gestión de exámenes y resultados
- **usuario**: Acceso limitado a exámenes

### Permisos
El sistema maneja permisos granulares para diferentes acciones:
- `exams.create`
- `exams.read`
- `exams.update`
- `exams.delete`
- `users.manage`
- `reports.view`

### Uso del AuthGuard
```typescript
// En las rutas
{
  path: 'admin',
  component: AdminComponent,
  canActivate: [AuthGuard],
  data: { 
    roles: ['admin'],
    permissions: ['users.manage']
  }
}
```

## 📡 Servicios API

### ApiService
Servicio centralizado para todas las peticiones HTTP:

```typescript
// GET
this.apiService.get('/exams').subscribe(response => {
  console.log(response.data);
});

// POST
this.apiService.post('/exams', examData).subscribe(response => {
  console.log(response.message);
});

// PUT
this.apiService.put(`/exams/${id}`, examData).subscribe(response => {
  console.log(response.message);
});

// DELETE
this.apiService.delete(`/exams/${id}`).subscribe(response => {
  console.log(response.message);
});
```

### Gestión de Errores
El servicio maneja automáticamente:
- Errores de red
- Errores de autenticación (401)
- Errores de validación (422)
- Errores del servidor (500)

## 🔔 Sistema de Notificaciones

### NotificationService
```typescript
// Notificaciones básicas
this.notificationService.success('Operación exitosa');
this.notificationService.error('Ha ocurrido un error');
this.notificationService.warning('Advertencia');
this.notificationService.info('Información');

// Notificaciones específicas
this.notificationService.showApiError(error);
this.notificationService.showValidationError(errors);
this.notificationService.showNetworkError();
```

## ⏳ Gestión de Carga

### LoadingService
```typescript
// Carga global
this.loadingService.showGlobalLoading('Cargando datos...');
this.loadingService.hideGlobalLoading();

// Carga específica
this.loadingService.showLoading('save', 'Guardando...');
this.loadingService.hideLoading('save');

// Con operaciones
this.loadingService.withLoading(
  this.apiService.post('/exams', data),
  'save',
  'Guardando examen...'
).subscribe(response => {
  // Manejar respuesta
});
```

## ✅ Validaciones de Formularios

### ValidationService
```typescript
// Validadores comunes
const validators = [
  this.validationService.required(),
  this.validationService.email(),
  this.validationService.minLength(3),
  this.validationService.maxLength(50)
];

// Validadores específicos
const examValidators = [
  this.validationService.examDuration(),
  this.validationService.examScore(),
  this.validationService.questionCount()
];
```

## 🎨 Estilos y Temas

### Variables CSS
El proyecto usa variables CSS para consistencia:
```css
:root {
  --primary-color: #43B049;
  --success-color: #43B049;
  --warning-color: #f59e0b;
  --danger-color: #ef4444;
  --background-color: #ffffff;
  --text-primary: #222;
  --border-color: #e5e7eb;
}
```

### Componentes Material
- Tema personalizado basado en indigo-pink
- Componentes adaptados al diseño del sistema
- Notificaciones con estilos consistentes

## 🔄 Interceptores HTTP

### AuthInterceptor
- Agrega automáticamente tokens JWT a las peticiones
- Maneja renovación automática de tokens
- Redirige a login en caso de errores de autenticación

## 📱 Responsive Design
- Diseño adaptativo para móviles y tablets
- Componentes flexibles con Angular Flex Layout
- Breakpoints consistentes

## 🧪 Testing
```bash
# Ejecutar tests unitarios
npm test

# Ejecutar tests con coverage
npm run test:coverage
```

## 🚀 Despliegue

### Producción
```bash
# Construir
npm run build

# Los archivos se generan en dist/frontend-examen/
```

### Configuración del Servidor
- Configurar el servidor web para servir `index.html` en todas las rutas
- Configurar CORS en el backend
- Configurar variables de entorno de producción

## 📚 Dependencias Principales

- **@angular/core**: 20.0.3
- **@angular/material**: 20.0.3
- **@angular/flex-layout**: 15.0.0-beta.42
- **@auth0/angular-jwt**: 5.2.0
- **@ngrx/store**: 19.2.1
- **rxjs**: 7.8.0

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para detalles.

## 📞 Soporte

Para soporte técnico o preguntas sobre el proyecto, contactar al equipo de desarrollo.
