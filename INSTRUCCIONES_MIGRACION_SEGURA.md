# 🛡️ Migración Segura: Usuarios-Roles (Uno a Muchos → Muchos a Muchos)

## ⚠️ IMPORTANTE: Preservar Datos de Prueba

Este proceso está diseñado para **NO PERDER** ningún dato existente. Todos los datos de prueba se preservarán durante la migración.

## 📋 Pasos para Ejecutar la Migración

### Paso 1: Verificar Conexión a Base de Datos

Antes de empezar, asegúrate de que:
- ✅ El servidor MySQL esté corriendo
- ✅ La base de datos `examen` exista
- ✅ Tengas permisos de administrador en la base de datos

### Paso 2: Crear Respaldo de Seguridad (Recomendado)

```sql
-- Crear respaldo completo de la base de datos
CREATE DATABASE examen_backup;
CREATE TABLE examen_backup.usuarios AS SELECT * FROM examen.usuarios;
CREATE TABLE examen_backup.roles AS SELECT * FROM examen.roles;
```

### Paso 3: Ejecutar Migración SQL

Usa el archivo `MIGRACION_SEGURA.sql` que se generó. Este archivo contiene:

1. **Creación de tabla pivot** `usuario_roles`
2. **Migración de datos existentes** de `usuarios.rol_id` a `usuario_roles`
3. **Verificaciones de integridad** para asegurar que no se pierdan datos
4. **Eliminación segura** de la columna `rol_id`

### Paso 4: Verificar Migración

Después de ejecutar el SQL, verifica que:

```sql
-- Debe mostrar "✅ MIGRACIÓN EXITOSA"
SELECT 
    'Verificación de integridad' as titulo,
    (SELECT COUNT(*) FROM usuarios WHERE rol_id IS NOT NULL) as usuarios_con_rol_original,
    (SELECT COUNT(DISTINCT usuario_id) FROM usuario_roles) as usuarios_con_rol_nuevo,
    CASE 
        WHEN (SELECT COUNT(*) FROM usuarios WHERE rol_id IS NOT NULL) = 
             (SELECT COUNT(DISTINCT usuario_id) FROM usuario_roles)
        THEN '✅ MIGRACIÓN EXITOSA'
        ELSE '❌ ERROR EN MIGRACIÓN'
    END as resultado;
```

### Paso 5: Actualizar Contraseñas (Opcional)

Si necesitas actualizar contraseñas de usuarios, usa los hashes generados:

```sql
-- Ejemplo: Actualizar contraseña de un usuario específico
UPDATE usuarios 
SET password = '$2y$10$hITp9BrtIPj4YzTBvXYSOOMCa7HTTVXcGq5d6W8Ybl5nB48LN2ivK' 
WHERE email = 'conductor@ejemplo.com';

-- El usuario podrá iniciar sesión con la contraseña: 'conductor123'
```

## 🔍 Verificación Post-Migración

### 1. Verificar Estructura de Base de Datos

```sql
-- Verificar que la tabla usuario_roles existe
SHOW TABLES LIKE 'usuario_roles';

-- Verificar estructura de la tabla
DESCRIBE usuario_roles;

-- Verificar que la columna rol_id fue eliminada
DESCRIBE usuarios;
```

### 2. Verificar Datos Migrados

```sql
-- Contar relaciones creadas
SELECT COUNT(*) as total_relaciones FROM usuario_roles;

-- Ver ejemplos de datos migrados
SELECT 
    u.nombre,
    u.apellido,
    r.nombre as rol_nombre
FROM usuario_roles ur
JOIN usuarios u ON u.usuario_id = ur.usuario_id
JOIN roles r ON r.rol_id = ur.rol_id
LIMIT 10;
```

### 3. Probar APIs del Backend

Después de la migración, prueba estas APIs:

```bash
# Probar login (debe incluir array de roles en JWT)
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@ejemplo.com","password":"contraseña"}'

# Probar gestión de usuarios
curl -X GET http://localhost/api/usuarios \
  -H "Authorization: Bearer TOKEN_JWT"

# Probar asignación de roles
curl -X POST http://localhost/api/usuarios/1/roles \
  -H "Authorization: Bearer TOKEN_JWT" \
  -H "Content-Type: application/json" \
  -d '{"roles":[1,2,3]}'
```

## 🔄 Rollback (En Caso de Problemas)

Si necesitas revertir la migración:

```sql
-- 1. Restaurar columna rol_id
ALTER TABLE usuarios ADD COLUMN rol_id INT;

-- 2. Restaurar datos (tomar el primer rol de cada usuario)
UPDATE usuarios u 
SET rol_id = (
    SELECT rol_id 
    FROM usuario_roles ur 
    WHERE ur.usuario_id = u.usuario_id 
    LIMIT 1
);

-- 3. Restaurar clave foránea
ALTER TABLE usuarios ADD FOREIGN KEY (rol_id) REFERENCES roles(rol_id);

-- 4. Eliminar tabla pivot
DROP TABLE usuario_roles;
```

## 📊 Datos de Prueba Generados

### Contraseñas Encriptadas Disponibles:

| Contraseña Original | Hash Encriptado | Uso Recomendado |
|-------------------|----------------|-----------------|
| `admin123` | `$2y$10$iOZLk7wag/b8iSqJQ9rz0eEcQXIp3XkJGzEiRtMU0qVngX15uX6ra` | Administradores |
| `conductor123` | `$2y$10$hITp9BrtIPj4YzTBvXYSOOMCa7HTTVXcGq5d6W8Ybl5nB48LN2ivK` | Conductores |
| `supervisor123` | `$2y$10$Lv20opI2At8Ls2eZZS1Ub.9bBJYQEWFC3qTUZT76Qw5.ztrHiO3r.` | Supervisores |
| `test123` | `$2y$10$4zA0Nx.xiXBggClGTMDGoeFxkv/MJy4Qxr8c/FaT2lOjlZYT5jnu2` | Usuarios de prueba |

### Comandos SQL para Actualizar Contraseñas:

```sql
-- Actualizar todos los usuarios con contraseña de prueba
UPDATE usuarios SET password = '$2y$10$4zA0Nx.xiXBggClGTMDGoeFxkv/MJy4Qxr8c/FaT2lOjlZYT5jnu2' WHERE password IS NULL OR password = '';

-- Actualizar usuarios específicos por rol
UPDATE usuarios u 
JOIN usuario_roles ur ON u.usuario_id = ur.usuario_id 
JOIN roles r ON ur.rol_id = r.rol_id 
SET u.password = '$2y$10$hITp9BrtIPj4YzTBvXYSOOMCa7HTTVXcGq5d6W8Ybl5nB48LN2ivK' 
WHERE r.nombre = 'conductor';
```

## ✅ Checklist Final

- [ ] Respaldo de seguridad creado
- [ ] Tabla `usuario_roles` creada
- [ ] Datos migrados correctamente
- [ ] Verificación de integridad exitosa
- [ ] Columna `rol_id` eliminada
- [ ] APIs funcionando correctamente
- [ ] Contraseñas actualizadas (si es necesario)
- [ ] Pruebas de login exitosas

## 🆘 Soporte

Si encuentras algún problema:

1. **Verifica los logs** de CodeIgniter en `writable/logs/`
2. **Revisa la conexión** a la base de datos
3. **Ejecuta las verificaciones** SQL proporcionadas
4. **Usa el rollback** si es necesario

---

**¡La migración está diseñada para ser 100% segura y preservar todos tus datos de prueba!** 🛡️
