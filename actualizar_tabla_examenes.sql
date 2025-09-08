-- Script para actualizar la tabla exámenes
-- Ejecutar este script directamente en MySQL

USE examen;

-- Agregar campos faltantes a la tabla examenes
ALTER TABLE examenes 
ADD COLUMN IF NOT EXISTS nombre VARCHAR(255) NULL AFTER titulo,
ADD COLUMN IF NOT EXISTS duracion_minutos INT(11) NULL AFTER tiempo_limite,
ADD COLUMN IF NOT EXISTS fecha_inicio DATETIME NULL AFTER puntaje_minimo,
ADD COLUMN IF NOT EXISTS fecha_fin DATETIME NULL AFTER fecha_inicio,
ADD COLUMN IF NOT EXISTS numero_preguntas INT(11) NULL AFTER fecha_fin,
ADD COLUMN IF NOT EXISTS dificultad ENUM('facil', 'medio', 'dificil') DEFAULT 'medio' AFTER numero_preguntas;

-- Crear tabla escuelas si no existe
CREATE TABLE IF NOT EXISTS escuelas (
    escuela_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    direccion TEXT NULL,
    ciudad VARCHAR(100) NULL,
    telefono VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

-- Insertar algunas escuelas de ejemplo
INSERT IGNORE INTO escuelas (nombre, direccion, ciudad, telefono, email, estado, created_at, updated_at) VALUES
('Escuela de Manejo Central', 'Av. Principal 123', 'Buenos Aires', '11-1234-5678', 'central@escuela.com', 'activo', NOW(), NOW()),
('Instituto de Conducción Norte', 'Calle Norte 456', 'Buenos Aires', '11-2345-6789', 'norte@instituto.com', 'activo', NOW(), NOW()),
('Centro de Formación Sur', 'Ruta Sur 789', 'Buenos Aires', '11-3456-7890', 'sur@centro.com', 'activo', NOW(), NOW());

-- Verificar la estructura actualizada
DESCRIBE examenes;
SELECT COUNT(*) as total_escuelas FROM escuelas;
