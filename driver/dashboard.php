<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('driver_conductor');
$user = currentUser();
$uid  = (int)$user['id'];
$role = $user['role'];
$db   = getDB();

// Get driver or conductor record
if ($role === 'driver') {
    $rec = $db->prepare("SELECT * FROM drivers WHERE user_id=?");
} else {
    $rec = $db->prepare("SELECT * FROM conductors WHERE user_id=?");
}
$rec->execute([$uid]);
$staff = $rec->fetch();
$staffId = $staff ? (int)($role==='driver'?$staff['driver_id']:$staff['conductor_id']) : 0;

// Today's schedule
$col = $role==='driver'?'driver_id':'conductor_id';
$trips= $db->query("SELECT s.*,r.source,r.destination,r.route_number,b.bus_number,b.bus_id FROM schedules s JOIN routes r ON r.route_id=s.route_id JOIN buses b ON b.bus_id=s.bus_id WHERE s.$col=$staffId AND s.travel_date=CURDATE() ORDER BY s.departure_time")->fetchAll();
$todayTrips = count($trips);

// Active emergencies count
$emergencies = $db->query("SELECT COUNT(*) FROM emergency_alerts WHERE driver_id=$staffId AND status='active'")->fetchColumn();

$pageTitle = ucfirst($role).' Dashboard';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_driver.php'; ?>
<div class="main-content">
  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title">🚌 <?= ucfirst($role) ?> Dashboard</div>
    <div class="topbar-actions">
      <button class="notif-btn"><i class="fa fa-bell"></i><span class="notif-badge"></span></button>
      <div class="user-avatar"><?= strtoupper(substr($user['name'],0,1)) ?></div>
    </div>
  </div>
  <div class="page-content">
    <!-- Welcome -->
    <div class="mb-4 p-4 rounded-3" style="background:linear-gradient(135deg,#1a6b3c,#0d4a28);color:#fff">
      <h1 style="font-size:20px;font-weight:700">Good <?= date('H')<12?'Morning':(date('H')<17?'Afternoon':'Evening') ?>, <?= htmlspecialchars($user['name']) ?>!</h1>
      <p style="opacity:.8;margin:4px 0 0;font-size:13px">Today: <?= date('l, d F Y') ?> &nbsp;|&nbsp; Role: <?= ucfirst($role) ?></p>
    </div>

    <!-- KPI -->
    <div class="row g-3 mb-4">
      <div class="col-sm-4">
        <div class="stat-card"><div class="stat-icon green"><i class="fa fa-calendar-day"></i></div>
          <div><div class="stat-label">Today's Trips</div><div class="stat-value"><?= $todayTrips ?></div></div></div>
      </div>
      <div class="col-sm-4">
        <div class="stat-card"><div class="stat-icon blue"><i class="fa fa-users"></i></div>
          <div><div class="stat-label">Passengers (Today)</div><div class="stat-value">—</div></div></div>
      </div>
      <div class="col-sm-4">
        <div class="stat-card"><div class="stat-icon red"><i class="fa fa-triangle-exclamation"></i></div>
          <div><div class="stat-label">Active Alerts</div><div class="stat-value"><?= $emergencies ?></div></div></div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
      <?php
      $actions = [['📋','Assigned Trips','assigned_trips.php'],['👥','Passenger List','passenger_list.php'],
        ['📷','Scan Ticket','scan_ticket.php'],['⏱','Delay Update','delay_update.php'],
        ['🚨','Emergency','emergency.php'],['📦','Lost Item','lost_item_report.php']];
      foreach ($actions as [$icon,$label,$href]):
      ?>
      <div class="col-4 col-md-2">
        <a href="<?= $href ?>" class="d-block text-center p-3 rounded-3 text-decoration-none" style="background:#fff;border:1px solid #e9ecef">
          <div style="font-size:24px"><?= $icon ?></div>
          <div style="font-size:11px;font-weight:600;margin-top:6px;color:#333"><?= $label ?></div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Today's Trips -->
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-calendar-check me-2 text-success"></i>Today's Schedule</h5></div>
      <?php if (empty($trips)): ?>
        <div class="empty-state"><i class="fa fa-calendar"></i>No trips assigned today.</div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="data-table">
          <thead><tr><th>Schedule</th><th>Route</th><th>Bus</th><th>Departs</th><th>Arrives</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($trips as $t): ?>
            <tr>
              <td>#<?= $t['schedule_id'] ?></td>
              <td><strong><?= htmlspecialchars($t['route_number']) ?></strong><div style="font-size:11px;color:#6c757d"><?= htmlspecialchars($t['source'].' → '.$t['destination']) ?></div></td>
              <td><?= htmlspecialchars($t['bus_number']) ?></td>
              <td><?= htmlspecialchars(substr($t['departure_time'],0,5)) ?></td>
              <td><?= htmlspecialchars(substr($t['arrival_time'],0,5)) ?></td>
              <td><span class="badge-custom badge-<?= $t['status']==='scheduled'?'info':($t['status']==='in_progress'?'success':($t['status']==='completed'?'secondary':'warning')) ?>"><?= ucfirst($t['status']) ?></span></td>
              <td>
                <a href="passenger_list.php?schedule_id=<?= $t['schedule_id'] ?>" class="btn-icon view" title="Passenger List"><i class="fa fa-users"></i></a>
                <a href="delay_update.php?schedule_id=<?= $t['schedule_id'] ?>" class="btn-icon edit" title="Update Delay"><i class="fa fa-clock"></i></a>
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
<?php include __DIR__ . '/../includes/footer.php'; ?>
