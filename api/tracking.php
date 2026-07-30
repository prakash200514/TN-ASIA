<?php /
// api/tracking.php – Returns live bus locations
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

$db    = getDB();
$busId = (int)($_GET['bus_id'] ?? 0);
$depotId = (int)($_GET['depot_id'] ?? 0);

$where = 'WHERE lt.bus_id IS NOT NULL';
$params = [];
if ($busId)   { $where .= ' AND lt.bus_id = ?'; $params[] = $busId; }
if ($depotId) { $where .= ' AND b.depot_id = ?'; $params[] = $depotId; }

$sql = "
  SELECT lt.bus_id, lt.latitude, lt.longitude, lt.speed,
         DATE_FORMAT(lt.updated_at,'%H:%i:%s') AS updated_at,
         b.bus_number, b.bus_type,
         r.route_number, r.source, r.destination,
         COALESCE(s.delay_minutes,0) AS delay_minutes,
         s.status AS trip_status
  FROM live_tracking lt
  JOIN buses b ON b.bus_id = lt.bus_id
  LEFT JOIN schedules s ON s.bus_id = lt.bus_id AND s.travel_date = CURDATE()
    AND s.status IN ('scheduled','in_progress','delayed')
  LEFT JOIN routes r ON r.route_id = s.route_id
  $where
  ORDER BY lt.updated_at DESC
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
