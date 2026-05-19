<?php
// minister/depot_performance.php
require_once __DIR__ . '/../config/db.php';
requireLogin('minister'); $db=getDB();
$perf=$db->query("SELECT d.depot_id,d.depot_name,
  COUNT(DISTINCT b.bus_id) buses,
  COUNT(DISTINCT s.schedule_id) trips,
  SUM(CASE WHEN s.status='completed' THEN 1 ELSE 0 END) completed,
  SUM(CASE WHEN s.status='delayed' THEN 1 ELSE 0 END) delayed,
  COALESCE(SUM(t.fare),0) revenue,
  COUNT(DISTINCT c.complaint_id) complaints
  FROM depots d
  LEFT JOIN buses b ON b.depot_id=d.depot_id
  LEFT JOIN schedules s ON s.bus_id=b.bus_id
  LEFT JOIN tickets t ON t.schedule_id=s.schedule_id AND t.payment_status='paid'
  LEFT JOIN complaints c ON c.depot_id=d.depot_id
  GROUP BY d.depot_id ORDER BY revenue DESC")->fetchAll();
$pageTitle='Depot Performance'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">📊 Depot Performance</div></div>
  <div class="page-content">
    <div class="table-card mb-4">
      <div class="table-header"><h5><i class="fa fa-building me-2 text-success"></i>All Depot Performance</h5></div>
      <div class="table-responsive"><table class="data-table">
        <thead><tr><th>Depot</th><th>Active Buses</th><th>Total Trips</th><th>Completed</th><th>Delayed</th><th>Revenue</th><th>Complaints</th><th>On-Time %</th></tr></thead>
        <tbody>
          <?php foreach($perf as $p):
            $ot = $p['trips']>0?round($p['completed']/$p['trips']*100):0;
            $otColor=$ot>=90?'#166534':($ot>=75?'#854d0e':'#991b1b');
          ?>
          <tr>
            <td><strong><?=htmlspecialchars($p['depot_name'])?></strong></td>
            <td><?=$p['buses']?></td>
            <td><?=$p['trips']?></td>
            <td><?=$p['completed']?></td>
            <td><?=$p['delayed']?></td>
            <td style="color:#1a6b3c;font-weight:700">₹<?=number_format($p['revenue'],2)?></td>
            <td><?=$p['complaints']?></td>
            <td><strong style="color:<?=$otColor?>"><?=$ot?>%</strong></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
    <div class="card"><div class="card-header-custom"><i class="fa fa-chart-bar text-success"></i> Revenue Comparison</div>
      <div class="card-body-custom"><canvas id="chart" height="80"></canvas></div></div>
  </div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chart'),{type:'bar',
  data:{labels:<?=json_encode(array_column($perf,'depot_name'))?>,
        datasets:[{label:'Revenue ₹',data:<?=json_encode(array_column($perf,'revenue'))?>,backgroundColor:'rgba(26,107,60,.7)',borderRadius:6}]},
  options:{indexAxis:'y',responsive:true,plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,ticks:{callback:v=>'₹'+v}}}}});
</script>
<?php include __DIR__.'/../includes/footer.php'; ?>
