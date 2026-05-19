<?php
// admin/manage_complaints.php
require_once __DIR__ . '/../config/db.php';
requireLogin('admin'); $db=getDB(); $flash=getFlash();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $cid=(int)$_POST['complaint_id']; $reply=sanitize($_POST['reply']??''); $status=sanitize($_POST['status']??'in_progress');
    $db->prepare("UPDATE complaints SET reply=?,status=?,replied_by=? WHERE complaint_id=?")->execute([$reply,$status,(int)currentUser()['id'],$cid]);
    redirectWithMessage(APP_URL.'/admin/manage_complaints.php','success','Complaint updated.');
}
$complaints=$db->query("SELECT c.*,u.name passenger_name,d.depot_name FROM complaints c JOIN users u ON u.user_id=c.passenger_id LEFT JOIN depots d ON d.depot_id=c.depot_id ORDER BY FIELD(c.status,'open','in_progress','resolved','closed'),c.created_at DESC")->fetchAll();
$pageTitle='Manage Complaints'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">💬 Manage Complaints</div></div>
  <div class="page-content">
    <?php if(!empty($flash['msg'])): ?><div class="flash-banner flash-<?=$flash['type']?> mb-3"><?=htmlspecialchars($flash['msg'])?></div><?php endif; ?>
    <?php if(empty($complaints)): ?><div class="empty-state card"><i class="fa fa-comments"></i>No complaints.</div>
    <?php else: foreach($complaints as $c): $sc=['open'=>'danger','in_progress'=>'warning','resolved'=>'success','closed'=>'secondary'][$c['status']]??'secondary'; ?>
    <div class="card mb-3">
      <div class="card-body-custom">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <strong>#<?=$c['complaint_id']?> – <?=ucfirst(str_replace('_',' ',$c['category']))?></strong>
            <span class="text-muted" style="font-size:12px"> by <?=htmlspecialchars($c['passenger_name'])?></span>
            <?php if($c['depot_name']): ?> · <span style="font-size:12px;color:#1a6b3c"><?=htmlspecialchars($c['depot_name'])?></span><?php endif; ?>
            <div style="font-size:11px;color:#6c757d"><?=date('d M Y h:i A',strtotime($c['created_at']))?></div>
          </div>
          <span class="badge-custom badge-<?=$sc?>"><?=ucfirst(str_replace('_',' ',$c['status']))?></span>
        </div>
        <p style="font-size:14px;margin:0 0 12px"><?=nl2br(htmlspecialchars($c['description']))?></p>
        <?php if($c['reply']): ?><div class="p-2 rounded mb-2" style="background:#e8f5ee;font-size:13px"><strong>Admin Reply:</strong> <?=htmlspecialchars($c['reply'])?></div><?php endif; ?>
        <form method="POST" class="d-flex gap-2 flex-wrap align-items-end">
          <input type="hidden" name="complaint_id" value="<?=$c['complaint_id']?>">
          <div style="flex:1;min-width:200px"><textarea name="reply" class="form-control" rows="2" placeholder="Type reply…" required><?=htmlspecialchars($c['reply']??'')?></textarea></div>
          <select name="status" class="form-select form-select-sm" style="width:140px">
            <?php foreach(['open','in_progress','resolved','closed'] as $s): ?><option value="<?=$s?>" <?=$c['status']===$s?'selected':''?>><?=ucfirst(str_replace('_',' ',$s))?></option><?php endforeach; ?>
          </select>
          <button type="submit" class="btn-primary-custom btn-sm-custom"><i class="fa fa-reply"></i> Update</button>
        </form>
      </div>
    </div>
    <?php endforeach; endif; ?>
  </div>
</div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
