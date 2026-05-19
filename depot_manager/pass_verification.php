<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('depot_manager');
$uid   = (int)currentUser()['id'];
$db    = getDB();
$depot = $db->query("SELECT * FROM depots WHERE manager_id=$uid LIMIT 1")->fetch();
if (!$depot) die('<p class="p-4 text-danger">No depot assigned.</p>');
$did   = (int)$depot['depot_id'];
$flash = getFlash();

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pid     = (int)$_POST['pass_id'];
    $action  = $_POST['action']??'';
    $remarks = sanitize($_POST['remarks']??'');
    if ($action==='approve') {
        $from = date('Y-m-d');
        $to   = date('Y-m-d', strtotime('+30 days'));
        $qr   = 'TNSTC-PASS-'.$pid.'-'.time();
        $db->prepare("UPDATE bus_pass SET status='approved',valid_from=?,valid_to=?,qr_code=?,remarks=?,verified_by=?,depot_id=? WHERE pass_id=?")
           ->execute([$from,$to,$qr,$remarks,$uid,$did,$pid]);
        redirectWithMessage(APP_URL.'/depot_manager/pass_verification.php','success','Pass approved successfully.');
    } elseif ($action==='reject') {
        $db->prepare("UPDATE bus_pass SET status='rejected',remarks=?,verified_by=?,depot_id=? WHERE pass_id=?")
           ->execute([$remarks,$uid,$did,$pid]);
        redirectWithMessage(APP_URL.'/depot_manager/pass_verification.php','success','Pass rejected.');
    }
}

$passes = $db->query("SELECT bp.*,u.name,u.email,u.phone FROM bus_pass bp JOIN users u ON u.user_id=bp.passenger_id WHERE (bp.depot_id=$did OR bp.depot_id IS NULL) ORDER BY FIELD(bp.status,'pending','approved','rejected'),bp.created_at DESC")->fetchAll();
$pageTitle = 'Pass Verification';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">🪪 Bus Pass Verification</div></div>
  <div class="page-content">
    <?php if (!empty($flash['msg'])): ?><div class="flash-banner flash-<?= $flash['type'] ?> mb-3"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-id-card me-2 text-success"></i>Pass Applications</h5></div>
      <?php if (empty($passes)): ?>
        <div class="empty-state p-5"><i class="fa fa-id-card"></i>No pass applications to review.</div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="data-table">
          <thead><tr><th>#</th><th>Passenger</th><th>Type</th><th>Route</th><th>Applied</th><th>Status</th><th>QR</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($passes as $p): $sc=['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$p['status']]??'secondary'; ?>
            <tr>
              <td><?= $p['pass_id'] ?></td>
              <td><strong><?= htmlspecialchars($p['name']) ?></strong><div style="font-size:11px;color:#6c757d"><?= htmlspecialchars($p['phone']) ?></div></td>
              <td><span class="badge-custom badge-primary"><?= ucfirst($p['pass_type']) ?></span></td>
              <td><?= htmlspecialchars($p['source'].' → '.$p['destination']) ?></td>
              <td><?= date('d M Y',strtotime($p['created_at'])) ?></td>
              <td><span class="badge-custom badge-<?= $sc ?>"><?= ucfirst($p['status']) ?></span></td>
              <td>
                <?php if ($p['status']==='approved' && $p['qr_code']): ?>
                  <a href="<?= qrCodeUrl($p['qr_code']) ?>" target="_blank" class="btn-icon view" title="View Pass QR"><i class="fa fa-qrcode"></i></a>
                <?php else: ?>—<?php endif; ?>
              </td>
              <td>
                <?php if ($p['status']==='pending'): ?>
                <button class="btn-icon view" title="Approve/Reject" onclick="openAction(<?= $p['pass_id'] ?>)" style="background:#e8f5ee;color:#1a6b3c"><i class="fa fa-check-circle"></i></button>
                <?php endif; ?>
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

<!-- Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header" style="background:#1a6b3c;color:#fff"><h5 class="modal-title">Verify Bus Pass</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="POST">
      <input type="hidden" name="pass_id" id="actionPassId">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Remarks (optional)</label>
          <textarea name="remarks" class="form-control" rows="2" placeholder="Any notes for the passenger…"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
        <button type="submit" name="action" value="approve" class="btn" style="background:#1a6b3c;color:#fff">Approve (30-day pass)</button>
      </div>
    </form>
  </div></div>
</div>
<script>
function openAction(id) {
  document.getElementById('actionPassId').value = id;
  new bootstrap.Modal(document.getElementById('actionModal')).show();
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
