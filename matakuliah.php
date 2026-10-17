<?php
require 'koneksi.php';

$cari = trim($_GET['cari'] ?? '');
$param_cari = "%" . $cari . "%";

$stmt = $conn->prepare("SELECT * FROM mata_kuliah WHERE kode LIKE ? OR nama LIKE ? ORDER BY kode ASC");
$stmt->bind_param('ss', $param_cari, $param_cari);
$stmt->execute();
$hasil = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Mata Kuliah - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div style="font-weight: 800; font-size: 18px;">LMS Lab EAD</div>
        <nav>
            <a href="index.php">Beranda</a>
            <a href="matakuliah.php" class="active">Mata Kuliah</a>
            <a href="materi.php">Materi</a>
            <a href="tugas.php">Tugas</a>
        </nav>
    </header>

    <main class="container">
        <?php render_flash(); ?>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">Daftar Mata Kuliah</h2>
                <a href="tambah_matakuliah.php" class="btn btn-primary">+ Tambah Mata Kuliah</a>
            </div>

            <form method="get" class="filter-bar">
                <input type="text" name="cari" placeholder="Cari kode atau nama matkul..." value="<?= htmlspecialchars($cari); ?>">
                <button type="submit" class="btn btn-primary">Cari</button>
                <a href="matakuliah.php" class="btn btn-secondary">Reset</a>
            </form>

            <p style="font-size: 13px; color: #718096;">Menampilkan <?= $hasil->num_rows; ?> data.</p>

            <?php if ($hasil->num_rows === 0): ?>
                <p>Data tidak ditemukan. Coba kata kunci lain.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Mata Kuliah</th>
                                <th>Dosen</th>
                                <th>Semester</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $hasil->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['kode']); ?></td>
                                    <td><strong><?= htmlspecialchars($row['nama']); ?></strong></td>
                                    <td><?= htmlspecialchars($row['dosen']); ?></td>
                                    <td><?= (int)$row['semester']; ?></td>
                                    <td>
                                        <a href="edit_matakuliah.php?id=<?= $row['id']; ?>" class="btn btn-secondary">Edit</a>
                                        <a href="hapus_matakuliah.php?id=<?= $row['id']; ?>" class="btn btn-danger">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>