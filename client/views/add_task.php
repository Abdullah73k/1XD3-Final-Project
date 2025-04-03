<?php
include '../connect.php';
session_start();

// always answer with JSON
header('Content-Type: application/json');

// Check the login 
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You are not logged in']);
    exit;
}

// input with JSON
$data = json_decode(file_get_contents("php://input"), true);

$title       = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$due_date    = $data['due_date'] ?? null;

// Check 
if ($title === '') {
    echo json_encode(['success' => false, 'message' => 'Title is required']);
    exit;
}

try {
    $stmt = $dbh->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $title,
        $description,
        $due_date ? date('Y-m-d H:i:s', strtotime($due_date)) : null
    ]);

    echo json_encode(['success' => true, 'message' => 'Task added successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
