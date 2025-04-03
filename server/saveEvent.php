<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Get form data
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);
$date = filter_input(INPUT_POST, 'event_date', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$userId = $_SESSION['user_id'];

try {
    $stmt = $db->prepare("INSERT INTO events 
                         (user_id, title, description, event_date, event_time) 
                         VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $title, $description, $date, $time]);
    
    // Redirect back to day view
    header("Location: dayView.php?date=$date");
    exit();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>