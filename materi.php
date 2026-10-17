<?php
require 'koneksi.php';

$cari = trim($_GET['cari'] ?? '');
$filter_mk = (int)($_GET['mata_kuliah_id'] ?? 0);

$query = "SELECT m.*, mk.nama AS nama_mk, mk.kode AS kode_mk 
          FROM materi m 
          JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id 
          WHERE m.judul LIKE ?";
$params = ["%" . $cari . "%"];
$types = "s";

if ($filter_mk > 0) {
    $query .= " AND m.mata_kuliah_id = ?";
    $params[] = $filter_mk;
    $types .= "i";
}
$query .= " ORDER BY m.id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$hasil = $stmt->get_result();

$res_all_mk = $conn->query("SELECT id, kode, nama FROM mata_kuliah ORDER BY kode ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Materi - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div style="font-weight: 800; font-size: 18px;">LMS Lab EAD</div>
        <nav>
            <a href="index.php">Beranda</a>
            <a href="matakuliah.php">Mata Kuliah</a>
            <a href="materi.php" class="active">Materi</a>
            <a href="tugas.php">Tugas</a>
        </nav>
    </header>

    <main class="container">
        <?php render_flash(); ?>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">Materi Pembelajaran</h2>
                <a href="tambah_materi.php" class="btn btn-primary">+ Tambah materi</a>
            </div>

            <form method="get" class="filter-bar">
                <input type="text" name="cari" placeholder="Cari judul materi..." value="<?= htmlspecialchars($cari); ?>">
                <select name="mata_kuliah_id">
                    <option value="">Semua mata kuliah</option>
                    <?php while ($mk = $res_all_mk->fetch_assoc()): ?>
                        <option value="<?= $mk['id']; ?>" <?= ($filter_mk === (int)$mk['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($mk['kode'] . ' - ' . $mk['nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary">Cari</button>
                <a href="materi.php" class="btn btn-secondary">Reset</a>
            </form>

            <p style="font-size: 13px; color: #718096;">Menampilkan <?= $hasil->num_rows; ?> data.</p>

            <?php if ($hasil->num_rows === 0): ?>
                <p>Data tidak ditemukan.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Mata Kuliah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $hasil->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['judul']); ?></strong></td>
                                    <td><?= htmlspecialchars($row['kode_mk'] . ' - ' . $row['nama_mk']); ?></td>
                                    <td>
                                        <a href="detail_materi.php?id=<?= $row['id']; ?>" class="btn btn-secondary">Detail</a>
                                        <a href="edit_materi.php?id=<?= $row['id']; ?>" class="btn btn-secondary">Edit</a>
                                        <a href="hapus_materi.php?id=<?= $row['id']; ?>" class="btn btn-danger">Hapus</a>
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