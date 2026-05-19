<?php
// depot_manager/reports.php
require_once __DIR__ . '/../config/db.php';
requireLogin('depot_manager');
$uid=(int)currentUser()['id']; $db=getDB();
$depot=$db->query("SELECT * FROM depots WHERE manager_id=$uid LIMIT 1")->fetch(); if(!$depot) die('No depot.');
$did=(int)$depot['depot_id'];

$month = $_GET['month'] ?? date('Y-m');
[$yr,$mn] = explode('-',$month);

// Revenue by day
$revenue=$db->query("SELECT DATE(t.booking_date) d,SUM(t.fare) total FROM tickets t JOIN schedules s ON s.schedule_id=t.schedule_id JOIN buses b ON b.bus_id=s.bus_id WHERE b.depot_id=$did AND t.payment_status='paid' AND DATE_FORMAT(t.booking_date,'%Y-%m')='$month' GROUP BY DATE(t.booking_date) ORDER BY d")->fetchAll();
$totalRev = array_sum(array_column($revenue,'total'));
$totalTickets=$db->query("SELECT COUNT(*) FROM tickets t JOIN schedules s ON s.schedule_id=t.schedule_id JOIN buses b ON b.bus_id=s.bus_id WHERE b.depot_id=$did AND t.payment_status='paid' AND DATE_FORMAT(t.booking_date,'%Y-%m')='$month'")->fetchColumn();
$totalTrips=$db->query("SELECT COUNT(*) FROM schedules s JOIN buses b ON b.bus_id=s.bus_id WHERE b.depot_id=$did AND DATE_FORMAT(s.travel_date,'%Y-%m')='$month'")->fetchColumn();
$compCount=$db->query("SELECT COUNT(*) FROM complaints WHERE depot_id=$did AND DATE_FORMAT(created_at,'%Y-%m')='$month'")->fetchColumn();

$chartLabels = json_encode(array_column($revenue,'d'));
$chartData   = json_encode(array_column($revenue,'total'));

$pageTitle = 'Depot Reports';
include __DIR__ . '/../includes/header.php';
$extraScripts = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">📊 Revenue & Reports</div></div>
  <div class="page-content">
    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
      <form class="d-flex gap-2 align-items-center">
        <label class="form-label mb-0">Month:</label>
        <input type="month" name="month" value="<?=htmlspecialchars($month)?>" class="form-control-custom" style="width:160px" onchange="this.form.submit()">
      </form>
    </div>
    <div class="row g-3 mb-4">
      <div class="col-sm-3"><div class="stat-card"><div class="stat-icon teal"><i class="fa fa-indian-rupee-sign"></i></div><div><div class="stat-label">Total Revenue</div><div class="stat-value">₹<?=number_format($totalRev,2)?></div></div></div></div>
      <div class="col-sm-3"><div class="stat-card"><div class="stat-icon blue"><i class="fa fa-ticket"></i></div><div><div class="stat-label">Tickets Sold</div><div class="stat-value"><?=$totalTickets?></div></div></div></div>
      <div class="col-sm-3"><div class="stat-card"><div class="stat-icon green"><i class="fa fa-bus"></i></div><div><div class="stat-label">Total Trips</div><div class="stat-value"><?=$totalTrips?></div></div></div></div>
      <div class="col-sm-3"><div class="stat-card"><div class="stat-icon orange"><i class="fa fa-comments"></i></div><div><div class="stat-label">Complaints</div><div class="stat-value"><?=$compCount?></div></div></div></div>
    </div>
    <div class="card">
      <div class="card-header-custom"><i class="fa fa-chart-bar text-success me-2"></i>Daily Revenue – <?=date('F Y',strtotime($month.'-01'))?></div>
      <div class="card-body-custom"><canvas id="revenueChart" height="80"></canvas></div>
    </div>
  </div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('revenueChart'),{
  type:'bar',
  data:{labels:<?=$chartLabels?>,datasets:[{label:'Revenue (₹)',data:<?=$chartData?>,backgroundColor:'rgba(26,107,60,.7)',borderColor:'#1a6b3c',borderWidth:2,borderRadius:6}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'₹'+v}}}}
});
</script>
<?php include __DIR__.'/../includes/footer.php'; ?>
