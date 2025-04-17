<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://cs1xd3.cas.mcmaster.ca');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

include '../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

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
        // Server-side email validation
        $emailRegex = '/^[^\s@]+@([^\s@]+)\.([^\s@]+)$/';
        if (!preg_match($emailRegex, $email)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email must be in a valid format.'
            ]);
            exit;
        }
        
        // Check if email domain is from a common provider
        $validEmailDomains = ["gmail", "hotmail", "outlook", "yahoo", "icloud", "aol", "protonmail"];
        $emailDomain = strtolower(explode('@', $email)[1]);
        $isDomainValid = false;
        
        foreach ($validEmailDomains as $domain) {
            if (strpos($emailDomain, $domain) !== false) {
                $isDomainValid = true;
                break;
            }
        }
        
        if (!$isDomainValid) {
            echo json_encode([
                'success' => false,
                'message' => 'Please use a common email provider.'
            ]);
            exit;
        }
        
        // Server-side password validation
        if (strlen($password) < 8) {
            echo json_encode([
                'success' => false,
                'message' => 'Password must be at least 8 characters long.'
            ]);
            exit;
        }
        
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            echo json_encode([
                'success' => false,
                'message' => 'Password must contain at least one uppercase letter and one number.'
            ]);
            exit;
        }

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
            $_SESSION['user_id'] = $user['id'];

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