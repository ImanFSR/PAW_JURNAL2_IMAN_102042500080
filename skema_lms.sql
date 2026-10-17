CREATE DATABASE IF NOT EXISTS paw_jurnal2_lms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE paw_jurnal2_lms;

-- 1. Tabel Mata Kuliah
CREATE TABLE IF NOT EXISTS mata_kuliah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    dosen VARCHAR(100) NOT NULL,
    semester TINYINT UNSIGNED NOT NULL
) ENGINE=InnoDB;

-- 2. Tabel Materi (1 Mata Kuliah ke N Materi)
CREATE TABLE IF NOT EXISTS materi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    tautan VARCHAR(255) NOT NULL,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 3. Tabel Tugas (1 Mata Kuliah ke N Tugas)
CREATE TABLE IF NOT EXISTS tugas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    instruksi TEXT NOT NULL,
    tenggat DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Data Awal (Masing-masing 2 data sesuai modul)
INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES
('ALP-101', 'Algoritma dan Pemrograman', 'Dosen ALP', 1),
('PAW-101', 'Pengembangan Aplikasi Website', 'Dosen PAW', 3);

INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES
(1, 'Dasar Pseudocode dan Algoritma', 'Materi pengantar dasar logika sekuensial dan struktur percabangan.', 'https://telkomuniversity.ac.id/algoritma'),
(2, 'Operasi CRUD dengan PHP dan MySQL', 'Membahas integrasi antarmuka web, konektivitas database, dan prepared statement.', 'https://www.php.net/manual/en/book.mysqli.php');

INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES
(1, 'Tugas Analisis Kompleksitas', 'Buat laporan singkat mengenai analisis kompleksitas waktu Big-O.', '2026-11-15', 'Aktif'),
(2, 'Jurnal Modul 2 Praktikum PAW', 'Bangun web LMS sederhana dengan operasi CRUD lengkap dan foreign key restrict.', '2026-10-25', 'Aktif');