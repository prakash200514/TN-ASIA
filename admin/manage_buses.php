<?php   >>>>>>>>>>>>
// admin/manage_buses.php
require_once __DIR__ . '/../config/db.php';
requireLogin('admin'); $db=getDB(); $flash=getFlash();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $act=$_POST['action']??'';
    if ($act==='add') {
        $db->prepare("INSERT INTO buses (bus_number,depot_id,bus_type,total_seats,status) VALUES (?,?,?,?,'active')")
           ->execute([sanitize($_POST['bus_number']),(int)$_POST['depot_id'],sanitize($_POST['bus_type']),(int)$_POST['total_seats']]);
        redirectWithMessage(APP_URL.'/admin/manage_buses.php','success','Bus added.');
    } elseif ($act==='delete') {
        $db->prepare("DELETE FROM buses WHERE bus_id=?")->execute([(int)$_POST['bus_id']]);
        redirectWithMessage(APP_URL.'/admin/manage_buses.php','success','Bus deleted.');
    } elseif ($act==='edit') {
        $db->prepare("UPDATE buses SET bus_type=?,total_seats=?,status=? WHERE bus_id=?")
           ->execute([sanitize($_POST['bus_type']),(int)$_POST['total_seats'],sanitize($_POST['status']),(int)$_POST['bus_id']]);
        redirectWithMessage(APP_URL.'/admin/manage_buses.php','success','Bus updated.');
    }
}
$depotFilter=(int)($_GET['depot_id']??0);
$buses=$db->query("SELECT b.*,d.depot_name FROM buses b JOIN depots d ON d.depot_id=b.depot_id ".($depotFilter?"WHERE b.depot_id=$depotFilter":'')." ORDER BY d.depot_name,b.bus_number")->fetchAll();
$depots=$db->query("SELECT depot_id,depot_name FROM depots ORDER BY depot_name")->fetchAll();
$pageTitle='Manage Buses'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">🚌 Manage Buses</div></div>
  <div class="page-content">
    <?php if(!empty($flash['msg'])): ?><div class="flash-banner flash-<?=$flash['type']?> mb-3"><?=htmlspecialchars($flash['msg'])?></div><?php endif; ?>
    <div class="d-flex gap-2 mb-3 align-items-center flex-wrap">
      <form class="d-flex gap-2"><select name="depot_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width:200px"><option value="">All Depots</option><?php foreach($depots as $d): ?><option value="<?=$d['depot_id']?>" <?=$depotFilter===$d['depot_id']?'selected':''?>><?=htmlspecialchars($d['depot_name'])?></option><?php endforeach; ?></select></form>
      <button class="btn-primary-custom btn-sm-custom ms-auto" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa fa-plus"></i> Add Bus</button>
    </div>
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-bus me-2 text-success"></i>All Buses (<?=count($buses)?>)</h5></div>
      <div class="table-responsive"><table class="data-table">
        <thead><tr><th>#</th><th>Bus Number</th><th>Depot</th><th>Type</th><th>Seats</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach($buses as $b): ?>
          <tr>
            <td><?=$b['bus_id']?></td>
            <td><strong><?=htmlspecialchars($b['bus_number'])?></strong></td>
            <td><?=htmlspecialchars($b['depot_name'])?></td>
            <td><span class="badge-custom badge-primary"><?=ucfirst(str_replace('_',' ',$b['bus_type']))?></span></td>
            <td><?=$b['total_seats']?></td>
            <td><span class="badge-custom badge-<?=$b['status']==='active'?'success':($b['status']==='maintenance'?'warning':'secondary')?>"><?=ucfirst($b['status'])?></span></td>
            <td>
              <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="edit"><input type="hidden" name="bus_id" value="<?=$b['bus_id']?>">
                <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                  <?php foreach(['active','maintenance','inactive'] as $s): ?><option value="<?=$s?>" <?=$b['status']===$s?'selected':''?>><?=ucfirst($s)?></option><?php endforeach; ?>
                </select>
                <button type="submit" class="btn-icon edit"><i class="fa fa-check"></i></button>
              </form>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete bus?')">
                <input type="hidden" name="action" value="delete"><input type="hidden" name="bus_id" value="<?=$b['bus_id']?>">
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
  <div class="modal-header" style="background:#1a6b3c;color:#fff"><h5>Add Bus</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <form method="POST"><input type="hidden" name="action" value="add">
  <div class="modal-body row g-3">
    <div class="col-12"><label class="form-label">Bus Number</label><input type="text" name="bus_number" class="form-control" placeholder="TN72 X XXXX" required></div>
    <div class="col-12"><label class="form-label">Depot</label><select name="depot_id" class="form-select" required><?php foreach($depots as $d): ?><option value="<?=$d['depot_id']?>"><?=htmlspecialchars($d['depot_name'])?></option><?php endforeach; ?></select></div>
    <div class="col-6"><label class="form-label">Type</label><select name="bus_type" class="form-select"><option value="ordinary">Ordinary</option><option value="express">Express</option><option value="super_express">Super Express</option><option value="ac">AC</option><option value="mini">Mini</option></select></div>
    <div class="col-6"><label class="form-label">Seats</label><input type="number" name="total_seats" class="form-control" value="40" min="10" max="60" required></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn" style="background:#1a6b3c;color:#fff">Add Bus</button></div>
  </form>
</div></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
