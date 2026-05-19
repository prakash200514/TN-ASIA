<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('minister');
$db = getDB();

// District-wide KPIs
$totalDepots    = 7;
$totalBuses     = $db->query("SELECT COUNT(*) FROM buses WHERE status='active'")->fetchColumn();
$totalPassengers= $db->query("SELECT COUNT(*) FROM users WHERE role='passenger' AND status='active'")->fetchColumn();
$todayBookings  = $db->query("SELECT COUNT(*) FROM tickets WHERE DATE(booking_date)=CURDATE() AND payment_status='paid'")->fetchColumn();
$totalRevenue   = $db->query("SELECT COALESCE(SUM(fare),0) FROM tickets WHERE payment_status='paid'")->fetchColumn();
$openComplaints = $db->query("SELECT COUNT(*) FROM complaints WHERE status='open'")->fetchColumn();
$activeAlerts   = $db->query("SELECT COUNT(*) FROM emergency_alerts WHERE status='active'")->fetchColumn();
$delayedToday   = $db->query("SELECT COUNT(*) FROM schedules WHERE status='delayed' AND travel_date=CURDATE()")->fetchColumn();

// Depot-wise revenue
$depotRevenue = $db->query("SELECT d.depot_name,COALESCE(SUM(t.fare),0) revenue FROM depots d LEFT JOIN buses b ON b.depot_id=d.depot_id LEFT JOIN schedules s ON s.bus_id=b.bus_id LEFT JOIN tickets t ON t.schedule_id=s.schedule_id AND t.payment_status='paid' GROUP BY d.depot_id ORDER BY revenue DESC")->fetchAll();

// Complaint category breakdown
$complaintCats = $db->query("SELECT category,COUNT(*) cnt FROM complaints GROUP BY category ORDER BY cnt DESC")->fetchAll();

$pageTitle = 'Minister Dashboard';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title">🏛️ TNSTC Tirunelveli – Minister Dashboard</div>
    <div class="topbar-actions"><div class="user-avatar"><?= strtoupper(substr(currentUser()['name'],0,1)) ?></div></div>
  </div>
  <div class="page-content">
    <!-- Banner -->
    <div class="mb-4 p-4 rounded-3" style="background:linear-gradient(135deg,#0a58ca,#1a6b3c);color:#fff">
      <h1 style="font-size:20px;font-weight:700">Tirunelveli District – Transport Overview</h1>
      <p style="opacity:.8;font-size:13px;margin:4px 0 0">Read-only Analytics Portal &nbsp;|&nbsp; <?= date('d F Y') ?></p>
    </div>

    <!-- KPIs Row 1 -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon green"><i class="fa fa-building"></i></div><div><div class="stat-label">Total Depots</div><div class="stat-value"><?= $totalDepots ?></div><div class="stat-sub">Tirunelveli District</div></div></div></div>
      <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon blue"><i class="fa fa-bus"></i></div><div><div class="stat-label">Active Buses</div><div class="stat-value"><?= $totalBuses ?></div></div></div></div>
      <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon purple"><i class="fa fa-users"></i></div><div><div class="stat-label">Registered Passengers</div><div class="stat-value"><?= $totalPassengers ?></div></div></div></div>
      <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon teal"><i class="fa fa-indian-rupee-sign"></i></div><div><div class="stat-label">Total Revenue</div><div class="stat-value">₹<?= number_format($totalRevenue/1000,1) ?>K</div></div></div></div>
    </div>

    <!-- KPIs Row 2 -->
    <div class="row g-3 mb-4">
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon blue"><i class="fa fa-ticket"></i></div><div><div class="stat-label">Today's Bookings</div><div class="stat-value"><?= $todayBookings ?></div></div></div></div>
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon orange"><i class="fa fa-comments"></i></div><div><div class="stat-label">Open Complaints</div><div class="stat-value"><?= $openComplaints ?></div></div></div></div>
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon red"><i class="fa fa-triangle-exclamation"></i></div><div><div class="stat-label">Active Emergencies</div><div class="stat-value"><?= $activeAlerts ?></div><div class="stat-sub"><?= $delayedToday ?> delayed today</div></div></div></div>
    </div>

    <!-- Charts -->
    <div class="row g-4">
      <!-- Depot Revenue Bar -->
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header-custom"><i class="fa fa-chart-bar text-success"></i> Revenue by Depot</div>
          <div class="card-body-custom"><canvas id="depotRevenueChart" height="100"></canvas></div>
        </div>
      </div>
      <!-- Complaint Doughnut -->
      <div class="col-lg-5">
        <div class="card">
          <div class="card-header-custom"><i class="fa fa-chart-pie text-danger"></i> Complaints by Category</div>
          <div class="card-body-custom"><canvas id="complaintChart"></canvas></div>
        </div>
      </div>
    </div>

    <!-- Navigation Cards -->
    <div class="row g-3 mt-3">
      <?php
      $links=[['📊','Depot Performance','depot_performance.php'],['💬','Complaint Analytics','complaint_analytics.php'],
              ['⏱','Delay Report','delay_report.php'],['🚨','Emergency Report','emergency_report.php']];
      foreach($links as [$i,$l,$h]):?>
      <div class="col-sm-3">
        <a href="<?=$h?>" class="feature-card d-block text-decoration-none">
          <div style="font-size:32px;margin-bottom:10px"><?=$i?></div>
          <div style="font-weight:700;font-size:14px"><?=$l?></div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('depotRevenueChart'),{
  type:'bar',
  data:{
    labels:<?= json_encode(array_map(fn($d)=>str_replace(' Depot','',$d['depot_name']),$depotRevenue)) ?>,
    datasets:[{label:'Revenue (₹)',data:<?= json_encode(array_column($depotRevenue,'revenue')) ?>,backgroundColor:['rgba(26,107,60,.7)','rgba(13,110,253,.7)','rgba(124,58,237,.7)','rgba(220,53,69,.7)','rgba(255,193,7,.7)','rgba(15,118,110,.7)','rgba(133,77,14,.7)'],borderRadius:6}]
  },
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'₹'+v}}}}
});
new Chart(document.getElementById('complaintChart'),{
  type:'doughnut',
  data:{
    labels:<?= json_encode(array_map(fn($c)=>ucfirst(str_replace('_',' ',$c['category'])),$complaintCats)) ?>,
    datasets:[{data:<?= json_encode(array_column($complaintCats,'cnt')) ?>,backgroundColor:['#dc3545','#ffc107','#0dcaf0','#198754','#6f42c1','#fd7e14'],borderWidth:0}]
  },
  options:{responsive:true,plugins:{legend:{position:'bottom'}}}
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
