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
$staff = $rec->fetch();
// Get current bus from today's active schedule
$busq  = $db->query("SELECT bus_id FROM schedules WHERE ".($role==='driver'?'driver_id':'conductor_id')."=".($staff?(int)$staff[$idc]:0)." AND travel_date=CURDATE() AND status='in_progress' LIMIT 1");
$busr  = $busq->fetch();
$bus_id = $busr?(int)$busr['bus_id']:0;

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = sanitize($_POST['item_name']??'');
    $desc = sanitize($_POST['description']??'');
    $db->prepare("INSERT INTO lost_found (reported_by,report_type,item_name,description,bus_id,depot_id,status) VALUES (?,?,?,?,?,?,?)")
       ->execute([$uid,'found',$name,$desc,$bus_id?:null,$staff?$staff['depot_id']:null,'open']);
    redirectWithMessage(APP_URL.'/driver/lost_item_report.php','success','Found item reported to depot. Passenger can now claim it.');
}

$myReports = $db->query("SELECT lf.*,b.bus_number FROM lost_found lf LEFT JOIN buses b ON b.bus_id=lf.bus_id WHERE lf.reported_by=$uid AND lf.report_type='found' ORDER BY lf.created_at DESC LIMIT 20")->fetchAll();
$pageTitle = 'Report Found Item';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_driver.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">📦 Report Found Item</div></div>
  <div class="page-content">
    <?php if (!empty($flash['msg'])): ?><div class="flash-banner flash-<?= $flash['type'] ?> mb-3"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header-custom"><i class="fa fa-box-open"></i> Report an Item Found in Bus</div>
          <div class="card-body-custom">
            <?php if ($bus_id): ?><div class="flash-banner flash-info mb-3"><i class="fa fa-info-circle"></i> Reporting for current active bus (Bus ID: <?= $bus_id ?>)</div><?php endif; ?>
            <form method="POST" class="row g-3">
              <div class="col-12">
                <label class="form-label">Item Name</label>
                <input type="text" name="item_name" class="form-control-custom" placeholder="e.g. Mobile Phone, Umbrella, Bag" required>
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control-custom" rows="3" placeholder="Describe the item (color, brand, contents…)" required></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-primary-custom"><i class="fa fa-paper-plane"></i> Report Found Item</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="table-card">
          <div class="table-header"><h5>My Recent Reports</h5></div>
          <?php if (empty($myReports)): ?><div class="empty-state p-4"><i class="fa fa-box-open"></i>No reports yet.</div>
          <?php else: ?>
          <?php foreach ($myReports as $r): ?>
          <div style="padding:12px 16px;border-bottom:1px solid #f1f3f5">
            <strong style="font-size:13px"><?= htmlspecialchars($r['item_name']) ?></strong>
            <span class="badge-custom badge-<?= $r['status']==='open'?'warning':($r['status']==='claimed'?'success':'secondary') ?> ms-2"><?= ucfirst($r['status']) ?></span>
            <div style="font-size:12px;color:#6c757d"><?= htmlspecialchars($r['description']) ?></div>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
