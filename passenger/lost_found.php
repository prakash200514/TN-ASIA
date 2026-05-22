<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$uid   = (int)currentUser()['id'];
$db    = getDB();
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['item_name'] ?? '');
    $desc = sanitize($_POST['description'] ?? '');
    $bid  = (int)($_POST['bus_id'] ?? 0);
    $db->prepare("INSERT INTO lost_found (reported_by,report_type,item_name,description,bus_id,status) VALUES (?,?,?,?,?,?)")
       ->execute([$uid,'lost',$name,$desc,$bid?:null,'open']);
    redirectWithMessage(APP_URL.'/passenger/lost_found.php','success','Lost item reported. We will notify you if it is found.');
}

$myItems   = $db->query("SELECT lf.*,b.bus_number FROM lost_found lf LEFT JOIN buses b ON b.bus_id=lf.bus_id WHERE lf.reported_by=$uid ORDER BY lf.created_at DESC")->fetchAll();
$foundItems= $db->query("SELECT lf.*,b.bus_number,d.depot_name FROM lost_found lf LEFT JOIN buses b ON b.bus_id=lf.bus_id LEFT JOIN depots d ON d.depot_id=lf.depot_id WHERE lf.report_type='found' AND lf.status='open' ORDER BY lf.created_at DESC")->fetchAll();
$buses     = $db->query("SELECT bus_id,bus_number FROM buses WHERE status='active' ORDER BY bus_number")->fetchAll();
$pageTitle = 'Lost & Found';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div class="main-content">

  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title"><i class="fa fa-box-open me-2" style="color:var(--primary)"></i>Lost & Found</div>
  </div>

  <div class="page-content">
  <?php if (!empty($flash['msg'])): ?><div class="flash-banner flash-<?= $flash['type'] ?> mb-3"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>

  <ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#reportTab">Report Lost Item</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#foundTab">Found Items (<?= count($foundItems) ?>)</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#myTab">My Reports (<?= count($myItems) ?>)</a></li>
  </ul>

  <div class="tab-content">
    <!-- Report -->
    <div class="tab-pane fade show active" id="reportTab">
      <div class="card">
        <div class="card-header-custom"><i class="fa fa-search"></i> Report a Lost Item</div>
        <div class="card-body-custom">
          <form method="POST" class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">Item Name</label>
              <input type="text" name="item_name" class="form-control-custom" placeholder="e.g. Black Wallet, Blue Bag" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Bus Number (if known)</label>
              <select name="bus_id" class="form-control-custom">
                <option value="">-- Select bus --</option>
                <?php foreach ($buses as $b): ?><option value="<?= $b['bus_id'] ?>"><?= htmlspecialchars($b['bus_number']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control-custom" rows="3" placeholder="Describe the item and when/where it was lost…" required></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn-primary-custom"><i class="fa fa-paper-plane"></i> Report Lost Item</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Found Items -->
    <div class="tab-pane fade" id="foundTab">
      <?php if (empty($foundItems)): ?>
        <div class="empty-state card"><i class="fa fa-box-open"></i>No found items reported currently.</div>
      <?php else: ?>
      <div class="row g-3">
        <?php foreach ($foundItems as $fi): ?>
        <div class="col-md-6">
          <div class="card" style="border-left:4px solid var(--primary)">
            <div class="card-body-custom">
              <strong><?= htmlspecialchars($fi['item_name']) ?></strong>
              <?php if ($fi['bus_number']): ?><span class="badge-custom badge-primary ms-2"><?= htmlspecialchars($fi['bus_number']) ?></span><?php endif; ?>
              <?php if ($fi['depot_name']): ?><div style="font-size:12px;color:#6c757d"><?= htmlspecialchars($fi['depot_name']) ?></div><?php endif; ?>
              <p style="font-size:13px;margin:8px 0 0"><?= nl2br(htmlspecialchars($fi['description'])) ?></p>
              <div style="font-size:11px;color:#6c757d;margin-top:6px"><?= date('d M Y',strtotime($fi['created_at'])) ?></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- My Reports -->
    <div class="tab-pane fade" id="myTab">
      <?php if (empty($myItems)): ?>
        <div class="empty-state card"><i class="fa fa-box-open"></i>No items reported by you.</div>
      <?php else: ?>
      <?php foreach ($myItems as $m): ?>
      <div class="card mb-3">
        <div class="card-body-custom d-flex justify-content-between align-items-start gap-3">
          <div>
            <strong><?= htmlspecialchars($m['item_name']) ?></strong>
            <?php if ($m['bus_number']): ?><span class="badge-custom badge-secondary ms-2"><?= htmlspecialchars($m['bus_number']) ?></span><?php endif; ?>
            <p style="font-size:13px;margin:6px 0 0"><?= nl2br(htmlspecialchars($m['description'])) ?></p>
            <div style="font-size:11px;color:#6c757d;margin-top:4px"><?= date('d M Y',strtotime($m['created_at'])) ?></div>
          </div>
          <span class="badge-custom badge-<?= $m['status']==='open'?'warning':($m['status']==='claimed'?'success':'secondary') ?>"><?= ucfirst($m['status']) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  </div><!-- /.page-content -->
</div><!-- /.main-content -->
</div><!-- /.app-layout -->
<?php include __DIR__ . '/../includes/footer.php'; ?>
