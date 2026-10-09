-- ========================================================
-- MIRACLE SPA - ESQUEMA DE BASE DE DATOS
-- Sistema de Gestión & Agendamiento de Citas
-- Compatible con MySQL / MariaDB (XAMPP)
-- ========================================================

CREATE DATABASE IF NOT EXISTS `miraclespa_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `miraclespa_db`;

-- Tabla de Configuración General del Spa
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` TEXT NOT NULL,
  `description` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Categorías de Servicio
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `icon` VARCHAR(50) DEFAULT 'sparkles',
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Servicios
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

-- Tabla de Profesionales / Especialistas
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

-- Asignación de Servicios por Profesional (Muchos a Muchos)
CREATE TABLE IF NOT EXISTS `professional_services` (
  `professional_id` INT NOT NULL,
  `service_id` INT NOT NULL,
  PRIMARY KEY (`professional_id`, `service_id`),
  INDEX (`service_id`),
  CONSTRAINT `fk_ps_professional` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ps_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Horarios Semanales de los Profesionales (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
CREATE TABLE IF NOT EXISTS `schedules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `professional_id` INT NOT NULL,
  `day_of_week` TINYINT NOT NULL COMMENT '0=Domingo, 1=Lunes, 2=Martes, 3=Miercoles, 4=Jueves, 5=Viernes, 6=Sabado',
  `start_time` TIME NOT NULL DEFAULT '09:00:00',
  `end_time` TIME NOT NULL DEFAULT '19:00:00',
  `lunch_start` TIME DEFAULT '13:00:00',
  `lunch_end` TIME DEFAULT '14:00:00',
  `is_off` TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `unique_prof_day` (`professional_id`, `day_of_week`),
  INDEX (`professional_id`),
  CONSTRAINT `fk_schedule_prof` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Clientes
CREATE TABLE IF NOT EXISTS `clients` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_client_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Citas / Reservas
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

-- Tabla de Auditoría y Registro de Notificaciones por Correo Electrónico
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

-- Tabla de Usuarios Administradores
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'recepcionista') NOT NULL DEFAULT 'admin',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
