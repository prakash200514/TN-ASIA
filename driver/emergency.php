<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('driver_conductor');
$uid   = (int)currentUser()['id'];
$role  = currentUser()['role'];
$db    = getDB();
$flash = getFlash();
$tbl   = $role==='driver'?'drivers':'conductors';
$idc   = $role==='driver'?'driver_id':'conductor_id';
$rec   = $db->prepare("SELECT $idc,depot_id FROM $tbl WHERE user_id=?"); $rec->execute([$uid]);
$staff = $rec->fetch(); $sid=$staff?(int)$staff[$idc]:0; $depot_id=$staff?(int)$staff['depot_id']:0;

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $msg  = sanitize($_POST['message']??'');
    $lat  = (float)($_POST['latitude']??0);
    $lng  = (float)($_POST['longitude']??0);
    $busq = $db->query("SELECT bus_id FROM schedules WHERE driver_id=$sid AND travel_date=CURDATE() AND status='in_progress' LIMIT 1");
    $bus  = $busq->fetch();
    $bid  = $bus?(int)$bus['bus_id']:0;
    $db->prepare("INSERT INTO emergency_alerts (bus_id,driver_id,depot_id,message,latitude,longitude,status) VALUES (?,?,?,?,?,?,?)")
       ->execute([$bid,$sid,$depot_id,$msg,$lat?:null,$lng?:null,'active']);
    redirectWithMessage(APP_URL.'/driver/emergency.php','success','Emergency alert sent to depot manager and admin!');
}

$myAlerts = $db->query("SELECT ea.*,b.bus_number FROM emergency_alerts ea LEFT JOIN buses b ON b.bus_id=ea.bus_id WHERE ea.driver_id=$sid ORDER BY ea.created_at DESC")->fetchAll();
$pageTitle = 'Emergency Alert';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_driver.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">🚨 Emergency Alert</div></div>
  <div class="page-content">
    <?php if (!empty($flash['msg'])): ?><div class="flash-banner flash-<?= $flash['type'] ?> mb-3"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>

    <!-- Alert form -->
    <div class="card mb-4" style="max-width:600px;border:2px solid #dc3545">
      <div class="card-header-custom" style="background:#dc3545;color:#fff">
        <i class="fa fa-triangle-exclamation"></i> Send Emergency Alert
      </div>
      <div class="card-body-custom">
        <div class="flash-banner flash-danger mb-3">
          <i class="fa fa-info-circle"></i> This will immediately notify your depot manager and admin.
        </div>
        <form method="POST" id="emergencyForm" class="row g-3">
          <input type="hidden" name="latitude" id="lat">
          <input type="hidden" name="longitude" id="lng">
          <div class="col-12">
            <label class="form-label">Emergency Message</label>
            <textarea name="message" class="form-control-custom" rows="3" placeholder="Describe the emergency: accident, medical emergency, bus breakdown…" required></textarea>
          </div>
          <div class="col-12">
            <button type="button" onclick="getLocation()" class="btn-accent-custom btn-sm-custom me-2">
              <i class="fa fa-location-dot"></i> Attach My GPS Location
            </button>
            <span id="gpsStatus" style="font-size:12px;color:#6c757d"></span>
          </div>
          <div class="col-12">
            <button type="submit" class="btn-primary-custom" style="background:#dc3545;width:100%;justify-content:center;font-size:15px;padding:14px">
              <i class="fa fa-triangle-exclamation"></i> SEND EMERGENCY ALERT NOW
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Past alerts -->
    <?php if (!empty($myAlerts)): ?>
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-history me-2"></i>My Previous Alerts</h5></div>
      <div class="table-responsive">
        <table class="data-table">
          <thead><tr><th>Alert ID</th><th>Bus</th><th>Message</th><th>Status</th><th>Time</th></tr></thead>
          <tbody>
            <?php foreach ($myAlerts as $a): ?>
            <tr>
              <td>#<?= $a['alert_id'] ?></td>
              <td><?= htmlspecialchars($a['bus_number']??'N/A') ?></td>
              <td><?= htmlspecialchars(substr($a['message'],0,60)) ?>…</td>
              <td><span class="badge-custom badge-<?= $a['status']==='active'?'danger':'success' ?>"><?= ucfirst($a['status']) ?></span></td>
              <td style="font-size:12px"><?= date('d M, h:i A',strtotime($a['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
</div>
<script>
function getLocation() {
  if (navigator.geolocation) {
    document.getElementById('gpsStatus').textContent = 'Getting location…';
    navigator.geolocation.getCurrentPosition(pos => {
      document.getElementById('lat').value = pos.coords.latitude;
      document.getElementById('lng').value = pos.coords.longitude;
      document.getElementById('gpsStatus').textContent = `✓ Location attached (${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)})`;
      document.getElementById('gpsStatus').style.color = '#166534';
    }, () => {
      document.getElementById('gpsStatus').textContent = 'Location unavailable.';
    });
  }
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
