<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // default XAMPP
define('DB_PASS', '');           // default XAMPP (kosong)
define('DB_NAME', 'gudang');

function get_db() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die('<p style="color:red;font-family:sans-serif;padding:20px">
            Koneksi database gagal: ' . $conn->connect_error . '<br>
            Pastikan XAMPP MySQL sudah berjalan dan database <b>gudang</b> sudah dibuat.
        </p>');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function redirect($url) {
    header("Location: $url");
    exit;
}
