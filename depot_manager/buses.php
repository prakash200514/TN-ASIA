<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('depot_manager');
$uid   = (int)currentUser()['id'];
$db    = getDB();
$depot = $db->query("SELECT * FROM depots WHERE manager_id=$uid LIMIT 1")->fetch();
if (!$depot) die('<p class="p-4 text-danger">No depot assigned.</p>');
$did   = (int)$depot['depot_id'];
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action']??'';
    if ($action==='add') {
        $num  = sanitize($_POST['bus_number']??'');
        $type = sanitize($_POST['bus_type']??'ordinary');
        $seats= (int)($_POST['total_seats']??40);
        $db->prepare("INSERT INTO buses (bus_number,depot_id,bus_type,total_seats,status) VALUES (?,?,?,?,'active')")->execute([$num,$did,$type,$seats]);
        redirectWithMessage(APP_URL.'/depot_manager/buses.php','success','Bus added successfully.');
    } elseif ($action==='edit') {
        $bid=$_POST['bus_id'];$type=sanitize($_POST['bus_type']);$seats=(int)$_POST['total_seats'];$status=sanitize($_POST['status']);
        $db->prepare("UPDATE buses SET bus_type=?,total_seats=?,status=? WHERE bus_id=? AND depot_id=?")->execute([$type,$seats,$status,$bid,$did]);
        redirectWithMessage(APP_URL.'/depot_manager/buses.php','success','Bus updated.');
    } elseif ($action==='delete') {
        $bid=(int)$_POST['bus_id'];
        $db->prepare("DELETE FROM buses WHERE bus_id=? AND depot_id=?")->execute([$bid,$did]);
        redirectWithMessage(APP_URL.'/depot_manager/buses.php','success','Bus removed.');
    }
}

$buses = $db->query("SELECT * FROM buses WHERE depot_id=$did ORDER BY bus_number")->fetchAll();
$pageTitle = 'Depot Buses';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">🚌 Depot Buses</div></div>
  <div class="page-content">
    <?php if (!empty($flash['msg'])): ?><div class="flash-banner flash-<?= $flash['type'] ?> mb-3"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header">
        <h5><i class="fa fa-bus me-2 text-success"></i><?= htmlspecialchars($depot['depot_name']) ?> Buses (<?= count($buses) ?>)</h5>
        <button class="btn-primary-custom btn-sm-custom" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa fa-plus"></i> Add Bus</button>
      </div>
      <div class="table-responsive">
        <table class="data-table">
          <thead><tr><th>#</th><th>Bus Number</th><th>Type</th><th>Total Seats</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($buses as $b): ?>
            <tr>
              <td><?= $b['bus_id'] ?></td>
              <td><strong><?= htmlspecialchars($b['bus_number']) ?></strong></td>
              <td><span class="badge-custom badge-primary"><?= ucfirst(str_replace('_',' ',$b['bus_type'])) ?></span></td>
              <td><?= $b['total_seats'] ?> seats</td>
              <td><span class="badge-custom badge-<?= $b['status']==='active'?'success':($b['status']==='maintenance'?'warning':'secondary') ?>"><?= ucfirst($b['status']) ?></span></td>
              <td>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="bus_id" value="<?= $b['bus_id'] ?>">
                  <select name="bus_type" class="form-select form-select-sm d-inline-block w-auto">
                    <?php foreach (['ordinary','express','super_express','ac','mini'] as $t): ?><option value="<?= $t ?>" <?= $b['bus_type']===$t?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$t)) ?></option><?php endforeach; ?>
                  </select>
                  <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                    <?php foreach (['active','maintenance','inactive'] as $s): ?><option value="<?= $s ?>" <?= $b['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
                  </select>
                  <input type="number" name="total_seats" class="form-control form-control-sm d-inline-block w-auto" value="<?= $b['total_seats'] ?>" style="width:65px">
                  <button type="submit" class="btn-icon edit" title="Save"><i class="fa fa-check"></i></button>
                </form>
                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this bus?')">
                  <input type="hidden" name="action" value="delete"><input type="hidden" name="bus_id" value="<?= $b['bus_id'] ?>">
                  <button type="submit" class="btn-icon delete" title="Delete"><i class="fa fa-trash"></i></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>

<!-- Add Bus Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header" style="background:#1a6b3c;color:#fff">
      <h5 class="modal-title">Add New Bus</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <form method="POST"><input type="hidden" name="action" value="add">
    <div class="modal-body row g-3">
      <div class="col-12"><label class="form-label">Bus Number</label><input type="text" name="bus_number" class="form-control" placeholder="TN72 A XXXX" required></div>
      <div class="col-6"><label class="form-label">Type</label><select name="bus_type" class="form-select"><option value="ordinary">Ordinary</option><option value="express">Express</option><option value="super_express">Super Express</option><option value="ac">AC</option><option value="mini">Mini</option></select></div>
      <div class="col-6"><label class="form-label">Seats</label><input type="number" name="total_seats" class="form-control" value="40" min="10" max="60" required></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn" style="background:#1a6b3c;color:#fff">Add Bus</button></div>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
