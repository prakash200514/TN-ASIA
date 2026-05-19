<?php
// admin/manage_users.php
require_once __DIR__ . '/../config/db.php';
requireLogin('admin'); $db=getDB(); $flash=getFlash();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $act=$_POST['action']??'';
    if ($act==='toggle') {
        $uid=(int)$_POST['user_id'];
        $cur=$db->query("SELECT status FROM users WHERE user_id=$uid")->fetchColumn();
        $new=$cur==='active'?'inactive':'active';
        $db->prepare("UPDATE users SET status=? WHERE user_id=?")->execute([$new,$uid]);
        redirectWithMessage(APP_URL.'/admin/manage_users.php','success','User status updated.');
    } elseif ($act==='create_staff') {
        $name=sanitize($_POST['name']??''); $email=trim($_POST['email']??''); $phone=sanitize($_POST['phone']??'');
        $pass=password_hash($_POST['password']??'Staff@123',PASSWORD_DEFAULT);
        $role=sanitize($_POST['role']??'driver');
        $db->prepare("INSERT INTO users (name,email,phone,password,role,status) VALUES (?,?,?,?,?,'active')")->execute([$name,$email,$phone,$pass,$role]);
        $newId=$db->lastInsertId();
        if ($role==='driver') {
            $lic=sanitize($_POST['license']??'N/A'); $did=(int)$_POST['depot_id'];
            $db->prepare("INSERT INTO drivers (user_id,license_number,depot_id) VALUES (?,?,?)")->execute([$newId,$lic,$did]);
        } elseif ($role==='conductor') {
            $did=(int)$_POST['depot_id'];
            $db->prepare("INSERT INTO conductors (user_id,depot_id) VALUES (?,?)")->execute([$newId,$did]);
        }
        redirectWithMessage(APP_URL.'/admin/manage_users.php','success','Staff account created.');
    }
}

$roleFilter = $_GET['role']??'';
$search     = sanitize($_GET['q']??'');
$where='WHERE 1'; $params=[];
if ($roleFilter) { $where.=' AND role=?'; $params[]=$roleFilter; }
if ($search)     { $where.=' AND (name LIKE ? OR email LIKE ?)'; $params[]="%$search%"; $params[]="%$search%"; }
$users=$db->prepare("SELECT * FROM users $where ORDER BY created_at DESC LIMIT 100");
$users->execute($params); $users=$users->fetchAll();
$depots=$db->query("SELECT depot_id,depot_name FROM depots ORDER BY depot_name")->fetchAll();
$pageTitle='Manage Users'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">👤 Manage Users</div></div>
  <div class="page-content">
    <?php if(!empty($flash['msg'])): ?><div class="flash-banner flash-<?=$flash['type']?> mb-3"><?=htmlspecialchars($flash['msg'])?></div><?php endif; ?>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body-custom">
        <form class="row g-2 align-items-end">
          <div class="col-sm-4"><input type="text" name="q" class="form-control" placeholder="Search name or email…" value="<?=htmlspecialchars($search)?>"></div>
          <div class="col-sm-3"><select name="role" class="form-select"><option value="">All Roles</option><?php foreach(['passenger','driver','conductor','depot_manager','minister','admin'] as $r): ?><option value="<?=$r?>" <?=$roleFilter===$r?'selected':''?>><?=ucfirst(str_replace('_',' ',$r))?></option><?php endforeach; ?></select></div>
          <div class="col-sm-2"><button type="submit" class="btn btn-success w-100">Filter</button></div>
          <div class="col-sm-3 text-end"><button type="button" class="btn-primary-custom btn-sm-custom" data-bs-toggle="modal" data-bs-target="#createModal"><i class="fa fa-plus"></i> Create Staff</button></div>
        </form>
      </div>
    </div>

    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-users me-2 text-success"></i>Users (<?=count($users)?>)</h5></div>
      <div class="table-responsive"><table class="data-table">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach($users as $u): ?>
          <tr>
            <td><?=$u['user_id']?></td>
            <td><strong><?=htmlspecialchars($u['name'])?></strong></td>
            <td><?=htmlspecialchars($u['email'])?></td>
            <td><?=htmlspecialchars($u['phone'])?></td>
            <td><span class="badge-custom badge-primary"><?=ucfirst(str_replace('_',' ',$u['role']))?></span></td>
            <td><span class="badge-custom badge-<?=$u['status']==='active'?'success':($u['status']==='pending'?'warning':'secondary')?>"><?=ucfirst($u['status'])?></span></td>
            <td style="font-size:12px"><?=date('d M Y',strtotime($u['created_at']))?></td>
            <td>
              <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="toggle"><input type="hidden" name="user_id" value="<?=$u['user_id']?>">
                <button type="submit" class="btn-icon <?=$u['status']==='active'?'delete':'view'?>" title="<?=$u['status']==='active'?'Deactivate':'Activate'?>">
                  <i class="fa fa-<?=$u['status']==='active'?'ban':'check'?>"></i>
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div></div>

<!-- Create Staff Modal -->
<div class="modal fade" id="createModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
  <div class="modal-header" style="background:#1a6b3c;color:#fff"><h5>Create Staff Account</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <form method="POST"><input type="hidden" name="action" value="create_staff">
  <div class="modal-body row g-3">
    <div class="col-6"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required></div>
    <div class="col-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="col-4"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" required></div>
    <div class="col-4"><label class="form-label">Role</label><select name="role" class="form-select" id="roleSelect" onchange="toggleLic()"><option value="driver">Driver</option><option value="conductor">Conductor</option><option value="depot_manager">Depot Manager</option></select></div>
    <div class="col-4"><label class="form-label">Depot</label><select name="depot_id" class="form-select"><?php foreach($depots as $d): ?><option value="<?=$d['depot_id']?>"><?=htmlspecialchars($d['depot_name'])?></option><?php endforeach; ?></select></div>
    <div class="col-6" id="licDiv"><label class="form-label">License Number (Driver)</label><input type="text" name="license" class="form-control"></div>
    <div class="col-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" placeholder="Default: Staff@123"></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn" style="background:#1a6b3c;color:#fff">Create Account</button></div>
  </form>
</div></div></div>
<script>
function toggleLic(){document.getElementById('licDiv').style.display=document.getElementById('roleSelect').value==='driver'?'block':'none';}
toggleLic();
</script>
<?php include __DIR__.'/../includes/footer.php'; ?>
