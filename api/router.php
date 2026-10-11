<?php
require_once __DIR__ . '/config.php';

$endpoint = $_GET['endpoint'] ?? '';
$method   = $_SERVER['REQUEST_METHOD'];
$db       = getDB();

// ── ROADMAP TASKS ─────────────────────────────────
if ($endpoint === 'roadmap') {
    $user = requireAuth();

    if ($method === 'GET') {
        $stmt = $db->prepare("SELECT phase_index, task_index, done FROM roadmap_tasks WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        respond($stmt->fetchAll());
    }

    if ($method === 'POST') {
        $data  = getBody();
        $phase = intval($data['phase_index'] ?? 0);
        $task  = intval($data['task_index']  ?? 0);
        $done  = $data['done'] ? 1 : 0;

        $stmt = $db->prepare("
            INSERT INTO roadmap_tasks (user_id, phase_index, task_index, done)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE done = VALUES(done), updated_at = NOW()
        ");
        $stmt->execute([$user['id'], $phase, $task, $done]);
        respond(['success' => true]);
    }
}

// ── NEWSLETTER SUBSCRIBE ──────────────────────────
if ($endpoint === 'subscribe') {
    if ($method === 'POST') {
        $data   = getBody();
        $email  = trim(strtolower($data['email'] ?? ''));
        $name   = trim($data['name'] ?? '');
        $source = $data['source'] ?? 'website';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respondError('Invalid email address');
        }

        $stmt = $db->prepare("
            INSERT INTO subscribers (email, name, source)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE status = 'active', name = IF(? != '', ?, name)
        ");
        $stmt->execute([$email, $name, $source, $name, $name]);
        respond(['success' => true, 'message' => 'Subscribed successfully']);
    }
}

// ── STRIPE UPGRADE ────────────────────────────────
if ($endpoint === 'upgrade') {
    $user = requireAuth();

    if ($method === 'POST') {
        // In production, integrate Stripe Checkout here
        // For now, return instructions
        respond([
            'message' => 'Stripe integration ready',
            'instructions' => 'Add your Stripe keys to /api/config.php and implement checkout session creation here',
            'stripe_key_needed' => STRIPE_SECRET_KEY === 'sk_live_YOUR_KEY_HERE'
        ]);
    }
}

// ── STRIPE WEBHOOK ────────────────────────────────
if ($endpoint === 'webhook') {
    $payload   = file_get_contents('php://input');
    $sig       = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

    // Verify webhook signature
    // When you add Stripe, uncomment and implement:
    // $event = \Stripe\Webhook::constructEvent($payload, $sig, STRIPE_WEBHOOK_SECRET);
    // if ($event->type === 'customer.subscription.created') {
    //   $email = $event->data->object->customer_email;
    //   $db->prepare("UPDATE users SET plan='pro' WHERE email=?")->execute([$email]);
    // }

    respond(['received' => true]);
}

respondError('Endpoint not found', 404);
?>
