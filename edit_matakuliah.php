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
    $kode     = strtoupper(trim($_POST['kode'] ?? ''));
    $nama     = trim($_POST['nama'] ?? '');
    $dosen    = trim($_POST['dosen'] ?? '');
    $semester = (int)($_POST['semester'] ?? 0);

    if (!preg_match('/^[A-Z0-9\-]{3,20}$/', $kode)) {
        $err = "Kode harus 3-20 karakter huruf kapital, angka, atau tanda strip (-).";
    } elseif (strlen($nama) < 3 || strlen($nama) > 100) {
        $err = "Nama mata kuliah harus 3-100 karakter.";
    } elseif (strlen($dosen) < 3 || strlen($dosen) > 100) {
        $err = "Dosen harus 3-100 karakter.";
    } elseif ($semester < 1 || $semester > 14) {
        $err = "Semester harus antara 1 sampai 14.";
    } else {
        // Cek duplikasi kode pada ID lain
        $stmt_cek = $conn->prepare("SELECT id FROM mata_kuliah WHERE kode = ? AND id <> ?");
        $stmt_cek->bind_param('si', $kode, $id);
        $stmt_cek->execute();
        if ($stmt_cek->get_result()->num_rows > 0) {
            $err = "Kode mata kuliah sudah digunakan oleh mata kuliah lain.";
        } else {
            // Cek apakah ada perubahan data
            if ($kode === $data['kode'] && $nama === $data['nama'] && $dosen === $data['dosen'] && $semester === (int)$data['semester']) {
                set_flash('error', 'Tidak ada data yang diubah.');
                header("Location: matakuliah.php");
                exit;
            }

            $stmt_up = $conn->prepare("UPDATE mata_kuliah SET kode=?, nama=?, dosen=?, semester=? WHERE id=?");
            $stmt_up->bind_param('sssii', $kode, $nama, $dosen, $semester, $id);
            $stmt_up->execute();
            set_flash('success', 'Mata kuliah berhasil diperbarui.');
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
    <title>Edit Mata Kuliah - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <div class="card" style="max-width: 650px; margin: 40px auto;">
            <h2>Edit Mata Kuliah</h2>
            <?php if ($err): ?><div class="pesan-error"><?= htmlspecialchars($err); ?></div><?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label>Kode Mata Kuliah</label>
                    <input type="text" name="kode" value="<?= htmlspecialchars($data['kode']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama Mata Kuliah</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Dosen Pengampu</label>
                    <input type="text" name="dosen" value="<?= htmlspecialchars($data['dosen']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Semester</label>
                    <input type="number" name="semester" min="1" max="14" value="<?= (int)$data['semester']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan data</button>
                <a href="matakuliah.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </main>
</body>
</html>