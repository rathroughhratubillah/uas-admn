<?php
// ============================================================
// src/db.php
// Singleton PDO connection — baca env via config.php
// ============================================================

require_once __DIR__ . '/config.php';

class DB {
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                DB_HOST, DB_PORT, DB_NAME
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
            } catch (PDOException $e) {
                // Jangan tampilkan detail error ke user di production
                error_log('[DB] Connection failed: ' . $e->getMessage());
                die('<div style="font-family:sans-serif;padding:2rem;color:#c0392b;">
                    <h2>&#9888; Koneksi Database Gagal</h2>
                    <p>Periksa konfigurasi environment variable DB_HOST, DB_NAME, DB_USER, DB_PASSWORD.</p>
                    <p><small>Detail error tersimpan di log server.</small></p>
                </div>');
            }
        }
        return self::$instance;
    }

    /** Shortcut: ambil PDO instance */
    public static function get(): PDO {
        return self::getInstance();
    }
}
