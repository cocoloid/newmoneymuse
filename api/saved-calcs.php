<?php
require_once __DIR__ . '/config.php';

$user   = requirePro();
$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();

if ($method === 'GET') {
    $stmt = $db->prepare("SELECT * FROM saved_calculations WHERE user_id = ? ORDER BY updated_at DESC");
    $stmt->execute([$user['id']]);
    respond($stmt->fetchAll());
}

if ($method === 'POST') {
    $data       = getBody();
    $calc_type  = $data['calc_type'] ?? 'other';
    $name       = $data['name'] ?? 'Saved Calculation';
    $inputs     = json_encode($data['inputs'] ?? []);
    $result     = $data['result'] ?? '';
    $result_cls = $data['result_class'] ?? 'green';

    // Upsert — update if same type exists, else insert
    $stmt = $db->prepare("
        INSERT INTO saved_calculations (user_id, calc_type, name, inputs, result, result_class)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            name=VALUES(name), inputs=VALUES(inputs),
            result=VALUES(result), result_class=VALUES(result_class),
            updated_at=NOW()
    ");
    $stmt->execute([$user['id'], $calc_type, $name, $inputs, $result, $result_cls]);
    respond(['success' => true], 201);
}

if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    $db->prepare("DELETE FROM saved_calculations WHERE id = ? AND user_id = ?")->execute([$id, $user['id']]);
    respond(['success' => true]);
}

respondError('Method not allowed', 405);
?>
