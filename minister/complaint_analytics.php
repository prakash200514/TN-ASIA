<?php
// minister/complaint_analytics.php
require_once __DIR__ . '/../config/db.php';
requireLogin('minister'); $db=getDB();
$cats=$db->query("SELECT category,COUNT(*) cnt FROM complaints GROUP BY category ORDER BY cnt DESC")->fetchAll();
$statuses=$db->query("SELECT status,COUNT(*) cnt FROM complaints GROUP BY status")->fetchAll();
$trend=$db->query("SELECT DATE_FORMAT(created_at,'%Y-%m') mon,COUNT(*) cnt FROM complaints GROUP BY mon ORDER BY mon DESC LIMIT 6")->fetchAll();
$pageTitle='Complaint Analytics'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">💬 Complaint Analytics</div></div>
  <div class="page-content">
    <div class="row g-4">
      <div class="col-md-5"><div class="card"><div class="card-header-custom"><i class="fa fa-chart-pie text-danger"></i> By Category</div><div class="card-body-custom"><canvas id="catChart"></canvas></div></div></div>
      <div class="col-md-4"><div class="card"><div class="card-header-custom"><i class="fa fa-circle-half-stroke"></i> By Status</div><div class="card-body-custom"><canvas id="statusChart"></canvas></div></div></div>
      <div class="col-md-3">
        <div class="table-card"><div class="table-header"><h5>Category Counts</h5></div>
          <?php foreach($cats as $c): ?><div style="padding:10px 16px;border-bottom:1px solid #f1f3f5;display:flex;justify-content:space-between">
            <span><?=ucfirst(str_replace('_',' ',$c['category']))?></span><strong><?=$c['cnt']?></strong></div><?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="card mt-4"><div class="card-header-custom"><i class="fa fa-chart-line text-primary"></i> Monthly Trend</div>
      <div class="card-body-custom"><canvas id="trendChart" height="60"></canvas></div></div>
  </div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const colors=['#dc3545','#ffc107','#0dcaf0','#198754','#6f42c1','#fd7e14'];
new Chart(document.getElementById('catChart'),{type:'doughnut',data:{labels:<?=json_encode(array_map(fn($c)=>ucfirst(str_replace('_',' ',$c['category'])),$cats))?>,datasets:[{data:<?=json_encode(array_column($cats,'cnt'))?>,backgroundColor:colors,borderWidth:0}]},options:{plugins:{legend:{position:'bottom'}}}});
new Chart(document.getElementById('statusChart'),{type:'pie',data:{labels:<?=json_encode(array_map(fn($s)=>ucfirst(str_replace('_',' ',$s['status'])),$statuses))?>,datasets:[{data:<?=json_encode(array_column($statuses,'cnt'))?>,backgroundColor:['#dc3545','#ffc107','#198754','#6c757d'],borderWidth:0}]},options:{plugins:{legend:{position:'bottom'}}}});
new Chart(document.getElementById('trendChart'),{type:'line',data:{labels:<?=json_encode(array_reverse(array_column($trend,'mon')))?>,datasets:[{label:'Complaints',data:<?=json_encode(array_reverse(array_column($trend,'cnt')))?>,borderColor:'#dc3545',backgroundColor:'rgba(220,53,69,.1)',fill:true,tension:.4}]},options:{responsive:true,plugins:{legend:{display:false}}}});
</script>
<?php include __DIR__.'/../includes/footer.php'; ?>
