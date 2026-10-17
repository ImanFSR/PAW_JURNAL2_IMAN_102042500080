<?php
require 'koneksi.php';

$kode = $nama = $dosen = $semester = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $kode     = strtoupper(trim($_POST['kode'] ?? ''));
    $nama     = trim($_POST['nama'] ?? '');
    $dosen    = trim($_POST['dosen'] ?? '');
    $semester = (int)($_POST['semester'] ?? 0);

    // Validasi aturan tabel mata kuliah
    if (!preg_match('/^[A-Z0-9\-]{3,20}$/', $kode)) {
        $err = "Kode harus 3-20 karakter berupa huruf kapital, angka, atau tanda strip (-).";
    } elseif (strlen($nama) < 3 || strlen($nama) > 100) {
        $err = "Nama mata kuliah harus 3-100 karakter.";
    } elseif (strlen($dosen) < 3 || strlen($dosen) > 100) {
        $err = "Nama dosen pengampu harus 3-100 karakter.";
    } elseif ($semester < 1 || $semester > 14) {
        $err = "Semester harus berupa bilangan bulat antara 1 sampai 14.";
    } else {
        // Cek duplikasi kode mata kuliah (UNIQUE)
        $stmt_cek = $conn->prepare("SELECT id FROM mata_kuliah WHERE kode = ?");
        $stmt_cek->bind_param('s', $kode);
        $stmt_cek->execute();
        if ($stmt_cek->get_result()->num_rows > 0) {
            $err = "Kode mata kuliah sudah digunakan.";
        } else {
            $stmt = $conn->prepare("INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('sssi', $kode, $nama, $dosen, $semester);
            $stmt->execute();
            set_flash('success', 'Mata kuliah berhasil ditambahkan.');
            header("Location: matakuliah.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mata Kuliah - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <div class="card" style="max-width: 650px; margin: 40px auto;">
            <h2>Tambah Mata Kuliah</h2>
            <?php if ($err): ?><div class="pesan-error"><?= htmlspecialchars($err); ?></div><?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label>Kode Mata Kuliah (Unik)</label>
                    <input type="text" name="kode" value="<?= htmlspecialchars($kode); ?>" placeholder="Contoh: PAW-101" required>
                </div>
                <div class="form-group">
                    <label>Nama Mata Kuliah</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($nama); ?>" placeholder="Contoh: Pengembangan Aplikasi Website" required>
                </div>
                <div class="form-group">
                    <label>Dosen Pengampu</label>
                    <input type="text" name="dosen" value="<?= htmlspecialchars($dosen); ?>" placeholder="Nama lengkap dosen" required>
                </div>
                <div class="form-group">
                    <label>Semester (1-14)</label>
                    <input type="number" name="semester" min="1" max="14" value="<?= htmlspecialchars($semester); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan data</button>
                <a href="matakuliah.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </main>
</body>
</html>