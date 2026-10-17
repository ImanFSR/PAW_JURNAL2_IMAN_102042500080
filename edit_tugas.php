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

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $mk_id     = (int)($_POST['mata_kuliah_id'] ?? 0);
    $judul     = trim($_POST['judul'] ?? '');
    $instruksi = trim($_POST['instruksi'] ?? '');
    $tenggat   = trim($_POST['tenggat'] ?? '');
    $status    = trim($_POST['status'] ?? 'Draf');

    if ($mk_id <= 0) {
        $err = "Mata kuliah wajib dipilih.";
    } elseif (strlen($judul) < 3 || strlen($judul) > 150) {
        $err = "Judul tugas harus 3-150 karakter.";
    } elseif (strlen($instruksi) < 3 || strlen($instruksi) > 2000) {
        $err = "Instruksi tugas harus 3-2000 karakter.";
    } elseif (empty($tenggat) || !strtotime($tenggat)) {
        $err = "Pilih tanggal tenggat yang valid.";
    } elseif (!in_array($status, ['Draf', 'Aktif', 'Selesai'], true)) {
        $err = "Status harus Draf, Aktif, atau Selesai.";
    } else {
        if ($mk_id === (int)$data['mata_kuliah_id'] && $judul === $data['judul'] && $instruksi === $data['instruksi'] && $tenggat === $data['tenggat'] && $status === $data['status']) {
            set_flash('error', 'Tidak ada data yang diubah.');
            header("Location: tugas.php");
            exit;
        }

        $stmt_up = $conn->prepare("UPDATE tugas SET mata_kuliah_id=?, judul=?, instruksi=?, tenggat=?, status=? WHERE id=?");
        $stmt_up->bind_param('issssi', $mk_id, $judul, $instruksi, $tenggat, $status, $id);
        $stmt_up->execute();
        set_flash('success', 'Tugas berhasil diperbarui.');
        header("Location: tugas.php");
        exit;
    }
}

$res_mk = $conn->query("SELECT id, kode, nama FROM mata_kuliah ORDER BY kode ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Tugas - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <div class="card" style="max-width: 650px; margin: 40px auto;">
            <h2>Edit Tugas</h2>
            <?php if ($err): ?><div class="pesan-error"><?= htmlspecialchars($err); ?></div><?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label>Mata Kuliah</label>
                    <select name="mata_kuliah_id" required>
                        <?php while ($mk = $res_mk->fetch_assoc()): ?>
                            <option value="<?= $mk['id']; ?>" <?= ($data['mata_kuliah_id'] == $mk['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($mk['kode'] . ' - ' . $mk['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Judul Tugas</label>
                    <input type="text" name="judul" value="<?= htmlspecialchars($data['judul']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Instruksi Tugas</label>
                    <textarea name="instruksi" required><?= htmlspecialchars($data['instruksi']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Tenggat Waktu</label>
                    <input type="date" name="tenggat" value="<?= htmlspecialchars($data['tenggat']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="Draf" <?= ($data['status'] === 'Draf') ? 'selected' : ''; ?>>Draf</option>
                        <option value="Aktif" <?= ($data['status'] === 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="Selesai" <?= ($data['status'] === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan data</button>
                <a href="tugas.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </main>
</body>
</html>