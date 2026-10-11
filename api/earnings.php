<?php
require_once __DIR__ . '/config.php';

$user   = requirePro();
$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();

// ── GET all earnings ──────────────────────────────
if ($method === 'GET') {
    $stmt = $db->prepare("
        SELECT * FROM earnings
        WHERE user_id = ?
        ORDER BY date DESC, created_at DESC
    ");
    $stmt->execute([$user['id']]);
    respond($stmt->fetchAll());
}

// ── POST new earning ──────────────────────────────
if ($method === 'POST') {
    $data = getBody();
    $date     = $data['date']     ?? date('Y-m-d');
    $platform = $data['platform'] ?? 'other';
    $hours    = floatval($data['hours']    ?? 0);
    $gross    = floatval($data['gross']    ?? 0);
    $expenses = floatval($data['expenses'] ?? 0);
    $notes    = $data['notes'] ?? '';

    if (!$gross) respondError('Gross amount is required');

    $stmt = $db->prepare("
        INSERT INTO earnings (user_id, date, platform, hours, gross, expenses, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user['id'], $date, $platform, $hours, $gross, $expenses, $notes]);

    respond(['id' => $db->lastInsertId(), 'success' => true], 201);
}

// ── DELETE earning ────────────────────────────────
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) respondError('ID required');

    $stmt = $db->prepare("DELETE FROM earnings WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user['id']]);

    respond(['success' => true]);
}

respondError('Method not allowed', 405);
?>
