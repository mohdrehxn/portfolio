<?php
/**
 * contact.php — Contact Form Handler with MySQL DB
 * Requires db.php in the same folder.
 */

require_once __DIR__ . '/db.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

/* ── Only POST allowed ──────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

/* ── Rate limiting via DB ───────────────────────────── */
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

try {
    ensureTable();
    $pdo = getDB();

    $rateCheck = $pdo->prepare("
        SELECT COUNT(*) FROM contacts
        WHERE ip = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
    ");
    $rateCheck->execute([$ip, RATE_LIMIT]);

    if ((int)$rateCheck->fetchColumn() > 0) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'Too many requests. Please wait a minute before trying again.'
        ]);
        exit;
    }
} catch (PDOException $e) {
    error_log('Rate check failed: ' . $e->getMessage());
}

/* ── Sanitize ───────────────────────────────────────── */
function clean(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

$name    = clean($_POST['name']    ?? '');
$email   = clean($_POST['email']   ?? '');
$budget  = clean($_POST['budget']  ?? 'Not specified');
$message = clean($_POST['message'] ?? '');

/* ── Validate ───────────────────────────────────────── */
$errors = [];

if (empty($name) || strlen($name) < 2)
    $errors[] = 'Please enter your full name.';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'Please enter a valid email address.';

if (empty($message) || strlen($message) < 10)
    $errors[] = 'Message must be at least 10 characters.';

$spamWords = ['viagra', 'casino', 'buy now', 'free money', 'click here'];
foreach ($spamWords as $w) {
    if (stripos($message . $name, $w) !== false) {
        $errors[] = 'Message flagged as spam.';
        break;
    }
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

/* ── Save to Database ───────────────────────────────── */
$saved    = false;
$insertId = null;

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare("
        INSERT INTO contacts (name, email, budget, message, ip, status, created_at)
        VALUES (?, ?, ?, ?, ?, 'new', NOW())
    ");
    $stmt->execute([$name, $email, $budget, $message, $ip]);
    $insertId = $pdo->lastInsertId();
    $saved    = true;
} catch (PDOException $e) {
    error_log('DB insert failed: ' . $e->getMessage());
}

/* ── Email to you ───────────────────────────────────── */
$dbBadge = $saved
    ? "<span style='background:#d4edda;color:#155724;padding:2px 10px;border-radius:20px;font-size:.78rem;'>✓ Saved to DB (ID #{$insertId})</span>"
    : "<span style='background:#fff3cd;color:#856404;padding:2px 10px;border-radius:20px;font-size:.78rem;'>⚠ DB save failed</span>";

$subject = "📬 New Lead from {$name} — " . SITE_NAME;
$body    = "<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>
  body{font-family:Arial,sans-serif;background:#f4f6fb;margin:0;padding:20px}
  .card{background:#fff;border-radius:12px;padding:32px;max-width:580px;margin:auto;border:1px solid #e0e5ef}
  h2{color:#e8512a;margin:0 0 6px;font-size:1.35rem}
  .meta{font-size:.78rem;color:#aaa;margin-bottom:22px}
  .row{margin-bottom:15px}
  .lbl{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#8a9ab5;margin-bottom:3px}
  .val{font-size:.95rem;color:#0f1a27;line-height:1.6}
  .msg{background:#f4f6fb;border-left:3px solid #e8512a;padding:13px 15px;border-radius:6px}
  .footer{margin-top:24px;font-size:.76rem;color:#aaa;border-top:1px solid #eee;padding-top:13px}
  a{color:#e8512a}
</style></head><body>
<div class='card'>
  <h2>New Contact Submission</h2>
  <div class='meta'>" . SITE_NAME . " &nbsp;·&nbsp; " . date('d M Y, H:i T') . " &nbsp;·&nbsp; {$dbBadge}</div>
  <div class='row'><div class='lbl'>Name</div><div class='val'>{$name}</div></div>
  <div class='row'><div class='lbl'>Email</div><div class='val'><a href='mailto:{$email}'>{$email}</a></div></div>
  <div class='row'><div class='lbl'>Budget</div><div class='val'>{$budget}</div></div>
  <div class='row'><div class='lbl'>Message</div><div class='val msg'>" . nl2br($message) . "</div></div>
  <div class='footer'>IP: {$ip} &nbsp;|&nbsp; <a href='http://localhost/portfolio/admin.php'>View Admin Panel</a></div>
</div></body></html>";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: " . SITE_NAME . " <no-reply@mohdrehan.com>\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";

$mailSent = mail(TO_EMAIL, $subject, $body, $headers);

/* ── Auto-reply to visitor ──────────────────────────── */
$autoReply = "<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>
  body{font-family:Arial,sans-serif;background:#f4f6fb;margin:0;padding:20px}
  .card{background:#fff;border-radius:12px;padding:32px;max-width:540px;margin:auto;border:1px solid #e0e5ef}
  h2{color:#e8512a;margin:0 0 12px}
  p{color:#5c6f88;line-height:1.75;margin:10px 0}
  .hl{color:#0f1a27;font-weight:600}
  .msg-box{background:#f4f6fb;border-left:3px solid #e8512a;padding:12px 15px;border-radius:6px;font-style:italic;color:#5c6f88;font-size:.93rem}
  .footer{margin-top:24px;font-size:.76rem;color:#aaa;border-top:1px solid #eee;padding-top:13px}
</style></head><body>
<div class='card'>
  <h2>Thanks for reaching out, {$name}! 👋</h2>
  <p>I've received your message and will reply <span class='hl'>within 24 hours</span>.</p>
  <p><strong>Your message:</strong></p>
  <div class='msg-box'>" . nl2br($message) . "</div>
  <p style='margin-top:18px'>Talk soon,<br><strong>" . TO_NAME . "</strong></p>
  <div class='footer'>Automated reply — please do not reply to this email.</div>
</div></body></html>";

$autoHeaders  = "MIME-Version: 1.0\r\n";
$autoHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
$autoHeaders .= "From: " . TO_NAME . " <no-reply@mohdrehan.com>\r\n";

mail($email, "Got your message — I'll be in touch soon! 👋", $autoReply, $autoHeaders);

/* ── Response ───────────────────────────────────────── */
if ($saved || $mailSent) {
    echo json_encode([
        'success' => true,
        'message' => "Thanks {$name}! Your message was received. I'll reply within 24h."
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Something went wrong. Please email me directly at ' . TO_EMAIL
    ]);
}