<?php
require_once __DIR__ . '/../config/db.php';
startAppSession();
session_destroy();
header('Location: ' . APP_URL . '/auth/login.php');
exit;
