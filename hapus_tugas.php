<?php
require 'koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM tugas WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Tugas tidak ditemukan. <a href='tugas.php'>Kembali</a>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt_del = $conn->prepare("DELETE FROM tugas WHERE id = ?");
    $stmt_del->bind_param('i', $id);
    $stmt_del->execute();
    set_flash('success', 'Tugas berhasil dihapus.');
    header("Location: tugas.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Tugas - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <div class="card" style="max-width: 600px; margin: 40px auto;">
            <h2>Hapus <?= htmlspecialchars($data['judul']); ?>?</h2>
            <p>Data baru dihapus setelah tombol konfirmasi dikirim. Tindakan ini tidak dapat dibatalkan.</p>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <button type="submit" class="btn btn-danger">Ya, hapus data</button>
                <a href="tugas.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </main>
</body>
</html>