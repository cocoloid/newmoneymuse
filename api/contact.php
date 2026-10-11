<?php
// ════════════════════════════════════════════════════
// NEW MONEY MUSE — CONTACT FORM API
// Upload to: public_html/api/contact.php
// ════════════════════════════════════════════════════

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://newmoneymuse.com');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('success' => false, 'error' => 'Method not allowed'));
    exit();
}

// ── CONFIG ────────────────────────────────────────────
$TO_EMAIL      = 'hello@newmoneymuse.com';
$FROM_EMAIL    = 'noreply@newmoneymuse.com';
$FROM_NAME     = 'New Money Muse';
$SITE_NAME     = 'New Money Muse';

// ── Parse request ─────────────────────────────────────
$body = json_decode(file_get_contents('php://input'), true);

if (!$body) {
    echo json_encode(array('success' => false, 'error' => 'Invalid request'));
    exit();
}

// ── Sanitize inputs ───────────────────────────────────
$type      = isset($body['type'])      ? htmlspecialchars(trim($body['type']))      : 'General';
$firstName = isset($body['firstName']) ? htmlspecialchars(trim($body['firstName'])) : '';
$lastName  = isset($body['lastName'])  ? htmlspecialchars(trim($body['lastName']))  : '';
$email     = isset($body['email'])     ? trim($body['email'])                       : '';
$subject   = isset($body['subject'])   ? htmlspecialchars(trim($body['subject']))   : '';
$message   = isset($body['message'])   ? htmlspecialchars(trim($body['message']))   : '';

// ── Validate ──────────────────────────────────────────
$errors = array();

if (empty($firstName)) $errors[] = 'First name is required';
if (empty($email))     $errors[] = 'Email is required';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';
if (empty($subject))   $errors[] = 'Subject is required';
if (empty($message))   $errors[] = 'Message is required';
if (strlen($message) < 10) $errors[] = 'Message is too short';

if (!empty($errors)) {
    echo json_encode(array('success' => false, 'error' => implode(', ', $errors)));
    exit();
}

$fullName = trim($firstName . ' ' . $lastName);

// ── Build email ───────────────────────────────────────
$emailSubject = '[' . $SITE_NAME . '] ' . $type . ': ' . $subject;

$emailBody = "You have a new contact form submission from " . $SITE_NAME . "\n";
$emailBody .= str_repeat('=', 60) . "\n\n";
$emailBody .= "INQUIRY TYPE: " . strtoupper($type) . "\n\n";
$emailBody .= "FROM:    " . $fullName . "\n";
$emailBody .= "EMAIL:   " . $email . "\n";
$emailBody .= "SUBJECT: " . $subject . "\n\n";
$emailBody .= str_repeat('-', 60) . "\n";
$emailBody .= "MESSAGE:\n\n";
$emailBody .= strip_tags($message) . "\n\n";
$emailBody .= str_repeat('-', 60) . "\n";
$emailBody .= "Sent: " . date('F j, Y \a\t g:i A') . "\n";
$emailBody .= "Site: https://newmoneymuse.com\n";

// HTML version
$htmlBody = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:\'DM Sans\',Arial,sans-serif;background:#f5f5f5;margin:0;padding:20px;">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
  <div style="background:linear-gradient(135deg,#1E7A5A,#2EAA7E);padding:28px 32px;">
    <div style="font-family:Georgia,serif;font-size:1.3rem;font-weight:700;color:#fff;">New Money <span style="color:#F0D98B;">Muse</span></div>
    <div style="color:rgba(255,255,255,0.7);font-size:0.85rem;margin-top:4px;">New contact form submission</div>
  </div>
  <div style="padding:32px;">
    <div style="background:#F5F3EE;border-radius:8px;padding:16px 20px;margin-bottom:24px;">
      <div style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:#7A7870;margin-bottom:4px;">Inquiry Type</div>
      <div style="font-size:1rem;font-weight:700;color:#1E7A5A;">' . $type . '</div>
    </div>
    <table style="width:100%;border-collapse:collapse;margin-bottom:24px;">
      <tr>
        <td style="padding:10px 0;border-bottom:1px solid #E8E5DC;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#7A7870;width:100px;">Name</td>
        <td style="padding:10px 0;border-bottom:1px solid #E8E5DC;font-size:0.9rem;color:#3A3A3A;">' . $fullName . '</td>
      </tr>
      <tr>
        <td style="padding:10px 0;border-bottom:1px solid #E8E5DC;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#7A7870;">Email</td>
        <td style="padding:10px 0;border-bottom:1px solid #E8E5DC;font-size:0.9rem;color:#3A3A3A;"><a href="mailto:' . $email . '" style="color:#1E7A5A;">' . $email . '</a></td>
      </tr>
      <tr>
        <td style="padding:10px 0;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#7A7870;">Subject</td>
        <td style="padding:10px 0;font-size:0.9rem;color:#3A3A3A;">' . $subject . '</td>
      </tr>
    </table>
    <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#7A7870;margin-bottom:10px;">Message</div>
    <div style="background:#FAFAF7;border:1.5px solid #E8E5DC;border-radius:8px;padding:16px 20px;font-size:0.9rem;color:#3A3A3A;line-height:1.7;">' . nl2br($message) . '</div>
    <div style="margin-top:24px;text-align:center;">
      <a href="mailto:' . $email . '?subject=Re: ' . rawurlencode($subject) . '" style="display:inline-block;background:#1E7A5A;color:#fff;text-decoration:none;padding:12px 28px;border-radius:100px;font-weight:700;font-size:0.88rem;">Reply to ' . $firstName . ' →</a>
    </div>
  </div>
  <div style="background:#F5F3EE;padding:16px 32px;text-align:center;font-size:0.75rem;color:#7A7870;">
    Sent ' . date('F j, Y \a\t g:i A') . ' · <a href="https://newmoneymuse.com" style="color:#1E7A5A;">newmoneymuse.com</a>
  </div>
</div>
</body>
</html>';

// ── Send email ────────────────────────────────────────
$boundary = md5(time());

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/alternative; boundary=\"" . $boundary . "\"\r\n";
$headers .= "From: " . $FROM_NAME . " <" . $FROM_EMAIL . ">\r\n";
$headers .= "Reply-To: " . $fullName . " <" . $email . ">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$body_full  = "--" . $boundary . "\r\n";
$body_full .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
$body_full .= $emailBody . "\r\n";
$body_full .= "--" . $boundary . "\r\n";
$body_full .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
$body_full .= $htmlBody . "\r\n";
$body_full .= "--" . $boundary . "--";

$sent = mail($TO_EMAIL, $emailSubject, $body_full, $headers);

if ($sent) {
    // Also send confirmation to the person who submitted
    $confirmSubject = 'We got your message — New Money Muse';
    $confirmBody    = "Hi " . $firstName . ",\n\n";
    $confirmBody   .= "Thanks for reaching out! We've received your message and will get back to you within 1-2 business days.\n\n";
    $confirmBody   .= "Here's what you sent us:\n\n";
    $confirmBody   .= "Subject: " . $subject . "\n";
    $confirmBody   .= "Message: " . strip_tags($message) . "\n\n";
    $confirmBody   .= "Talk soon,\nThe New Money Muse Team\nhttps://newmoneymuse.com\n";

    $confirmHeaders  = "From: " . $FROM_NAME . " <" . $FROM_EMAIL . ">\r\n";
    $confirmHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";

    mail($email, $confirmSubject, $confirmBody, $confirmHeaders);

    echo json_encode(array(
        'success' => true,
        'message' => 'Message sent successfully'
    ));
} else {
    echo json_encode(array(
        'success' => false,
        'error'   => 'Mail server error. Please try emailing us directly at hello@newmoneymuse.com'
    ));
}
exit();
?>
