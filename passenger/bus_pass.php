<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$uid = (int)currentUser()['id'];
$db  = getDB();
$flash = getFlash();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type  = in_array($_POST['pass_type'],['monthly','student']) ? $_POST['pass_type'] : 'monthly';
    $src   = sanitize($_POST['source'] ?? '');
    $dst   = sanitize($_POST['destination'] ?? '');
    $doc   = '';
    if (!empty($_FILES['proof_document']['name'])) {
        $ext   = pathinfo($_FILES['proof_document']['name'], PATHINFO_EXTENSION);
        $fname = 'proof_'.$uid.'_'.time().'.'.$ext;
        $dir   = __DIR__.'/../uploads/proofs/';
        if (!is_dir($dir)) mkdir($dir,0755,true);
        move_uploaded_file($_FILES['proof_document']['tmp_name'], $dir.$fname);
        $doc = $fname;
    }
    $db->prepare("INSERT INTO bus_pass (passenger_id,pass_type,source,destination,proof_document,status) VALUES (?,?,?,?,?,?)")
       ->execute([$uid,$type,$src,$dst,$doc,'pending']);
    redirectWithMessage(APP_URL.'/passenger/bus_pass.php','success','Pass application submitted! Awaiting depot verification.');
}

$passes = $db->query("SELECT * FROM bus_pass WHERE passenger_id=$uid ORDER BY created_at DESC")->fetchAll();
$pageTitle = 'Bus Pass';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div class="main-content">

  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title"><i class="fa fa-id-card me-2" style="color:var(--primary)"></i>Bus Pass</div>
  </div>

  <div class="page-content">
  <?php if (!empty($flash['msg'])): ?>
    <div class="flash-banner flash-<?= $flash['type'] ?> mb-3"><?= htmlspecialchars($flash['msg']) ?></div>
  <?php endif; ?>

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-4" id="passTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#applyTab">Apply for Pass</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#statusTab">My Applications (<?= count($passes) ?>)</a></li>
  </ul>

  <div class="tab-content">
    <!-- Apply Tab -->
    <div class="tab-pane fade show active" id="applyTab">
      <div class="card">
        <div class="card-header-custom"><i class="fa fa-plus-circle"></i> Apply for Bus Pass</div>
        <div class="card-body-custom">
          <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">Pass Type</label>
              <select name="pass_type" class="form-control-custom" required>
                <option value="monthly">Monthly Pass</option>
                <option value="student">Student Pass</option>
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Upload ID Proof</label>
              <input type="file" name="proof_document" class="form-control-custom" accept=".jpg,.jpeg,.png,.pdf" required>
              <div style="font-size:11px;color:#6c757d;margin-top:4px">Student ID / Aadhaar (JPG/PNG/PDF)</div>
            </div>
            <div class="col-sm-6">
              <label class="form-label">From Stop</label>
              <input type="text" name="source" class="form-control-custom" placeholder="Tirunelveli Junction" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label">To Stop</label>
              <input type="text" name="destination" class="form-control-custom" placeholder="Valliyoor" required>
            </div>
            <div class="col-12">
              <button type="submit" class="btn-primary-custom"><i class="fa fa-paper-plane"></i> Submit Application</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Status Tab -->
    <div class="tab-pane fade" id="statusTab">
      <?php if (empty($passes)): ?>
        <div class="empty-state card"><i class="fa fa-id-card"></i>No pass applications yet.</div>
      <?php else: ?>
      <div class="row g-3">
        <?php foreach ($passes as $p):
          $colors=['pending'=>['#854d0e','#fef9c3'],'approved'=>['#166534','#dcfce7'],'rejected'=>['#991b1b','#fee2e2']];
          [$tc,$bg] = $colors[$p['status']];
        ?>
        <div class="col-md-6">
          <div class="card" style="border-left:4px solid <?= $tc ?>">
            <div class="card-body-custom">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <strong style="font-size:15px"><?= ucfirst($p['pass_type']) ?> Pass</strong>
                  <div style="font-size:12px;color:#6c757d">Applied: <?= date('d M Y',strtotime($p['created_at'])) ?></div>
                </div>
                <span class="badge-custom" style="background:<?= $bg ?>;color:<?= $tc ?>"><?= ucfirst($p['status']) ?></span>
              </div>
              <div class="ticket-row"><span class="label">Route</span><span class="value"><?= htmlspecialchars($p['source'].' → '.$p['destination']) ?></span></div>
              <?php if ($p['status']==='approved'): ?>
              <div class="ticket-row"><span class="label">Valid</span><span class="value"><?= $p['valid_from'].' to '.$p['valid_to'] ?></span></div>
              <div class="ticket-qr mt-2 text-center">
                <img src="<?= qrCodeUrl('TNSTC-PASS-'.$p['pass_id'].'-'.$p['passenger_id']) ?>" style="width:100px;height:100px" alt="Pass QR">
              </div>
              <?php endif; ?>
              <?php if ($p['remarks']): ?>
              <div class="mt-2 p-2 rounded" style="background:#f8f9fa;font-size:12px"><strong>Remarks:</strong> <?= htmlspecialchars($p['remarks']) ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  </div><!-- /.page-content -->
</div><!-- /.main-content -->
</div><!-- /.app-layout -->
<?php include __DIR__ . '/../includes/footer.php'; ?>
