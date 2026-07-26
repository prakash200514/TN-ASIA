<?php<<<<<<<<
require_once __DIR__ . '/../config/db.php';
requireLogin('admin');
$db = getDB();
$users      = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$buses      = $db->query("SELECT COUNT(*) FROM buses")->fetchColumn();
$routes     = $db->query("SELECT COUNT(*) FROM routes")->fetchColumn();
$schedules  = $db->query("SELECT COUNT(*) FROM schedules WHERE travel_date=CURDATE()")->fetchColumn();
$tickets    = $db->query("SELECT COUNT(*) FROM tickets WHERE DATE(booking_date)=CURDATE()")->fetchColumn();
$revenue    = $db->query("SELECT COALESCE(SUM(fare),0) FROM tickets WHERE payment_status='paid'")->fetchColumn();
$complaints = $db->query("SELECT COUNT(*) FROM complaints WHERE status='open'")->fetchColumn();
$alerts     = $db->query("SELECT COUNT(*) FROM emergency_alerts WHERE status='active'")->fetchColumn();
$passes     = $db->query("SELECT COUNT(*) FROM bus_pass WHERE status='pending'")->fetchColumn();

$recentActivity = $db->query("SELECT 'ticket' type, CONCAT('Ticket #',ticket_id,' booked') msg, booking_date ts FROM tickets ORDER BY booking_date DESC LIMIT 4
UNION ALL SELECT 'complaint','Complaint: '.LEFT(description,40), created_at FROM complaints ORDER BY created_at DESC LIMIT 3
UNION ALL SELECT 'alert','Emergency alert from bus '+COALESCE(bus_id,'?'), created_at FROM emergency_alerts ORDER BY created_at DESC LIMIT 2
ORDER BY ts DESC LIMIT 8")->fetchAll();

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title">⚙️ Admin Dashboard – TNSTC Tirunelveli</div>
    <div class="topbar-actions">
      <button class="notif-btn"><i class="fa fa-bell"></i><span class="notif-badge"></span></button>
      <div class="user-avatar">A</div>
    </div>
  </div>
  <div class="page-content">
    <div class="mb-4 p-4 rounded-3" style="background:linear-gradient(135deg,#1a6b3c,#0a58ca);color:#fff">
      <h1 style="font-size:20px;font-weight:700">System Administration Dashboard</h1>
      <p style="opacity:.8;font-size:13px;margin:4px 0 0">Full access · TNSTC Tirunelveli District · <?= date('d F Y') ?></p>
    </div>

    <!-- KPI Grid -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon green"><i class="fa fa-users"></i></div><div><div class="stat-label">Total Users</div><div class="stat-value"><?=$users?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon blue"><i class="fa fa-bus"></i></div><div><div class="stat-label">Total Buses</div><div class="stat-value"><?=$buses?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon purple"><i class="fa fa-route"></i></div><div><div class="stat-label">Routes</div><div class="stat-value"><?=$routes?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon teal"><i class="fa fa-indian-rupee-sign"></i></div><div><div class="stat-label">Total Revenue</div><div class="stat-value" style="font-size:18px">₹<?=number_format($revenue,0)?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon blue"><i class="fa fa-calendar-day"></i></div><div><div class="stat-label">Today's Schedules</div><div class="stat-value"><?=$schedules?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon green"><i class="fa fa-ticket"></i></div><div><div class="stat-label">Today's Tickets</div><div class="stat-value"><?=$tickets?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon orange"><i class="fa fa-comments"></i></div><div><div class="stat-label">Open Complaints</div><div class="stat-value"><?=$complaints?></div></div></div></div>
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon red"><i class="fa fa-triangle-exclamation"></i></div><div><div class="stat-label">Active Emergencies</div><div class="stat-value"><?=$alerts?></div><div class="stat-sub"><?=$passes?> pending passes</div></div></div></div>
    </div>

    <!-- Quick Admin Links -->
    <div class="row g-3 mb-4">
      <?php
      $ql=[['👤','Users','manage_users.php'],['🏢','Depots','manage_depots.php'],['🚌','Buses','manage_buses.php'],
           ['🛣','Routes','manage_routes.php'],['📅','Schedules','manage_schedules.php'],['🎫','Tickets','manage_tickets.php'],
           ['🪪','Passes','manage_passes.php'],['💬','Complaints','manage_complaints.php'],['📦','Lost & Found','manage_lost_found.php'],['📊','Reports','reports.php']];
      foreach($ql as [$i,$l,$h]):
      ?>
      <div class="col-6 col-md-3 col-lg-2-4" style="width:20%">
        <a href="<?=$h?>" class="feature-card d-block text-center text-decoration-none">
          <div style="font-size:26px;margin-bottom:8px"><?=$i?></div>
          <div style="font-weight:700;font-size:13px"><?=$l?></div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if(!empty($alerts)): ?>
    <div class="flash-banner flash-danger mb-3">
      <i class="fa fa-triangle-exclamation"></i> <strong><?=$alerts?> active emergency alert(s)</strong> require immediate attention.
      <a href="manage_lost_found.php" style="color:inherit;margin-left:8px">View →</a>
    </div>
    <?php endif; ?>
  </div>
</div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
