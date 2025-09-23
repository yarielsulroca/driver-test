-- =====================================================
-- MIGRACIÓN SEGURA: USUARIOS-ROLES (UNO A MUCHOS → MUCHOS A MUCHOS)
-- =====================================================
-- Este script migra la relación entre usuarios y roles de uno a muchos a muchos a muchos
-- PRESERVA todos los datos existentes
-- =====================================================

-- Paso 1: Crear respaldo de seguridad (OPCIONAL - ya deberías tener uno)
-- CREATE DATABASE examen_backup;
-- CREATE TABLE examen_backup.usuarios AS SELECT * FROM examen.usuarios;
-- CREATE TABLE examen_backup.roles AS SELECT * FROM examen.roles;

-- Paso 2: Crear la tabla pivot usuario_roles
CREATE TABLE IF NOT EXISTS usuario_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rol_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Claves foráneas
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES roles(rol_id) ON DELETE CASCADE,
    
    -- Índice único para evitar duplicados
    UNIQUE KEY unique_usuario_rol (usuario_id, rol_id),
    
    -- Índices para mejorar rendimiento
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_rol_id (rol_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Paso 3: Migrar datos existentes de usuarios.rol_id a usuario_roles
-- Solo inserta si la relación no existe ya
INSERT IGNORE INTO usuario_roles (usuario_id, rol_id, created_at, updated_at)
SELECT 
    usuario_id, 
    rol_id, 
    NOW(), 
    NOW()
FROM usuarios 
WHERE rol_id IS NOT NULL;

-- Paso 4: Verificar que la migración fue exitosa
-- Mostrar cuántos registros se migraron
SELECT 
    'Datos migrados' as estado,
    COUNT(*) as total_relaciones
FROM usuario_roles;

-- Mostrar algunos ejemplos de la nueva estructura
SELECT 
    'Ejemplos de migración' as titulo,
    u.usuario_id,
    u.nombre,
    u.apellido,
    r.nombre as rol_nombre,
    ur.created_at as fecha_migracion
FROM usuario_roles ur
JOIN usuarios u ON u.usuario_id = ur.usuario_id
JOIN roles r ON r.rol_id = ur.rol_id
ORDER BY ur.created_at DESC
LIMIT 10;

-- Paso 5: Verificar integridad de datos
-- Contar usuarios que tenían rol_id vs usuarios en la nueva tabla
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

-- Paso 6: ELIMINAR la columna rol_id de la tabla usuarios
-- ⚠️ IMPORTANTE: Solo ejecutar este paso si la verificación anterior fue exitosa
-- ALTER TABLE usuarios DROP COLUMN rol_id;

-- =====================================================
-- COMANDOS PARA EJECUTAR PASO A PASO:
-- =====================================================
-- 1. Ejecutar hasta el Paso 5
-- 2. Verificar que "resultado" sea "✅ MIGRACIÓN EXITOSA"
-- 3. Solo entonces ejecutar el Paso 6 (descomentar la última línea)
-- =====================================================

-- Paso 7: Verificación final (ejecutar después del Paso 6)
-- SELECT 
--     'Verificación final' as titulo,
--     COUNT(*) as total_usuarios,
--     (SELECT COUNT(*) FROM usuario_roles) as total_relaciones_rol
-- FROM usuarios;

-- =====================================================
-- ROLLBACK (en caso de problemas):
-- =====================================================
-- Si necesitas revertir la migración:
-- 1. ALTER TABLE usuarios ADD COLUMN rol_id INT;
-- 2. UPDATE usuarios u 
--    SET rol_id = (SELECT rol_id FROM usuario_roles ur WHERE ur.usuario_id = u.usuario_id LIMIT 1);
-- 3. ALTER TABLE usuarios ADD FOREIGN KEY (rol_id) REFERENCES roles(rol_id);
-- 4. DROP TABLE usuario_roles;
-- =====================================================
