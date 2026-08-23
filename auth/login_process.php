<?php
require_once __DIR__ . '/../config/db.php';
startAppSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_URL . '/auth/login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';
$roleHint = $_POST['role_hint'] ?? '';

$failRedirect = ($roleHint === 'minister') ? (APP_URL . '/minister/login.php') : (APP_URL . '/auth/login.php');

if (!$email || !$pass) {
    header('Location: ' . $failRedirect . '?err=invalid');
    exit;
}

$db   = getDB();
$stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($pass, $user['password'])) {
    header('Location: ' . $failRedirect . '?err=invalid');
    exit;
}

if ($user['status'] === 'pending') {
    header('Location: ' . $failRedirect . '?err=inactive');
    exit;
}

if ($user['status'] === 'inactive') {
    header('Location: ' . $failRedirect . '?err=inactive');
    exit;
}

// Set session
$_SESSION['user_id']   = $user['user_id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['email']     = $user['email'];
$_SESSION['role']      = $user['role'];

// Role → dashboard map
$map = [
    'passenger'    => '/passenger/dashboard.php',
    'driver'       => '/driver/dashboard.php',
    'conductor'    => '/driver/dashboard.php',
    'depot_manager'=> '/depot_manager/dashboard.php',
    'minister'     => '/minister/dashboard.php',
    'admin'        => '/admin/dashboard.php',
];

$dest = APP_URL . ($map[$user['role']] ?? '/');
header("Location: $dest");
exit;
