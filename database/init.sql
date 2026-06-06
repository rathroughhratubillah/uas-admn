-- ============================================================
-- UAS Cloud Computing II - Database Init
-- NIM  : 2388010012
-- File : database/init.sql
-- Note : setup.php tetap memvalidasi hash admin123 saat container start
-- ============================================================

CREATE DATABASE IF NOT EXISTS `uascloud_lingga_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `uascloud_lingga_db`;

-- -------------------------------------------------------
-- Table: users
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(50)     NOT NULL UNIQUE,
  `password_hash` VARCHAR(255)    NOT NULL,
  `email`         VARCHAR(100)    NOT NULL,
  `full_name`     VARCHAR(100)    NOT NULL DEFAULT '',
  `role`          ENUM('admin','viewer') NOT NULL DEFAULT 'viewer',
  `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed user admin default.
-- setup.php akan memperbarui hash ini jika tidak cocok dengan password admin123.
INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `full_name`, `role`)
VALUES
  (1, 'admin', '$2b$10$AYEh3JcdZRr76lMBa3jyVOp7ljwlJRiS205Hs.3vNVKXqsxCrlUrq', 'admin@uas.local', 'Administrator UAS', 'admin')
ON DUPLICATE KEY UPDATE
  `username` = VALUES(`username`),
  `email` = VALUES(`email`),
  `full_name` = VALUES(`full_name`),
  `role` = VALUES(`role`);

-- -------------------------------------------------------
-- Table: tasks (fitur CRUD utama)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tasks` (
  `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED    NOT NULL,
  `title`       VARCHAR(150)    NOT NULL,
  `description` TEXT,
  `status`      ENUM('todo','in_progress','done') NOT NULL DEFAULT 'todo',
  `priority`    ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  `due_date`    DATE,
  `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_tasks_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- Table: contacts (fitur CRUD dynamic app)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contacts` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `age`        TINYINT UNSIGNED NOT NULL,
  `email`      VARCHAR(100) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_contacts_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `contacts` (`id`, `name`, `age`, `email`)
VALUES
  (1, 'Ratu Billah Lingga Suci', 21, 'ratu.lingga@example.com'),
  (2, 'Data Contoh UAS', 22, 'contoh.uas@example.com');

-- -------------------------------------------------------
-- Seed data tasks (user_id=1 = admin)
-- -------------------------------------------------------
INSERT IGNORE INTO `tasks`
  (`id`, `user_id`, `title`, `description`, `status`, `priority`, `due_date`)
VALUES
  (1, 1, 'Setup AWS EC2 Instance',
      'Buat instance UAS-2388010012 di region ap-southeast-1, konfigurasi security group port 80 dan SSH.',
      'done', 'high', '2026-06-01'),

  (2, 1, 'Konfigurasi Docker dan Docker Compose',
      'Install Docker Engine, siapkan service static-cv, dynamic-app, mariadb, dan reverse-proxy.',
      'done', 'high', '2026-06-03'),

  (3, 1, 'Deploy Web Statis CV',
      'Build image dari folder static-cv dan push ke Docker Hub ratubillahlinggasuci/uas-static.',
      'in_progress', 'high', '2026-06-05'),

  (4, 1, 'Deploy Web Dinamis PHP MariaDB',
      'Build image dari folder dynamic-app dan push ke Docker Hub ratubillahlinggasuci/uas-dynamic.',
      'in_progress', 'high', '2026-06-05'),

  (5, 1, 'Konfigurasi Nginx Reverse Proxy',
      'Atur reverse proxy agar public port 80 dapat mengakses web statis dan web dinamis.',
      'todo', 'medium', '2026-06-07'),

  (6, 1, 'Setup CI/CD GitHub Actions',
      'Buat workflow paths filter untuk build, push image, dan deploy otomatis ke AWS EC2.',
      'todo', 'medium', '2026-06-08'),

  (7, 1, 'Dokumentasi README',
      'Tulis arsitektur, port mapping, environment variable, screenshot, dan log pengujian.',
      'todo', 'low', '2026-06-09'),

  (8, 1, 'Persiapan Demo UAS',
      'Uji login admin, CRUD, database seed, GitHub Actions, dan live test zero-touch deployment.',
      'todo', 'high', '2026-06-10');
