<?php
// ── GOALS ─────────────────────────────────────────
// /api/goals.php
require_once __DIR__ . '/config.php';

$user   = requirePro();
$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();

if ($method === 'GET') {
    $stmt = $db->prepare("SELECT * FROM goals WHERE user_id = ? ORDER BY created_at ASC");
    $stmt->execute([$user['id']]);
    respond($stmt->fetchAll());
}

if ($method === 'POST') {
    $data = getBody();
    $action = $data['action'] ?? 'create';

    if ($action === 'create') {
        $name     = trim($data['name'] ?? '');
        $target   = floatval($data['target'] ?? 0);
        $color    = $data['color'] ?? 'green';
        $deadline = $data['deadline'] ?? '';
        if (!$name || !$target) respondError('Name and target are required');

        $stmt = $db->prepare("INSERT INTO goals (user_id, name, target_amount, color, deadline) VALUES (?,?,?,?,?)");
        $stmt->execute([$user['id'], $name, $target, $color, $deadline]);
        respond(['id' => $db->lastInsertId(), 'success' => true], 201);
    }

    if ($action === 'update_progress') {
        $id     = intval($data['id'] ?? 0);
        $amount = floatval($data['amount'] ?? 0);
        $stmt   = $db->prepare("UPDATE goals SET current_amount = LEAST(target_amount, current_amount + ?) WHERE id = ? AND user_id = ?");
        $stmt->execute([$amount, $id, $user['id']]);
        respond(['success' => true]);
    }
}

if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    $db->prepare("DELETE FROM goals WHERE id = ? AND user_id = ?")->execute([$id, $user['id']]);
    respond(['success' => true]);
}

respondError('Method not allowed', 405);
?>
