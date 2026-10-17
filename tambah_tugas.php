<?php
require 'koneksi.php';

$mk_id = $judul = $instruksi = $tenggat = '';
$status = 'Draf';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $mk_id     = (int)($_POST['mata_kuliah_id'] ?? 0);
    $judul     = trim($_POST['judul'] ?? '');
    $instruksi = trim($_POST['instruksi'] ?? '');
    $tenggat   = trim($_POST['tenggat'] ?? '');
    $status    = trim($_POST['status'] ?? 'Draf');

    // Validasi aturan kolom tugas
    if ($mk_id <= 0) {
        $err = "Mata kuliah wajib dipilih.";
    } elseif (strlen($judul) < 3 || strlen($judul) > 150) {
        $err = "Judul tugas harus 3-150 karakter.";
    } elseif (strlen($instruksi) < 3 || strlen($instruksi) > 2000) {
        $err = "Instruksi tugas harus 3-2000 karakter.";
    } elseif (empty($tenggat) || !strtotime($tenggat)) {
        $err = "Pilih tanggal tenggat yang valid.";
    } elseif (!in_array($status, ['Draf', 'Aktif', 'Selesai'], true)) {
        $err = "Status harus berupa Draf, Aktif, atau Selesai.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issss', $mk_id, $judul, $instruksi, $tenggat, $status);
        $stmt->execute();
        set_flash('success', 'Tugas berhasil ditambahkan.');
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
    <title>Tambah Tugas - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <div class="card" style="max-width: 650px; margin: 40px auto;">
            <h2>Tambah Tugas</h2>
            <?php if ($err): ?><div class="pesan-error"><?= htmlspecialchars($err); ?></div><?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label>Mata Kuliah</label>
                    <select name="mata_kuliah_id" required>
                        <option value="">Pilih Mata Kuliah</option>
                        <?php while ($mk = $res_mk->fetch_assoc()): ?>
                            <option value="<?= $mk['id']; ?>" <?= ($mk_id === (int)$mk['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($mk['kode'] . ' - ' . $mk['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Judul Tugas</label>
                    <input type="text" name="judul" value="<?= htmlspecialchars($judul); ?>" required>
                </div>
                <div class="form-group">
                    <label>Instruksi Tugas</label>
                    <textarea name="instruksi" required><?= htmlspecialchars($instruksi); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Tenggat Waktu</label>
                    <input type="date" name="tenggat" value="<?= htmlspecialchars($tenggat); ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="Draf" <?= ($status === 'Draf') ? 'selected' : ''; ?>>Draf</option>
                        <option value="Aktif" <?= ($status === 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="Selesai" <?= ($status === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan data</button>
                <a href="tugas.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </main>
</body>
</html>