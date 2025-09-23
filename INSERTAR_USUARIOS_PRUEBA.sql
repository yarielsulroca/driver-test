-- =====================================================
-- INSERTAR USUARIOS DE PRUEBA
-- =====================================================
-- Este script inserta usuarios de prueba para poder probar
-- la funcionalidad de creación de conductores
-- =====================================================

-- Insertar usuarios de prueba (solo si no existen)
INSERT IGNORE INTO usuarios (dni, nombre, apellido, email, password, estado, created_at, updated_at) VALUES
('12345678', 'Juan', 'Pérez', 'juan.perez@ejemplo.com', '$2y$10$hITp9BrtIPj4YzTBvXYSOOMCa7HTTVXcGq5d6W8Ybl5nB48LN2ivK', 'activo', NOW(), NOW()),
('87654321', 'María', 'González', 'maria.gonzalez@ejemplo.com', '$2y$10$hITp9BrtIPj4YzTBvXYSOOMCa7HTTVXcGq5d6W8Ybl5nB48LN2ivK', 'activo', NOW(), NOW()),
('11223344', 'Carlos', 'López', 'carlos.lopez@ejemplo.com', '$2y$10$hITp9BrtIPj4YzTBvXYSOOMCa7HTTVXcGq5d6W8Ybl5nB48LN2ivK', 'activo', NOW(), NOW()),
('55667788', 'Ana', 'Martínez', 'ana.martinez@ejemplo.com', '$2y$10$hITp9BrtIPj4YzTBvXYSOOMCa7HTTVXcGq5d6W8Ybl5nB48LN2ivK', 'activo', NOW(), NOW()),
('99887766', 'Luis', 'Rodríguez', 'luis.rodriguez@ejemplo.com', '$2y$10$hITp9BrtIPj4YzTBvXYSOOMCa7HTTVXcGq5d6W8Ybl5nB48LN2ivK', 'activo', NOW(), NOW());

-- Verificar que los usuarios se insertaron correctamente
SELECT 
    'Usuarios insertados' as estado,
    COUNT(*) as total_usuarios
FROM usuarios 
WHERE estado = 'activo';

-- Mostrar los usuarios disponibles
SELECT 
    usuario_id,
    nombre,
    apellido,
    dni,
    email,
    estado,
    created_at
FROM usuarios 
WHERE estado = 'activo'
ORDER BY created_at DESC;

-- =====================================================
-- INFORMACIÓN IMPORTANTE:
-- =====================================================
-- Contraseña para todos los usuarios: 'conductor123'
-- Hash encriptado: $2y$10$hITp9BrtIPj4YzTBvXYSOOMCa7HTTVXcGq5d6W8Ybl5nB48LN2ivK
-- 
-- Usuarios creados:
-- 1. Juan Pérez - juan.perez@ejemplo.com
-- 2. María González - maria.gonzalez@ejemplo.com  
-- 3. Carlos López - carlos.lopez@ejemplo.com
-- 4. Ana Martínez - ana.martinez@ejemplo.com
-- 5. Luis Rodríguez - luis.rodriguez@ejemplo.com
-- =====================================================
