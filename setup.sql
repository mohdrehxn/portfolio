-- ═══════════════════════════════════════════════════
--  setup.sql — Portfolio Contact Form Database
--  Run this in phpMyAdmin or MySQL CLI:
--    mysql -u root -p portfolio_db < setup.sql
-- ═══════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS `portfolio_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `portfolio_db`;

CREATE TABLE IF NOT EXISTS `contacts` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120)     NOT NULL,
  `email`      VARCHAR(180)     NOT NULL,
  `budget`     VARCHAR(60)      DEFAULT 'Not specified',
  `message`    TEXT             NOT NULL,
  `ip`         VARCHAR(45)      DEFAULT NULL,
  `status`     ENUM('new','read','replied','archived') DEFAULT 'new',
  `created_at` DATETIME         DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email`   (`email`),
  INDEX `idx_status`  (`status`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Sample data (delete after testing) ──────────────
INSERT INTO `contacts` (`name`, `email`, `budget`, `message`, `ip`, `status`) VALUES
('Rahul Sharma',   'rahul@example.com',  'standard', 'Hi! I need a 5-page website for my startup. Can we discuss?', '192.168.1.1', 'replied'),
('Sarah Williams', 'sarah@example.com',  'premium',  'Looking for a full web app with login system and dashboard.', '192.168.1.2', 'read'),
('Aman Verma',     'aman@example.com',   'basic',    'Need a simple portfolio site for myself. Budget is tight.', '192.168.1.3', 'new');
