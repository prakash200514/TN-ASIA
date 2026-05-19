<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('depot_manager');
$uid   = (int)currentUser()['id'];
$db    = getDB();
$depot = $db->query("SELECT * FROM depots WHERE manager_id=$uid LIMIT 1")->fetch();
if (!$depot) die('No depot assigned.');
$did   = (int)$depot['depot_id'];
$flash = getFlash();

// Handle schedule add/edit/delete
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $act = $_POST['action']??'';
    if ($act==='add') {
        $db->prepare("INSERT INTO schedules (bus_id,route_id,driver_id,conductor_id,departure_time,arrival_time,travel_date,status) VALUES (?,?,?,?,?,?,?,'scheduled')")
           ->execute([(int)$_POST['bus_id'],(int)$_POST['route_id'],$_POST['driver_id']?:null,$_POST['conductor_id']?:null,$_POST['departure_time'],$_POST['arrival_time'],$_POST['travel_date']]);
        redirectWithMessage(APP_URL.'/depot_manager/schedules.php','success','Schedule added.');
    } elseif ($act==='delete') {
        $db->prepare("DELETE FROM schedules WHERE schedule_id=?")->execute([(int)$_POST['schedule_id']]);
        redirectWithMessage(APP_URL.'/depot_manager/schedules.php','success','Schedule deleted.');
    }
}

$schedules = $db->query("SELECT s.*,b.bus_number,r.route_number,r.source,r.destination,u1.name driver_name,u2.name conductor_name FROM schedules s JOIN buses b ON b.bus_id=s.bus_id JOIN routes r ON r.route_id=s.route_id LEFT JOIN drivers dr ON dr.driver_id=s.driver_id LEFT JOIN users u1 ON u1.user_id=dr.user_id LEFT JOIN conductors co ON co.conductor_id=s.conductor_id LEFT JOIN users u2 ON u2.user_id=co.user_id WHERE b.depot_id=$did ORDER BY s.travel_date DESC,s.departure_time")->fetchAll();
$myBuses   = $db->query("SELECT bus_id,bus_number FROM buses WHERE depot_id=$did AND status='active'")->fetchAll();
$routes    = $db->query("SELECT route_id,route_number,source,destination FROM routes ORDER BY route_number")->fetchAll();
$drivers   = $db->query("SELECT d.driver_id,u.name FROM drivers d JOIN users u ON u.user_id=d.user_id WHERE d.depot_id=$did AND d.status='active'")->fetchAll();
$conductors= $db->query("SELECT c.conductor_id,u.name FROM conductors c JOIN users u ON u.user_id=c.user_id WHERE c.depot_id=$did AND c.status='active'")->fetchAll();
$pageTitle = 'Schedules';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">📅 Manage Schedules</div></div>
  <div class="page-content">
    <?php if (!empty($flash['msg'])): ?><div class="flash-banner flash-<?= $flash['type'] ?> mb-3"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header"><h5>Schedules – <?= htmlspecialchars($depot['depot_name']) ?></h5>
        <button class="btn-primary-custom btn-sm-custom" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa fa-plus"></i> Add Schedule</button>
      </div>
      <div class="table-responsive">
        <table class="data-table">
          <thead><tr><th>#</th><th>Bus</th><th>Route</th><th>Date</th><th>Departs</th><th>Arrives</th><th>Driver</th><th>Conductor</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($schedules as $s): ?>
            <tr>
              <td><?= $s['schedule_id'] ?></td>
              <td><?= htmlspecialchars($s['bus_number']) ?></td>
              <td><?= htmlspecialchars($s['route_number'].': '.$s['source'].'→'.$s['destination']) ?></td>
              <td><?= $s['travel_date'] ?></td>
              <td><?= substr($s['departure_time'],0,5) ?></td>
              <td><?= substr($s['arrival_time'],0,5) ?></td>
              <td><?= htmlspecialchars($s['driver_name']??'—') ?></td>
              <td><?= htmlspecialchars($s['conductor_name']??'—') ?></td>
              <td><span class="badge-custom badge-<?= $s['status']==='scheduled'?'info':($s['status']==='in_progress'?'success':($s['status']==='completed'?'secondary':'warning')) ?>"><?= ucfirst($s['status']) ?></span></td>
              <td>
                <form method="POST" style="display:inline" onsubmit="return confirm('Delete?')">
                  <input type="hidden" name="action" value="delete"><input type="hidden" name="schedule_id" value="<?= $s['schedule_id'] ?>">
                  <button type="submit" class="btn-icon delete"><i class="fa fa-trash"></i></button>
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
<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header" style="background:#1a6b3c;color:#fff"><h5>Add Schedule</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="POST"><input type="hidden" name="action" value="add">
    <div class="modal-body row g-3">
      <div class="col-sm-6"><label class="form-label">Bus</label><select name="bus_id" class="form-select" required><?php foreach ($myBuses as $b): ?><option value="<?= $b['bus_id'] ?>"><?= htmlspecialchars($b['bus_number']) ?></option><?php endforeach; ?></select></div>
      <div class="col-sm-6"><label class="form-label">Route</label><select name="route_id" class="form-select" required><?php foreach ($routes as $r): ?><option value="<?= $r['route_id'] ?>"><?= htmlspecialchars($r['route_number'].': '.$r['source'].' → '.$r['destination']) ?></option><?php endforeach; ?></select></div>
      <div class="col-sm-6"><label class="form-label">Driver</label><select name="driver_id" class="form-select"><option value="">-- None --</option><?php foreach ($drivers as $d): ?><option value="<?= $d['driver_id'] ?>"><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?></select></div>
      <div class="col-sm-6"><label class="form-label">Conductor</label><select name="conductor_id" class="form-select"><option value="">-- None --</option><?php foreach ($conductors as $c): ?><option value="<?= $c['conductor_id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?></select></div>
      <div class="col-sm-4"><label class="form-label">Date</label><input type="date" name="travel_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
      <div class="col-sm-4"><label class="form-label">Departure</label><input type="time" name="departure_time" class="form-control" required></div>
      <div class="col-sm-4"><label class="form-label">Arrival</label><input type="time" name="arrival_time" class="form-control" required></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn" style="background:#1a6b3c;color:#fff">Add Schedule</button></div>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
