<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$uid   = (int)currentUser()['id'];
$db    = getDB();
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat  = sanitize($_POST['category'] ?? '');
    $desc = sanitize($_POST['description'] ?? '');
    $did  = (int)($_POST['depot_id'] ?? 0);
    $db->prepare("INSERT INTO complaints (passenger_id,depot_id,category,description,status) VALUES (?,?,?,?,?)")
       ->execute([$uid,$did?:null,$cat,$desc,'open']);
    redirectWithMessage(APP_URL.'/passenger/complaints.php','success','Complaint submitted. We will respond within 2 working days.');
}

$complaints = $db->query("SELECT c.*,d.depot_name FROM complaints c LEFT JOIN depots d ON d.depot_id=c.depot_id WHERE c.passenger_id=$uid ORDER BY c.created_at DESC")->fetchAll();
$depots     = $db->query("SELECT depot_id,depot_name FROM depots ORDER BY depot_name")->fetchAll();
$pageTitle  = 'Complaints & Feedback';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div class="main-content">

  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title"><i class="fa fa-comments me-2" style="color:var(--primary)"></i>Complaints & Feedback</div>
  </div>

  <div class="page-content">
  <?php if (!empty($flash['msg'])): ?><div class="flash-banner flash-<?= $flash['type'] ?> mb-3"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>

  <ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#submitTab">Submit Complaint</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#myTab">My Complaints (<?= count($complaints) ?>)</a></li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane fade show active" id="submitTab">
      <div class="card">
        <div class="card-header-custom"><i class="fa fa-paper-plane"></i> New Complaint / Feedback</div>
        <div class="card-body-custom">
          <form method="POST" class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">Category</label>
              <select name="category" class="form-control-custom" required>
                <option value="">Select category…</option>
                <option value="delay">Bus Delay</option>
                <option value="staff_behavior">Staff Behavior</option>
                <option value="cleanliness">Bus Cleanliness</option>
                <option value="ticket_issue">Ticket Issue</option>
                <option value="route_issue">Route Issue</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Related Depot (optional)</label>
              <select name="depot_id" class="form-control-custom">
                <option value="">-- Select Depot --</option>
                <?php foreach ($depots as $d): ?>
                <option value="<?= $d['depot_id'] ?>"><?= htmlspecialchars($d['depot_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control-custom" rows="4" placeholder="Describe the issue in detail…" required style="resize:vertical"></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn-primary-custom"><i class="fa fa-paper-plane"></i> Submit Complaint</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="myTab">
      <?php if (empty($complaints)): ?>
        <div class="empty-state card"><i class="fa fa-comments"></i>No complaints submitted yet.</div>
      <?php else: ?>
      <?php foreach ($complaints as $c):
        $statusColors=['open'=>['#991b1b','#fee2e2'],'in_progress'=>['#854d0e','#fef9c3'],'resolved'=>['#166534','#dcfce7'],'closed'=>['#6c757d','#f8f9fa']];
        [$tc,$bg]=$statusColors[$c['status']]??['#6c757d','#f8f9fa'];
      ?>
      <div class="card mb-3">
        <div class="card-body-custom">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <strong><?= ucfirst(str_replace('_',' ',$c['category'])) ?></strong>
              <?php if ($c['depot_name']): ?><span class="text-muted" style="font-size:12px"> · <?= htmlspecialchars($c['depot_name']) ?></span><?php endif; ?>
              <div style="font-size:12px;color:#6c757d"><?= date('d M Y, h:i A',strtotime($c['created_at'])) ?></div>
            </div>
            <span class="badge-custom" style="background:<?= $bg ?>;color:<?= $tc ?>"><?= ucfirst(str_replace('_',' ',$c['status'])) ?></span>
          </div>
          <p style="margin:10px 0 0;font-size:14px"><?= nl2br(htmlspecialchars($c['description'])) ?></p>
          <?php if ($c['reply']): ?>
          <div class="mt-3 p-3 rounded" style="background:var(--primary-light);border-left:3px solid var(--primary)">
            <strong style="font-size:12px;color:var(--primary)">Depot Reply:</strong>
            <p style="margin:4px 0 0;font-size:13px"><?= nl2br(htmlspecialchars($c['reply'])) ?></p>
          </div>
          <?php endif; ?>
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
