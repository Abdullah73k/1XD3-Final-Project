<?php
include 'connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.html");
    exit;
}

// Get date parameters from URL
$day = isset($_GET['day']) ? (int)$_GET['day'] : date('j');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n') - 1; // Adjust for 0-based month
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Convert to proper date format (month is 1-12 in SQL)
$dateStr = sprintf('%04d-%02d-%02d', $year, $month + 1, $day);

// Get tasks for this date
try {
    $stmt = $dbh->prepare("
        SELECT t.title, t.description, t.status, s.start_time, s.end_time 
        FROM tasks t
        LEFT JOIN schedules s ON t.id = s.task_id
        WHERE t.user_id = :user_id 
        AND DATE(s.start_time) = :date
        ORDER BY s.start_time
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events for <?php echo "$day/" . ($month + 1) . "/$year"; ?></title>
    <link rel="stylesheet" href="../Styles/dayView.css">
</head>
<body>
    <h1>Events for <?php echo "$day/" . ($month + 1) . "/$year"; ?></h1>
    
    <div class="events-container">
        <?php if (empty($events)): ?>
            <p>No events scheduled for this day.</p>
        <?php else: ?>
            <ul class="events-list">
                <?php foreach ($events as $event): ?>
                    <li class="event-item">
                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p><?php echo htmlspecialchars($event['description']); ?></p>
                        <p>Status: <?php echo htmlspecialchars($event['status']); ?></p>
                        <?php if ($event['start_time']): ?>
                            <p>Time: 
                                <?php echo date('H:i', strtotime($event['start_time'])) . ' - ' . 
                                     date('H:i', strtotime($event['end_time'])); ?>
                            </p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    
    <h2>Add a New Task</h2>
<form action="../api/add_task.php" method="POST">
    <input type="hidden" name="day" value="<?= $day ?>">
    <input type="hidden" name="month" value="<?= $month ?>">
    <input type="hidden" name="year" value="<?= $year ?>">

    <label>Title:</label>
    <input type="text" name="title" required><br>

    <label>Description:</label>
    <textarea name="description" rows="3"></textarea><br>

    <button type="submit">➕ Add Task</button>
</form>
    
    <a href="../index.html">Back to Calendar</a>
</body>
</html>