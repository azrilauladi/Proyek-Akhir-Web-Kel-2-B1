<?php
session_start();
include('config.php');

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Ambil kata kunci pencarian dari parameter URL atau form
$search_query = isset($_GET['q']) ? mysqli_real_escape_string($con, $_GET['q']) : '';

// Query untuk mencari postingan berdasarkan judul dan konten
$post_query = "SELECT p.*, u.username, u.profile_photo, 
               (SELECT photo_path FROM photos WHERE post_id = p.id LIMIT 1) as photo,
               (SELECT COUNT(*) FROM votes WHERE post_id = p.id AND vote_type = 'upvote') as upvote_count,
               (SELECT COUNT(*) FROM votes WHERE post_id = p.id AND vote_type = 'downvote') as downvote_count,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
               FROM posts p 
               JOIN users u ON p.user_id = u.id 
               WHERE p.title LIKE ? OR p.body LIKE ?
               ORDER BY p.created_at DESC";
$stmt = $con->prepare($post_query);
$search_term = '%' . $search_query . '%';
$stmt->bind_param('ss', $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/Search.css">
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
    <div class="Search">
        <form class="Input-search" method="GET" action="search.php">
            <input type="text" name="q" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>" required>
            <button type="submit" class="button"><i class="fa fa-search"></i></button>
        </form>
    </div>
    
    <div class="Content" id="Content">
        <?php if ($result->num_rows > 0): ?>
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

                    <!-- Comment Form -->
                    <form method="POST" action="comments.php?post_id=<?php echo $post['id']; ?>" class="comment-form">
                        <textarea class="comment-box" name="comment" placeholder="Add a comment" required></textarea>
                        <button class="button" type="submit" name="add_comment"><i class="fa fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts found</p>
        <?php endif; ?>
    </div>

    <script>
    let isVoting = false;
    
    async function vote(postId, voteType) {
        if (isVoting) return;
        
        isVoting = true;
        try {
            const response = await fetch('vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    post_id: postId,
                    vote_type: voteType
                })
            });
            
            const result = await response.json();
            if (result.success) {
                // Refresh halaman untuk memperbarui jumlah vote
                location.reload();
            } else {
                alert(result.message || 'Error recording vote');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error recording vote');
        } finally {
            isVoting = false;
        }
    }
    </script>
</body>
</html>