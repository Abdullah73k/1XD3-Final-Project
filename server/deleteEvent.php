<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit();
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$userId = $_SESSION['user_id'];

try {
    // Verify the event belongs to the user before deleting
    $stmt = $db->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    
    echo json_encode(['success' => $stmt->rowCount() > 0]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>