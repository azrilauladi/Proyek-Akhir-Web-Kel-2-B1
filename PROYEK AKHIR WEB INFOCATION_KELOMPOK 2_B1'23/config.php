<?php
// Konfigurasi koneksi database
$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "wisata";

// Membuat koneksi ke database
$con = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Cek koneksi
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
