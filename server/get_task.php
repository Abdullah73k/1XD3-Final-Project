<?php
header('Content-Type: application/json');

session_start();
include '../connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $dbh->prepare("SELECT id, title, description, due_date, status FROM tasks WHERE user_id = ? ORDER BY due_date ASC");
    $stmt->execute([$user_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($tasks);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
