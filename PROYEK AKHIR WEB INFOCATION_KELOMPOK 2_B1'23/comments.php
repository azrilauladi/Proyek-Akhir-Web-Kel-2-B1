<?php
session_start();
include('config.php');

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Ambil ID postingan dari parameter URL
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

// Ambil data postingan dari database
$post_query = "SELECT p.*, u.username, u.profile_photo, 
               (SELECT photo_path FROM photos WHERE post_id = p.id LIMIT 1) as photo
               FROM posts p 
               JOIN users u ON p.user_id = u.id 
               WHERE p.id = ?";
$stmt = $con->prepare($post_query);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post_result = $stmt->get_result();
$post = $post_result->fetch_assoc();

// Ambil komentar dari database
$comment_query = "SELECT c.*, u.username, u.profile_photo 
                  FROM comments c 
                  JOIN users u ON c.user_id = u.id 
                  WHERE c.post_id = ? 
                  ORDER BY c.created_at ASC";
$stmt = $con->prepare($comment_query);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$comment_result = $stmt->get_result();

// Proses form untuk menambahkan komentar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_comment'])) {
    $comment_text = mysqli_real_escape_string($con, $_POST['comment']);
    $user_id = $_SESSION['user_id'];

    $insert_comment_query = "INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $con->prepare($insert_comment_query);
    $stmt->bind_param('iis', $post_id, $user_id, $comment_text);

    if ($stmt->execute()) {
        header("Location: comments.php?post_id=$post_id");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Proses form untuk mengedit komentar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_comment'])) {
    $comment_id = intval($_POST['comment_id']);
    $comment_text = mysqli_real_escape_string($con, $_POST['comment']);
    $user_id = $_SESSION['user_id'];

    $update_comment_query = "UPDATE comments SET comment = ? WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($update_comment_query);
    $stmt->bind_param('sii', $comment_text, $comment_id, $user_id);

    if ($stmt->execute()) {
        header("Location: comments.php?post_id=$post_id");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Proses untuk menghapus komentar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_comment'])) {
    $comment_id = intval($_POST['comment_id']);
    $user_id = $_SESSION['user_id'];

    $delete_comment_query = "DELETE FROM comments WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($delete_comment_query);
    $stmt->bind_param('ii', $comment_id, $user_id);

    if ($stmt->execute()) {
        header("Location: comments.php?post_id=$post_id");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Comments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Style/Comments.css">
</head>
<body>
    <div class="post-container">
        <h2>Oleh: <?php echo htmlspecialchars($post['username']); ?> | 
        <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?></h2>
        <?php if (!empty($post['profile_photo'])): ?>
            <img src="<?php echo htmlspecialchars($post['profile_photo']); ?>" 
                 alt="Profile Photo" 
                 class="profile-photo">
            <?php endif; ?>
        <div class="Title">
            <p>
                <?php echo htmlspecialchars($post['title']); ?>
            </p>
        </div>
        <p><?php echo nl2br(htmlspecialchars($post['body'])); ?></p>
        
        <?php if (!empty($post['photo'])): ?>
            <img src="<?php echo htmlspecialchars($post['photo']); ?>" 
                 alt="Post image" 
                 class="post-image">
        <?php endif; ?>
    </div>

    <div class="comments-section">
        <h3>Comments</h3>
        <?php if ($comment_result->num_rows > 0): ?>
            <?php while ($comment = $comment_result->fetch_assoc()): ?>
                <div class="comment" onclick="showActions(<?php echo $comment['id']; ?>)">
                    <p>
                        <?php if (!empty($comment['profile_photo'])): ?>
                            <img src="<?php echo htmlspecialchars($comment['profile_photo']); ?>" 
                                 alt="Profile Photo" 
                                 class="profile-photo">
                        <?php endif; ?>
                        <strong><?php echo htmlspecialchars($comment['username']); ?></strong> | 
                        <?php echo date('d M Y H:i', strtotime($comment['created_at'])); ?>
                    </p>
                    <p><?php echo str_replace(array("\r\n", "\r", "\n"), '<br>', htmlspecialchars($comment['comment'])); ?></p>
                    
                    <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
                        <div class="comment-actions" id="actions-<?php echo $comment['id']; ?>">
                            <form method="POST" action="" style="display: inline;" onclick="event.stopPropagation();">
                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                <textarea name="comment" required><?php echo htmlspecialchars($comment['comment']); ?></textarea>
                                <button type="submit" name="edit_comment" class="edit-button">Edit</button>
                            </form>
                            <form method="POST" action="" style="display: inline;" onclick="event.stopPropagation();">
                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                <button type="submit" name="delete_comment" class="delete-button">Delete</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="No-comment">
                <p>No comments yet</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Comment Form -->
    <form method="POST" action="" class="comment-form" onclick="event.stopPropagation();">
        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
        <div class="Comment-comment">
            <textarea name="comment" placeholder="Add a comment" required></textarea>
            <button class="button" type="submit" name="add_comment">
                <i class="fa fa-paper-plane"></i>
            </button>

        </div>
        <a href="home.php">
            <button type="button" class="btn">Back</button>
        </a>
    </form>

    <script>
    function showActions(commentId) {
        var actions = document.getElementById('actions-' + commentId);
        if (actions.style.display === 'none' || actions.style.display === '') {
            actions.style.display = 'block';
            var textarea = actions.querySelector('textarea');
            if (textarea) {
                textarea.focus();
            }
        } else {
            actions.style.display = 'none';
        }
    }
    </script>
</body>
</html>