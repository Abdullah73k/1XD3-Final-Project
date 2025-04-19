<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../client/index.html");
    exit;
}

$day = isset($_GET['day']) ? (int)$_GET['day'] : date('j');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n') - 1;
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

$dateStr = sprintf('%04d-%02d-%02d', $year, $month + 1, $day);

try {
    $stmt = $dbh->prepare("
        SELECT t.id, t.title, t.description, t.status, t.due_date
        FROM tasks t
        WHERE t.user_id = :user_id 
        AND DATE(t.due_date) = :date
        ORDER BY t.due_date
    ");
    
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':date' => $dateStr
    ]);
    
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events for <?= "$day/" . ($month + 1) . "/$year"; ?></title>
    <base href="..">
    <link rel="stylesheet" href="client/Styles/task.css">
</head>
<body>
    <h1>Events for <?= "$day/" . ($month + 1) . "/$year"; ?></h1>
    
    <div class="events-container">
        <?php if (empty($events)): ?>
            <p>No events scheduled for this day.</p>
        <?php else: ?>
            <ul class="events-list">
                <?php foreach ($events as $event): ?>
                    <li class="event-item" data-id="<?= $event['id'] ?>">
                        <h3><?= htmlspecialchars($event['title']) ?></h3>
                        <p><?= htmlspecialchars($event['description']) ?></p>
                        <p>Status: <?= htmlspecialchars($event['status']) ?></p>
                        <p>Due at: <?= htmlspecialchars($event['due_date']) ?></p>
                        <button onclick="completeTask(<?= $event['id'] ?>)">✔ Complete</button>
                        <button onclick="deleteTask(<?= $event['id'] ?>)">🗑 Delete</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <h2>Add a New Task</h2>
    <form action="server/add_task.php" method="POST">
        <input type="hidden" name="day" value="<?= $day ?>">
        <input type="hidden" name="month" value="<?= $month ?>">
        <input type="hidden" name="year" value="<?= $year ?>">

        <label>Title:</label>
        <input type="text" name="title" required><br>

        <label>Description:</label>
        <textarea name="description" rows="3"></textarea><br>

        <label>Due Date:</label>
        <input type="datetime-local" name="due_date" required><br>

        <button type="submit">➕ Add Task</button>
    </form>

    <a href="client/calendar.html">Back to Calendar</a>

    <script>
        function completeTask(id) {
            fetch('server/complete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ task_id: id })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
        }

        function deleteTask(id) {
            if (!confirm('Are you sure you want to delete this task?')) return;

            fetch('server/delete_task.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ task_id: id })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
        }
    </script>
</body>
</html>
