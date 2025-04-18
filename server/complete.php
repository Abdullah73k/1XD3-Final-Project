<?php
session_start();

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: https://cs1xd3.cas.mcmaster.ca');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once(__DIR__ . '/connect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$task_id = $data['task_id'] ?? null;

if (!$task_id) {
    echo json_encode(['success' => false, 'message' => 'Task ID missing']);
    exit;
}

try {
    $stmt = $dbh->prepare("UPDATE tasks SET status = 'completed' WHERE id = :task_id AND user_id = :user_id");
    $stmt->execute([
        ':task_id' => $task_id,
        ':user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Task marked as completed']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
