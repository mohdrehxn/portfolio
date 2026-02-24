<?php
/**
 * contact.php — Contact Form Handler
 * Place this file in the same folder as index.html on your PHP server.
 * Update the $to email address below to your own.
 */

/* ── Config ─────────────────────────────────────────── */
define('TO_EMAIL', 'rehanaisha28@gmail.com'); // ← your real email
define('TO_NAME',    'Mohd Rehan');
define('SITE_NAME',  'Mohd Rehan Portfolio');
define('RATE_LIMIT', 60);                       // Seconds between submissions per IP

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

/* ── Only allow POST ─────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

/* ── Rate limiting (simple file-based) ──────────────── */
$ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$lockFile = sys_get_temp_dir() . '/contact_rl_' . md5($ip) . '.tmp';

if (file_exists($lockFile)) {
    $elapsed = time() - (int) file_get_contents($lockFile);
    if ($elapsed < RATE_LIMIT) {
        $wait = RATE_LIMIT - $elapsed;
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => "Too many requests. Please wait {$wait} seconds."
        ]);
        exit;
    }
}
file_put_contents($lockFile, time());

/* ── Collect & sanitize input ───────────────────────── */
function clean(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

$name    = clean($_POST['name']    ?? '');
$email   = clean($_POST['email']   ?? '');
$budget  = clean($_POST['budget']  ?? 'Not specified');
$message = clean($_POST['message'] ?? '');

/* ── Validation ──────────────────────────────────────── */
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Please enter your full name (at least 2 characters).';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters long.';
}

// Block common spam patterns
$spamKeywords = ['viagra', 'casino', 'click here', 'buy now', 'free money', 'http://', 'https://'];
foreach ($spamKeywords as $kw) {
    if (stripos($message, $kw) !== false || stripos($name, $kw) !== false) {
        $errors[] = 'Message flagged as spam.';
        break;
    }
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

/* ── Build email ──────────────────────────────────────── */
$subject = "New Contact from {$name} — " . SITE_NAME;

$body = "
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<style>
  body { font-family: Arial, sans-serif; background:#f4f6fb; margin:0; padding:20px; }
  .card { background:#fff; border-radius:12px; padding:32px; max-width:560px; margin:auto; border:1px solid #e0e5ef; }
  h2   { color:#e8512a; margin:0 0 20px; font-size:1.4rem; }
  .row { margin-bottom:16px; }
  .label { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#8a9ab5; margin-bottom:4px; }
  .value { font-size:.97rem; color:#0f1a27; line-height:1.6; }
  .msg  { background:#f4f6fb; border-left:3px solid #e8512a; padding:14px 16px; border-radius:6px; }
  .footer { margin-top:28px; font-size:.78rem; color:#aaa; border-top:1px solid #eee; padding-top:14px; }
</style>
</head>
<body>
<div class='card'>
  <h2>📬 New Message — " . SITE_NAME . "</h2>

  <div class='row'>
    <div class='label'>Name</div>
    <div class='value'>{$name}</div>
  </div>

  <div class='row'>
    <div class='label'>Email</div>
    <div class='value'><a href='mailto:{$email}' style='color:#e8512a;'>{$email}</a></div>
  </div>

  <div class='row'>
    <div class='label'>Budget Range</div>
    <div class='value'>{$budget}</div>
  </div>

  <div class='row'>
    <div class='label'>Message</div>
    <div class='value msg'>" . nl2br($message) . "</div>
  </div>

  <div class='footer'>
    Sent from: " . SITE_NAME . " contact form &nbsp;|&nbsp; IP: {$ip} &nbsp;|&nbsp; Time: " . date('d M Y, H:i:s T') . "
  </div>
</div>
</body>
</html>
";

/* ── Auto-reply to sender ─────────────────────────────── */
$autoReply = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><style>
  body { font-family:Arial,sans-serif; background:#f4f6fb; margin:0; padding:20px; }
  .card { background:#fff; border-radius:12px; padding:32px; max-width:560px; margin:auto; border:1px solid #e0e5ef; }
  h2   { color:#e8512a; margin:0 0 12px; }
  p    { color:#5c6f88; line-height:1.75; margin:10px 0; }
  .highlight { color:#0f1a27; font-weight:600; }
  .footer { margin-top:28px; font-size:.78rem; color:#aaa; border-top:1px solid #eee; padding-top:14px; }
</style></head>
<body>
<div class='card'>
  <h2>Thanks for reaching out, {$name}! 👋</h2>
  <p>I've received your message and will get back to you <span class='highlight'>within 24 hours</span>.</p>
  <p>Here's a quick summary of what you sent:</p>
  <p><strong>Budget:</strong> {$budget}</p>
  <p><strong>Your message:</strong><br><em>" . nl2br($message) . "</em></p>
  <p>Talk soon,<br><strong>" . TO_NAME . "</strong></p>
  <div class='footer'>This is an automated reply. Please do not reply to this email.</div>
</div>
</body>
</html>
";

/* ── Send emails ──────────────────────────────────────── */
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: " . SITE_NAME . " <no-reply@mohdrehan.com>\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// Send notification to you
$sent = mail(TO_EMAIL, $subject, $body, $headers);

// Send auto-reply to the visitor
$autoHeaders  = "MIME-Version: 1.0\r\n";
$autoHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
$autoHeaders .= "From: " . TO_NAME . " <no-reply@mohdrehan.com>\r\n";
$autoHeaders .= "X-Mailer: PHP/" . phpversion() . "\r\n";

mail($email, "Got your message — I'll be in touch soon!", $autoReply, $autoHeaders);

/* ── Response ─────────────────────────────────────────── */
if ($sent) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => "Thanks {$name}! Your message was sent. I'll reply within 24h."
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Mail server error. Please email me directly at ' . TO_EMAIL
    ]);
}
