-- Crear tabla pivote para relación muchos a muchos entre conductores y escuelas
CREATE TABLE IF NOT EXISTS `conductor_escuela` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `conductor_id` INT(11) UNSIGNED NOT NULL,
    `escuela_id` INT(11) UNSIGNED NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_conductor_escuela` (`conductor_id`, `escuela_id`),
    CONSTRAINT `fk_conductor_escuela_conductor` FOREIGN KEY (`conductor_id`) REFERENCES `conductores`(`conductor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_conductor_escuela_escuela` FOREIGN KEY (`escuela_id`) REFERENCES `escuelas`(`escuela_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 