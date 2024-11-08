<?php
session_start();
include('config.php');

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password'];

    // Cek username di database
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session untuk user yang berhasil login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Arahkan berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: admin_page.php");
            } else {
                header("Location: home.php");  // Diarahkan ke home.php untuk user biasa
            }
            exit();
        } else {
            $_SESSION['error'] = "Username atau password salah.";
        }
    } else {
        $_SESSION['error'] = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>InfoCation Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="Style/Login.css">
</head>
<body>
    <div class="wrapper">
        <div class="Left">
            <div class="Text-1">
                <p>InfoCation helps you Find the best travel spots and hidden gem effortlessly,</p>
            </div>

            <div class="Text-2">
                <p>making every trip unforgettable.</p>
            </div>
        </div>
        <div class="Right">
            <div class="Frame">
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>
                <form method="post">
                    <img src="Asset/Group 427321674.png" alt="">
                    <div class="input-box">
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-box">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" name="login" class="btn">Login</button>
                </form>
                <div class="Login-link">
                    Don't have an account? <a href="register.php">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</body>