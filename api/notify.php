<?php
// api/notify.php – Returns unread notification count + list
require_once __DIR__ . '/../config/db.php';
startAppSession();
header('Content-Type: application/json');
if (!isLoggedIn()) { echo json_encode(['unread'=>0,'items',[]]); exit; }
>>>>>
$db   = getDB();
$uid  = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM notifications WHERE (user_id=? OR user_id IS NULL) ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$uid]);
$items  = $stmt->fetchAll();
$unread = array_reduce($items, fn($c,$n) => $c + (int)!$n['is_read'], 0);

// Mark as read
$db->prepare("UPDATE notifications SET is_read=1 WHERE (user_id=? OR user_id IS NULL) AND is_read=0")->execute([$uid]);

echo json_encode(['unread'=>$unread,'items'=>$items]);
