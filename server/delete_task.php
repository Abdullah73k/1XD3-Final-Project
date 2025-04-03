<?php
include '../connect.php';
session_start();

// check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// get post data
$data = json_decode(file_get_contents('php://input'), true);
$task_id = $data['task_id'] ?? null;

// if there is no task id, generate error
if (!$task_id) {
    echo json_encode(['success' => false, 'message' => 'Task ID missing']);
    exit;
}

try {
    // only the user's own tasks can be deleted.
    $stmt = $dbh->prepare("DELETE FROM tasks WHERE id = :task_id AND user_id = :user_id");
    $stmt->execute([
        ':task_id' => $task_id,
        ':user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
