<?php
// ════════════════════════════════════════════════════
// NEW MONEY MUSE — SEO ADMIN API
// Upload to: public_html/api/seo-admin.php
// ════════════════════════════════════════════════════

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$ROOT   = dirname(__DIR__);
$body   = json_decode(file_get_contents('php://input'), true);
$action = isset($body['action']) ? $body['action'] : 'ping';

// ── PING ──────────────────────────────────────────────
if ($action === 'ping' || !$body) {
    echo json_encode(array(
        'success' => true,
        'message' => 'NMM API is alive',
        'php'     => phpversion(),
        'root'    => $ROOT,
        'time'    => date('Y-m-d H:i:s'),
    ));
    exit();
}

// ── SAVE SOCIAL ───────────────────────────────────────
if ($action === 'save_social') {
    $social    = isset($body['social']) ? $body['social'] : array();
    $yt        = isset($social['yt'])        ? trim($social['yt'])        : '';
    $ytHandle  = isset($social['ytHandle'])  ? trim($social['ytHandle'])  : '';
    $ig        = isset($social['ig'])        ? trim($social['ig'])        : '';
    $igHandle  = isset($social['igHandle'])  ? trim($social['igHandle'])  : '';
    $pin       = isset($social['pin'])       ? trim($social['pin'])       : '';
    $pinHandle = isset($social['pinHandle']) ? trim($social['pinHandle']) : '';

    // Always save config JSON first — this is the source of truth
    $config = array(
        'youtube'   => array('url' => $yt,  'handle' => $ytHandle),
        'instagram' => array('url' => $ig,  'handle' => $igHandle),
        'pinterest' => array('url' => $pin, 'handle' => $pinHandle),
        'updated'   => date('Y-m-d H:i:s'),
    );
    $configPath  = $ROOT . '/api/social-config.json';
    $configSaved = file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT));

    $updated = 0;
    $errors  = 0;
    $log     = array();

    $files = array(
        $ROOT . '/components/nav.js',
        $ROOT . '/components/footer.js',
    );

    // Known placeholder URLs to replace — covers all variations
    $ytPlaceholders = array(
        'https://www.youtube.com/@newmoneymuse',
        'https://youtube.com/@newmoneymuse',
        'https://www.youtube.com/newmoneymuse',
    );
    $igPlaceholders = array(
        'https://www.instagram.com/newmoneymuse',
        'https://instagram.com/newmoneymuse',
    );
    $pinPlaceholders = array(
        'https://www.pinterest.com/newmoneymuse',
        'https://pinterest.com/newmoneymuse',
    );

    foreach ($files as $filePath) {
        if (!file_exists($filePath)) {
            $log[] = 'NOT FOUND: ' . $filePath;
            $errors++;
            continue;
        }
        if (!is_writable($filePath)) {
            $log[] = 'NOT WRITABLE: ' . basename($filePath) . ' — right-click in File Manager and set permissions to 644';
            $errors++;
            continue;
        }

        $fc   = file_get_contents($filePath);
        $orig = $fc;

        if ($yt !== '') {
            foreach ($ytPlaceholders as $placeholder) {
                $fc = str_replace($placeholder, $yt, $fc);
            }
        }
        if ($ig !== '') {
            foreach ($igPlaceholders as $placeholder) {
                $fc = str_replace($placeholder, $ig, $fc);
            }
        }
        if ($pin !== '') {
            foreach ($pinPlaceholders as $placeholder) {
                $fc = str_replace($placeholder, $pin, $fc);
            }
        }

        if ($fc !== $orig) {
            $result = file_put_contents($filePath, $fc, LOCK_EX);
            if ($result !== false) {
                $updated++;
                $log[] = 'UPDATED: ' . basename($filePath);
            } else {
                $errors++;
                $log[] = 'WRITE FAILED: ' . basename($filePath);
            }
        } else {
            // File unchanged — scan what URLs are actually in the file
            $found = array();
            if (strpos($fc, 'youtube.com') !== false)   $found[] = 'youtube found in file';
            if (strpos($fc, 'instagram.com') !== false) $found[] = 'instagram found in file';
            if (strpos($fc, 'pinterest.com') !== false) $found[] = 'pinterest found in file';
            $log[] = 'NO CHANGE: ' . basename($filePath) . ' (' . (count($found) ? implode(', ', $found) : 'no social URLs found in file') . ')';
        }
    }

    echo json_encode(array(
        'success'      => true,
        'updated'      => $updated,
        'errors'       => $errors,
        'config_saved' => ($configSaved !== false),
        'message'      => implode(' | ', $log),
        'log'          => $log,
    ));
    exit();
}

// ── GET SOCIAL ────────────────────────────────────────
if ($action === 'get_social') {
    $configPath = $ROOT . '/api/social-config.json';
    if (file_exists($configPath)) {
        $data = json_decode(file_get_contents($configPath), true);
        echo json_encode(array('success' => true, 'data' => $data));
    } else {
        echo json_encode(array('success' => true, 'data' => null));
    }
    exit();
}

// ── GET SEO ───────────────────────────────────────────
if ($action === 'get_seo') {
    $page = isset($body['page']) ? $body['page'] : '';
    $page = str_replace(array('..', '\\'), '', $page);
    $page = trim($page, '/');
    $filePath = $ROOT . '/' . $page . '.html';

    if (!file_exists($filePath)) {
        echo json_encode(array('success' => false, 'message' => 'File not found: ' . $filePath));
        exit();
    }

    $html = file_get_contents($filePath);
    $data = array();

    if (preg_match('/<title>(.*?)<\/title>/si', $html, $m)) {
        $data['title'] = html_entity_decode(trim($m[1]));
    }
    if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']/si', $html, $m)) {
        $data['description'] = html_entity_decode($m[1]);
    }
    if (preg_match('/<link\s+rel=["\']canonical["\']\s+href=["\'](.*?)["\']/si', $html, $m)) {
        $data['canonical'] = $m[1];
    }

    echo json_encode(array('success' => true, 'data' => $data));
    exit();
}

// ── SAVE SEO ──────────────────────────────────────────
if ($action === 'save_seo') {
    $page = isset($body['page']) ? $body['page'] : '';
    $data = isset($body['data']) ? $body['data'] : array();

    $page     = str_replace(array('..', '\\'), '', $page);
    $page     = trim($page, '/');
    $filePath = $ROOT . '/' . $page . '.html';

    if (!file_exists($filePath)) {
        echo json_encode(array('success' => false, 'message' => 'File not found: ' . $filePath));
        exit();
    }
    if (!is_writable($filePath)) {
        echo json_encode(array('success' => false, 'message' => 'File not writable: ' . basename($filePath)));
        exit();
    }

    $html  = file_get_contents($filePath);
    $title = isset($data['title']) ? htmlspecialchars($data['title'], ENT_QUOTES) : '';
    $desc  = isset($data['desc'])  ? htmlspecialchars($data['desc'],  ENT_QUOTES) : '';
    $canon = isset($data['canonical']) ? $data['canonical'] : '';

    if ($title) {
        $html = preg_replace('/<title>.*?<\/title>/si', '<title>' . $title . '</title>', $html);
    }
    if ($desc) {
        $html = preg_replace(
            '/<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']\s*\/?>/si',
            '<meta name="description" content="' . $desc . '" />',
            $html
        );
    }
    if ($canon) {
        $html = preg_replace(
            '/<link\s+rel=["\']canonical["\']\s+href=["\'](.*?)["\']\s*\/?>/si',
            '<link rel="canonical" href="' . $canon . '" />',
            $html
        );
    }

    if (file_put_contents($filePath, $html, LOCK_EX) !== false) {
        echo json_encode(array('success' => true, 'message' => 'SEO updated on live page'));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Could not write file'));
    }
    exit();
}

// ── INJECT SCRIPTS ────────────────────────────────────
if ($action === 'inject_scripts') {
    echo json_encode(array('success' => true, 'message' => 'Script injection handled via nav.js'));
    exit();
}

// ── UNKNOWN ───────────────────────────────────────────
echo json_encode(array('success' => false, 'message' => 'Unknown action: ' . $action));
exit();
?>
