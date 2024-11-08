<?php
session_start();
include('config.php');

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    
    // Validasi panjang password
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password harus minimal 6 karakter.";
        header("Location: register.php");
        exit();
    }

    // Hash password menggunakan password_hash yang lebih aman
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepared statement untuk mencegah SQL injection
    $check_query = $con->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $check_query->bind_param("ss", $username, $email);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username atau email sudah terpakai, silakan gunakan yang lain.";
        header("Location: register.php");
        exit();
    } else {
        // Prepared statement untuk insert
        $insert_query = $con->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $insert_query->bind_param("sss", $username, $email, $hashed_password);
        
        if ($insert_query->execute()) {
            $_SESSION['success'] = "Registrasi berhasil. Silakan login.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error'] = "Terjadi kesalahan saat registrasi.";
            header("Location: register.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/Register.css">
</head>
<body>
    <div class="body-register">
        <div class="wrapper">
            <h1>Register</h1>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="success-message">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            ?>
            <form method="post" action="">
                <div class="input-box">
                    <h4>Email</h4>
                    <input type="email" name="email" required>
                </div>
                <div class="input-box">
                    <h4>Username</h4>
                    <input type="text" name="username" required>
                </div>
                <div class="input-box">
                    <h4>Password</h4>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="register" class="btn">Register</button>
                <div class="Login-link">
                    <p>Have an account?
                        <a href="login.php">Sign In</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>