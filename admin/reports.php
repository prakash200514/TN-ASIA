<?php
// admin/reports.php
require_once __DIR__ . '/../config/db.php';
requireLogin('admin'); $db=getDB();
$month=$_GET['month']??date('Y-m');

$revenue   =$db->query("SELECT COALESCE(SUM(fare),0) FROM tickets WHERE payment_status='paid' AND DATE_FORMAT(booking_date,'%Y-%m')='$month'")->fetchColumn();
$tickets   =$db->query("SELECT COUNT(*) FROM tickets WHERE DATE_FORMAT(booking_date,'%Y-%m')='$month'")->fetchColumn();
$trips     =$db->query("SELECT COUNT(*) FROM schedules WHERE DATE_FORMAT(travel_date,'%Y-%m')='$month'")->fetchColumn();
$complaints=$db->query("SELECT COUNT(*) FROM complaints WHERE DATE_FORMAT(created_at,'%Y-%m')='$month'")->fetchColumn();
$passes    =$db->query("SELECT COUNT(*) FROM bus_pass WHERE DATE_FORMAT(created_at,'%Y-%m')='$month'")->fetchColumn();
$alerts    =$db->query("SELECT COUNT(*) FROM emergency_alerts WHERE DATE_FORMAT(created_at,'%Y-%m')='$month'")->fetchColumn();

$dailyRevenue=$db->query("SELECT DATE(booking_date) d,SUM(fare) total FROM tickets WHERE payment_status='paid' AND DATE_FORMAT(booking_date,'%Y-%m')='$month' GROUP BY d ORDER BY d")->fetchAll();
$depotRev    =$db->query("SELECT dep.depot_name,COALESCE(SUM(t.fare),0) rev FROM depots dep LEFT JOIN buses b ON b.depot_id=dep.depot_id LEFT JOIN schedules s ON s.bus_id=b.bus_id LEFT JOIN tickets t ON t.schedule_id=s.schedule_id AND t.payment_status='paid' AND DATE_FORMAT(t.booking_date,'%Y-%m')='$month' GROUP BY dep.depot_id ORDER BY rev DESC")->fetchAll();

$pageTitle='System Reports'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">📊 System Reports</div></div>
  <div class="page-content">
    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
      <form class="d-flex gap-2"><input type="month" name="month" value="<?=htmlspecialchars($month)?>" class="form-control-custom" style="width:160px" onchange="this.form.submit()"></form>
    </div>
    <div class="row g-3 mb-4">
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon teal"><i class="fa fa-indian-rupee-sign"></i></div><div><div class="stat-label">Revenue</div><div class="stat-value">₹<?=number_format($revenue,2)?></div></div></div></div>
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon blue"><i class="fa fa-ticket"></i></div><div><div class="stat-label">Tickets</div><div class="stat-value"><?=$tickets?></div></div></div></div>
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon green"><i class="fa fa-bus"></i></div><div><div class="stat-label">Trips</div><div class="stat-value"><?=$trips?></div></div></div></div>
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon orange"><i class="fa fa-comments"></i></div><div><div class="stat-label">Complaints</div><div class="stat-value"><?=$complaints?></div></div></div></div>
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon purple"><i class="fa fa-id-card"></i></div><div><div class="stat-label">Pass Applications</div><div class="stat-value"><?=$passes?></div></div></div></div>
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon red"><i class="fa fa-triangle-exclamation"></i></div><div><div class="stat-label">Emergencies</div><div class="stat-value"><?=$alerts?></div></div></div></div>
    </div>
    <div class="row g-4">
      <div class="col-md-7"><div class="card"><div class="card-header-custom"><i class="fa fa-chart-bar text-success"></i> Daily Revenue</div><div class="card-body-custom"><canvas id="dailyChart" height="100"></canvas></div></div></div>
      <div class="col-md-5"><div class="card"><div class="card-header-custom"><i class="fa fa-chart-bar text-primary"></i> Depot-wise Revenue</div><div class="card-body-custom"><canvas id="depotChart"></canvas></div></div></div>
    </div>
  </div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('dailyChart'),{type:'bar',data:{labels:<?=json_encode(array_column($dailyRevenue,'d'))?>,datasets:[{label:'₹',data:<?=json_encode(array_column($dailyRevenue,'total'))?>,backgroundColor:'rgba(26,107,60,.7)',borderRadius:6}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>'₹'+v}}}}});
new Chart(document.getElementById('depotChart'),{type:'doughnut',data:{labels:<?=json_encode(array_map(fn($d)=>str_replace(' Depot','',$d['depot_name']),$depotRev))?>,datasets:[{data:<?=json_encode(array_column($depotRev,'rev'))?>,backgroundColor:['#1a6b3c','#0a58ca','#7c3aed','#c2410c','#ffc107','#0f766e','#854d0e'],borderWidth:0}]},options:{responsive:true,plugins:{legend:{position:'bottom'}}}});
</script>
<?php include __DIR__.'/../includes/footer.php'; ?>
