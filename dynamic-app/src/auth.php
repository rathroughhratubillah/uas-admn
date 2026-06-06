<?php
// ============================================================
// src/auth.php
// Fungsi autentikasi berbasis session PHP
// ============================================================

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

/**
 * Login: verifikasi username + password, set session jika valid.
 * Return: ['success' => bool, 'message' => string]
 */
function attemptLogin(string $username, string $password): array {
    if (empty(trim($username)) || empty($password)) {
        return ['success' => false, 'message' => 'Username dan password wajib diisi.'];
    }

    $pdo  = DB::get();
    $stmt = $pdo->prepare('SELECT id, username, password_hash, full_name, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Username atau password salah.'];
    }

    // Regenerate session ID untuk mencegah session fixation
    session_regenerate_id(true);

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role']      = $user['role'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_at']  = time();

    // Rehash jika diperlukan (best practice)
    if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT)) {
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([$newHash, $user['id']]);
    }

    return ['success' => true, 'message' => 'Login berhasil.'];
}

/** Cek apakah user sudah login */
function isLoggedIn(): bool {
    startSecureSession();
    return !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/** Guard: redirect ke login jika belum login */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /login.php?msg=unauthorized');
        exit;
    }
    // Timeout session
    if (isset($_SESSION['login_at']) && (time() - $_SESSION['login_at']) > SESSION_LIFETIME) {
        logout();
        header('Location: /login.php?msg=timeout');
        exit;
    }
}

/** Logout: destroy session */
function logout(): void {
    startSecureSession();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

/** Ambil data user yang sedang login */
function currentUser(): array {
    return [
        'id'        => $_SESSION['user_id']   ?? 0,
        'username'  => $_SESSION['username']  ?? '',
        'full_name' => $_SESSION['full_name'] ?? '',
        'role'      => $_SESSION['role']      ?? '',
    ];
}
