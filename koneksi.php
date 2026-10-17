<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'paw_jurnal2_lms';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi basis data gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

// Inisialisasi token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verify_csrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        die("Permintaan ditolak: Token CSRF tidak sah atau kedaluwarsa.");
    }
}

function set_flash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function render_flash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        $cls = $f['type'] === 'success' ? 'pesan-sukses' : 'pesan-error';
        echo '<div class="' . $cls . '">' . htmlspecialchars($f['msg']) . '</div>';
        unset($_SESSION['flash']);
    }
}
?>