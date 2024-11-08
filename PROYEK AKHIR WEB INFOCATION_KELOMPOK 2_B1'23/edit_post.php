<?php
session_start();
include('config.php');

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
$user_id = $_SESSION['user_id'];

// Ambil data postingan dari database
$query = "SELECT title, body, (SELECT photo_path FROM photos WHERE post_id = ? LIMIT 1) as photo FROM posts WHERE id = ? AND user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("iii", $post_id, $post_id, $user_id);
$stmt->execute();
$stmt->bind_result($title, $body, $photo);
$stmt->fetch();
$stmt->close();

// Proses form untuk mengupdate postingan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_post'])) {
    $new_title = mysqli_real_escape_string($con, $_POST['title']);
    $new_body = mysqli_real_escape_string($con, $_POST['body']);
    $new_photo = $_FILES['photo'];

    // Proses upload foto baru jika ada
    if ($new_photo['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($new_photo["name"]);
        move_uploaded_file($new_photo["tmp_name"], $target_file);

        // Hapus foto lama jika ada
        if (!empty($photo)) {
            unlink($photo);
        }

        // Update data postingan dan foto di database
        $update_query = "UPDATE posts SET title = ?, body = ? WHERE id = ? AND user_id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param('ssii', $new_title, $new_body, $post_id, $user_id);
        $stmt->execute();

        $update_photo_query = "UPDATE photos SET photo_path = ? WHERE post_id = ?";
        $stmt = $con->prepare($update_photo_query);
        $stmt->bind_param('si', $target_file, $post_id);
        $stmt->execute();
    } else {
        // Update data postingan tanpa mengubah foto
        $update_query = "UPDATE posts SET title = ?, body = ? WHERE id = ? AND user_id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param('ssii', $new_title, $new_body, $post_id, $user_id);
        $stmt->execute();
    }

    header("Location: userprofile.php");
    exit();
}

// Proses untuk menghapus postingan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_post'])) {
    // Hapus foto jika ada
    if (!empty($photo)) {
        unlink($photo);
    }

    // Hapus data postingan dan foto dari database
    $delete_query = "DELETE FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($delete_query);
    $stmt->bind_param('ii', $post_id, $user_id);
    $stmt->execute();

    $delete_photo_query = "DELETE FROM photos WHERE post_id = ?";
    $stmt = $con->prepare($delete_photo_query);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();

    header("Location: userprofile.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" href="Style/edit_post.css">
</head>
<body>
    <div class="Top">
        <<div class="Top">
        <div class="Logo">
            <img src="Asset/Logo kecil.png" alt="">
        </div>
        <a href="logout.php">
            <button type="button" class="btn">Logout</button>
        </a>
    </div>
    <div class="Line"></div>
    <div class="wrapper">
        <div class="Edit-post">
            <h2>Edit Post</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="input-box">
                    <label>Title:</label><br>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>
                
                <div class="input-box">
                    <label>Content:</label><br>
                    <textarea name="body" required><?php echo htmlspecialchars($body); ?></textarea>
                </div>
                
                <div class="input-box">
                    <label>Photo:</label><br>
                    <?php if (!empty($photo)): ?>
                        <img src="<?php echo htmlspecialchars($photo); ?>" alt="Post Photo" style="max-width: 200px;"><br>
                    <?php endif; ?>
                    <input type="file" name="photo" class="Photo">
                </div>
                
                <button type="submit" name="update_post" class="btn">Update Post</button>
                <button type="submit" name="delete_post" class="btn2" onclick="return confirm('Are you sure you want to delete this post?');">Delete Post</button>
            </form>
        </div>
    </div>
</body>
</html>