<?php
session_start();
include('config.php');

// Cek login dan cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Proses untuk menghapus akun pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);

    // Hapus data pengguna dari database
    $delete_user_query = "DELETE FROM users WHERE id = ?";
    $stmt = $con->prepare($delete_user_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    header("Location: check_user.php");
    exit();
}

// Ambil data pengguna dari database
$user_query = "SELECT id, username, email, role FROM users";
$result = $con->query($user_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Users</title>
    <link rel="stylesheet" href="Style/Cek_user.css">
</head>
<body>
    <div class="navbar">
        <p>Dasboard</p>
        <hr>
        <a href="cek_post.php">Periksa Postingan</a>
        <a href="check_user.php">Check Users</a>
        <a href="logout.php">Logout</a>
    </div>
    <h1>Check Users</h1>
    <div class="Content">
        <div class="Post">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="delete-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>