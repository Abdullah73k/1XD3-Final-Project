<?php
session_start();
include('connect.php');

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: https://cs1xd3.cas.mcmaster.ca');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'You are not logged in.'
    ]);
    exit;
}

$data = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) 
    ? $_POST 
    : json_decode(file_get_contents('php://input'), true);

$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$due_date = $data['due_date'] ?? null;

$day = $data['day'] ?? null;
$month = $data['month'] ?? null;
$year = $data['year'] ?? null;

if ($title === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Title is required.'
    ]);
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $day !== null && $month !== null && $year !== null) {
        header("Location: dayView.php?day=$day&month=$month&year=$year");
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Task added successfully.'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'DB Error: ' . $e->getMessage()
    ]);
}
