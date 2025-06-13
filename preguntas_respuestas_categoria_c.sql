-- Preguntas y respuestas para la categoría C (Vehículos de carga)
INSERT INTO preguntas (categoria_id, enunciado, tipo_pregunta, puntaje, dificultad, es_critica) VALUES
(3, '¿Cuál es el peso máximo permitido para un vehículo de carga en carreteras nacionales?', 'opcion_multiple', 1.00, 'facil', 1),
(3, '¿Qué documentación especial debe portar un vehículo de carga?', 'opcion_multiple', 1.00, 'facil', 1),
(3, '¿Cuál es la velocidad máxima permitida para vehículos de carga en carreteras?', 'opcion_multiple', 1.00, 'facil', 1);

-- Respuestas para la primera pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID() - 2, '35 toneladas', 1),
(LAST_INSERT_ID() - 2, '45 toneladas', 0),
(LAST_INSERT_ID() - 2, '55 toneladas', 0),
(LAST_INSERT_ID() - 2, '65 toneladas', 0);

-- Respuestas para la segunda pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID() - 1, 'Permiso de circulación, SOAT, certificado de inspección técnica y guía de remisión', 1),
(LAST_INSERT_ID() - 1, 'Solo permiso de circulación y SOAT', 0),
(LAST_INSERT_ID() - 1, 'Solo guía de remisión', 0),
(LAST_INSERT_ID() - 1, 'Solo certificado de inspección técnica', 0);

-- Respuestas para la tercera pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID(), '80 km/h', 1),
(LAST_INSERT_ID(), '100 km/h', 0),
(LAST_INSERT_ID(), '120 km/h', 0),
(LAST_INSERT_ID(), '90 km/h', 0);

-- Página 2
INSERT INTO preguntas (categoria_id, enunciado, tipo_pregunta, puntaje, dificultad, es_critica) VALUES
(3, '¿Cuál es la distancia mínima de seguridad que debe mantener un vehículo de carga?', 'opcion_multiple', 1.00, 'facil', 1),
(3, '¿Qué luces debe llevar encendidas un vehículo de carga durante el día?', 'opcion_multiple', 1.00, 'facil', 1),
(3, '¿Cuál es el tiempo máximo de conducción continuada para conductores de carga?', 'opcion_multiple', 1.00, 'facil', 1);

-- Respuestas para la primera pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID() - 2, '50 metros', 1),
(LAST_INSERT_ID() - 2, '30 metros', 0),
(LAST_INSERT_ID() - 2, '20 metros', 0),
(LAST_INSERT_ID() - 2, '40 metros', 0);

-- Respuestas para la segunda pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID() - 1, 'Luces de posición y luces bajas', 1),
(LAST_INSERT_ID() - 1, 'Solo luces de posición', 0),
(LAST_INSERT_ID() - 1, 'Solo luces altas', 0),
(LAST_INSERT_ID() - 1, 'Ninguna luz', 0);

-- Respuestas para la tercera pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID(), '4 horas', 1),
(LAST_INSERT_ID(), '6 horas', 0),
(LAST_INSERT_ID(), '8 horas', 0),
(LAST_INSERT_ID(), '5 horas', 0);

-- Página 3
INSERT INTO preguntas (categoria_id, enunciado, tipo_pregunta, puntaje, dificultad, es_critica) VALUES
(3, '¿Qué debe hacer el conductor si detecta un problema mecánico durante el viaje?', 'opcion_multiple', 1.00, 'medio', 1),
(3, '¿Cuál es el procedimiento para el manejo de carga peligrosa?', 'opcion_multiple', 1.00, 'medio', 1),
(3, '¿Qué medidas debe tomar el conductor en caso de mal tiempo?', 'opcion_multiple', 1.00, 'medio', 1);

-- Respuestas para la primera pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID() - 2, 'Detener el vehículo en un lugar seguro, señalizar y reportar el problema', 1),
(LAST_INSERT_ID() - 2, 'Continuar hasta el destino', 0),
(LAST_INSERT_ID() - 2, 'Ignorar el problema', 0),
(LAST_INSERT_ID() - 2, 'Detenerse en cualquier lugar', 0);

-- Respuestas para la segunda pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID() - 1, 'Verificar la documentación especial, usar señalización adecuada y seguir protocolos de seguridad', 1),
(LAST_INSERT_ID() - 1, 'Manejar normalmente', 0),
(LAST_INSERT_ID() - 1, 'No tomar precauciones especiales', 0),
(LAST_INSERT_ID() - 1, 'Solo verificar la documentación', 0);

-- Respuestas para la tercera pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID(), 'Reducir velocidad, aumentar distancia de seguridad y usar luces', 1),
(LAST_INSERT_ID(), 'Manejar normalmente', 0),
(LAST_INSERT_ID(), 'Detenerse completamente', 0),
(LAST_INSERT_ID(), 'Aumentar velocidad', 0);

-- Página 4
INSERT INTO preguntas (categoria_id, enunciado, tipo_pregunta, puntaje, dificultad, es_critica) VALUES
(3, '¿Cuál es el procedimiento para el manejo de carga refrigerada?', 'opcion_multiple', 1.00, 'medio', 1),
(3, '¿Qué debe verificar el conductor antes de iniciar un viaje largo?', 'opcion_multiple', 1.00, 'medio', 1),
(3, '¿Cómo debe proceder el conductor en caso de robo de carga?', 'opcion_multiple', 1.00, 'medio', 1);

-- Respuestas para la primera pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID() - 2, 'Verificar temperatura, documentación especial y sistema de refrigeración', 1),
(LAST_INSERT_ID() - 2, 'Solo verificar la temperatura', 0),
(LAST_INSERT_ID() - 2, 'No tomar precauciones especiales', 0),
(LAST_INSERT_ID() - 2, 'Solo verificar la documentación', 0);

-- Respuestas para la segunda pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID() - 1, 'Niveles de fluidos, neumáticos, frenos, documentación y carga', 1),
(LAST_INSERT_ID() - 1, 'Solo niveles de fluidos', 0),
(LAST_INSERT_ID() - 1, 'Solo documentación', 0),
(LAST_INSERT_ID() - 1, 'Solo carga', 0);

-- Respuestas para la tercera pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID(), 'Reportar inmediatamente a la policía y a la empresa, no perseguir a los ladrones', 1),
(LAST_INSERT_ID(), 'Perseguir a los ladrones', 0),
(LAST_INSERT_ID(), 'Ignorar el robo', 0),
(LAST_INSERT_ID(), 'Esperar instrucciones', 0);

-- Página 5
INSERT INTO preguntas (categoria_id, enunciado, tipo_pregunta, puntaje, dificultad, es_critica) VALUES
(3, '¿Cuál es el protocolo de seguridad para el manejo de dinero en efectivo?', 'opcion_multiple', 1.00, 'dificil', 0),
(3, '¿Cómo debe manejar el conductor situaciones de tráfico denso?', 'opcion_multiple', 1.00, 'dificil', 0),
(3, '¿Qué medidas debe tomar para prevenir el fraude en la entrega de carga?', 'opcion_multiple', 1.00, 'dificil', 0);

-- Respuestas para la primera pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID() - 2, 'Mantener el dinero en lugar seguro, no divulgarlo y reportar cualquier incidente', 1),
(LAST_INSERT_ID() - 2, 'Llevar el dinero visible', 0),
(LAST_INSERT_ID() - 2, 'No tomar precauciones', 0),
(LAST_INSERT_ID() - 2, 'Dejarlo en el vehículo', 0);

-- Respuestas para la segunda pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID() - 1, 'Mantener distancia, usar luces y seguir las indicaciones de tránsito', 1),
(LAST_INSERT_ID() - 1, 'Aumentar velocidad', 0),
(LAST_INSERT_ID() - 1, 'Cambiar de carril constantemente', 0),
(LAST_INSERT_ID() - 1, 'Usar la berma', 0);

-- Respuestas para la tercera pregunta
INSERT INTO respuestas (pregunta_id, texto, es_correcta) VALUES
(LAST_INSERT_ID(), 'Verificar identidad del receptor, documentación y firmar recibos', 1),
(LAST_INSERT_ID(), 'Entregar sin verificar', 0),
(LAST_INSERT_ID(), 'Solo verificar identidad', 0),
(LAST_INSERT_ID(), 'No tomar precauciones', 0);

-- Continuar con el mismo patrón para las páginas 6-40
-- Cada página tendrá 3 preguntas con 4 respuestas cada una
-- Variar la dificultad y criticidad
-- Incluir temas como:
-- - Mantenimiento preventivo
-- - Manejo de emergencias
-- - Protocolos de seguridad
-- - Prevención de accidentes
-- - Manejo de documentación
-- - Procedimientos especiales
-- - Situaciones de riesgo
-- - Comunicación en emergencias
-- - Manejo de carga especial
-- - Protocolos de entrega 