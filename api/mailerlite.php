<?php
// ════════════════════════════════════════════════════
// NEW MONEY MUSE — MAILERLITE API PROXY
// Upload to: public_html/api/mailerlite.php
// ════════════════════════════════════════════════════

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://newmoneymuse.com');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); exit();
}

require_once __DIR__ . '/secrets.php';
define('ML_API_URL', 'https://connect.mailerlite.com/api');

$body   = json_decode(file_get_contents('php://input'), true);
$action = isset($body['action']) ? $body['action'] : 'ping';

// ── Helper: call MailerLite API ───────────────────────
function ml_request($endpoint, $method = 'GET', $data = null) {
    $url = ML_API_URL . $endpoint;
    $ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . ML_API_KEY,
    ));
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array('code' => $httpCode, 'body' => json_decode($response, true));
}

// ── PING ──────────────────────────────────────────────
if ($action === 'ping') {
    echo json_encode(array('success' => true, 'message' => 'MailerLite proxy alive'));
    exit();
}

// ── GET SUBSCRIBERS ───────────────────────────────────
if ($action === 'get_subscribers') {
    $page  = isset($body['page'])  ? intval($body['page'])  : 1;
    $limit = isset($body['limit']) ? intval($body['limit']) : 25;

    $result = ml_request('/subscribers?limit=' . $limit . '&page=' . $page . '&sort=-created_at');

    if ($result['code'] === 200) {
        $data  = $result['body'];
        $subs  = array();

        foreach ($data['data'] as $s) {
            $subs[] = array(
                'id'      => $s['id'],
                'email'   => $s['email'],
                'status'  => $s['status'],
                'source'  => isset($s['source']) ? $s['source'] : 'form',
                'joined'  => isset($s['created_at']) ? date('M j, Y', strtotime($s['created_at'])) : '—',
                'name'    => isset($s['fields']['name']) ? $s['fields']['name'] : '',
            );
        }

        echo json_encode(array(
            'success' => true,
            'subscribers' => $subs,
            'total'   => $data['meta']['total'] ?? count($subs),
            'pages'   => $data['meta']['last_page'] ?? 1,
            'current' => $data['meta']['current_page'] ?? 1,
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'MailerLite API error: ' . $result['code'],
            'detail'  => $result['body'],
        ));
    }
    exit();
}

// ── GET STATS ─────────────────────────────────────────
if ($action === 'get_stats') {
    // Get subscriber counts by status
    $active   = ml_request('/subscribers?filter[status]=active&limit=1');
    $total    = ml_request('/subscribers?limit=1');

    $activeCount = 0;
    $totalCount  = 0;

    if ($active['code'] === 200)
        $activeCount = $active['body']['meta']['total'] ?? 0;
    if ($total['code'] === 200)
        $totalCount  = $total['body']['meta']['total'] ?? 0;

    echo json_encode(array(
        'success'  => true,
        'total'    => $totalCount,
        'active'   => $activeCount,
        'unsubscribed' => $totalCount - $activeCount,
    ));
    exit();
}

// ── UNSUBSCRIBE ───────────────────────────────────────
if ($action === 'unsubscribe') {
    $email = isset($body['email']) ? trim($body['email']) : '';
    if (!$email) {
        echo json_encode(array('success' => false, 'message' => 'Email required'));
        exit();
    }

    // Find subscriber by email first
    $find = ml_request('/subscribers/' . urlencode($email));
    if ($find['code'] !== 200) {
        echo json_encode(array('success' => false, 'message' => 'Subscriber not found'));
        exit();
    }

    $subId = $find['body']['data']['id'];

    // Update status to unsubscribed
    $result = ml_request('/subscribers/' . $subId, 'POST',
        array('status' => 'unsubscribed'));

    echo json_encode(array(
        'success' => $result['code'] === 200,
        'message' => $result['code'] === 200 ? 'Unsubscribed' : 'Error unsubscribing',
    ));
    exit();
}

// ── EXPORT CSV ────────────────────────────────────────
if ($action === 'export_csv') {
    // Get all subscribers (up to 1000)
    $result = ml_request('/subscribers?limit=1000&filter[status]=active');

    if ($result['code'] !== 200) {
        echo json_encode(array('success' => false, 'message' => 'Could not fetch subscribers'));
        exit();
    }

    $subs = $result['body']['data'];
    $rows = array('Email,Name,Status,Joined');

    foreach ($subs as $s) {
        $name    = isset($s['fields']['name']) ? $s['fields']['name'] : '';
        $status  = $s['status'];
        $joined  = isset($s['created_at']) ? date('Y-m-d', strtotime($s['created_at'])) : '';
        $rows[]  = '"' . $s['email'] . '","' . $name . '","' . $status . '","' . $joined . '"';
    }

    echo json_encode(array(
        'success' => true,
        'csv'     => implode("\n", $rows),
        'count'   => count($subs),
    ));
    exit();
}

echo json_encode(array('success' => false, 'message' => 'Unknown action'));
exit();
?>