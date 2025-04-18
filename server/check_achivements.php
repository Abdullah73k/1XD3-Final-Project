<?php
//BAVISHAN ADD
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

$user_id = $_SESSION['user_id'];

try {
    // Check for task count achievements
    $stmt = $dbh->prepare("
        SELECT COUNT(*) as total_tasks 
        FROM tasks 
        WHERE user_id = ? AND status = 'completed'
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_tasks = $result['total_tasks'];

    // Check for daily tasks achievement
    $stmt = $dbh->prepare("
        SELECT COUNT(*) as daily_tasks 
        FROM tasks 
        WHERE user_id = ? AND status = 'completed' 
        AND DATE(completed_at) = CURDATE()
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $daily_tasks = $result['daily_tasks'];

    // Get all possible achievements
    $stmt = $dbh->prepare("SELECT * FROM achievements");
    $stmt->execute();
    $achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $unlocked = [];

    foreach ($achievements as $achievement) {
        // Check if user already has this achievement
        $stmt = $dbh->prepare("
            SELECT COUNT(*) as has_achievement 
            FROM user_achievements 
            WHERE user_id = ? AND achievement_id = ?
        ");
        $stmt->execute([$user_id, $achievement['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['has_achievement'] == 0) {
            $condition_met = false;
            
            switch ($achievement['condition_type']) {
                case 'total_tasks':
                    $condition_met = ($total_tasks >= $achievement['condition_value']);
                    break;
                case 'daily_tasks':
                    $condition_met = ($daily_tasks >= $achievement['condition_value']);
                    break;
                // Add more condition types as needed
            }

            if ($condition_met) {
                // Award achievement
                $stmt = $dbh->prepare("
                    INSERT INTO user_achievements (user_id, achievement_id) 
                    VALUES (?, ?)
                ");
                $stmt->execute([$user_id, $achievement['id']]);
                $unlocked[] = $achievement;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'unlocked' => $unlocked
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}