-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando datos para la tabla examen.categorias: ~19 rows (aproximadamente)
INSERT INTO `categorias` (`categoria_id`, `codigo`, `nombre`, `descripcion`, `requisitos`, `estado`, `created_at`, `updated_at`) VALUES
	(2, 'A1', 'Motos hasta 150cc', 'Licencia para conducir motocicletas y motovehículos de hasta 150 centímetros cúbicos de cilindrada.', 'Edad mínima 16 años. Examen teórico y práctico. Certificado médico. No requiere experiencia previa.', 'activo', '2025-08-11 08:48:41', '2025-08-11 17:46:33'),
	(3, 'A2', 'Motos hasta 300cc', 'Licencia para conducir motocicletas y motovehículos de hasta 300 centímetros cúbicos de cilindrada', 'Edad mínima 17 años. Examen teórico y práctico. Certificado médico. Licencia A1 por al menos 1 año.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(4, 'A3', 'Motos sin límite de cilindrada', 'Licencia para conducir motocicletas y motovehículos sin límite de cilindrada', 'Edad mínima 18 años. Examen teórico y práctico. Certificado médico. Licencia A2 por al menos 1 año.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(5, 'B1', 'Automóviles particulares', 'Licencia para conducir automóviles particulares, camionetas y utilitarios de hasta 3500 kg de peso total', 'Edad mínima 17 años. Examen teórico y práctico. Certificado médico. No requiere experiencia previa.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(6, 'B2', 'Automóviles y camiones livianos', 'Licencia para conducir automóviles particulares y camiones de hasta 3500 kg de peso total', 'Edad mínima 18 años. Examen teórico y práctico. Certificado médico. Licencia B1 por al menos 1 año.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(7, 'C1', 'Camiones medianos', 'Licencia para conducir camiones de más de 3500 kg hasta 8000 kg de peso total', 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia B2 por al menos 2 años.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(8, 'C2', 'Camiones pesados', 'Licencia para conducir camiones de más de 8000 kg de peso total', 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia C1 por al menos 2 años.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(9, 'C3', 'Camiones con acoplado', 'Licencia para conducir camiones con acoplado o semirremolque', 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia C2 por al menos 2 años.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(10, 'D1', 'Ómnibus medianos', 'Licencia para conducir ómnibus de hasta 20 asientos para pasajeros', 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia B2 por al menos 2 años.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(11, 'D2', 'Ómnibus grandes', 'Licencia para conducir ómnibus de más de 20 asientos para pasajeros', 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia D1 por al menos 2 años.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(12, 'D3', 'Ómnibus con acoplado', 'Licencia para conducir ómnibus con acoplado o semirremolque', 'Edad mínima 21 años. Examen teórico y práctico. Certificado médico. Licencia D2 por al menos 2 años.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(13, 'E1', 'Tractores agrícolas', 'Licencia para conducir tractores agrícolas y maquinaria agrícola', 'Edad mínima 16 años. Examen teórico y práctico. Certificado médico. No requiere experiencia previa.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(14, 'E2', 'Maquinaria vial', 'Licencia para conducir maquinaria vial y de construcción', 'Edad mínima 18 años. Examen teórico y práctico. Certificado médico. Licencia B1 por al menos 1 año.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(15, 'F', 'Vehículos para discapacitados', 'Licencia especial para conducir vehículos adaptados para personas con discapacidad', 'Edad mínima 17 años. Examen teórico y práctico. Certificado médico especializado. Evaluación de capacidades.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(16, 'G1', 'Transporte de carga profesional', 'Licencia profesional para transporte de carga en general', 'Edad mínima 21 años. Examen teórico y práctico avanzado. Certificado médico. Licencia C2 por al menos 3 años. Curso de capacitación profesional.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(17, 'G2', 'Transporte de pasajeros profesional', 'Licencia profesional para transporte de pasajeros en general', 'Edad mínima 21 años. Examen teórico y práctico avanzado. Certificado médico. Licencia D2 por al menos 3 años. Curso de capacitación profesional.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(18, 'G3', 'Transporte de sustancias peligrosas', 'Licencia especial para transporte de sustancias peligrosas y materiales tóxicos', 'Edad mínima 23 años. Examen teórico y práctico especializado. Certificado médico. Licencia G1 o G2 por al menos 2 años. Curso de manejo de sustancias peligrosas.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(19, 'T1', 'Licencia temporal de aprendizaje', 'Licencia temporal para aprender a conducir vehículos de categoría B1', 'Edad mínima 16 años. Certificado médico. Debe estar acompañado por un conductor con licencia válida. Válida por 1 año.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41'),
	(20, 'T2', 'Licencia temporal de prueba', 'Licencia temporal otorgada después de aprobar examen teórico, válida para práctica', 'Examen teórico aprobado. Certificado médico. Debe estar acompañado por un conductor con licencia válida. Válida por 6 meses.', 'activo', '2025-08-11 08:48:41', '2025-08-11 08:48:41');

-- Volcando datos para la tabla examen.categorias_aprobadas: ~2 rows (aproximadamente)
INSERT INTO `categorias_aprobadas` (`categoria_aprobada_id`, `conductor_id`, `categoria_id`, `examen_id`, `estado`, `puntaje_obtenido`, `fecha_aprobacion`, `fecha_vencimiento`, `observaciones`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 2, 2, 1, 'aprobado', 85.50, '2025-09-22 16:00:00', '2026-09-22 16:00:00', 'Aprobado exitosamente', '2025-09-23 16:17:14', '2025-09-23 16:17:14', NULL),
	(2, 1, 2, 1, 'aprobado', 82.00, '2025-09-22 16:30:00', '2026-09-22 16:30:00', 'Aprobado exitosamente', '2025-09-23 16:27:14', '2025-09-23 16:27:14', NULL);

-- Volcando datos para la tabla examen.categorias_asignadas: ~7 rows (aproximadamente)
INSERT INTO `categorias_asignadas` (`categoria_asignada_id`, `conductor_id`, `estado`, `categoria_id`, `examen_id`, `intentos_realizados`, `intentos_maximos`, `fecha_asignacion`, `fecha_ultimo_intento`, `fecha_aprobacion`, `puntaje_obtenido`, `observaciones`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(2, 2, 'Aprobada', 2, 1, 1, 3, '2025-09-22 15:53:19', '2025-09-22 16:00:00', '2025-09-22 16:00:00', 85.50, 'Aprobado en primer intento', '2025-09-23 16:17:14', '2025-09-23 16:17:14', NULL),
	(3, 2, 'Reprobado', 5, 2, 2, 3, '2025-09-22 16:03:15', '2025-09-22 16:30:00', NULL, 65.00, 'Reprobado, necesita más estudio', '2025-09-23 16:17:14', '2025-09-23 16:17:14', NULL),
	(4, 1, 'Aprobada', 2, 1, 1, 3, '2025-09-22 15:53:19', '2025-09-22 16:30:00', '2025-09-22 16:30:00', 82.00, 'Aprobado en primer intento', '2025-09-23 16:17:14', '2025-09-23 16:17:14', NULL),
	(5, 1, 'Reprobado', 5, 2, 1, 3, '2025-09-22 15:53:19', '2025-09-22 16:15:00', NULL, 60.00, 'Reprobado, puntaje insuficiente', '2025-09-23 16:17:14', '2025-09-23 16:17:14', NULL),
	(6, 5, 'Reprobado', 2, 1, 2, 3, '2025-09-22 16:03:44', '2025-09-22 16:45:00', NULL, 55.00, 'Reprobado dos veces, necesita más práctica', '2025-09-23 16:17:14', '2025-09-23 16:17:14', NULL),
	(7, 5, 'Iniciado', 5, 2, 0, 3, '2025-09-22 16:09:32', NULL, NULL, NULL, 'Examen asignado recientemente', '2025-09-23 16:17:14', '2025-09-23 16:17:14', NULL),
	(8, 4, 'Reprobado', 2, 1, 1, 3, '2025-09-22 15:53:19', '2025-09-22 16:20:00', NULL, 58.00, 'Reprobado, puntaje bajo', '2025-09-23 16:17:14', '2025-09-23 16:17:14', NULL);

-- Volcando datos para la tabla examen.conductores: ~5 rows (aproximadamente)
INSERT INTO `conductores` (`conductor_id`, `usuario_id`, `estado`, `documentos_presentados`, `created_at`, `updated_at`) VALUES
	(1, 1, 'b', 'DNI, Licencia de conducir A', '2025-09-22 12:46:18', '2025-09-23 16:29:59'),
	(2, 2, 'b', 'DNI, Licencia de conducir B, Certificado médico', '2025-09-22 12:46:18', '2025-09-22 12:46:18'),
	(3, 3, 'p', 'DNI, Licencia de conducir C', '2025-09-22 12:46:18', '2025-09-22 12:46:18'),
	(4, 4, 'p', 'Documentos de prueba del usuario nuevo', '2025-09-22 18:16:51', '2025-09-22 18:16:51'),
	(5, 5, 'p', 'Todo ok', '2025-09-22 18:25:40', '2025-09-23 16:33:42');

-- Volcando datos para la tabla examen.conductor_escuela: ~0 rows (aproximadamente)

-- Volcando datos para la tabla examen.escuelas: ~6 rows (aproximadamente)
INSERT INTO `escuelas` (`escuela_id`, `nombre`, `direccion`, `ciudad`, `provincia`, `codigo_postal`, `telefono`, `email`, `horario_atencion`, `estado`, `capacidad_diaria`, `servicios_disponibles`, `coordenadas_lat`, `coordenadas_lng`, `created_at`, `updated_at`) VALUES
	(1, 'Oficina de Tránsito Centro', 'Av. San Martín 1234', 'Buenos Aires', 'Buenos Aires', NULL, '011-1234-5678', 'centro@transito.gob.ar', 'Lunes a Viernes 8:00-16:00', 'inactivo', 50, NULL, NULL, NULL, '2025-08-11 11:15:27', '2025-08-11 14:30:39'),
	(2, 'Oficina de Tránsito Norte', 'Av. Libertador 5678', 'Buenos Aires', 'Buenos Aires', NULL, '011-8765-4321', 'norte@transito.gob.ar', 'Lunes a Viernes 8:00-16:00', 'activo', 50, NULL, NULL, NULL, '2025-08-11 11:15:27', '2025-08-11 11:15:27'),
	(3, 'Oficina de Tránsito Sur', 'Av. 9 de Julio 9012', 'Buenos Aires', 'Buenos Aires', NULL, '011-2109-8765', 'sur@transito.gob.ar', 'Lunes a Viernes 8:00-16:00', 'activo', 50, NULL, NULL, NULL, '2025-08-11 11:15:27', '2025-08-11 11:15:27'),
	(4, 'Oficina de Tránsito Oeste', 'Av. Rivadavia 3456', 'Buenos Aires', 'Buenos Aires', NULL, '011-6543-2109', 'oeste@transito.gob.ar', 'Lunes a Viernes 8:00-16:00', 'activo', 50, NULL, NULL, NULL, '2025-08-11 11:15:27', '2025-08-11 11:15:27'),
	(5, 'Oficina de Tránsito Este', 'Av. Corrientes 7890', 'Buenos Aires', 'Buenos Aires', NULL, '011-0987-6543', 'este@transito.gob.ar', 'Lunes a Viernes 8:00-16:00', 'activo', 50, NULL, NULL, NULL, '2025-08-11 11:15:27', '2025-08-11 11:15:27'),
	(6, 'Oficina de Transito  Central (B)', 'Pedro Carrión  1080', 'Buenos Aires', '', NULL, '230 15-435-1043', 'ofcina1@gmail.com', NULL, 'activo', 50, NULL, NULL, NULL, '2025-08-11 14:31:56', '2025-08-11 14:32:19');

-- Volcando datos para la tabla examen.estados_examen: ~0 rows (aproximadamente)

-- Volcando datos para la tabla examen.examenes: ~4 rows (aproximadamente)
INSERT INTO `examenes` (`examen_id`, `titulo`, `nombre`, `descripcion`, `tiempo_limite`, `duracion_minutos`, `puntaje_minimo`, `fecha_inicio`, `fecha_fin`, `numero_preguntas`, `dificultad`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 'Examen B2 Completo', 'Examen B2 Completo', 'Examen teórico completo para la obtención de licencia de conducir categoría B2. Incluye 60 preguntas sobre normativa de tránsito, señales de tránsito, mecánica básica y seguridad vial.', 90, 90, 70.00, '2025-08-04 16:46:22', '2026-08-04 16:46:22', 60, 'medio', 'activo', '2025-08-04 16:46:22', '2025-08-20 17:56:31'),
	(2, 'Examen de Prueba B2', 'Examen de Prueba B2', 'Examen de prueba para la categoría B2', 30, 30, 70.00, '2025-08-11 12:00:56', '2026-08-11 12:00:56', 2, 'medio', 'activo', '2025-08-11 09:00:56', '2025-08-11 14:30:12'),
	(8, 'Test Examen Frontend', 'Test Examen Frontend', NULL, 60, 60, 70.00, '2025-08-14 17:02:32', '2026-08-14 17:02:32', 1, 'medio', 'activo', '2025-08-14 17:02:33', '2025-08-14 17:02:33'),
	(9, 'Examen para cat D2', 'Examen para cat D2', 'b2', 60, 60, 70.00, '2025-08-14 14:49:00', '2025-08-20 14:49:00', 10, 'medio', 'activo', '2025-08-14 17:50:47', '2025-08-14 17:52:09');

-- Volcando datos para la tabla examen.examen_asignado: ~7 rows (aproximadamente)
INSERT INTO `examen_asignado` (`id`, `conductor_id`, `examen_id`, `intentos_disponibles`, `aprobado`, `fecha_asignacion`, `fecha_aprobacion`, `puntaje_final`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 2, 0, '2025-09-22 15:53:19', NULL, NULL, '2025-09-22 15:53:19', '2025-09-22 15:53:19'),
	(2, 1, 2, 1, 0, '2025-09-22 15:53:19', NULL, NULL, '2025-09-22 15:53:19', '2025-09-22 15:53:19'),
	(3, 2, 1, 0, 1, '2025-09-22 15:53:19', NULL, 85.50, '2025-09-22 15:53:19', '2025-09-22 15:53:19'),
	(4, 4, 8, 3, 0, '2025-09-22 15:53:19', NULL, NULL, '2025-09-22 15:53:19', '2025-09-22 15:53:19'),
	(5, 2, 2, 3, 0, '2025-09-22 16:03:15', NULL, NULL, '2025-09-22 19:03:15', '2025-09-22 19:03:15'),
	(6, 5, 1, 3, 0, '2025-09-22 16:03:44', NULL, NULL, '2025-09-22 19:03:44', '2025-09-22 19:03:44'),
	(7, 5, 8, 2, 0, '2025-09-22 16:09:32', NULL, NULL, '2025-09-22 19:09:32', '2025-09-22 19:09:32');

-- Volcando datos para la tabla examen.examen_categoria: ~6 rows (aproximadamente)
INSERT INTO `examen_categoria` (`examen_categoria_id`, `examen_id`, `categoria_id`, `created_at`, `updated_at`) VALUES
	(1, 2, 2, '2025-08-11 09:00:56', '2025-08-11 09:00:56'),
	(7, 8, 5, '2025-08-14 17:02:33', '2025-08-14 17:02:33'),
	(8, 9, 6, '2025-08-14 17:50:47', '2025-08-14 17:50:47'),
	(9, 9, 5, '2025-08-14 17:50:47', '2025-08-14 17:50:47'),
	(11, 1, 6, '2025-08-20 17:56:31', '2025-08-20 17:56:31'),
	(12, 1, 5, '2025-08-20 17:56:31', '2025-08-20 17:56:31');

-- Volcando datos para la tabla examen.examen_conductor: ~5 rows (aproximadamente)
INSERT INTO `examen_conductor` (`examen_conductor_id`, `examen_id`, `conductor_id`, `estado`, `fecha_inicio`, `fecha_fin`, `puntaje_obtenido`, `tiempo_utilizado`, `intentos_restantes`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'pendiente', NULL, NULL, NULL, NULL, 3, '2025-09-22 15:07:25', '2025-09-22 15:07:25'),
	(2, 2, 1, 'en_progreso', NULL, NULL, NULL, NULL, 2, '2025-09-22 15:07:25', '2025-09-22 15:07:25'),
	(3, 1, 2, 'completado', NULL, NULL, 85.50, NULL, 1, '2025-09-22 15:07:25', '2025-09-22 15:07:25'),
	(4, 2, 3, 'aprobado', NULL, NULL, 92.00, NULL, 0, '2025-09-22 15:07:25', '2025-09-22 15:07:25'),
	(5, 8, 4, 'pendiente', NULL, NULL, NULL, NULL, 3, '2025-09-22 18:36:11', '2025-09-22 18:36:11');

-- Volcando datos para la tabla examen.examen_escuela: ~0 rows (aproximadamente)

-- Volcando datos para la tabla examen.examen_pregunta: ~0 rows (aproximadamente)

-- Volcando datos para la tabla examen.migrations: ~2 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
	(1, '2024-04-21-000000', 'App\\Database\\Migrations\\CreateAllTables', 'default', 'App', 1754312650, 1),
	(2, '2024-04-21-000012', 'App\\Database\\Migrations\\UpdateConductoresTable', 'default', 'App', 1754312651, 1);

-- Volcando datos para la tabla examen.perfiles: ~0 rows (aproximadamente)

-- Volcando datos para la tabla examen.preguntas: ~7 rows (aproximadamente)
INSERT INTO `preguntas` (`pregunta_id`, `categoria_id`, `enunciado`, `tipo_pregunta`, `dificultad`, `puntaje`, `es_critica`, `created_at`, `updated_at`) VALUES
	(64, 6, '¿Cuál es la velocidad máxima permitida en calles de la Ciudad de Buenos Aires?.', 'multiple', 'dificil', 10.00, 1, '2025-08-11 12:42:15', '2025-08-21 17:57:57'),
	(65, 2, '¿Cuál es la velocidad máxima permitida en calles de la Ciudad de Buenos Aires?', 'multiple', 'facil', 1.00, 0, '2025-08-11 13:48:02', '2025-08-21 17:59:27'),
	(66, 2, '¿Cuál es la distancia mínima de seguridad que debe mantener con el vehículo de adelante al conducir un vehículo con remolque?', 'multiple', 'medio', 10.00, 0, '2025-08-11 16:11:47', '2025-08-11 16:11:47'),
	(67, 2, '¿Qué documentación adicional se requiere para conducir un vehículo con remolque?', 'multiple', 'medio', 10.00, 0, '2025-08-11 16:11:47', '2025-08-11 16:11:47'),
	(68, 2, '¿Cuál es la velocidad máxima permitida para vehículos con remolque en autopista?', 'multiple', 'medio', 10.00, 0, '2025-08-11 16:11:47', '2025-08-22 21:52:59'),
	(69, 6, 'Pregunta 2', 'multiple', 'medio', 10.00, 0, '2025-08-14 20:20:40', '2025-08-14 20:20:40'),
	(71, 5, 'la pregunta numro 6 99', 'multiple', 'medio', 10.00, 0, '2025-08-21 19:31:33', '2025-08-21 19:42:22');

-- Volcando datos para la tabla examen.respuestas: ~20 rows (aproximadamente)
INSERT INTO `respuestas` (`respuesta_id`, `pregunta_id`, `texto`, `imagen`, `es_correcta`, `created_at`, `updated_at`) VALUES
	(246, 66, '50 metros', NULL, 1, '2025-08-11 16:11:47', '2025-08-11 16:11:47'),
	(247, 66, '30 metros', NULL, 0, '2025-08-11 16:11:47', '2025-08-11 16:11:47'),
	(248, 66, '20 metros', NULL, 0, '2025-08-11 16:11:47', '2025-08-11 16:11:47'),
	(249, 66, '10 metros', NULL, 0, '2025-08-11 16:11:47', '2025-08-11 16:11:47'),
	(250, 67, 'Permiso de circulación del remolque y seguro específico', NULL, 1, '2025-08-11 16:11:47', '2025-08-11 16:11:47'),
	(251, 67, 'Solo el permiso de conducir E1', NULL, 0, '2025-08-11 16:11:47', '2025-08-11 16:11:47'),
	(252, 67, 'No se requiere documentación adicional', NULL, 0, '2025-08-11 16:11:47', '2025-08-11 16:11:47'),
	(253, 67, 'Solo el seguro del vehículo principal', NULL, 0, '2025-08-11 16:11:47', '2025-08-11 16:11:47'),
	(258, 69, 'A- Comando', NULL, 0, '2025-08-14 20:20:40', '2025-08-14 20:20:40'),
	(259, 69, 'B- Terminando', NULL, 1, '2025-08-14 20:20:40', '2025-08-14 20:20:40'),
	(270, 64, 'A- Comando', '1755792759_e157a7637e9a419a369e.jpg', 1, '2025-08-21 17:57:57', '2025-08-21 17:57:57'),
	(271, 64, 'B- Terminando', '1755792799_cfd4fca62e58998945c4.jpg', 0, '2025-08-21 17:57:57', '2025-08-21 17:57:57'),
	(272, 65, 'A- Comando', '1755799139_bad106cb88e4449d20f0.jpeg', 1, '2025-08-21 17:59:27', '2025-08-21 17:59:27'),
	(273, 65, 'B-Segunda ', '1755799158_0ec9981f915f5fbd1c32.jpg', 0, '2025-08-21 17:59:27', '2025-08-21 17:59:27'),
	(279, 71, 'A- probando ', '1755804660_e31d88828fffd8ea3a8e.jpg', 1, '2025-08-21 19:42:22', '2025-08-21 19:42:22'),
	(280, 71, 'B-Segunda  e rrrr t', '1755804676_05c16b0bc11b6afe7c4f.jpg', 0, '2025-08-21 19:42:22', '2025-08-21 19:42:22'),
	(281, 68, '80 km/h', NULL, 0, '2025-08-22 21:52:59', '2025-08-22 21:52:59'),
	(282, 68, '100 km/h', NULL, 1, '2025-08-22 21:52:59', '2025-08-22 21:52:59'),
	(283, 68, '120 km/h', NULL, 0, '2025-08-22 21:52:59', '2025-08-22 21:52:59'),
	(284, 68, '90 km/h', NULL, 0, '2025-08-22 21:52:59', '2025-08-22 21:52:59');

-- Volcando datos para la tabla examen.respuestas_conductor: ~0 rows (aproximadamente)

-- Volcando datos para la tabla examen.roles: ~3 rows (aproximadamente)
INSERT INTO `roles` (`rol_id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'adminisrador', '2025-09-16 14:45:17', '2025-09-16 14:45:19'),
	(2, 'sistema', 'tecnico Operario', '2025-09-16 14:45:40', '2025-09-16 14:45:41'),
	(3, 'conductor', 'Conductor', '2025-09-16 14:46:25', '2025-09-16 14:46:27');

-- Volcando datos para la tabla examen.user_roles: ~0 rows (aproximadamente)

-- Volcando datos para la tabla examen.usuarios: ~7 rows (aproximadamente)
INSERT INTO `usuarios` (`usuario_id`, `dni`, `nombre`, `apellido`, `email`, `password`, `estado`, `created_at`, `updated_at`) VALUES
	(1, '11340998', 'Yariel', 'Sulroca', 'sulrca@domain.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'activo', '2025-09-16 15:10:18', '2025-09-16 15:10:18'),
	(2, '32123445', 'Conructor1', 'ApellidoConductor1', 'condec1@damon.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'activo', '2025-09-16 15:12:19', '2025-09-16 15:12:20'),
	(3, '21345334', 'Conductor2', 'ApellidoConducor2', 'conduc2@domain.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'activo', '2025-09-16 15:13:11', '2025-09-16 15:13:12'),
	(4, '99999999', 'Usuario', 'Prueba', 'usuario.prueba@test.com', '$2y$10$/0JBmFkhdaczvpwEN7DVYeW0WZPwSAVplMLE/TcnkGgYZ.VvHpm4.', 'activo', '2025-09-22 15:16:45', '2025-09-22 15:16:45'),
	(5, '11111111', 'María', 'González', 'maria.gonzalez@test.com', '$2y$10$jTtF2X.X9b586rzPXyQtpe/RYkMmmS5pV1UaGuO60HFNldLXq4z5m', 'activo', '2025-09-22 15:23:20', '2025-09-22 15:23:20'),
	(6, '22222222', 'Carlos', 'Rodríguez', 'carlos.rodriguez@test.com', '$2y$10$dxGShMcyx2eoBSQUQQDKhOcWtdS917RuQg3/5SqdysrmalBoxerzm', 'activo', '2025-09-22 15:23:20', '2025-09-22 15:23:20'),
	(7, '33333333', 'Ana', 'Martínez', 'ana.martinez@test.com', '$2y$10$zmbaKB/59Y1dhoD9UU7QQOx3dfqz0Tvp2cm12DsZXWJj9m2c1oTie', 'activo', '2025-09-22 15:23:20', '2025-09-22 15:23:20');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
