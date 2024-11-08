<?php
session_start();
include('config.php');

// Pastikan request adalah POST dan user sudah login
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Ambil data dari request
$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'] ?? null;
$vote_type = $data['vote_type'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$post_id || !$vote_type || !in_array($vote_type, ['upvote', 'downvote'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

try {
    // Mulai transaction
    $con->begin_transaction();

    // Hapus vote yang sudah ada (jika ada)
    $delete_query = "DELETE FROM votes WHERE post_id = ? AND user_id = ?";
    $stmt = $con->prepare($delete_query);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();

    // Insert vote baru
    $insert_query = "INSERT INTO votes (post_id, user_id, vote_type) VALUES (?, ?, ?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param("iis", $post_id, $user_id, $vote_type);
    $stmt->execute();

    $con->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $con->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>