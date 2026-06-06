<?php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

startSecureSession();

if (isLoggedIn()) {
    redirect('/index.php');
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '', 50);
    $password = $_POST['password'] ?? '';
    $result = attemptLogin($username, $password);

    if ($result['success']) {
        setFlash('success', 'Login berhasil. Selamat datang di dashboard admin.');
        redirect('/index.php');
    }

    $error = $result['message'];
}

$message = $_GET['msg'] ?? '';

echo pageHeader('Login Admin');
?>
<main class="login-shell">
  <section class="login-card">
    <p class="eyebrow">Admin Area</p>
    <h1>Login Admin</h1>
    <p class="login-copy">Masuk untuk mengelola data kontak pada web dinamis UAS.</p>

    <?php if ($message === 'unauthorized'): ?>
      <div class="alert alert-warning">Silakan login terlebih dahulu.</div>
    <?php elseif ($message === 'timeout'): ?>
      <div class="alert alert-warning">Sesi login habis. Silakan login kembali.</div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= e($error); ?></div>
    <?php endif; ?>

    <form class="data-form" method="post">
      <label>
        <span>Username</span>
        <input type="text" name="username" value="<?= e($username); ?>" autocomplete="username" required>
      </label>
      <label>
        <span>Password</span>
        <input type="password" name="password" autocomplete="current-password" required>
      </label>
      <button class="btn btn-primary" type="submit">Login</button>
    </form>

    <div class="login-hint">
      <strong>Default admin</strong>
      <span>Username: admin</span>
      <span>Password: admin123</span>
    </div>
  </section>
</main>
<?= pageFooter(); ?>
