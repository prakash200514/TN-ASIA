<?php
// admin/manage_passes.php
require_once __DIR__ . '/../config/db.php';
requireLogin('admin'); $db=getDB(); $flash=getFlash();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pid=(int)$_POST['pass_id']; $act=sanitize($_POST['action']??'');
    $remarks=sanitize($_POST['remarks']??'');
    if ($act==='approve') {
        $from=date('Y-m-d'); $to=date('Y-m-d',strtotime('+30 days'));
        $qr='TNSTC-PASS-'.$pid.'-'.time();
        $db->prepare("UPDATE bus_pass SET status='approved',valid_from=?,valid_to=?,qr_code=?,remarks=?,verified_by=? WHERE pass_id=?")->execute([$from,$to,$qr,$remarks,(int)currentUser()['id'],$pid]);
        redirectWithMessage(APP_URL.'/admin/manage_passes.php','success','Pass approved.');
    } elseif ($act==='reject') {
        $db->prepare("UPDATE bus_pass SET status='rejected',remarks=?,verified_by=? WHERE pass_id=?")->execute([$remarks,(int)currentUser()['id'],$pid]);
        redirectWithMessage(APP_URL.'/admin/manage_passes.php','success','Pass rejected.');
    }
}
$passes=$db->query("SELECT bp.*,u.name,u.phone FROM bus_pass bp JOIN users u ON u.user_id=bp.passenger_id ORDER BY FIELD(bp.status,'pending','approved','rejected'),bp.created_at DESC")->fetchAll();
$pageTitle='Manage Passes'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">🪪 Manage Bus Passes</div></div>
  <div class="page-content">
    <?php if(!empty($flash['msg'])): ?><div class="flash-banner flash-<?=$flash['type']?> mb-3"><?=htmlspecialchars($flash['msg'])?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-id-card me-2 text-success"></i>Bus Pass Applications (<?=count($passes)?>)</h5></div>
      <div class="table-responsive"><table class="data-table">
        <thead><tr><th>#</th><th>Passenger</th><th>Type</th><th>Route</th><th>Applied</th><th>Valid</th><th>Status</th><th>QR</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach($passes as $p): $sc=['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$p['status']]??'secondary'; ?>
          <tr>
            <td><?=$p['pass_id']?></td>
            <td><?=htmlspecialchars($p['name'])?><div style="font-size:11px;color:#6c757d"><?=htmlspecialchars($p['phone'])?></div></td>
            <td><span class="badge-custom badge-primary"><?=ucfirst($p['pass_type'])?></span></td>
            <td><?=htmlspecialchars($p['source'].' → '.$p['destination'])?></td>
            <td style="font-size:12px"><?=date('d M Y',strtotime($p['created_at']))?></td>
            <td style="font-size:12px"><?=$p['valid_from']?$p['valid_from'].' to '.$p['valid_to']:'—'?></td>
            <td><span class="badge-custom badge-<?=$sc?>"><?=ucfirst($p['status'])?></span></td>
            <td><?=$p['qr_code']?'<a href="'.qrCodeUrl($p['qr_code']).'" target="_blank" class="btn-icon view"><i class="fa fa-qrcode"></i></a>':'—'?></td>
            <td>
              <?php if($p['status']==='pending'): ?>
              <button class="btn-icon view" onclick="openAction(<?=$p['pass_id']?>)"><i class="fa fa-check-circle"></i></button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div></div>
<div class="modal fade" id="actModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header" style="background:#1a6b3c;color:#fff"><h5>Verify Pass</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <form method="POST"><input type="hidden" name="pass_id" id="actPassId">
  <div class="modal-body"><textarea name="remarks" class="form-control" rows="2" placeholder="Optional remarks…"></textarea></div>
  <div class="modal-footer"><button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button><button type="submit" name="action" value="approve" class="btn" style="background:#1a6b3c;color:#fff">Approve</button></div>
  </form>
</div></div></div>
<script>function openAction(id){document.getElementById('actPassId').value=id;new bootstrap.Modal(document.getElementById('actModal')).show();}</script>
<?php include __DIR__.'/../includes/footer.php'; ?>
