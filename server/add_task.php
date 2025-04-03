<?php
include '../connect.php';
session_start();

// check the login status
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.html");
    exit;
}

// get the input 
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$due_date    = $_POST['due_date'] ?? null;  // datetime-local 은 'YYYY-MM-DDTHH:MM' 형식

// check
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
        $due_date ? date('Y-m-d H:i:s', strtotime($due_date)) : null
    ]);

    // add and redirection
    header("Location: ../views/dayView.php?day=" . date('j') . "&month=" . (date('n') - 1) . "&year=" . date('Y'));
    exit;

} catch (PDOException $e) {
    echo "Error adding task: " . $e->getMessage();
}
?>
