<?php
// admin/manage_routes.php
require_once __DIR__ . '/../config/db.php';
requireLogin('admin'); $db=getDB(); $flash=getFlash();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $act=$_POST['action']??'';
    if ($act==='add') {
        $db->prepare("INSERT INTO routes (route_number,source,destination,distance,estimated_time) VALUES (?,?,?,?,?)")
           ->execute([sanitize($_POST['route_number']),sanitize($_POST['source']),sanitize($_POST['destination']),(float)$_POST['distance'],(int)$_POST['estimated_time']]);
        redirectWithMessage(APP_URL.'/admin/manage_routes.php','success','Route added.');
    } elseif ($act==='delete') {
        $db->prepare("DELETE FROM routes WHERE route_id=?")->execute([(int)$_POST['route_id']]);
        redirectWithMessage(APP_URL.'/admin/manage_routes.php','success','Route deleted.');
    }
}
$routes=$db->query("SELECT r.*,(SELECT COUNT(*) FROM route_stops WHERE route_id=r.route_id) stop_count FROM routes r ORDER BY r.route_number")->fetchAll();
$pageTitle='Manage Routes'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">🛣 Manage Routes</div></div>
  <div class="page-content">
    <?php if(!empty($flash['msg'])): ?><div class="flash-banner flash-<?=$flash['type']?> mb-3"><?=htmlspecialchars($flash['msg'])?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-route me-2 text-success"></i>Routes (<?=count($routes)?>)</h5>
        <button class="btn-primary-custom btn-sm-custom" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa fa-plus"></i> Add Route</button></div>
      <div class="table-responsive"><table class="data-table">
        <thead><tr><th>#</th><th>Route No.</th><th>Source</th><th>Destination</th><th>Distance</th><th>Est. Time</th><th>Stops</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach($routes as $r): ?>
          <tr>
            <td><?=$r['route_id']?></td>
            <td><strong><?=htmlspecialchars($r['route_number'])?></strong></td>
            <td><?=htmlspecialchars($r['source'])?></td>
            <td><?=htmlspecialchars($r['destination'])?></td>
            <td><?=$r['distance']?> km</td>
            <td><?=$r['estimated_time']?> min</td>
            <td><?=$r['stop_count']?> stops</td>
            <td>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete route?')">
                <input type="hidden" name="action" value="delete"><input type="hidden" name="route_id" value="<?=$r['route_id']?>">
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
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header" style="background:#1a6b3c;color:#fff"><h5>Add Route</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <form method="POST"><input type="hidden" name="action" value="add">
  <div class="modal-body row g-3">
    <div class="col-6"><label class="form-label">Route Number</label><input type="text" name="route_number" class="form-control" placeholder="58A" required></div>
    <div class="col-6"><label class="form-label">Distance (km)</label><input type="number" name="distance" step="0.1" class="form-control" required></div>
    <div class="col-6"><label class="form-label">Source</label><input type="text" name="source" class="form-control" required></div>
    <div class="col-6"><label class="form-label">Destination</label><input type="text" name="destination" class="form-control" required></div>
    <div class="col-12"><label class="form-label">Estimated Time (minutes)</label><input type="number" name="estimated_time" class="form-control" required></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn" style="background:#1a6b3c;color:#fff">Add Route</button></div>
  </form>
</div></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
