<?php
require 'koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM materi WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Materi tidak ditemukan. <a href='materi.php'>Kembali</a>");
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $mk_id  = (int)($_POST['mata_kuliah_id'] ?? 0);
    $judul  = trim($_POST['judul'] ?? '');
    $isi    = trim($_POST['isi'] ?? '');
    $tautan = trim($_POST['tautan'] ?? '');

    if ($mk_id <= 0) {
        $err = "Mata kuliah wajib dipilih.";
    } elseif (strlen($judul) < 3 || strlen($judul) > 150) {
        $err = "Judul materi harus 3-150 karakter.";
    } elseif (strlen($isi) < 3 || strlen($isi) > 2000) {
        $err = "Isi materi harus 3-2000 karakter.";
    } elseif (!filter_var($tautan, FILTER_VALIDATE_URL) || (!str_starts_with($tautan, 'http://') && !str_starts_with($tautan, 'https://'))) {
        $err = "Gunakan tautan http/https yang valid.";
    } else {
        if ($mk_id === (int)$data['mata_kuliah_id'] && $judul === $data['judul'] && $isi === $data['isi'] && $tautan === $data['tautan']) {
            set_flash('error', 'Tidak ada data yang diubah.');
            header("Location: materi.php");
            exit;
        }

        $stmt_up = $conn->prepare("UPDATE materi SET mata_kuliah_id=?, judul=?, isi=?, tautan=? WHERE id=?");
        $stmt_up->bind_param('isssi', $mk_id, $judul, $isi, $tautan, $id);
        $stmt_up->execute();
        set_flash('success', 'Materi berhasil diperbarui.');
        header("Location: materi.php");
        exit;
    }
}

$res_mk = $conn->query("SELECT id, kode, nama FROM mata_kuliah ORDER BY kode ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Materi - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <div class="card" style="max-width: 650px; margin: 40px auto;">
            <h2>Edit Materi</h2>
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
                    <label>Judul Materi</label>
                    <input type="text" name="judul" value="<?= htmlspecialchars($data['judul']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Isi Materi</label>
                    <textarea name="isi" required><?= htmlspecialchars($data['isi']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Tautan Referensi</label>
                    <input type="text" name="tautan" value="<?= htmlspecialchars($data['tautan']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan data</button>
                <a href="materi.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </main>
</body>
</html>