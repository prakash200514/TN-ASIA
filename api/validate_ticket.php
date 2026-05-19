<?php
// api/validate_ticket.php – QR ticket validation for conductor scan
require_once __DIR__ . '/../config/db.php';
startAppSession();
header('Content-Type: application/json');
requireLogin('driver_conductor');

$input = json_decode(file_get_contents('php://input'), true);
$qr_code = sanitize($input['qr_code'] ?? '');

if (!$qr_code) { echo json_encode(['valid'=>false,'message'=>'No QR data provided.']); exit; }

$db   = getDB();
$stmt = $db->prepare("SELECT t.*,u.name AS passenger_name,r.source,r.destination FROM tickets t JOIN users u ON u.user_id=t.passenger_id JOIN schedules s ON s.schedule_id=t.schedule_id JOIN routes r ON r.route_id=s.route_id WHERE t.qr_code=? LIMIT 1");
$stmt->execute([$qr_code]);
$ticket = $stmt->fetch();

if (!$ticket) { echo json_encode(['valid'=>false,'message'=>'Ticket not found. Invalid QR.']); exit; }
if ($ticket['ticket_status'] === 'used')     { echo json_encode(['valid'=>false,'message'=>'Ticket already used!']); exit; }
if ($ticket['ticket_status'] === 'cancelled'){ echo json_encode(['valid'=>false,'message'=>'Ticket has been cancelled.']); exit; }

// Mark as used
$db->prepare("UPDATE tickets SET ticket_status='used' WHERE ticket_id=?")->execute([$ticket['ticket_id']]);

echo json_encode([
    'valid'   => true,
    'message' => 'Valid Ticket ✓',
    'ticket'  => [
        'id'        => $ticket['ticket_id'],
        'passenger' => $ticket['passenger_name'],
        'seat'      => $ticket['seat_number'],
        'from'      => $ticket['source_stop'],
        'to'        => $ticket['dest_stop'],
        'fare'      => '₹'.$ticket['fare'],
    ],
]);
