<?php
session_start();
include('config.php');

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


// Generate token untuk mencegah double submit
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi token
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
        die("Invalid form submission");
    }

    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    
    // Validasi input
    if (empty($title) || empty($body)) {
        die("Title and body are required");
    }

    // Cek duplikasi post dalam 1 menit terakhir
    $check_query = "SELECT id FROM posts 
                   WHERE user_id = ? AND title = ? 
                   AND created_at >= NOW() - INTERVAL 1 MINUTE";
    $stmt = $con->prepare($check_query);
    $stmt->bind_param("is", $user_id, $title);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        die("Please wait a moment before posting again");
    }
    
    // Mulai transaction
    $con->begin_transaction();
    
    try {
        // Insert post
        $post_query = "INSERT INTO posts (user_id, title, body) VALUES (?, ?, ?)";
        $stmt = $con->prepare($post_query);
        $stmt->bind_param("iss", $user_id, $title, $body);
        $stmt->execute();
        $post_id = $con->insert_id;

        // Handle file upload jika ada
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $upload_path = 'uploads/' . uniqid() . '.' . $ext;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    $photo_query = "INSERT INTO photos (post_id, photo_path) VALUES (?, ?)";
                    $stmt = $con->prepare($photo_query);
                    $stmt->bind_param("is", $post_id, $upload_path);
                    $stmt->execute();
                }
            }
        }

        $con->commit();
        // Hapus token setelah berhasil
        unset($_SESSION['form_token']);
        
        header("Location: home.php");
        exit();
        
    } catch (Exception $e) {
        $con->rollback();
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
    <link rel="stylesheet" href="Style/Create_post.css">
</head>
<body>
    <div class="Top">
        <div class="Logo">
            <img src="Asset/Logo kecil.png" alt="">
        </div>
        <a href="logout.php">
            <button type="button" class="btn">Logout</button>
        </a>
    </div>
    <div class="Line"></div>

    <div class="wrapper">
        <div class="New-post">
            <h2>Create New Post</h2>
            <form class="Form" method="POST" enctype="multipart/form-data" onsubmit="this.submit.disabled=true;">
                <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">
                
                <div class="input-box">
                    <label>Title:</label><br>
                    <input type="text" name="title" required>
                </div>
                
                <div class="input-box">
                    <label>Content:</label><br>
                    <textarea name="body" required></textarea>
                </div>
                
                <div>
                    <label>Photo (optional):</label><br>
                    <input class="Photo" type="file" name="photo" accept="image/*">
                </div>
                
                <button class="btn" type="submit" name="submit">Post</button>

                <a href="home.php">
                    <button class="btn2" type="button">Batalkan</button>
                </a>
            </form>
        </div>
    </div>
</body>
</html>