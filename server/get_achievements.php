<?php
//BAVISHAN ADD
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://cs1xd3.cas.mcmaster.ca');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');

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
    $stmt = $dbh->prepare("
        SELECT 
            a.id, 
            a.name, 
            a.description, 
            a.condition_type, 
            a.condition_value,
            CASE WHEN ua.id IS NOT NULL THEN 1 ELSE 0 END as completed,
            CASE 
                WHEN a.condition_type = 'total_tasks' THEN 
                    (SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status = 'completed')
                WHEN a.condition_type = 'daily_tasks' THEN 
                    (SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status = 'completed' AND DATE(completed_at) = CURDATE())
                WHEN a.condition_type = 'morning_tasks' THEN
                    (SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status = 'completed' AND HOUR(completed_at) < 9)
                WHEN a.condition_type = 'evening_tasks' THEN
                    (SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status = 'completed' AND HOUR(completed_at) > 20)
                ELSE 0
            END as progress
        FROM achievements a
        LEFT JOIN user_achievements ua ON a.id = ua.achievement_id AND ua.user_id = ?
        ORDER BY completed DESC, a.id
    ");
    $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);
    $achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($achievements);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}