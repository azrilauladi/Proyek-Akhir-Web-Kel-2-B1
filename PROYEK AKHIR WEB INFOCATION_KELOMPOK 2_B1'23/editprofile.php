<?php
session_start();
include('config.php');

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Proses form jika ada data yang dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $profile_photo = $_FILES['profile_photo'];

    // Proses upload foto profil
    if ($profile_photo['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_photo["name"]);
        move_uploaded_file($profile_photo["tmp_name"], $target_file);

        // Update data pengguna di database
        $update_query = "UPDATE users SET username = ?, email = ?, profile_photo = ? WHERE id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param('sssi', $username, $email, $target_file, $user_id);
    } else {
        // Update data pengguna tanpa mengubah foto profil
        $update_query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param('ssi', $username, $email, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profil berhasil diperbarui.";
        header("Location: userprofile.php");
        exit();
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat memperbarui profil.";
    }
}

// Ambil data pengguna dari database
$user_query = "SELECT username, email, profile_photo FROM users WHERE id = ?";
$stmt = $con->prepare($user_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel = "stylesheet" href = "Style/editprofile.css">	
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
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
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_photo">Profile Photo:</label>
                <input type="file" id="profile_photo" name="profile_photo">
            </div>
            <div class="form-group">
                <button type="submit">Update Profile</button>
                <button class="tombol-back"> 
                    <a href="userprofile.php" class="back-user">Back</a>
                </button>
            </div>
        </form>
    </div>
</body>
</html>