<?php
require 'koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT t.*, mk.kode AS kode_mk, mk.nama AS nama_mk 
                        FROM tugas t 
                        JOIN mata_kuliah mk ON t.mata_kuliah_id = mk.id 
                        WHERE t.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Tugas tidak ditemukan. <a href='tugas.php'>Kembali</a>");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Tugas - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <div class="card" style="max-width: 700px; margin: 40px auto;">
            <p style="font-size: 13px; color: #718096; margin-bottom: 5px;">Tugas // Detail</p>
            <h2 style="margin-top: 0;"><?= htmlspecialchars($data['judul']); ?></h2>

            <table>
                <tr>
                    <th style="width: 180px;">Mata Kuliah</th>
                    <td><?= htmlspecialchars($data['kode_mk'] . ' - ' . $data['nama_mk']); ?></td>
                </tr>
                <tr>
                    <th>Tenggat Waktu</th>
                    <td><?= date('d F Y', strtotime($data['tenggat'])); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><span class="badge badge-<?= strtolower($data['status']); ?>"><?= htmlspecialchars($data['status']); ?></span></td>
                </tr>
                <tr>
                    <th>Instruksi</th>
                    <td><?= nl2br(htmlspecialchars($data['instruksi'])); ?></td>
                </tr>
            </table>

            <div style="margin-top: 25px;">
                <a href="edit_tugas.php?id=<?= $id; ?>" class="btn btn-primary">Edit data</a>
                <a href="tugas.php" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </main>
</body>
</html>