<?php
session_start();
include('config.php');

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Ambil data pengguna dari database
$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, email, profile_photo FROM users WHERE id = ?";
$stmt = $con->prepare($user_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Ambil postingan pengguna dari database
$post_query = "SELECT p.id, p.title, p.body, p.created_at, 
               (SELECT photo_path FROM photos WHERE post_id = p.id LIMIT 1) as photo,
               (SELECT COUNT(*) FROM votes WHERE post_id = p.id AND vote_type = 'upvote') as upvote_count,
               (SELECT COUNT(*) FROM votes WHERE post_id = p.id AND vote_type = 'downvote') as downvote_count,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
               FROM posts p WHERE p.user_id = ? ORDER BY p.created_at DESC";
$stmt = $con->prepare($post_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$post_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/User-profile.css">
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="home.php"><img src="Asset/logo home.png" alt=""></a></li>
            <li><a href="search.php"><img src="Asset/logo search.png" alt=""></a></li>
            <li><a href="create_post.php"><img src="Asset/logo tambah.png" alt=""></a></li>
            <li><a href="userprofile.php"><img src="Asset/logo Profile.png" alt=""></a></li>
        </ul>
    </nav>
    <div class="Top">
        <div class="Logo">
            <img src="Asset/Logo kecil.png" alt="">
        </div>
        <a href="logout.php">
            <button type="button" class="btn">Logout</button>
        </a>
    </div>
    <div class="Line"></div>
    <div class="Account">
        <div class="Main-account">
            <div class="Photo">
                <?php if (!empty($user['profile_photo'])): ?>
                <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" 
                alt="Profile Photo" 
                class="profile-photo">
                <?php endif; ?>
                <div class="Email">
                    <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
            <div class="button">
                <a href="editprofile.php" class="edit-button">Edit</a>
            </div>
        </div>
    </div>
        
    <h2 class="Post-kamu">Your Posts</h2>
    <div class="Content">
        <?php while ($post = $post_result->fetch_assoc()): ?>
            <div class="Post">
                <div class="Upper-text">
                        <span><?php echo date('d M Y H:i', strtotime($post['created_at'])); ?></span>
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
                
                <a href="edit_post.php?post_id=<?php echo $post['id']; ?>" class="button">Edit</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>