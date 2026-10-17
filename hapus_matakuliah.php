<?php
require 'koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM mata_kuliah WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Mata kuliah tidak ditemukan. <a href='matakuliah.php'>Kembali</a>");
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    // Verifikasi relasi ON DELETE RESTRICT di sisi server
    $stmt_relasi = $conn->prepare("SELECT 
        (SELECT COUNT(*) FROM materi WHERE mata_kuliah_id = ?) + 
        (SELECT COUNT(*) FROM tugas WHERE mata_kuliah_id = ?) AS total_relasi");
    $stmt_relasi->bind_param('ii', $id, $id);
    $stmt_relasi->execute();
    $total_relasi = $stmt_relasi->get_result()->fetch_assoc()['total_relasi'];

    if ($total_relasi > 0) {
        $err = "Data belum tersimpan. Mata kuliah masih memiliki materi atau tugas. Hapus data terkait terlebih dahulu.";
    } else {
        $stmt_del = $conn->prepare("DELETE FROM mata_kuliah WHERE id = ?");
        $stmt_del->bind_param('i', $id);
        $stmt_del->execute();
        set_flash('success', 'Mata kuliah berhasil dihapus.');
        header("Location: matakuliah.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Mata Kuliah - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <div class="card" style="max-width: 650px; margin: 40px auto;">
            <h2>Hapus <?= htmlspecialchars($data['nama']); ?>?</h2>
            <?php if ($err): ?><div class="pesan-error"><?= htmlspecialchars($err); ?></div><?php endif; ?>
            <p>Data baru dihapus setelah tombol konfirmasi dikirim. Data yang masih terhubung ke materi/tugas tidak dapat dihapus.</p>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <button type="submit" class="btn btn-danger">Ya, hapus data</button>
                <a href="matakuliah.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </main>
</body>
</html>