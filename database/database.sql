-- ========================================================
-- MIRACLE SPA - BASE DE DATOS COMPLETA CON DATOS INICIALES
-- ========================================================

CREATE DATABASE IF NOT EXISTS `miraclespa_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `miraclespa_db`;

-- 1. Tabla de Configuración
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` TEXT NOT NULL,
  `description` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categorías
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `icon` VARCHAR(50) DEFAULT 'sparkles',
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Servicios
CREATE TABLE IF NOT EXISTS `services` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `duration_minutes` INT NOT NULL DEFAULT 60,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `image_url` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (`category_id`),
  CONSTRAINT `fk_service_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Profesionales
CREATE TABLE IF NOT EXISTS `professionals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(50) NULL,
  `title` VARCHAR(100) DEFAULT 'Especialista en Belleza y Bienestar',
  `bio` TEXT NULL,
  `avatar` VARCHAR(255) NULL,
  `rating` DECIMAL(3,2) DEFAULT 4.90,
  `active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Asignación Profesional - Servicio
CREATE TABLE IF NOT EXISTS `professional_services` (
  `professional_id` INT NOT NULL,
  `service_id` INT NOT NULL,
  PRIMARY KEY (`professional_id`, `service_id`),
  INDEX (`service_id`),
  CONSTRAINT `fk_ps_professional` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ps_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Horarios Semanales
CREATE TABLE IF NOT EXISTS `schedules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `professional_id` INT NOT NULL,
  `day_of_week` TINYINT NOT NULL COMMENT '0=Dom, 1=Lun, 2=Mar, 3=Mie, 4=Jue, 5=Vie, 6=Sab',
  `start_time` TIME NOT NULL DEFAULT '09:00:00',
  `end_time` TIME NOT NULL DEFAULT '19:00:00',
  `lunch_start` TIME DEFAULT '13:00:00',
  `lunch_end` TIME DEFAULT '14:00:00',
  `is_off` TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `unique_prof_day` (`professional_id`, `day_of_week`),
  INDEX (`professional_id`),
  CONSTRAINT `fk_schedule_prof` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Clientes
CREATE TABLE IF NOT EXISTS `clients` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_client_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Citas
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(30) NOT NULL UNIQUE,
  `client_id` INT NOT NULL,
  `service_id` INT NOT NULL,
  `professional_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `duration_minutes` INT NOT NULL,
  `buffer_minutes` INT NOT NULL DEFAULT 15,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('pendiente', 'confirmada', 'completada', 'cancelada', 'reprogramada') NOT NULL DEFAULT 'confirmada',
  `manage_token` VARCHAR(64) NOT NULL UNIQUE,
  `client_notes` TEXT NULL,
  `admin_notes` TEXT NULL,
  `cancellation_reason` TEXT NULL,
  `cancelled_at` DATETIME NULL,
  `rescheduled_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_date_prof` (`date`, `professional_id`),
  INDEX `idx_date_status` (`date`, `status`),
  INDEX `idx_token` (`manage_token`),
  CONSTRAINT `fk_app_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_app_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_app_prof` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Email Logs
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `appointment_id` INT NULL,
  `recipient_email` VARCHAR(150) NOT NULL,
  `recipient_name` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `email_type` ENUM('confirmacion', 'reprogramacion', 'cancelacion', 'recordatorio', 'test') NOT NULL,
  `status` ENUM('enviado', 'simulado', 'fallido') NOT NULL DEFAULT 'simulado',
  `body_html` MEDIUMTEXT NOT NULL,
  `error_message` TEXT NULL,
  `sent_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email_app` (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Usuarios Administradores
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'recepcionista') NOT NULL DEFAULT 'admin',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- INSERCIÓN DE DATOS SEMILLA (SEED DATA)
-- ========================================================

-- Configuraciones iniciales
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('spa_name', 'Miracle Spa Sanctuary', 'Nombre comercial del establecimiento'),
('spa_address', 'Av. Las Palmas 450, Centro de Bienestar, Piso 2', 'Dirección física del spa'),
('spa_phone', '+1 (555) 789-2345', 'Teléfono principal de atención'),
('spa_email', 'citas@miraclespa.com', 'Correo oficial de contacto'),
('currency_symbol', '$', 'Símbolo de moneda'),
('buffer_minutes', '15', 'Tiempo de amortiguamiento e higienización entre citas (minutos)'),
('min_cancel_hours', '4', 'Horas mínimas de anticipación requeridas para cancelar o reprogramar'),
('opening_time', '09:00:00', 'Hora de apertura del spa'),
('closing_time', '20:00:00', 'Hora de cierre del spa'),
('slot_interval_minutes', '30', 'Intervalo en minutos para la grilla de disponibilidad'),
('mail_driver', 'simulated', 'Controlador de correo: simulated (visibles en panel), smtp o mail'),
('smtp_host', 'sandbox.smtp.mailtrap.io', 'Servidor SMTP'),
('smtp_port', '2525', 'Puerto SMTP'),
('smtp_user', '', 'Usuario SMTP'),
('smtp_pass', '', 'Contraseña SMTP'),
('smtp_secure', 'tls', 'Seguridad SMTP (tls, ssl o none)'),
('smtp_from_email', 'citas@miraclespa.com', 'Remitente de correos'),
('smtp_from_name', 'Miracle Spa Reservas', 'Nombre del remitente')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- Usuario Administrador inicial (admin / admin123)
INSERT INTO `admin_users` (`username`, `password_hash`, `name`, `email`, `role`) VALUES
('admin', '$2y$10$yuSUaeLCWNEwSbUr0qnO4.Ih7T3uSrim8iB1FddC4wjTlJQJ6jMGG', 'Administrador General', 'admin@miraclespa.com', 'admin')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Categorías
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `display_order`) VALUES
(1, 'Uñas & Manicura Spa', 'unas', 'Cuidado estético integral de manos y pies con productos orgánicos y esmaltes premium.', 'hand-sparkles', 1),
(2, 'Corte & Peluquería', 'cabello', 'Cortes de tendencia, estilismo profesional y tratamientos capilares de alta nutrición.', 'scissors', 2),
(3, 'Pintura & Colorimetría', 'pintura', 'Coloración sin amoníaco, balayage luminoso, mechas babylights y matización.', 'palette', 3),
(4, 'Masajes Terapéuticos', 'masajes', 'Relajación muscular profunda, aromaterapia botánica y piedras calientes volcánicas.', 'heart-pulse', 4),
(5, 'Faciales & Cuidado de Piel', 'faciales', 'Tratamientos de luminosidad, limpieza dermo-profunda e hidratación celular.', 'sparkles', 5)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Servicios
INSERT INTO `services` (`id`, `category_id`, `name`, `description`, `duration_minutes`, `price`, `active`, `image_url`) VALUES
-- Uñas
(1, 1, 'Manicura Spa de Lujo & Parafina', 'Exfoliación con sales marinas, nutrición con cera de parafina tibia y esmaltado de larga duración.', 50, 35.00, 1, 'assets/img/manicura.jpg'),
(2, 1, 'Uñas Esculpidas Acrílicas / Gel', 'Extensión y esculpido artístico con acabado natural o almendrado, incluye diseño básico.', 90, 65.00, 1, 'assets/img/acrilicas.jpg'),
(3, 1, 'Esmaltado Semipermanente & Nail Art', 'Esmaltado en gel con curado UV LED, brillo espejo hasta por 21 días y arte personalizado.', 45, 28.00, 1, 'assets/img/semipermanente.jpg'),
(4, 1, 'Pedicura Spa Rejuvenecedora', 'Baño de sales aromáticas, remoción de asperezas, masaje podal relajante y esmaltado impecable.', 60, 42.00, 1, 'assets/img/pedicura.jpg'),

-- Corte & Cabello
(5, 2, 'Corte de Cabello de Autor (Dama)', 'Asesoría de visagismo, lavado con champú botánico, corte de diseño y secado profesional.', 45, 35.00, 1, 'assets/img/corte-dama.jpg'),
(6, 2, 'Corte de Cabello Caballero & Barba', 'Corte con tijera y máquina de precisión, perfilado de barba y toalla caliente aromática.', 40, 28.00, 1, 'assets/img/corte-caballero.jpg'),
(7, 2, 'Tratamiento Botox Capilar & Hidratación', 'Inyección botánica de aminoácidos y ácido hialurónico para sellado de puntas y brillo espejo.', 75, 70.00, 1, 'assets/img/botox-capilar.jpg'),

-- Pintura & Color
(8, 3, 'Pintura y Coloración Completa Premium', 'Cobertura total de canas o cambio de tono con tintes europeos ricos en aceites esenciales.', 120, 85.00, 1, 'assets/img/tintura.jpg'),
(9, 3, 'Balayage & Efectos de Luz Iluminadores', 'Técnica a mano alzada para crear degradados naturales y luminosos, incluye matizador y plex.', 150, 130.00, 1, 'assets/img/balayage.jpg'),
(10, 3, 'Matización Express & Baño de Brillo', 'Neutralización de tonos indeseados y aporte de brillo radiante en 45 minutos.', 45, 45.00, 1, 'assets/img/matiz.jpg'),

-- Masajes
(11, 4, 'Masaje Relajante Holístico con Aromaterapia', 'Maniobras suaves y fluidas con aceites esenciales orgánicos para disolver el estrés y la fatiga.', 60, 60.00, 1, 'assets/img/masaje-relajante.jpg'),
(12, 4, 'Masaje Descontracturante & Tejido Profundo', 'Alivio de sobrecargas musculares en espalda, hombros y cuello mediante presión focalizada.', 60, 70.00, 1, 'assets/img/masaje-descontracturante.jpg'),
(13, 4, 'Terapia de Piedras Calientes Volcánicas', 'Termoterapia con piedras de basalto caliente que mejora la circulación y calma el sistema nervioso.', 75, 80.00, 1, 'assets/img/piedras-calientes.jpg'),

-- Faciales
(14, 5, 'Limpieza Facial Profunda con Hidrodermoabrasión', 'Extracción suave de impurezas, peeling ultrasónico, máscara de colágeno y fototerapia LED.', 60, 55.00, 1, 'assets/img/limpieza-facial.jpg'),
(15, 5, 'Facial Glow Iluminador Antiedad Vitamina C', 'Shock antioxidante con vitamina C pura al 20%, masaje lifting miofascial y velo de ácido hialurónico.', 50, 50.00, 1, 'assets/img/facial-glow.jpg')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Profesionales
INSERT INTO `professionals` (`id`, `name`, `email`, `phone`, `title`, `bio`, `avatar`, `rating`, `active`) VALUES
(1, 'Valentina Restrepo', 'valentina@miraclespa.com', '+1 (555) 201-9988', 'Master Colorista & Estilista Capilar', 'Más de 9 años transformando cabelleras con técnicas avanzadas de colorimetría y diseño de autor.', 'valentina.jpg', 4.98, 1),
(2, 'Camila Morales', 'camila@miraclespa.com', '+1 (555) 302-8877', 'Especialista en Uñas & Diseños Nail Art', 'Experta en manicura rusa, esculpido y salud ungueal con acabados de alta definición.', 'camila.jpg', 4.95, 1),
(3, 'David Mendoza', 'david@miraclespa.com', '+1 (555) 403-7766', 'Masoterapeuta & Especialista Holístico', 'Certificado internacionalmente en liberación miofascial, piedras volcánicas y masaje deportivo.', 'david.jpg', 4.92, 1),
(4, 'Sofía Carvajal', 'sofia@miraclespa.com', '+1 (555) 504-6655', 'Cosmiatra & Terapeuta de Belleza Facial', 'Especialista en regeneración de piel, hidrodermoabrasión y tratamientos antiedad de vanguardia.', 'sofia.jpg', 4.97, 1),
(5, 'Mateo Gómez', 'mateo@miraclespa.com', '+1 (555) 605-5544', 'Estilista Integral & Terapeuta Spa', 'Profesional versátil enfocado en corte vanguardista, color express y masajes de relajación.', 'mateo.jpg', 4.88, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Mapeo de Profesionales a Servicios
-- Valentina Restrepo: Cortes y Todo Color (5, 6, 7, 8, 9, 10)
INSERT IGNORE INTO `professional_services` (`professional_id`, `service_id`) VALUES
(1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10),
-- Camila Morales: Todo Uñas (1, 2, 3, 4)
(2, 1), (2, 2), (2, 3), (2, 4),
-- David Mendoza: Todos los Masajes (11, 12, 13)
(3, 11), (3, 12), (3, 13),
-- Sofía Carvajal: Faciales y Uñas Spa (14, 15, 1, 3)
(4, 14), (4, 15), (4, 1), (4, 3),
-- Mateo Gómez: Cortes, Matiz y Masajes (5, 6, 10, 11)
(5, 5), (5, 6), (5, 10), (5, 11);

-- Horarios de Trabajo (Lunes a Sábado 09:00 a 19:00, Almuerzo 13:00 a 14:00, Domingo Libre)
-- Profesionales 1 al 5
INSERT IGNORE INTO `schedules` (`professional_id`, `day_of_week`, `start_time`, `end_time`, `lunch_start`, `lunch_end`, `is_off`) VALUES
-- Valentina Restrepo (Lun a Sab)
(1, 1, '09:00:00', '19:00:00', '13:00:00', '14:00:00', 0),
(1, 2, '09:00:00', '19:00:00', '13:00:00', '14:00:00', 0),
(1, 3, '09:00:00', '19:00:00', '13:00:00', '14:00:00', 0),
(1, 4, '09:00:00', '19:00:00', '13:00:00', '14:00:00', 0),
(1, 5, '09:00:00', '19:00:00', '13:00:00', '14:00:00', 0),
(1, 6, '09:00:00', '17:00:00', '13:00:00', '14:00:00', 0),
(1, 0, '09:00:00', '14:00:00', NULL, NULL, 1),

-- Camila Morales (Lun a Sab)
(2, 1, '09:00:00', '19:00:00', '13:00:00', '14:00:00', 0),
(2, 2, '09:00:00', '19:00:00', '13:00:00', '14:00:00', 0),
(2, 3, '09:00:00', '19:00:00', '13:00:00', '14:00:00', 0),
(2, 4, '09:00:00', '19:00:00', '13:00:00', '14:00:00', 0),
(2, 5, '09:00:00', '19:00:00', '13:00:00', '14:00:00', 0),
(2, 6, '09:00:00', '18:00:00', '13:00:00', '14:00:00', 0),
(2, 0, '09:00:00', '14:00:00', NULL, NULL, 1),

-- David Mendoza (Lun a Sab)
(3, 1, '10:00:00', '20:00:00', '14:00:00', '15:00:00', 0),
(3, 2, '10:00:00', '20:00:00', '14:00:00', '15:00:00', 0),
(3, 3, '10:00:00', '20:00:00', '14:00:00', '15:00:00', 0),
(3, 4, '10:00:00', '20:00:00', '14:00:00', '15:00:00', 0),
(3, 5, '10:00:00', '20:00:00', '14:00:00', '15:00:00', 0),
(3, 6, '10:00:00', '18:00:00', '14:00:00', '15:00:00', 0),
(3, 0, '09:00:00', '14:00:00', NULL, NULL, 1),

-- Sofía Carvajal (Lun a Sab)
(4, 1, '09:00:00', '18:00:00', '13:00:00', '14:00:00', 0),
(4, 2, '09:00:00', '18:00:00', '13:00:00', '14:00:00', 0),
(4, 3, '09:00:00', '18:00:00', '13:00:00', '14:00:00', 0),
(4, 4, '09:00:00', '18:00:00', '13:00:00', '14:00:00', 0),
(4, 5, '09:00:00', '18:00:00', '13:00:00', '14:00:00', 0),
(4, 6, '09:00:00', '16:00:00', '13:00:00', '14:00:00', 0),
(4, 0, '09:00:00', '14:00:00', NULL, NULL, 1),

-- Mateo Gómez (Mar a Sab, Lunes Libre)
(5, 1, '09:00:00', '18:00:00', NULL, NULL, 1), -- Lunes libre
(5, 2, '09:30:00', '19:30:00', '13:30:00', '14:30:00', 0),
(5, 3, '09:30:00', '19:30:00', '13:30:00', '14:30:00', 0),
(5, 4, '09:30:00', '19:30:00', '13:30:00', '14:30:00', 0),
(5, 5, '09:30:00', '19:30:00', '13:30:00', '14:30:00', 0),
(5, 6, '09:00:00', '18:00:00', '13:00:00', '14:00:00', 0),
(5, 0, '09:00:00', '14:00:00', NULL, NULL, 1);

-- Clientes Frecuentes de Muestra
INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `notes`) VALUES
(1, 'Lucía Fernández', 'lucia.fernandez@gmail.com', '+1 (555) 777-1001', 'Prefiere esmaltes veganos. Cliente VIP.'),
(2, 'Mariana Gómez', 'mariana.gomez@yahoo.com', '+1 (555) 777-1002', 'Sensible a aromas cítricos fuertes.'),
(3, 'Roberto Sánchez', 'roberto.sanchez@outlook.com', '+1 (555) 777-1003', 'Corte clásico y masaje descontracturante regular.'),
(4, 'Andrés Silva', 'andres.silva@gmail.com', '+1 (555) 777-1004', 'Cliente corporativo, agenda sábados.'),
(5, 'Elena Vásquez', 'elena.vasquez@gmail.com', '+1 (555) 777-1005', 'Tratamientos de luminosidad facial y balayage.'),
(6, 'Carolina Ortiz', 'carolina.ortiz@hotmail.com', '+1 (555) 777-1006', 'Uñas esculpidas y nail art moderno.')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Citas de demostración (cubre pasado para estadísticas, hoy y futuro)
INSERT INTO `appointments` (`id`, `code`, `client_id`, `service_id`, `professional_id`, `date`, `start_time`, `end_time`, `duration_minutes`, `buffer_minutes`, `price`, `status`, `manage_token`, `client_notes`) VALUES
-- Citas Pasadas (Completadas para Analytics)
(1, 'MRC-2026-1001', 1, 1, 2, '2026-10-01', '10:00:00', '10:50:00', 50, 15, 35.00, 'completada', 'tok_demo_1001_a9f8', 'Manicura de cumpleaños'),
(2, 'MRC-2026-1002', 2, 9, 1, '2026-10-02', '11:00:00', '13:30:00', 150, 15, 130.00, 'completada', 'tok_demo_1002_b8e7', 'Balayage miel'),
(3, 'MRC-2026-1003', 3, 12, 3, '2026-10-03', '15:00:00', '16:00:00', 60, 15, 70.00, 'completada', 'tok_demo_1003_c7d6', 'Dolor en cervicales'),
(4, 'MRC-2026-1004', 4, 6, 5, '2026-10-05', '16:00:00', '16:40:00', 40, 15, 28.00, 'completada', 'tok_demo_1004_d6c5', 'Corte y barba antes de viaje'),
(5, 'MRC-2026-1005', 5, 14, 4, '2026-10-06', '10:00:00', '11:00:00', 60, 15, 55.00, 'completada', 'tok_demo_1005_e5b4', 'Limpieza profunda mensual'),
(6, 'MRC-2026-1006', 6, 2, 2, '2026-10-07', '14:30:00', '16:00:00', 90, 15, 65.00, 'completada', 'tok_demo_1006_f4a3', 'Esculpidas baby boomer'),

-- Citas de Hoy y Próximos días
(7, 'MRC-2026-1007', 1, 3, 2, CURDATE(), '10:00:00', '10:45:00', 45, 15, 28.00, 'confirmada', 'tok_demo_1007_01a2', 'Esmaltado semipermanente rojo vino'),
(8, 'MRC-2026-1008', 3, 11, 3, CURDATE(), '11:30:00', '12:30:00', 60, 15, 60.00, 'confirmada', 'tok_demo_1008_12b3', 'Masaje relajante antiestrés'),
(9, 'MRC-2026-1009', 5, 8, 1, CURDATE(), '14:30:00', '16:30:00', 120, 15, 85.00, 'confirmada', 'tok_demo_1009_23c4', 'Tinte chocolate cobrizo'),
(10, 'MRC-2026-1010', 2, 15, 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:30:00', '11:20:00', 50, 15, 50.00, 'confirmada', 'tok_demo_1010_34d5', 'Facial vitamina C glow'),
(11, 'MRC-2026-1011', 4, 5, 5, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:00:00', '15:45:00', 45, 15, 35.00, 'confirmada', 'tok_demo_1011_45e6', 'Mantenimiento de corte'),
(12, 'MRC-2026-1012', 6, 4, 2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '16:30:00', '17:30:00', 60, 15, 42.00, 'confirmada', 'tok_demo_1012_56f7', 'Pedicura spa completa')
ON DUPLICATE KEY UPDATE `code` = VALUES(`code`);

-- Email log inicial simulado
INSERT INTO `email_logs` (`id`, `appointment_id`, `recipient_email`, `recipient_name`, `subject`, `email_type`, `status`, `body_html`) VALUES
(1, 7, 'lucia.fernandez@gmail.com', 'Lucía Fernández', 'Confirmación de Cita - Miracle Spa Sanctuary [MRC-2026-1007]', 'confirmacion', 'simulado', '<h3>¡Tu cita está confirmada!</h3><p>Servicio: Esmaltado Semipermanente & Nail Art</p><p>Especialista: Camila Morales</p>')
ON DUPLICATE KEY UPDATE `subject` = VALUES(`subject`);
