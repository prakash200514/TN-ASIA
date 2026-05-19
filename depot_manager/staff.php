<?php
// depot_manager/staff.php
require_once __DIR__ . '/../config/db.php';
requireLogin('depot_manager');
$uid=$db=null; $uid=(int)currentUser()['id']; $db=getDB();
$depot=$db->query("SELECT * FROM depots WHERE manager_id=$uid LIMIT 1")->fetch(); if(!$depot) die('No depot.');
$did=(int)$depot['depot_id'];
$drivers=$db->query("SELECT dr.*,u.name,u.phone,u.email FROM drivers dr JOIN users u ON u.user_id=dr.user_id WHERE dr.depot_id=$did ORDER BY u.name")->fetchAll();
$conductors=$db->query("SELECT co.*,u.name,u.phone,u.email FROM conductors co JOIN users u ON u.user_id=co.user_id WHERE co.depot_id=$did ORDER BY u.name")->fetchAll();
$pageTitle='Staff Management'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">👥 Staff Management</div></div>
  <div class="page-content">
    <ul class="nav nav-tabs mb-4"><li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#driverTab">Drivers (<?=count($drivers)?>)</a></li><li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#condTab">Conductors (<?=count($conductors)?>)</a></li></ul>
    <div class="tab-content">
      <div class="tab-pane fade show active" id="driverTab">
        <div class="table-card"><div class="table-responsive"><table class="data-table"><thead><tr><th>#</th><th>Name</th><th>Phone</th><th>License</th><th>Status</th></tr></thead><tbody>
          <?php foreach($drivers as $d): ?><tr><td><?=$d['driver_id']?></td><td><strong><?=htmlspecialchars($d['name'])?></strong></td><td><?=htmlspecialchars($d['phone'])?></td><td><?=htmlspecialchars($d['license_number'])?></td><td><span class="badge-custom badge-<?=$d['status']==='active'?'success':'warning'?>"><?=ucfirst($d['status'])?></span></td></tr><?php endforeach; ?>
        </tbody></table></div></div>
      </div>
      <div class="tab-pane fade" id="condTab">
        <div class="table-card"><div class="table-responsive"><table class="data-table"><thead><tr><th>#</th><th>Name</th><th>Phone</th><th>Status</th></tr></thead><tbody>
          <?php foreach($conductors as $c): ?><tr><td><?=$c['conductor_id']?></td><td><strong><?=htmlspecialchars($c['name'])?></strong></td><td><?=htmlspecialchars($c['phone'])?></td><td><span class="badge-custom badge-<?=$c['status']==='active'?'success':'warning'?>"><?=ucfirst($c['status'])?></span></td></tr><?php endforeach; ?>
        </tbody></table></div></div>
      </div>
    </div>
  </div>
</div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
