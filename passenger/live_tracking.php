<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$db = getDB();
$depots = $db->query("SELECT depot_id,depot_name FROM depots ORDER BY depot_name")->fetchAll();
$buses  = $db->query("SELECT bus_id,bus_number FROM buses WHERE status='active' ORDER BY bus_number")->fetchAll();
$extraHead = '<style>#map{height:500px;border-radius:12px;width:100%}</style>';
$pageTitle = 'Live Bus Tracking';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div class="main-content">

  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title"><i class="fa fa-map-location-dot me-2" style="color:var(--primary)"></i>Live Bus Tracking</div>
  </div>

  <div class="page-content">
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
              <span style="color:var(--primary)">● Blue bus</span> = On time &nbsp;
              <span style="color:#ffc107">● Yellow bus</span> = Delayed &nbsp;
              <span style="color:var(--primary)">● D marker</span> = Depot
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
  </div><!-- /.page-content -->

</div><!-- /.main-content -->
</div><!-- /.app-layout -->

<script>
window.TNSTC_DEPOTS = <?= json_encode(
  $db->query("SELECT depot_name AS name, latitude AS lat, longitude AS lng FROM depots")->fetchAll()
) ?>;
</script>
<script src="<?= APP_URL ?>/assets/js/tracking.js"></script>
<!-- Replace YOUR_KEY with Google Maps API key in config/db.php -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&callback=initMap" async defer
  onerror="document.getElementById('map').innerHTML='<div class=\'p-4 text-center text-muted\'><i class=\'fa fa-map fa-2x mb-2\'></i><br>Map requires Google Maps API key.<br><small>Add your key to config/db.php</small></div>'"></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
