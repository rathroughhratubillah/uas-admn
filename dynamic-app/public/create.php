<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/auth.php';

requireLogin();

$input = ['name' => '', 'age' => '', 'email' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['name'] = sanitizeInput($_POST['name'] ?? '', 100);
    $input['age'] = sanitizeInput($_POST['age'] ?? '', 3);
    $input['email'] = sanitizeInput($_POST['email'] ?? '', 100);

    if ($input['name'] === '') {
        $errors[] = 'Nama wajib diisi.';
    }

    if (!ctype_digit($input['age']) || (int)$input['age'] < 1 || (int)$input['age'] > 120) {
        $errors[] = 'Umur harus berupa angka 1 sampai 120.';
    }

    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email belum valid.';
    }

    if (!$errors) {
        $stmt = DB::get()->prepare('INSERT INTO contacts (name, age, email) VALUES (?, ?, ?)');
        $stmt->execute([$input['name'], (int)$input['age'], $input['email']]);
        setFlash('success', 'Data kontak berhasil ditambahkan.');
        redirect('/index.php');
    }
}

echo pageHeader('Tambah Data');
?>
<main class="app-shell">
  <section class="content-card form-card">
    <div class="section-toolbar">
      <div>
        <p class="eyebrow">Tambah Data</p>
        <h1>Kontak Baru</h1>
        <p>Isi data dengan lengkap sebelum disimpan.</p>
      </div>
      <a class="btn btn-secondary" href="/index.php">Kembali</a>
    </div>

    <?php if ($errors): ?>
      <div class="alert alert-error">
        <?= e(implode(' ', $errors)); ?>
      </div>
    <?php endif; ?>

    <form class="data-form" method="post">
      <label>
        <span>Nama</span>
        <input type="text" name="name" value="<?= e($input['name']); ?>" required>
      </label>
      <label>
        <span>Umur</span>
        <input type="number" name="age" min="1" max="120" value="<?= e($input['age']); ?>" required>
      </label>
      <label>
        <span>Email</span>
        <input type="email" name="email" value="<?= e($input['email']); ?>" required>
      </label>
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="/index.php">Batal</a>
      </div>
    </form>
  </section>
</main>
<?= pageFooter(); ?>
