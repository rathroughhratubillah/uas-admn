<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/auth.php';

requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect('/index.php');
}

$pdo = DB::get();
$stmt = $pdo->prepare('SELECT id, name, age, email FROM contacts WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$contact = $stmt->fetch();

if (!$contact) {
    setFlash('error', 'Data kontak tidak ditemukan.');
    redirect('/index.php');
}

$input = $contact;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['name'] = sanitizeInput($_POST['name'] ?? '', 100);
    $input['age'] = sanitizeInput($_POST['age'] ?? '', 3);
    $input['email'] = sanitizeInput($_POST['email'] ?? '', 100);

    if ($input['name'] === '') {
        $errors[] = 'Nama wajib diisi.';
    }

    if (!ctype_digit((string)$input['age']) || (int)$input['age'] < 1 || (int)$input['age'] > 120) {
        $errors[] = 'Umur harus berupa angka 1 sampai 120.';
    }

    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email belum valid.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE contacts SET name = ?, age = ?, email = ? WHERE id = ?');
        $stmt->execute([$input['name'], (int)$input['age'], $input['email'], $id]);
        setFlash('success', 'Data kontak berhasil diperbarui.');
        redirect('/index.php');
    }
}

echo pageHeader('Edit Data');
?>
<main class="app-shell">
  <section class="content-card form-card">
    <div class="section-toolbar">
      <div>
        <p class="eyebrow">Edit Data</p>
        <h1><?= e($contact['name']); ?></h1>
        <p>Perbarui data kontak yang dipilih.</p>
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
        <input type="number" name="age" min="1" max="120" value="<?= e((string)$input['age']); ?>" required>
      </label>
      <label>
        <span>Email</span>
        <input type="email" name="email" value="<?= e($input['email']); ?>" required>
      </label>
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        <a class="btn btn-secondary" href="/index.php">Batal</a>
      </div>
    </form>
  </section>
</main>
<?= pageFooter(); ?>
