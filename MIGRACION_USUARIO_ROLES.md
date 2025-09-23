# Migración de Usuario-Roles: Uno a Muchos → Muchos a Muchos

## 📋 Resumen de Cambios

Se ha actualizado la relación entre usuarios y roles de **uno a muchos** a **muchos a muchos** para permitir que:
- Un usuario tenga múltiples roles
- Un rol sea asignado a múltiples usuarios
- Mayor flexibilidad en la gestión de permisos

## 🗄️ Cambios en la Base de Datos

### 1. Crear tabla pivot `usuario_roles`
```sql
CREATE TABLE usuario_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rol_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES roles(rol_id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_rol (usuario_id, rol_id)
);
```

### 2. Migrar datos existentes
```bash
# Ejecutar el seeder para migrar datos existentes
php spark db:seed MigrateUsuarioRolesSeeder
```

### 3. Eliminar columna `rol_id` de la tabla `usuarios`
```sql
-- Eliminar foreign key constraint primero
ALTER TABLE usuarios DROP FOREIGN KEY usuarios_rol_id_foreign;

-- Eliminar columna rol_id
ALTER TABLE usuarios DROP COLUMN rol_id;
```

## 🔧 Archivos Modificados en el Backend

### Nuevos Archivos Creados:
1. **`app/Database/Migrations/2025-01-15-000001_CreateUsuarioRolesManyToMany.php`** - Crea tabla pivot
2. **`app/Database/Migrations/2025-01-15-000002_RemoveRolIdFromUsuarios.php`** - Elimina columna rol_id
3. **`app/Models/UsuarioRolModel.php`** - Modelo para tabla pivot
4. **`app/Controllers/UsuarioController.php`** - Controlador para gestión de usuarios y roles
5. **`app/Filters/RoleFilter.php`** - Filtro avanzado para verificar roles
6. **`app/Database/Seeds/MigrateUsuarioRolesSeeder.php`** - Seeder para migrar datos

### Archivos Modificados:
1. **`app/Models/UsuarioModel.php`** - Actualizado para relación muchos a muchos
2. **`app/Models/RolModel.php`** - Actualizado para relación muchos a muchos
3. **`app/Controllers/AuthController.php`** - Actualizado para manejar múltiples roles
4. **`app/Config/Routes.php`** - Agregadas rutas para gestión de usuarios y roles

## 🚀 Pasos para la Migración

### 1. Ejecutar Migraciones (NO EJECUTAR AUTOMÁTICAMENTE)
```bash
# Crear tabla pivot
php spark migrate -g default 2025-01-15-000001

# Migrar datos existentes
php spark db:seed MigrateUsuarioRolesSeeder

# Eliminar columna rol_id (DESPUÉS de migrar datos)
php spark migrate -g default 2025-01-15-000002
```

### 2. Verificar Migración
```sql
-- Verificar que todos los usuarios tienen roles asignados
SELECT 
    u.usuario_id, 
    u.nombre, 
    u.apellido,
    COUNT(ur.rol_id) as total_roles,
    GROUP_CONCAT(r.nombre) as roles
FROM usuarios u
LEFT JOIN usuario_roles ur ON u.usuario_id = ur.usuario_id
LEFT JOIN roles r ON ur.rol_id = r.rol_id
GROUP BY u.usuario_id;
```

## 📡 Nuevas APIs Disponibles

### Gestión de Usuarios:
- `GET /api/usuarios` - Listar todos los usuarios con sus roles
- `GET /api/usuarios/{id}` - Obtener usuario específico con roles
- `POST /api/usuarios` - Crear usuario con roles
- `PUT /api/usuarios/{id}` - Actualizar usuario y roles
- `DELETE /api/usuarios/{id}` - Eliminar usuario

### Gestión de Roles:
- `POST /api/usuarios/{id}/roles` - Asignar roles a usuario
- `GET /api/usuarios/{id}/roles` - Obtener roles de usuario
- `POST /api/usuarios/{id}/tiene-rol` - Verificar si usuario tiene rol específico
- `GET /api/roles/{id}/usuarios` - Obtener usuarios con rol específico

## 🔐 Autenticación Actualizada

### JWT Token Actualizado:
```json
{
  "iss": "Sistema de Exámenes",
  "sub": "conductor_id",
  "usuario_id": 123,
  "roles": ["conductor", "supervisor"],
  "rol_principal": "conductor",
  "dni": "12345678"
}
```

### Uso del Filtro de Roles:
```php
// En Routes.php - Requerir rol específico
$routes->get('admin/dashboard', 'AdminController::dashboard', ['filter' => 'role:admin']);

// Requerir múltiples roles (cualquiera de ellos)
$routes->get('admin/users', 'AdminController::users', ['filter' => 'role:admin,supervisor']);
```

## ⚠️ Consideraciones Importantes

1. **Backup**: Hacer backup completo antes de la migración
2. **Datos Existentes**: El seeder migra automáticamente todos los datos existentes
3. **Compatibilidad**: Los tokens JWT antiguos seguirán funcionando hasta expirar
4. **Frontend**: Actualizar frontend para manejar array de roles en lugar de rol único
5. **Validaciones**: Actualizar validaciones que dependían de un solo rol

## 🧪 Testing

### Verificar Funcionalidad:
```bash
# Probar login (debe devolver array de roles)
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"dni":"12345678","password":"12345678"}'

# Probar gestión de roles
curl -X GET http://localhost/api/usuarios/1/roles \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 📝 Notas Adicionales

- Los roles existentes se mantienen intactos
- Los usuarios existentes mantienen su rol actual como rol principal
- La migración es reversible (ver migraciones down())
- Se mantiene compatibilidad con código existente que usa rol único

## 🔄 Rollback (Si es necesario)

```bash
# Revertir migraciones en orden inverso
php spark migrate:rollback -g default
php spark migrate:rollback -g default
```

---

**IMPORTANTE**: No ejecutar las migraciones automáticamente. Ejecutar manualmente después de hacer backup de la base de datos.
