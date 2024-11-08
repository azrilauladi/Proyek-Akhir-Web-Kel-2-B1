<?php
session_start();
include('config.php');

// Cek login dan cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Proses untuk menghapus postingan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_post'])) {
    $post_id = intval($_POST['post_id']);

    // Hapus foto jika ada
    $photo_query = "SELECT photo_path FROM photos WHERE post_id = ?";
    $stmt = $con->prepare($photo_query);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $photo_result = $stmt->get_result();
    while ($photo = $photo_result->fetch_assoc()) {
        if (!empty($photo['photo_path'])) {
            unlink($photo['photo_path']);
        }
    }

    // Hapus data postingan dan foto dari database
    $delete_post_query = "DELETE FROM posts WHERE id = ?";
    $stmt = $con->prepare($delete_post_query);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();

    $delete_photo_query = "DELETE FROM photos WHERE post_id = ?";
    $stmt = $con->prepare($delete_photo_query);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();

    header("Location: cek_post.php");
    exit();
}

// Ambil data postingan dari database
$post_query = "SELECT 
    p.*, 
    u.username,
    u.profile_photo,
    (SELECT photo_path FROM photos WHERE post_id = p.id LIMIT 1) as photo,
    (SELECT COUNT(*) FROM votes WHERE post_id = p.id AND vote_type = 'upvote') as upvote_count,
    (SELECT COUNT(*) FROM votes WHERE post_id = p.id AND vote_type = 'downvote') as downvote_count,
    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
FROM posts p 
JOIN users u ON p.user_id = u.id 
ORDER BY p.created_at DESC";

$stmt = $con->prepare($post_query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Posts</title>
    <link rel="stylesheet" href="Style/Cek_post.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="navbar">
        <p>Dasboard</p>
        <hr>
        <a href="cek_post.php">Periksa Postingan</a>
        <a href="check_user.php">Check Users</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="Content">
        <h1>Check Posts</h1>
        <div id="posts-container">
            <?php while ($post = $result->fetch_assoc()): ?>
                <div class="Post">
                    <div class="Upper-text">
                        <p>
                            <?php if (!empty($post['profile_photo'])): ?>
                                <img src="<?php echo htmlspecialchars($post['profile_photo']); ?>" 
                                    alt="Profile Photo" 
                                    class="profile-photo">
                            <?php endif; ?>
                            Oleh: <?php echo htmlspecialchars($post['username']); ?> | 
                            <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                        </p>
                    </div>
                    <div class="Main-text">
                        <div class="title">
                            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                        </div>
                        <div class="body-text">
                            <p><?php echo nl2br(htmlspecialchars($post['body'])); ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($post['photo'])): ?>
                        <img src="<?php echo htmlspecialchars($post['photo']); ?>" 
                             alt="Post image" 
                             class="post-image">
                    <?php endif; ?>
                    
                    <div class="Buttons">
                    <div class="stat-item">
                        <button class="button" onclick="vote(<?php echo $post['id']; ?>, 'upvote')">
                            <i class="fa fa-thumbs-up"></i>
                        </button>
                        <span class="vote-count"><?php echo $post['upvote_count']; ?></span>
                    </div>
                    <div class="stat-item">
                        <button class="downvote-button" onclick="vote(<?php echo $post['id']; ?>, 'downvote')">
                            <i class="fa fa-thumbs-down"></i>
                        </button>
                        <span class="vote-count"><?php echo $post['downvote_count']; ?></span>
                    </div>
                    <div class="stat-item">
                        <a href="comments.php?post_id=<?php echo $post['id']; ?>" >
                            <i class="fa fa-comments"></i>
                        </a>
                        <span class="vote-count"><?php echo $post['comment_count']; ?></span>
                    </div>
                </div>
                    
                    <form class="Form" method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this post?');">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit" name="delete_post" class="delete-button">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>