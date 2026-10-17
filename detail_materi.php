<?php
require 'koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT m.*, mk.kode AS kode_mk, mk.nama AS nama_mk 
                        FROM materi m 
                        JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id 
                        WHERE m.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Materi tidak ditemukan. <a href='materi.php'>Kembali</a>");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Materi - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <div class="card" style="max-width: 700px; margin: 40px auto;">
            <p style="font-size: 13px; color: #718096; margin-bottom: 5px;">Materi // Detail</p>
            <h2 style="margin-top: 0;"><?= htmlspecialchars($data['judul']); ?></h2>

            <table>
                <tr>
                    <th style="width: 180px;">Mata Kuliah</th>
                    <td><?= htmlspecialchars($data['kode_mk'] . ' - ' . $data['nama_mk']); ?></td>
                </tr>
                <tr>
                    <th>Isi Materi</th>
                    <td><?= nl2br(htmlspecialchars($data['isi'])); ?></td>
                </tr>
                <tr>
                    <th>Tautan Referensi</th>
                    <td><a href="<?= htmlspecialchars($data['tautan']); ?>" target="_blank"><?= htmlspecialchars($data['tautan']); ?></a></td>
                </tr>
            </table>

            <div style="margin-top: 25px;">
                <a href="edit_materi.php?id=<?= $id; ?>" class="btn btn-primary">Edit data</a>
                <a href="materi.php" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </main>
</body>
</html>