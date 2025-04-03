<?php
header('Content-Type: application/json');
include '../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
// error_log(print_r($data, return: true));  // Logs data to your PHP error log
// echo json_encode(['action' => $data]);
// exit;

$action   = $data['action'] ?? '';
$email    = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password || $action === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Server error.'
    ]);
    exit;
}

try {
    if ($action === 'signup') {
        $stmt = $dbh->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already registered.'
            ]);
            exit;
        }

        $username = explode('@', $email)[0];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $dbh->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);

        echo json_encode([
            'success' => true,
            'message' => 'Signup successful!'
        ]);

    } else if ($action === 'login') {
        $stmt = $dbh->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Login successful!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email or password.'
            ]);
        }
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
