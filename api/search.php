<?php
// api/search.php – Returns bus schedules matching source/destination
require_once __DIR__ . '/../config/db.php';
startAppSession();
header('Content-Type: application/json');

$src  = trim($_GET['src']  ?? '');
$dst  = trim($_GET['dst']  ?? '');
$date = $_GET['date'] ?? date('Y-m-d');

if (!$src || !$dst) { echo json_encode([]); exit; }

$db = getDB();
$sql = "
  SELECT s.schedule_id, s.departure_time, s.arrival_time, s.status, s.delay_minutes,
         b.bus_number, b.bus_type, b.total_seats, b.bus_id,
         r.route_number, r.source, r.destination, r.distance, r.estimated_time,
         d.depot_name,
         u1.name AS driver_name, u2.name AS conductor_name,
         (SELECT COUNT(*) FROM tickets t WHERE t.schedule_id=s.schedule_id AND t.ticket_status='active') AS booked_seats
  FROM schedules s
  JOIN buses b ON b.bus_id = s.bus_id
  JOIN routes r ON r.route_id = s.route_id
  JOIN depots d ON d.depot_id = b.depot_id
  LEFT JOIN drivers dr ON dr.driver_id = s.driver_id
  LEFT JOIN users u1 ON u1.user_id = dr.user_id
  LEFT JOIN conductors c ON c.conductor_id = s.conductor_id
  LEFT JOIN users u2 ON u2.user_id = c.user_id
  WHERE s.travel_date = ?
    AND s.status NOT IN ('cancelled','completed')
    AND (
      (r.source LIKE ? AND r.destination LIKE ?)
      OR EXISTS (
        SELECT 1 FROM route_stops rs1
        JOIN route_stops rs2 ON rs2.route_id=rs1.route_id AND rs2.stop_order > rs1.stop_order
        WHERE rs1.route_id=r.route_id AND rs1.stop_name LIKE ? AND rs2.stop_name LIKE ?
      )
    )
  ORDER BY s.departure_time
";
$like_src = "%$src%";
$like_dst = "%$dst%";
$stmt = $db->prepare($sql);
$stmt->execute([$date, $like_src, $like_dst, $like_src, $like_dst]);
$rows = $stmt->fetchAll();

foreach ($rows as &$r) {
    $r['available_seats'] = max(0, (int)$r['total_seats'] - (int)$r['booked_seats']);
    $r['fare_per_km'] = 0.60; // ₹0.60/km base
    $r['fare'] = round($r['distance'] * $r['fare_per_km'], 2);
}
echo json_encode($rows);
