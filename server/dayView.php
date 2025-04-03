<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Get date from URL parameter
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$dateObj = new DateTime($date);
$formattedDate = $dateObj->format('F j, Y');

// Get events for this date and user
$userId = $_SESSION['user_id'];
try {
    $stmt = $db->prepare("SELECT * FROM events 
                         WHERE user_id = ? AND event_date = ? 
                         ORDER BY event_time");
    $stmt->execute([$userId, $date]);
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events for <?= $formattedDate ?></title>
    <link rel="stylesheet" href="styles/events.css">
</head>
<body>
    <div class="container">
        <h1>Events for <?= $formattedDate ?></h1>
        
        <!-- Events List -->
        <div class="events-list">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <h3><?= htmlspecialchars($event['title']) ?></h3>
                        <p class="event-time">
                            <?= date('g:i A', strtotime($event['event_time'])) ?>
                        </p>
                        <p class="event-description">
                            <?= htmlspecialchars($event['description']) ?>
                        </p>
                        <button class="delete-btn" data-id="<?= $event['id'] ?>">Delete</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-events">No events scheduled for this day.</p>
            <?php endif; ?>
        </div>

        <!-- Add Event Form -->
        <div class="add-event-form">
            <h2>Add New Event</h2>
            <form id="eventForm" method="POST" action="saveEvent.php">
                <input type="hidden" name="event_date" value="<?= $date ?>">
                
                <div class="form-group">
                    <label for="title">Event Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <button type="submit">Save Event</button>
            </form>
        </div>
        
        <a href="index.php" class="back-link">← Back to Calendar</a>
    </div>

    <script>
        // Delete button functionality
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this event?')) {
                    const eventId = this.getAttribute('data-id');
                    fetch('deleteEvent.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${eventId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.event-card').remove();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>