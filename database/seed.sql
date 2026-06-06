-- ============================================================
-- UAS Cloud Computing II - Seed Data
-- File : database/seed.sql
-- Dijalankan setelah setup.php membuat user admin (id=1)
-- ============================================================

USE `uascloud_lingga_db`;

INSERT IGNORE INTO `contacts`
  (`id`, `name`, `age`, `email`)
VALUES
  (1, 'Ratu Billah Lingga Suci', 21, 'ratu.lingga@example.com'),
  (2, 'Data Contoh UAS', 22, 'contoh.uas@example.com');

INSERT IGNORE INTO `tasks`
  (`user_id`, `title`, `description`, `status`, `priority`, `due_date`)
VALUES
  (1, 'Setup AWS EC2 Instance',
      'Buat instance UAS-2388010012 di region ap-southeast-1, konfigurasi security group port 80 dan SSH.',
      'done', 'high', '2026-06-01'),

  (1, 'Konfigurasi Docker & Docker Compose',
      'Install Docker Engine, buat docker-compose.yml dengan service: static, dynamic, db, nginx.',
      'done', 'high', '2026-06-03'),

  (1, 'Deploy Web Statis (CV)',
      'Build image dari folder static-cv, push ke Docker Hub ratubillahlinggasuci/uas-static, running di container.',
      'in_progress', 'high', '2026-06-05'),

  (1, 'Deploy Web Dinamis (PHP + MariaDB)',
      'Build image dari folder dynamic-app, push ke Docker Hub ratubillahlinggasuci/uas-dynamic, uji CRUD.',
      'in_progress', 'high', '2026-06-05'),

  (1, 'Konfigurasi Nginx Reverse Proxy',
      'Setup nginx.conf agar / menuju static-cv dan /app menuju dynamic-app.',
      'todo', 'medium', '2026-06-07'),

  (1, 'Setup CI/CD GitHub Actions',
      'Buat workflow .github/workflows/deploy.yml untuk auto-build dan push image ke Docker Hub.',
      'todo', 'medium', '2026-06-08'),

  (1, 'Dokumentasi README.md',
      'Tulis README lengkap: cara build, cara run, penjelasan arsitektur, screenshot demo.',
      'todo', 'low', '2026-06-09'),

  (1, 'Persiapan Demo UAS',
      'Uji semua service berjalan, login admin, CRUD berhasil, screenshot untuk laporan.',
      'todo', 'high', '2026-06-10');
