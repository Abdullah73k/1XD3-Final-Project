<?php
include '../connect.php';
session_start();

// check log in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.html");
    exit;
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

$day = $_POST['day'] ?? '';
$month = $_POST['month'] ?? '';
$year = $_POST['year'] ?? '';

$due_date = null;
if ($day && $month !== '' && $year) {
    $due_date = sprintf('%04d-%02d-%02d 00:00:00', $year, $month + 1, $day);
}

if ($title === '') {
    echo "Title is required.";
    exit;
}

try {
    $stmt = $dbh->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $title,
        $description,
        $due_date
    ]);

    //go back to the date
    header("Location: https://cs1xd3.cas.mcmaster.ca/~khamia4/1XD3-Final-Project/server/dayView.php?day=$day&month=$month&year=$year");
    exit;
} catch (PDOException $e) {
    echo "Error adding task: " . $e->getMessage();
}
