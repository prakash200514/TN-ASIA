<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('driver_conductor');
$uid   = (int)currentUser()['id'];
$role  = currentUser()['role'];
$db    = getDB();
$flash = getFlash();
$tbl   = $role==='driver'?'drivers':'conductors';
$idc   = $role==='driver'?'driver_id':'conductor_id';
$rec   = $db->prepare("SELECT $idc FROM $tbl WHERE user_id=?"); $rec->execute([$uid]);
$staff = $rec->fetch(); $sid = $staff?(int)$staff[$idc]:0;
$col   = $role==='driver'?'driver_id':'conductor_id';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $scid   = (int)$_POST['schedule_id'];
    $reason = sanitize($_POST['delay_reason']??'');
    $mins   = (int)($_POST['delay_minutes']??0);
    $db->prepare("UPDATE schedules SET status='delayed',delay_reason=?,delay_minutes=? WHERE schedule_id=? AND $col=?")
       ->execute([$reason,$mins,$scid,$sid]);
    redirectWithMessage(APP_URL.'/driver/delay_update.php','success','Delay reported successfully.');
}

$todaySchedules = $db->query("SELECT s.*,r.source,r.destination,b.bus_number FROM schedules s JOIN routes r ON r.route_id=s.route_id JOIN buses b ON b.bus_id=s.bus_id WHERE s.$col=$sid AND s.travel_date=CURDATE() AND s.status NOT IN ('completed','cancelled')")->fetchAll();
$preSelected = (int)($_GET['schedule_id']??0);
$pageTitle = 'Delay Update';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_driver.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">⏱ Delay Update</div></div>
  <div class="page-content">
    <?php if (!empty($flash['msg'])): ?><div class="flash-banner flash-<?= $flash['type'] ?> mb-3"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <div class="card" style="max-width:600px">
      <div class="card-header-custom"><i class="fa fa-clock"></i> Report Bus Delay</div>
      <div class="card-body-custom">
        <?php if (empty($todaySchedules)): ?>
          <div class="empty-state"><i class="fa fa-calendar"></i>No active trips today to report delay for.</div>
        <?php else: ?>
        <form method="POST" class="row g-3">
          <div class="col-12">
            <label class="form-label">Select Trip</label>
            <select name="schedule_id" class="form-control-custom" required>
              <?php foreach ($todaySchedules as $t): ?>
              <option value="<?= $t['schedule_id'] ?>" <?= $preSelected===$t['schedule_id']?'selected':'' ?>>
                #<?= $t['schedule_id'] ?> – <?= htmlspecialchars($t['source'].' → '.$t['destination']) ?> (<?= $t['bus_number'] ?>)
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Delay Duration (minutes)</label>
            <input type="number" name="delay_minutes" class="form-control-custom" min="1" max="240" placeholder="e.g. 20" required>
          </div>
          <div class="col-12">
            <label class="form-label">Reason for Delay</label>
            <textarea name="delay_reason" class="form-control-custom" rows="3" placeholder="Traffic, mechanical issue, road block…" required></textarea>
          </div>
          <div class="col-12">
            <button type="submit" class="btn-primary-custom"><i class="fa fa-clock"></i> Submit Delay Report</button>
          </div>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
