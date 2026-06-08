<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/auth.php';

requireLogin();

$pdo = DB::get();
$search = sanitizeInput($_GET['q'] ?? '', 80);

$sql = 'SELECT id, name, age, email, created_at FROM contacts';
$params = [];

if ($search !== '') {
    $sql .= ' WHERE name LIKE ? OR email LIKE ?';
    $params = ["%{$search}%", "%{$search}%"];
}

$sql .= ' ORDER BY id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$contacts = $stmt->fetchAll();

$total = (int)$pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
$avgAge = $pdo->query('SELECT ROUND(AVG(age)) FROM contacts')->fetchColumn();
$latest = $pdo->query('SELECT created_at FROM contacts ORDER BY id DESC LIMIT 1')->fetchColumn();

echo pageHeader('Data Kontak');
?>
<main class="app-shell">
  <section class="hero-panel">
    <div>
      <p class="eyebrow">Dynamic Web App</p>
      <h1>CRUD Data Kontak Lingga 2388010012</h1>
      <p>Aplikasi PHP dan MariaDB untuk UAS Administrasi Server milik Ratu Billah Lingga Suci |NIM 2388010012.</p>
    </div>
    <div class="hero-actions">
      <a class="btn btn-secondary" href="/logout.php">Logout</a>
      <a class="btn btn-primary" href="/create.php">Tambah Data</a>
    </div>
  </section>

  <?= renderFlash(); ?>

  <section class="stats-grid">
    <article class="stat-card">
      <span class="stat-label">Total Data</span>
      <strong class="stat-value"><?= $total; ?></strong>
    </article>
    <article class="stat-card">
      <span class="stat-label">Rata-rata Umur</span>
      <strong class="stat-value"><?= $avgAge ? (int)$avgAge : 0; ?></strong>
    </article>
    <article class="stat-card">
      <span class="stat-label">Data Terbaru</span>
      <strong class="stat-value stat-date"><?= $latest ? formatDate($latest) : '-'; ?></strong>
    </article>
  </section>

  <section class="content-card">
    <div class="section-toolbar">
      <div>
        <h2>Daftar Kontak</h2>
        <p>Kelola data nma, umur, dan email.</p>
      </div>
      <form class="search-form" method="get">
        <input type="search" name="q" value="<?= e($search); ?>" placeholder="Cari nama atau email">
        <button class="btn btn-secondary" type="submit">Cari</button>
      </form>
    </div>

    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Umur</th>
            <th>Email</th>
            <th>Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$contacts): ?>
            <tr>
              <td colspan="5" class="empty-state">Belum ada data kontak.</td>
            </tr>
          <?php endif; ?>

          <?php foreach ($contacts as $contact): ?>
            <tr>
              <td><?= e($contact['name']); ?></td>
              <td><?= (int)$contact['age']; ?> tahun</td>
              <td><a href="mailto:<?= e($contact['email']); ?>"><?= e($contact['email']); ?></a></td>
              <td><?= formatDate($contact['created_at']); ?></td>
              <td class="action-cell">
                <a class="btn btn-small btn-secondary" href="/edit.php?id=<?= (int)$contact['id']; ?>">Edit</a>
                <form action="/delete.php" method="post" onsubmit="return confirm('Hapus data ini?')">
                  <input type="hidden" name="id" value="<?= (int)$contact['id']; ?>">
                  <button class="btn btn-small btn-danger" type="submit">Hapus</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
<?= pageFooter(); ?>
