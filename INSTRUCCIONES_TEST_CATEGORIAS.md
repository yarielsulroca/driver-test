# 🧪 Instrucciones para Test de Categorías

## Problemas Comunes y Soluciones

### 1. **Error de CORS (Cross-Origin Resource Sharing)**

**Síntomas:**
- Error en consola: "Access to fetch at 'http://examen.test/api/categorias' from origin '...' has been blocked by CORS policy"
- Las peticiones fallan con error de red

**Soluciones:**

#### A. Verificar configuración del filtro CORS
```bash
# Verificar que el filtro CORS esté registrado en app/Config/Filters.php
php spark route:list
```

#### B. Verificar headers en el servidor web
Agregar al archivo `.htaccess` en la raíz del proyecto:
```apache
# Headers CORS adicionales
Header always set Access-Control-Allow-Origin "*"
Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header always set Access-Control-Allow-Headers "X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization"
```

#### C. Verificar configuración de Laragon/XAMPP
- Asegurar que el módulo `mod_headers` esté habilitado
- Verificar que el dominio `examen.test` esté configurado correctamente

### 2. **Error de Conexión al Servidor**

**Síntomas:**
- Error: "Failed to fetch" o "Network Error"
- El servidor no responde

**Soluciones:**

#### A. Verificar que el servidor esté corriendo
```bash
# Verificar que Apache esté corriendo
# En Laragon: verificar que el servidor esté iniciado
```

#### B. Verificar configuración del host
Agregar al archivo `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 examen.test
```

#### C. Verificar puerto del servidor
- Por defecto Laragon usa puerto 80
- Si usas otro puerto, actualizar las URLs en el test

### 3. **Error 404 - Endpoint no encontrado**

**Síntomas:**
- Error 404 al acceder a `/api/categorias`
- La ruta no existe

**Soluciones:**

#### A. Verificar rutas en CodeIgniter
```bash
# Listar todas las rutas disponibles
php spark route:list
```

#### B. Verificar que las rutas estén registradas
En `app/Config/Routes.php`, verificar que exista:
```php
$routes->group('categorias', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'CategoriaController::index');
    // ... otras rutas
});
```

#### C. Verificar que el controlador exista
```bash
# Verificar que el archivo existe
ls app/Controllers/CategoriaController.php
```

### 4. **Error de Base de Datos**

**Síntomas:**
- Error 500 interno del servidor
- Mensaje de error relacionado con la base de datos

**Soluciones:**

#### A. Verificar conexión a la base de datos
```bash
# Verificar configuración en app/Config/Database.php
# Verificar que las credenciales sean correctas
```

#### B. Verificar que la tabla exista
```bash
# Ejecutar migraciones si no se han ejecutado
php spark migrate
```

#### C. Verificar datos de prueba
```bash
# Ejecutar seeders para tener datos de prueba
php spark db:seed CategoriaSeeder
```

### 5. **Error de Proxy en Angular**

**Síntomas:**
- El proxy no funciona correctamente
- Las peticiones desde Angular fallan

**Soluciones:**

#### A. Verificar configuración del proxy
En `frontend-examen/proxy.conf.json`:
```json
{
  "/api": {
    "target": "http://examen.test",
    "secure": false,
    "changeOrigin": true,
    "logLevel": "debug"
  }
}
```

#### B. Reiniciar el servidor de desarrollo
```bash
cd frontend-examen
ng serve
```

#### C. Verificar que Angular esté usando el proxy
En `angular.json`, verificar que esté configurado:
```json
"serve": {
  "options": {
    "proxyConfig": "proxy.conf.json"
  }
}
```

## Pasos de Verificación

### 1. **Verificar Servidor Backend**
```bash
# 1. Ir al directorio del proyecto
cd /c/laragon/www/examen

# 2. Verificar que el servidor esté corriendo
curl http://examen.test/

# 3. Verificar endpoint de categorías
curl http://examen.test/api/categorias
```

### 2. **Verificar Frontend Angular**
```bash
# 1. Ir al directorio del frontend
cd frontend-examen

# 2. Instalar dependencias si es necesario
npm install

# 3. Iniciar servidor de desarrollo
ng serve

# 4. Abrir en navegador: http://localhost:4200
```

### 3. **Verificar Base de Datos**
```bash
# 1. Verificar conexión
php spark db:show_tables

# 2. Verificar tabla categorias
php spark db:table_info categorias

# 3. Verificar datos
php spark db:query "SELECT * FROM categorias LIMIT 5"
```

## Archivos de Log Importantes

### 1. **Logs de CodeIgniter**
- `writable/logs/log-YYYY-MM-DD.log`
- Contiene errores del backend

### 2. **Log de CORS**
- `writable/cors_simple.log`
- Contiene información sobre peticiones CORS

### 3. **Logs del navegador**
- Abrir DevTools (F12)
- Ir a la pestaña Console
- Revisar errores de red en Network

## Comandos Útiles

```bash
# Limpiar cache de CodeIgniter
php spark cache:clear

# Limpiar cache de Angular
cd frontend-examen
ng cache clean

# Verificar rutas disponibles
php spark route:list

# Verificar configuración
php spark env
```

## Contacto y Soporte

Si después de seguir estas instrucciones el problema persiste:

1. Revisar los logs mencionados arriba
2. Verificar la configuración del servidor web (Apache/Nginx)
3. Verificar que todos los servicios estén corriendo
4. Revisar la configuración de firewall/antivirus

---

**Nota:** Este archivo de test está diseñado para funcionar con la configuración específica de tu proyecto. Si cambias las URLs o configuraciones, actualiza el archivo de test correspondiente. 