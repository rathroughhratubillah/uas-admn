<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    setFlash('error', 'ID data tidak valid.');
    redirect('/index.php');
}

$stmt = DB::get()->prepare('DELETE FROM contacts WHERE id = ?');
$stmt->execute([$id]);

setFlash('success', 'Data kontak berhasil dihapus.');
redirect('/index.php');
