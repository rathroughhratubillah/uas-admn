<?php
// ============================================================
// src/helpers.php
// Fungsi utilitas umum
// ============================================================

/** Sanitasi output HTML untuk mencegah XSS */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** Redirect + exit */
function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

/** Set flash message ke session */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Ambil + hapus flash message */
function getFlash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** Render flash message sebagai HTML */
function renderFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';

    $icons = ['success' => '&#10003;', 'error' => '&#9888;', 'info' => '&#9432;', 'warning' => '&#9888;'];
    $icon  = $icons[$flash['type']] ?? '&#9432;';

    return sprintf(
        '<div class="alert alert-%s"><span class="alert-icon">%s</span> %s</div>',
        e($flash['type']),
        $icon,
        e($flash['message'])
    );
}

/** Format tanggal Indonesia */
function formatDate(?string $date): string {
    if (!$date) return '-';
    $d = new DateTime($date);
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                   'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return $d->format('d') . ' ' . $bulan[(int)$d->format('n')] . ' ' . $d->format('Y');
}

/** Label badge status */
function statusBadge(string $status): string {
    $map = [
        'todo'        => ['label' => 'To Do',       'class' => 'badge-todo'],
        'in_progress' => ['label' => 'In Progress',  'class' => 'badge-progress'],
        'done'        => ['label' => 'Done',          'class' => 'badge-done'],
    ];
    $s = $map[$status] ?? ['label' => $status, 'class' => 'badge-todo'];
    return sprintf('<span class="badge %s">%s</span>', $s['class'], $s['label']);
}

/** Label badge prioritas */
function priorityBadge(string $priority): string {
    $map = [
        'low'    => ['label' => 'Low',    'class' => 'badge-low'],
        'medium' => ['label' => 'Medium', 'class' => 'badge-medium'],
        'high'   => ['label' => 'High',   'class' => 'badge-high'],
    ];
    $p = $map[$priority] ?? ['label' => $priority, 'class' => 'badge-low'];
    return sprintf('<span class="badge %s">%s</span>', $p['class'], $p['label']);
}

/** Validasi & sanitasi string input */
function sanitizeInput(string $value, int $maxLen = 255): string {
    return mb_substr(trim($value), 0, $maxLen);
}

/** Apakah tanggal valid format YYYY-MM-DD */
function isValidDate(string $date): bool {
    if (empty($date)) return true; // opsional
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/** Header HTML template */
function pageHeader(string $title, string $activePage = ''): string {
    $appName = APP_NAME;
    $nim     = APP_NIM;
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> &mdash; <?= e($appName) ?></title>
  <link rel="stylesheet" href="/assets/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>&#9729;</text></svg>">
</head>
<body>
    <?php
    return ob_get_clean();
}

/** Footer HTML template */
function pageFooter(): string {
    $year    = date('Y');
    $appName = APP_NAME;
    $nim     = APP_NIM;
    return "
  <footer class=\"app-footer\">
    <p>&copy; {$year} &mdash; {$appName} &mdash; NIM: {$nim}</p>
  </footer>
</body>
</html>";
}
