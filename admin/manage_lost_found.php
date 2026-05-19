<?php
// admin/manage_lost_found.php
require_once __DIR__ . '/../config/db.php';
requireLogin('admin'); $db=getDB(); $flash=getFlash();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $iid=(int)$_POST['item_id']; $status=sanitize($_POST['status']??'open');
    $db->prepare("UPDATE lost_found SET status=? WHERE item_id=?")->execute([$status,$iid]);
    redirectWithMessage(APP_URL.'/admin/manage_lost_found.php','success','Item updated.');
}
$items=$db->query("SELECT lf.*,u.name reporter_name,b.bus_number,d.depot_name FROM lost_found lf JOIN users u ON u.user_id=lf.reported_by LEFT JOIN buses b ON b.bus_id=lf.bus_id LEFT JOIN depots d ON d.depot_id=lf.depot_id ORDER BY lf.created_at DESC")->fetchAll();
$pageTitle='Lost & Found'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">📦 Manage Lost & Found</div></div>
  <div class="page-content">
    <?php if(!empty($flash['msg'])): ?><div class="flash-banner flash-<?=$flash['type']?> mb-3"><?=htmlspecialchars($flash['msg'])?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-box-open me-2 text-success"></i>All Lost & Found Items (<?=count($items)?>)</h5></div>
      <div class="table-responsive"><table class="data-table">
        <thead><tr><th>#</th><th>Item</th><th>Type</th><th>Reported By</th><th>Bus</th><th>Depot</th><th>Date</th><th>Status</th><th>Update</th></tr></thead>
        <tbody>
          <?php foreach($items as $i): ?>
          <tr>
            <td><?=$i['item_id']?></td>
            <td><strong><?=htmlspecialchars($i['item_name'])?></strong><div style="font-size:11px;color:#6c757d"><?=htmlspecialchars(substr($i['description'],0,40))?></div></td>
            <td><span class="badge-custom badge-<?=$i['report_type']==='found'?'success':'warning'?>"><?=ucfirst($i['report_type'])?></span></td>
            <td><?=htmlspecialchars($i['reporter_name'])?></td>
            <td><?=htmlspecialchars($i['bus_number']??'—')?></td>
            <td><?=htmlspecialchars($i['depot_name']??'—')?></td>
            <td style="font-size:12px"><?=date('d M Y',strtotime($i['created_at']))?></td>
            <td><span class="badge-custom badge-<?=$i['status']==='open'?'warning':($i['status']==='claimed'?'success':'secondary')?>"><?=ucfirst($i['status'])?></span></td>
            <td>
              <form method="POST" style="display:flex;gap:4px">
                <input type="hidden" name="item_id" value="<?=$i['item_id']?>">
                <select name="status" class="form-select form-select-sm" style="width:110px">
                  <?php foreach(['open','claimed','closed'] as $s): ?><option value="<?=$s?>" <?=$i['status']===$s?'selected':''?>><?=ucfirst($s)?></option><?php endforeach; ?>
                </select>
                <button type="submit" class="btn-icon edit"><i class="fa fa-check"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
