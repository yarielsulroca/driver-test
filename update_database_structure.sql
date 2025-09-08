-- Script para actualizar la estructura de la base de datos
-- Eliminar lógica de páginas y establecer relación directa examen-pregunta

-- 1. Eliminar tablas relacionadas con páginas
DROP TABLE IF EXISTS `pagina_conductor`;
DROP TABLE IF EXISTS `paginas`;

-- 2. Eliminar tabla examen_pregunta (relación many-to-many)
DROP TABLE IF EXISTS `examen_pregunta`;

-- 3. Agregar campo examen_id a la tabla preguntas
ALTER TABLE `preguntas` ADD COLUMN `examen_id` INT(11) UNSIGNED NULL AFTER `pregunta_id`;

-- 4. Agregar campo categoria_id a la tabla preguntas si no existe
ALTER TABLE `preguntas` ADD COLUMN `categoria_id` INT(11) UNSIGNED NULL AFTER `examen_id`;

-- 5. Agregar clave foránea para examen_id
ALTER TABLE `preguntas` ADD CONSTRAINT `fk_preguntas_examen` 
FOREIGN KEY (`examen_id`) REFERENCES `examenes`(`examen_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 6. Agregar clave foránea para categoria_id
ALTER TABLE `preguntas` ADD CONSTRAINT `fk_preguntas_categoria` 
FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`categoria_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 7. Actualizar el campo 'pregunta' a 'enunciado' si es necesario
ALTER TABLE `preguntas` CHANGE COLUMN `pregunta` `enunciado` TEXT;

-- 8. Actualizar el campo 'tipo_pregunta' a 'tipo' si es necesario
ALTER TABLE `preguntas` CHANGE COLUMN `tipo_pregunta` `tipo` ENUM('multiple', 'unica', 'verdadero_falso');

-- 9. Actualizar valores de dificultad
UPDATE `preguntas` SET `dificultad` = 'facil' WHERE `dificultad` = 'baja';
UPDATE `preguntas` SET `dificultad` = 'medio' WHERE `dificultad` = 'media';
UPDATE `preguntas` SET `dificultad` = 'dificil' WHERE `dificultad` = 'alta';

-- 10. Cambiar el tipo de columna dificultad
ALTER TABLE `preguntas` MODIFY COLUMN `dificultad` ENUM('facil', 'medio', 'dificil');

-- 11. Agregar campo es_critica si no existe
ALTER TABLE `preguntas` ADD COLUMN `es_critica` BOOLEAN DEFAULT FALSE AFTER `puntaje`;

-- 12. Actualizar el campo 'pregunta' a 'enunciado' en la tabla respuestas si es necesario
ALTER TABLE `respuestas` CHANGE COLUMN `pregunta` `texto` TEXT;

-- Verificar que los cambios se aplicaron correctamente
SELECT 'Estructura de base de datos actualizada correctamente' as status; 