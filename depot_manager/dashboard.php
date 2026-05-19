<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('depot_manager');
$uid    = (int)currentUser()['id'];
$db     = getDB();
$depot  = $db->query("SELECT * FROM depots WHERE manager_id=$uid LIMIT 1")->fetch();
if (!$depot) { echo '<p class="p-4 text-danger">No depot assigned to your account. Contact admin.</p>'; exit; }
$did    = (int)$depot['depot_id'];

// KPIs
$buses      = $db->query("SELECT COUNT(*) FROM buses WHERE depot_id=$did AND status='active'")->fetchColumn();
$todayTrips = $db->query("SELECT COUNT(*) FROM schedules s JOIN buses b ON b.bus_id=s.bus_id WHERE b.depot_id=$did AND s.travel_date=CURDATE()")->fetchColumn();
$complaints = $db->query("SELECT COUNT(*) FROM complaints WHERE depot_id=$did AND status='open'")->fetchColumn();
$passes     = $db->query("SELECT COUNT(*) FROM bus_pass WHERE depot_id=$did AND status='pending'")->fetchColumn();
$revenue    = $db->query("SELECT COALESCE(SUM(t.fare),0) FROM tickets t JOIN schedules s ON s.schedule_id=t.schedule_id JOIN buses b ON b.bus_id=s.bus_id WHERE b.depot_id=$did AND t.payment_status='paid' AND DATE(t.booking_date)=CURDATE()")->fetchColumn();
$alerts     = $db->query("SELECT * FROM emergency_alerts WHERE depot_id=$did AND status='active' ORDER BY created_at DESC LIMIT 5")->fetchAll();

$pageTitle = 'Depot Manager Dashboard';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title">🏢 <?= htmlspecialchars($depot['depot_name']) ?> – Manager Dashboard</div>
    <div class="topbar-actions">
      <button class="notif-btn"><i class="fa fa-bell"></i><span class="notif-badge"></span></button>
      <div class="user-avatar"><?= strtoupper(substr(currentUser()['name'],0,1)) ?></div>
    </div>
  </div>
  <div class="page-content">
    <!-- Banner -->
    <div class="mb-4 p-4 rounded-3" style="background:linear-gradient(135deg,#1a6b3c,#0a58ca);color:#fff">
      <h1 style="font-size:20px;font-weight:700"><?= htmlspecialchars($depot['depot_name']) ?></h1>
      <p style="opacity:.8;font-size:13px;margin:4px 0 0"><i class="fa fa-location-dot me-1"></i><?= htmlspecialchars($depot['location']) ?> &nbsp;|&nbsp; Today: <?= date('d F Y') ?></p>
    </div>

    <!-- KPI -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon green"><i class="fa fa-bus"></i></div><div><div class="stat-label">Active Buses</div><div class="stat-value"><?= $buses ?></div></div></div></div>
      <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon blue"><i class="fa fa-calendar-day"></i></div><div><div class="stat-label">Today's Trips</div><div class="stat-value"><?= $todayTrips ?></div></div></div></div>
      <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon orange"><i class="fa fa-comments"></i></div><div><div class="stat-label">Open Complaints</div><div class="stat-value"><?= $complaints ?></div></div></div></div>
      <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon purple"><i class="fa fa-id-card"></i></div><div><div class="stat-label">Pending Passes</div><div class="stat-value"><?= $passes ?></div></div></div></div>
    </div>

    <div class="row g-4">
      <!-- Revenue -->
      <div class="col-md-4">
        <div class="stat-card" style="flex-direction:column;align-items:flex-start">
          <div class="d-flex align-items-center gap-3 w-100">
            <div class="stat-icon teal"><i class="fa fa-indian-rupee-sign"></i></div>
            <div><div class="stat-label">Today's Revenue</div><div class="stat-value" style="color:#0f766e">₹<?= number_format($revenue,2) ?></div></div>
          </div>
          <div class="mt-3 w-100"><a href="reports.php" class="btn-primary-custom btn-sm-custom w-100 justify-content-center"><i class="fa fa-chart-bar"></i> View Full Report</a></div>
        </div>
      </div>

      <!-- Emergency Alerts -->
      <div class="col-md-8">
        <div class="table-card h-100">
          <div class="table-header"><h5 style="color:#dc3545"><i class="fa fa-triangle-exclamation me-2"></i>Active Emergency Alerts</h5></div>
          <?php if (empty($alerts)): ?>
            <div class="empty-state p-4"><i class="fa fa-check-circle" style="color:#1a6b3c"></i>No active emergencies.</div>
          <?php else: ?>
          <?php foreach ($alerts as $a): ?>
          <div style="padding:12px 16px;border-bottom:1px solid #fee2e2;background:#fff5f5">
            <div class="d-flex justify-content-between">
              <strong style="color:#dc3545">#<?= $a['alert_id'] ?> Emergency Alert</strong>
              <span style="font-size:11px;color:#6c757d"><?= date('h:i A',strtotime($a['created_at'])) ?></span>
            </div>
            <p style="margin:4px 0 0;font-size:13px"><?= htmlspecialchars($a['message']) ?></p>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Quick links -->
    <div class="row g-3 mt-2">
      <?php
      $ql=[['🚌','Buses','buses.php'],['📅','Schedules','schedules.php'],['👥','Staff','staff.php'],
           ['💬','Complaints','complaints.php'],['🪪','Pass Verify','pass_verification.php'],
           ['📦','Lost & Found','lost_found.php'],['🗺','Live Map','live_map.php'],['📊','Reports','reports.php']];
      foreach ($ql as [$i,$l,$h]):?>
      <div class="col-6 col-md-3">
        <a href="<?= $h ?>" class="feature-card d-flex align-items-center gap-3 text-decoration-none" style="padding:16px;text-align:left">
          <span style="font-size:22px"><?= $i ?></span>
          <span style="font-weight:600;font-size:14px;color:#333"><?= $l ?></span>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
