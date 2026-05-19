<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('driver_conductor');
$uid  = (int)currentUser()['id'];
$role = currentUser()['role'];
$db   = getDB();

$col = $role==='driver'?'driver_id':'conductor_id';
$tbl = $role==='driver'?'drivers':'conductors';
$idc = $role==='driver'?'driver_id':'conductor_id';
$rec = $db->prepare("SELECT $idc FROM $tbl WHERE user_id=?");
$rec->execute([$uid]); $staff = $rec->fetch();
$sid = $staff ? (int)$staff[$idc] : 0;

$schedules = $db->query("SELECT s.*,r.source,r.destination,r.route_number,r.distance,b.bus_number,b.bus_type FROM schedules s JOIN routes r ON r.route_id=s.route_id JOIN buses b ON b.bus_id=s.bus_id WHERE s.$col=$sid ORDER BY s.travel_date DESC,s.departure_time")->fetchAll();

// Handle status update
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_status'])) {
    $scid   = (int)$_POST['schedule_id'];
    $status = in_array($_POST['status'],['scheduled','in_progress','completed','cancelled','delayed'])?$_POST['status']:'scheduled';
    $db->prepare("UPDATE schedules SET status=? WHERE schedule_id=?")->execute([$status,$scid]);
    redirectWithMessage(APP_URL.'/driver/assigned_trips.php','success','Trip status updated.');
}

$pageTitle = 'Assigned Trips';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_driver.php'; ?>
<div class="main-content">
  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title">📋 Assigned Trips</div>
  </div>
  <div class="page-content">
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-calendar-check me-2 text-success"></i>All Assigned Trips</h5></div>
      <?php if (empty($schedules)): ?>
        <div class="empty-state"><i class="fa fa-calendar"></i>No trips assigned to you.</div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="data-table">
          <thead><tr><th>#</th><th>Route</th><th>Bus</th><th>Date</th><th>Departs</th><th>Status</th><th>Update</th></tr></thead>
          <tbody>
            <?php foreach ($schedules as $t): ?>
            <tr>
              <td><?= $t['schedule_id'] ?></td>
              <td><strong><?= htmlspecialchars($t['route_number']) ?></strong><br><small class="text-muted"><?= htmlspecialchars($t['source'].' → '.$t['destination']) ?></small></td>
              <td><?= htmlspecialchars($t['bus_number']) ?></td>
              <td><?= htmlspecialchars($t['travel_date']) ?></td>
              <td><?= htmlspecialchars(substr($t['departure_time'],0,5)) ?></td>
              <td><span class="badge-custom badge-<?= $t['status']==='scheduled'?'info':($t['status']==='in_progress'?'success':($t['status']==='completed'?'secondary':'warning')) ?>"><?= ucfirst($t['status']) ?></span></td>
              <td>
                <form method="POST" style="display:flex;gap:4px;align-items:center">
                  <input type="hidden" name="schedule_id" value="<?= $t['schedule_id'] ?>">
                  <select name="status" class="form-select form-select-sm" style="width:130px">
                    <?php foreach (['scheduled','in_progress','completed','delayed','cancelled'] as $st): ?>
                    <option value="<?= $st ?>" <?= $t['status']===$st?'selected':'' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" name="update_status" class="btn-icon edit" title="Save"><i class="fa fa-check"></i></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
