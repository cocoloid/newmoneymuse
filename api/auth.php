<?php
require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// ── SIGN UP ───────────────────────────────────────
if ($method === 'POST' && $action === 'signup') {
    $data = getBody();
    $name  = trim($data['name'] ?? '');
    $email = trim(strtolower($data['email'] ?? ''));
    $pass  = $data['password'] ?? '';

    if (!$name || !$email || !$pass) respondError('Name, email and password are required');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) respondError('Invalid email address');
    if (strlen($pass) < 6) respondError('Password must be at least 6 characters');

    $db = getDB();

    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) respondError('An account with that email already exists');

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hash]);
    $userId = $db->lastInsertId();

    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
    $stmt = $db->prepare("INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $token, $expires]);

    respond(['token' => $token, 'name' => $name, 'plan' => 'free', 'id' => $userId]);
}

// ── LOGIN ─────────────────────────────────────────
if ($method === 'POST' && $action === 'login') {
    $data  = getBody();
    $email = trim(strtolower($data['email'] ?? ''));
    $pass  = $data['password'] ?? '';

    if (!$email || !$pass) respondError('Email and password are required');

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($pass, $user['password_hash'])) {
        respondError('Invalid email or password', 401);
    }

    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
    $stmt = $db->prepare("INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user['id'], $token, $expires]);

    respond([
        'token' => $token,
        'name'  => $user['name'],
        'plan'  => $user['plan'],
        'id'    => $user['id']
    ]);
}

// ── LOGOUT ────────────────────────────────────────
if ($method === 'POST' && $action === 'logout') {
    $user = requireAuth();
    $headers = getallheaders();
    $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
    $db = getDB();
    $db->prepare("DELETE FROM sessions WHERE token = ?")->execute([$token]);
    respond(['success' => true]);
}

// ── ME (get current user) ─────────────────────────
if ($method === 'GET' && $action === 'me') {
    $user = requireAuth();
    respond([
        'id'   => $user['id'],
        'name' => $user['name'],
        'email'=> $user['email'],
        'plan' => $user['plan'],
        'hustle_match' => $user['hustle_match']
    ]);
}

respondError('Invalid request', 404);
?>
