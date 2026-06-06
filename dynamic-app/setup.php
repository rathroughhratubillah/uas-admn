<?php
// ============================================================
// setup.php  (dijalankan oleh docker-entrypoint.sh)
// Buat user admin dengan password_hash jika belum ada,
// lalu jalankan seed.sql untuk data awal.
// File ini TIDAK bisa diakses dari browser (ada di root, bukan public/).
// ============================================================

require_once __DIR__ . '/src/config.php';

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

$maxTry = 15;
$pdo    = null;

// Tunggu MariaDB siap (retry loop)
for ($i = 1; $i <= $maxTry; $i++) {
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        echo "[setup] Koneksi database berhasil.\n";
        break;
    } catch (PDOException $e) {
        echo "[setup] Percobaan {$i}/{$maxTry}: database belum siap, tunggu 3 detik...\n";
        sleep(3);
    }
}

if (!$pdo) {
    echo "[setup] ERROR: Tidak bisa koneksi ke database setelah {$maxTry} percobaan. Abort.\n";
    exit(1);
}

// ---- Pastikan tabel CRUD kontak tersedia ----
$pdo->exec("
    CREATE TABLE IF NOT EXISTS contacts (
      id INT UNSIGNED NOT NULL AUTO_INCREMENT,
      name VARCHAR(100) NOT NULL,
      age TINYINT UNSIGNED NOT NULL,
      email VARCHAR(100) NOT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      UNIQUE KEY uniq_contacts_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$contactCount = (int)$pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
if ($contactCount === 0) {
    $stmt = $pdo->prepare('INSERT INTO contacts (name, age, email) VALUES (?, ?, ?)');
    $stmt->execute(['Ratu Billah Lingga Suci', 21, 'ratu.lingga@example.com']);
    $stmt->execute(['Data Contoh UAS', 22, 'contoh.uas@example.com']);
    echo "[setup] Seed data contacts berhasil dibuat.\n";
} else {
    echo "[setup] Tabel contacts sudah berisi data, skip seed contacts.\n";
}

// ---- Buat / validasi admin default ----
$stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE username = ? LIMIT 1');
$stmt->execute(['admin']);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 11]);
    $ins  = $pdo->prepare('
        INSERT INTO users (username, password_hash, email, full_name, role)
        VALUES (?, ?, ?, ?, ?)
    ');
    $ins->execute(['admin', $hash, 'admin@uas.local', 'Administrator UAS', 'admin']);
    echo "[setup] User admin berhasil dibuat (password: admin123).\n";
} else {
    if (!password_verify('admin123', $admin['password_hash'])) {
        $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 11]);
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([$hash, $admin['id']]);
        echo "[setup] Hash password admin diperbarui agar cocok dengan admin123.\n";
    } else {
        echo "[setup] User admin sudah ada dan password valid, skip.\n";
    }
}

// ---- Jalankan seed.sql jika tasks masih kosong ----
$taskCount = (int)$pdo->query('SELECT COUNT(*) FROM tasks')->fetchColumn();

if ($taskCount === 0) {
    $seedFile = getenv('SEED_FILE') ?: '/docker-seed/seed.sql';
    // Coba path alternatif jika tidak ada di mount Docker Compose
    if (!file_exists($seedFile)) {
        $seedFile = '/docker-entrypoint-initdb.d/seed.sql';
    }

    if (file_exists($seedFile)) {
        $sql = file_get_contents($seedFile);
        // Eksekusi per statement
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $q) {
            if (!empty($q)) {
                try { $pdo->exec($q); } catch (PDOException $e) { /* skip USE db dll */ }
            }
        }
        echo "[setup] Seed data tasks berhasil dimuat dari {$seedFile}.\n";
    } else {
        echo "[setup] File seed.sql tidak ditemukan, skip seed.\n";
    }
} else {
    echo "[setup] Tabel tasks sudah berisi data, skip seed.\n";
}

echo "[setup] Setup selesai. Menjalankan Apache...\n";
