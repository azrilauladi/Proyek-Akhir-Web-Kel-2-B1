<?php
session_start();
include('config.php');

// Cek login dan cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Ambil jumlah pengguna dari database
$user_count_query = "SELECT COUNT(*) as user_count FROM users";
$result = $con->query($user_count_query);
$user_count = $result->fetch_assoc()['user_count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" href="Style/Admin_page.css">
</head>
<body>
    <div class="navbar">
        <p>Dasboard</p>
        <hr>
        <a href="cek_post.php">Periksa Postingan</a>
        <a href="check_user.php">Check Users</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <h1>Admin Page</h1>
        <p><strong>Total Users:</strong> <?php echo $user_count; ?></p>
    </div>
</body>
</html>