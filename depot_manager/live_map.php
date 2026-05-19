<?php
// depot_manager/live_map.php
require_once __DIR__ . '/../config/db.php';
requireLogin('depot_manager');
$uid=(int)currentUser()['id']; $db=getDB();
$depot=$db->query("SELECT * FROM depots WHERE manager_id=$uid LIMIT 1")->fetch(); if(!$depot) die('No depot.');
$did=(int)$depot['depot_id'];
$buses=$db->query("SELECT bus_id,bus_number FROM buses WHERE depot_id=$did AND status='active'")->fetchAll();
$pageTitle='Live Map'; 
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Live Map | Depot Manager</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="<?=APP_URL?>/assets/css/tnstc.css" rel="stylesheet">
<style>#map{height:500px;border-radius:12px}</style>
</head>
<body>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">🗺 Live Bus Map – <?=htmlspecialchars($depot['depot_name'])?></div></div>
  <div class="page-content">
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card"><div class="card-header-custom"><i class="fa fa-map"></i> Live Tracking
          <span class="ms-auto"><select id="busFilter" class="form-select form-select-sm" style="width:170px" onchange="pollBuses()"><option value="">All Depot Buses</option><?php foreach($buses as $b): ?><option value="<?=$b['bus_id']?>"><?=htmlspecialchars($b['bus_number'])?></option><?php endforeach; ?></select></span>
        </div>
        <div class="card-body-custom p-2"><div id="map"></div></div></div>
      </div>
      <div class="col-lg-4"><div class="card h-100"><div class="card-header-custom"><i class="fa fa-bus"></i> Depot Buses</div><div id="busList" style="overflow-y:auto;max-height:500px"><div class="p-3 text-muted">Loading…</div></div></div></div>
    </div>
  </div>
</div></div>
<script>
window.TNSTC_DEPOTS=[{name:'<?=addslashes($depot['depot_name'])?>',lat:<?=(float)$depot['latitude']?>,lng:<?=(float)$depot['longitude']?>}];
// Pass depot_id to tracking API
const origPollBuses = typeof pollBuses !== 'undefined' ? pollBuses : null;
</script>
<script src="<?=APP_URL?>/assets/js/main.js"></script>
<script src="<?=APP_URL?>/assets/js/tracking.js"></script>
<script>
// Override pollBuses to filter by depot
async function pollBuses() {
  const busId = document.getElementById('busFilter')?.value||'';
  const url = `/TNSTC/api/tracking.php?depot_id=<?=$did?>${busId?'&bus_id='+busId:''}`;
  try{const buses=await apiGet(url);buses.forEach(b=>updateBusMarker(b));updateBusList(buses);}catch(_){}
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=GOOGLE_MAPS_API_KEY?>&callback=initMap" async defer
  onerror="document.getElementById('map').innerHTML='<div class=\'p-4 text-center\'>Map requires Google Maps API key.</div>'"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
