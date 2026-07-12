<?php

// ============================================================
// TNSTC Smart Bus Management System
// config/db.php – Database connection & global constants
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'tnstc_tirunelveli');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_CHARSET', 'utf8mb4');

// App constants
define('APP_NAME',    'TNSTC Smart Bus System');
define('APP_DISTRICT','Tirunelveli');
define('APP_URL',     'http://localhost/TNSTC');
define('APP_VERSION', '1.0.0');

// Email (SMTP) – replace with your credentials
define('SMTP_HOST',     'smtp.gmail.com');
define('SMTP_PORT',     587);
define('SMTP_USER',     'your_email@gmail.com');
define('SMTP_PASS',     'your_app_password');
define('SMTP_FROM',     'noreply@tnstc.tn.gov.in');
define('SMTP_FROM_NAME','TNSTC Tirunelveli');

// Google Maps API Key (replace with your key)
define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY');

// Session lifetime (seconds)
define('SESSION_LIFETIME', 7200);

// ============================================================
// PDO Connection
// ============================================================
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

// ============================================================
// Session helper
// ============================================================
function startAppSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(SESSION_LIFETIME);
        session_start();
    }
}

// ============================================================
// Auth helpers
// ============================================================
function isLoggedIn(): bool {
    startAppSession();
    return isset($_SESSION['user_id']);
}

function requireLogin(string $role = ''): void {
    startAppSession();
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . APP_URL . '/auth/login.php');
        exit;
    }
    if ($role !== '' && $_SESSION['role'] !== $role) {
        // Allow driver and conductor to share the same panel
        if ($role === 'driver_conductor' && in_array($_SESSION['role'], ['driver','conductor'])) return;
        header('Location: ' . APP_URL . '/auth/login.php?err=unauthorized');
        exit;
    }
}

function currentUser(): array {
    startAppSession();
    return [
        'id'   => $_SESSION['user_id']   ?? null,
        'name' => $_SESSION['user_name'] ?? 'Guest',
        'role' => $_SESSION['role']      ?? 'guest',
        'email'=> $_SESSION['email']     ?? '',
    ];
}

// ============================================================
// Utility helpers
// ============================================================
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function generateOTP(int $length = 6): string {
    return str_pad((string)random_int(0, (int)str_repeat('9', $length)), $length, '0', STR_PAD_LEFT);
}

function generateToken(int $bytes = 32): string {
    return bin2hex(random_bytes($bytes));
}

function redirectWithMessage(string $url, string $type, string $msg): never {
    startAppSession();
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_msg']  = $msg;
    header("Location: $url");
    exit;
}

function getFlash(): array {
    startAppSession();
    $flash = [
        'type' => $_SESSION['flash_type'] ?? '',
        'msg'  => $_SESSION['flash_msg']  ?? '',
    ];
    unset($_SESSION['flash_type'], $_SESSION['flash_msg']);
    return $flash;
}

// Simple QR code URL (uses Google Charts API as fallback)
function qrCodeUrl(string $data): string {
    return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data);
}
