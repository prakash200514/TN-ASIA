<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$db = getDB();
$depots = $db->query("SELECT depot_id,depot_name FROM depots ORDER BY depot_name")->fetchAll();
$buses  = $db->query("SELECT bus_id,bus_number FROM buses WHERE status='active' ORDER BY bus_number")->fetchAll();
// Pass depot info to JS for map markers
$depotJson = json_encode(array_map(fn($d)=>['name'=>$d['depot_name'],'lat'=>0,'lng'=>0],$depots));
$pageTitle = 'Live Bus Tracking';
$extraHead = '<style>#map{height:500px;width:100%;border-radius:12px}</style>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Live Tracking | TNSTC Tirunelveli</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="<?= APP_URL ?>/assets/css/tnstc.css" rel="stylesheet">
  <style>#map{height:500px;border-radius:12px}</style>
</head>
<body style="background:#f8f9fa;min-height:100vh">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div style="max-width:1200px;margin:0 auto;padding:24px 16px">
  <h1 style="font-size:20px;font-weight:700;margin-bottom:20px"><i class="fa fa-map-location-dot me-2 text-success"></i>Live Bus Tracking</h1>

  <div class="row g-4">
    <!-- Map -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header-custom">
          <i class="fa fa-map"></i> District Live Map
          <span class="ms-auto">
            <select id="busFilter" class="form-select form-select-sm" style="width:180px" onchange="pollBuses()">
              <option value="">All Buses</option>
              <?php foreach ($buses as $b): ?>
              <option value="<?= $b['bus_id'] ?>"><?= htmlspecialchars($b['bus_number']) ?></option>
              <?php endforeach; ?>
            </select>
          </span>
        </div>
        <div class="card-body-custom p-2">
          <div id="map"></div>
          <div style="font-size:11px;color:#6c757d;padding:8px">
            <span style="color:#1a6b3c">● Green bus</span> = On time &nbsp;
            <span style="color:#ffc107">● Yellow bus</span> = Delayed &nbsp;
            <span style="color:#1a6b3c">● D marker</span> = Depot
            &nbsp;|&nbsp; Auto-refreshes every 10 seconds
          </div>
        </div>
      </div>
    </div>

    <!-- Bus List -->
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header-custom"><i class="fa fa-bus"></i> Active Buses</div>
        <div id="busList" style="overflow-y:auto;max-height:500px">
          <div class="text-center p-4 text-muted">Loading bus data…</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
window.TNSTC_DEPOTS = <?= json_encode(
  $db->query("SELECT depot_name AS name, latitude AS lat, longitude AS lng FROM depots")->fetchAll()
) ?>;
</script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script src="<?= APP_URL ?>/assets/js/tracking.js"></script>
<!-- Replace YOUR_KEY with Google Maps API key in config/db.php, then update this src -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&callback=initMap" async defer
  onerror="document.getElementById('map').innerHTML='<div class=\'p-4 text-center text-muted\'><i class=\'fa fa-map fa-2x mb-2\'></i><br>Map requires Google Maps API key.<br><small>Add your key to config/db.php</small></div>'"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
