<?php
require 'koneksi.php';

// Hitung jumlah masing-masing entitas dengan COUNT
$jml_mk = $conn->query("SELECT COUNT(*) AS total FROM mata_kuliah")->fetch_assoc()['total'];
$jml_materi = $conn->query("SELECT COUNT(*) AS total FROM materi")->fetch_assoc()['total'];
$jml_tugas = $conn->query("SELECT COUNT(*) AS total FROM tugas")->fetch_assoc()['total'];

// Ambil daftar mata kuliah untuk beranda
$res_mk = $conn->query("SELECT * FROM mata_kuliah ORDER BY kode ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda - Learning Management System EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div style="font-weight: 800; font-size: 18px;">LMS Lab EAD</div>
        <nav>
            <a href="index.php" class="active">Beranda</a>
            <a href="matakuliah.php">Mata Kuliah</a>
            <a href="materi.php">Materi</a>
            <a href="tugas.php">Tugas</a>
        </nav>
    </header>

    <main class="container">
        <?php render_flash(); ?>
        
        <h2>Dashboard Pembelajaran</h2>
        <p style="color: #718096; margin-top: -8px;">Pengelolaan mata kuliah, materi pembelajaran, dan instruksi tugas laboratorium.</p>

        <div class="stats-grid">
            <div class="stat-box blue">
                <h3>Mata Kuliah</h3>
                <div class="count"><?= $jml_mk; ?></div>
            </div>
            <div class="stat-box green">
                <h3>Materi Pembelajaran</h3>
                <div class="count"><?= $jml_materi; ?></div>
            </div>
            <div class="stat-box purple">
                <h3>Instruksi Tugas</h3>
                <div class="count"><?= $jml_tugas; ?></div>
            </div>
        </div>

        <div class="card">
            <h3>Mata Kuliah Tersedia</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>Dosen Pengampu</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($m = $res_mk->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($m['kode']); ?></strong></td>
                                <td><?= htmlspecialchars($m['nama']); ?></td>
                                <td><?= htmlspecialchars($m['dosen']); ?></td>
                                <td>Semester <?= (int)$m['semester']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>