<?php >....
//api/book_ticket.php
require_once __DIR__ . '/../config/db.php';
startAppSession();
header('Content-Type: application/json');
requireLogin('passenger');

$input = json_decode(file_get_contents('php://input'), true);
$schedule_id  = (int)($input['schedule_id'] ?? 0);
$seat_number  = (int)($input['seat_number'] ?? 0);
$source_stop  = sanitize($input['source_stop'] ?? '');
$dest_stop    = sanitize($input['dest_stop']   ?? '');
$passenger_id = (int)$_SESSION['user_id'];

if (!$schedule_id || !$seat_number || !$source_stop || !$dest_stop) {
    echo json_encode(['success'=>false,'message'=>'Missing required fields.']); exit;
}

$db = getDB();

// Check seat not already booked
$chk = $db->prepare("SELECT ticket_id FROM tickets WHERE schedule_id=? AND seat_number=? AND ticket_status='active'");
$chk->execute([$schedule_id, $seat_number]);
if ($chk->fetch()) {
    echo json_encode(['success'=>false,'message'=>'Seat already booked. Please choose another.']); exit;
}

// Get fare from route
$sched = $db->prepare("SELECT r.distance FROM schedules s JOIN routes r ON r.route_id=s.route_id WHERE s.schedule_id=?");
$sched->execute([$schedule_id]);
$row = $sched->fetch();
$fare = $row ? round($row['distance'] * 0.60, 2) : 20.00;

// Insert ticket
$qr_data = 'TNSTC-TKT-' . time() . '-' . $passenger_id . '-' . $seat_number;
$ins = $db->prepare("INSERT INTO tickets (passenger_id,schedule_id,seat_number,source_stop,dest_stop,fare,qr_code,payment_status,ticket_status) VALUES (?,?,?,?,?,?,?,?,?)");
$ins->execute([$passenger_id,$schedule_id,$seat_number,$source_stop,$dest_stop,$fare,$qr_data,'paid','active']);
$ticket_id = $db->lastInsertId();

// Update QR with ticket ID
$qr_data = "TNSTC-TKT-$ticket_id-$passenger_id-$schedule_id-SEAT$seat_number";
$db->prepare("UPDATE tickets SET qr_code=? WHERE ticket_id=?")->execute([$qr_data,$ticket_id]);

echo json_encode([
    'success'   => true,
    'ticket_id' => $ticket_id,
    'qr_data'   => $qr_data,
    'qr_url'    => qrCodeUrl($qr_data),
    'fare'      => $fare,
    'message'   => 'Ticket booked successfully!',
]);
