<?php
// minister/delay_report.php
require_once __DIR__ . '/../config/db.php';
requireLogin('minister'); $db=getDB();
$delays=$db->query("SELECT s.*,r.route_number,r.source,r.destination,b.bus_number,d.depot_name FROM schedules s JOIN routes r ON r.route_id=s.route_id JOIN buses b ON b.bus_id=s.bus_id JOIN depots d ON d.depot_id=b.depot_id WHERE s.status='delayed' ORDER BY s.delay_minutes DESC LIMIT 50")->fetchAll();
$avgDelay=$db->query("SELECT AVG(delay_minutes) FROM schedules WHERE status='delayed'")->fetchColumn();
$totalDelayed=$db->query("SELECT COUNT(*) FROM schedules WHERE status='delayed'")->fetchColumn();
$worstRoute=$db->query("SELECT r.source,r.destination,COUNT(*) cnt FROM schedules s JOIN routes r ON r.route_id=s.route_id WHERE s.status='delayed' GROUP BY s.route_id ORDER BY cnt DESC LIMIT 1")->fetch();
$pageTitle='Delay Report'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">⏱ Delay Report</div></div>
  <div class="page-content">
    <div class="row g-3 mb-4">
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon orange"><i class="fa fa-clock"></i></div><div><div class="stat-label">Total Delayed Trips</div><div class="stat-value"><?=$totalDelayed?></div></div></div></div>
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon red"><i class="fa fa-hourglass-half"></i></div><div><div class="stat-label">Avg Delay</div><div class="stat-value"><?=round($avgDelay)?> min</div></div></div></div>
      <div class="col-sm-4"><div class="stat-card"><div class="stat-icon purple"><i class="fa fa-route"></i></div><div><div class="stat-label">Most Delayed Route</div><div class="stat-value" style="font-size:14px"><?=$worstRoute?htmlspecialchars($worstRoute['source'].'→'.$worstRoute['destination']):'N/A'?></div></div></div></div>
    </div>
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-clock me-2 text-warning"></i>Delayed Trips</h5></div>
      <div class="table-responsive"><table class="data-table">
        <thead><tr><th>#</th><th>Bus</th><th>Route</th><th>Depot</th><th>Date</th><th>Delay</th><th>Reason</th></tr></thead>
        <tbody>
          <?php foreach($delays as $d): ?>
          <tr>
            <td><?=$d['schedule_id']?></td>
            <td><?=htmlspecialchars($d['bus_number'])?></td>
            <td><?=htmlspecialchars($d['route_number'].': '.$d['source'].'→'.$d['destination'])?></td>
            <td><?=htmlspecialchars($d['depot_name'])?></td>
            <td><?=$d['travel_date']?></td>
            <td><span class="badge-custom badge-warning"><?=$d['delay_minutes']?> min</span></td>
            <td style="font-size:12px"><?=htmlspecialchars($d['delay_reason']??'Not specified')?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
