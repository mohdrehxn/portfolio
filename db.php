<?php
/**
 * db.php — Database Configuration
 * Reads credentials from environment variables (set in Render dashboard)
 * Falls back to XAMPP defaults for local development
 */

/* ── Credentials from ENV (Render) or fallback (XAMPP) ── */
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'portfolio_db';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';

/* ── Admin credentials from ENV or fallback ────────────── */
define('ADMIN_USER', getenv('ADMIN_USER') ?: 'admin');
define('ADMIN_PASS', getenv('ADMIN_PASS') ?: 'rehan@2026');

/* ── App config ────────────────────────────────────────── */
define('TO_EMAIL',   getenv('TO_EMAIL')  ?: 'mohdrehan@email.com');
define('TO_NAME',    'Mohd Rehan');
define('SITE_NAME',  'Mohd Rehan Portfolio');
define('RATE_LIMIT', 60);

/* ── PDO Singleton Connection ──────────────────────────── */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $host    = $GLOBALS['DB_HOST'];
    $dbname  = $GLOBALS['DB_NAME'];
    $user    = $GLOBALS['DB_USER'];
    $pass    = $GLOBALS['DB_PASS'];
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('DB Connection failed: ' . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed. Please try again later.'
        ]));
    }

    return $pdo;
}

/* ── Auto-create contacts table if missing ─────────────── */
function ensureTable(): void {
    $pdo = getDB();
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `contacts` (
            `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name`       VARCHAR(120)  NOT NULL,
            `email`      VARCHAR(180)  NOT NULL,
            `budget`     VARCHAR(60)   DEFAULT 'Not specified',
            `message`    TEXT          NOT NULL,
            `ip`         VARCHAR(45)   DEFAULT NULL,
            `status`     ENUM('new','read','replied','archived') DEFAULT 'new',
            `created_at` DATETIME      DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email   (`email`),
            INDEX idx_status  (`status`),
            INDEX idx_created (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}
