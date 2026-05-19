<?php
// admin/manage_depots.php
require_once __DIR__ . '/../config/db.php';
requireLogin('admin'); $db=getDB(); $flash=getFlash();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $act=$_POST['action']??'';
    if ($act==='add') {
        $db->prepare("INSERT INTO depots (depot_name,location,district,manager_id,latitude,longitude) VALUES (?,?,?,?,?,?)")
           ->execute([sanitize($_POST['depot_name']),sanitize($_POST['location']),'Tirunelveli',$_POST['manager_id']?:null,$_POST['latitude']?:null,$_POST['longitude']?:null]);
        redirectWithMessage(APP_URL.'/admin/manage_depots.php','success','Depot added.');
    } elseif ($act==='delete') {
        $db->prepare("DELETE FROM depots WHERE depot_id=?")->execute([(int)$_POST['depot_id']]);
        redirectWithMessage(APP_URL.'/admin/manage_depots.php','success','Depot deleted.');
    }
}
$depots=$db->query("SELECT d.*,u.name manager_name,u.email manager_email,(SELECT COUNT(*) FROM buses WHERE depot_id=d.depot_id) bus_count FROM depots d LEFT JOIN users u ON u.user_id=d.manager_id ORDER BY d.depot_name")->fetchAll();
$managers=$db->query("SELECT user_id,name,email FROM users WHERE role='depot_manager' AND status='active' ORDER BY name")->fetchAll();
$pageTitle='Manage Depots'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">🏢 Manage Depots</div></div>
  <div class="page-content">
    <?php if(!empty($flash['msg'])): ?><div class="flash-banner flash-<?=$flash['type']?> mb-3"><?=htmlspecialchars($flash['msg'])?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-building me-2 text-success"></i>Tirunelveli Depots (<?=count($depots)?>)</h5>
        <button class="btn-primary-custom btn-sm-custom" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa fa-plus"></i> Add Depot</button>
      </div>
      <div class="table-responsive"><table class="data-table">
        <thead><tr><th>#</th><th>Depot Name</th><th>Location</th><th>Manager</th><th>Buses</th><th>Coordinates</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach($depots as $d): ?>
          <tr>
            <td><?=$d['depot_id']?></td>
            <td><strong><?=htmlspecialchars($d['depot_name'])?></strong></td>
            <td><?=htmlspecialchars($d['location'])?></td>
            <td><?=htmlspecialchars($d['manager_name']??'Not Assigned')?><div style="font-size:11px;color:#6c757d"><?=htmlspecialchars($d['manager_email']??'')?></div></td>
            <td><span class="badge-custom badge-primary"><?=$d['bus_count']?> buses</span></td>
            <td style="font-size:11px;color:#6c757d"><?=$d['latitude']?$d['latitude'].', '.$d['longitude']:'—'?></td>
            <td>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete this depot and all its data?')">
                <input type="hidden" name="action" value="delete"><input type="hidden" name="depot_id" value="<?=$d['depot_id']?>">
                <button type="submit" class="btn-icon delete"><i class="fa fa-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div></div>
<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header" style="background:#1a6b3c;color:#fff"><h5>Add Depot</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <form method="POST"><input type="hidden" name="action" value="add">
  <div class="modal-body row g-3">
    <div class="col-12"><label class="form-label">Depot Name</label><input type="text" name="depot_name" class="form-control" required></div>
    <div class="col-12"><label class="form-label">Location</label><input type="text" name="location" class="form-control" required></div>
    <div class="col-12"><label class="form-label">Assign Manager</label><select name="manager_id" class="form-select"><option value="">-- None --</option><?php foreach($managers as $m): ?><option value="<?=$m['user_id']?>"><?=htmlspecialchars($m['name'])?></option><?php endforeach; ?></select></div>
    <div class="col-6"><label class="form-label">Latitude</label><input type="number" step="any" name="latitude" class="form-control"></div>
    <div class="col-6"><label class="form-label">Longitude</label><input type="number" step="any" name="longitude" class="form-control"></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn" style="background:#1a6b3c;color:#fff">Add Depot</button></div>
  </form>
</div></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
