<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('driver_conductor');
$db = getDB();
$schedule_id = (int)($_GET['schedule_id'] ?? 0);
if (!$schedule_id) { header('Location: assigned_trips.php'); exit; }

$passengers = $db->query("SELECT t.*,u.name,u.phone,t.ticket_status FROM tickets t JOIN users u ON u.user_id=t.passenger_id WHERE t.schedule_id=$schedule_id ORDER BY t.seat_number")->fetchAll();
$sched      = $db->query("SELECT s.*,r.source,r.destination,b.bus_number FROM schedules s JOIN routes r ON r.route_id=s.route_id JOIN buses b ON b.bus_id=s.bus_id WHERE s.schedule_id=$schedule_id LIMIT 1")->fetch();

$pageTitle = 'Passenger List';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_driver.php'; ?>
<div class="main-content">
  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title">👥 Passenger List – Schedule #<?= $schedule_id ?></div>
  </div>
  <div class="page-content">
    <?php if ($sched): ?>
    <div class="card mb-4 p-3" style="background:linear-gradient(135deg,#1a6b3c,#0d4a28);color:#fff;border:none">
      <div class="d-flex gap-4">
        <div><div style="font-size:11px;opacity:.7">Route</div><strong><?= htmlspecialchars($sched['source'].' → '.$sched['destination']) ?></strong></div>
        <div><div style="font-size:11px;opacity:.7">Bus</div><strong><?= htmlspecialchars($sched['bus_number']) ?></strong></div>
        <div><div style="font-size:11px;opacity:.7">Date</div><strong><?= htmlspecialchars($sched['travel_date']) ?></strong></div>
        <div><div style="font-size:11px;opacity:.7">Booked Seats</div><strong><?= count($passengers) ?></strong></div>
      </div>
    </div>
    <?php endif; ?>

    <div class="table-card">
      <div class="table-header">
        <h5><i class="fa fa-users me-2 text-success"></i>Passengers (<?= count($passengers) ?>)</h5>
        <a href="scan_ticket.php" class="btn-primary-custom btn-sm-custom"><i class="fa fa-qrcode"></i> Scan QR</a>
      </div>
      <?php if (empty($passengers)): ?>
        <div class="empty-state"><i class="fa fa-users"></i>No passengers booked for this schedule.</div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="data-table">
          <thead><tr><th>Seat</th><th>Passenger Name</th><th>Phone</th><th>From</th><th>To</th><th>Fare</th><th>Status</th></tr></thead>
          <tbody>
            <?php foreach ($passengers as $p): ?>
            <tr>
              <td><strong>S<?= str_pad($p['seat_number'],2,'0',STR_PAD_LEFT) ?></strong></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['phone']) ?></td>
              <td><?= htmlspecialchars($p['source_stop']) ?></td>
              <td><?= htmlspecialchars($p['dest_stop']) ?></td>
              <td>₹<?= number_format($p['fare'],2) ?></td>
              <td><span class="badge-custom badge-<?= $p['ticket_status']==='active'?'success':($p['ticket_status']==='used'?'secondary':'danger') ?>"><?= ucfirst($p['ticket_status']) ?></span></td>
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
