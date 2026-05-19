<?php
// minister/emergency_report.php
require_once __DIR__ . '/../config/db.php';
requireLogin('minister'); $db=getDB();
$alerts=$db->query("SELECT ea.*,b.bus_number,d.depot_name,u.name driver_name FROM emergency_alerts ea LEFT JOIN buses b ON b.bus_id=ea.bus_id JOIN depots d ON d.depot_id=ea.depot_id LEFT JOIN drivers dr ON dr.driver_id=ea.driver_id LEFT JOIN users u ON u.user_id=dr.user_id ORDER BY ea.created_at DESC")->fetchAll();
$active=$db->query("SELECT COUNT(*) FROM emergency_alerts WHERE status='active'")->fetchColumn();
$resolved=$db->query("SELECT COUNT(*) FROM emergency_alerts WHERE status='resolved'")->fetchColumn();
$pageTitle='Emergency Report'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">🚨 Emergency Report</div></div>
  <div class="page-content">
    <div class="row g-3 mb-4">
      <div class="col-sm-6"><div class="stat-card"><div class="stat-icon red"><i class="fa fa-triangle-exclamation"></i></div><div><div class="stat-label">Active Emergencies</div><div class="stat-value"><?=$active?></div></div></div></div>
      <div class="col-sm-6"><div class="stat-card"><div class="stat-icon green"><i class="fa fa-check-circle"></i></div><div><div class="stat-label">Resolved</div><div class="stat-value"><?=$resolved?></div></div></div></div>
    </div>
    <div class="table-card">
      <div class="table-header"><h5 style="color:#dc3545"><i class="fa fa-triangle-exclamation me-2"></i>All Emergency Alerts</h5></div>
      <?php if(empty($alerts)): ?><div class="empty-state"><i class="fa fa-check-circle" style="color:#1a6b3c"></i>No emergency alerts recorded.</div>
      <?php else: ?>
      <div class="table-responsive"><table class="data-table">
        <thead><tr><th>#</th><th>Bus</th><th>Depot</th><th>Driver</th><th>Message</th><th>Status</th><th>Time</th></tr></thead>
        <tbody>
          <?php foreach($alerts as $a): ?>
          <tr style="<?=$a['status']==='active'?'background:#fff5f5':''?>">
            <td><?=$a['alert_id']?></td>
            <td><?=htmlspecialchars($a['bus_number']??'—')?></td>
            <td><?=htmlspecialchars($a['depot_name'])?></td>
            <td><?=htmlspecialchars($a['driver_name']??'—')?></td>
            <td><?=htmlspecialchars(substr($a['message'],0,80))?><?=strlen($a['message'])>80?'…':''?></td>
            <td><span class="badge-custom badge-<?=$a['status']==='active'?'danger':'success'?>"><?=ucfirst($a['status'])?></span></td>
            <td style="font-size:11px"><?=date('d M, h:i A',strtotime($a['created_at']))?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
      <?php endif; ?>
    </div>
  </div>
</div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
