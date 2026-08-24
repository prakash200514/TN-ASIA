<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$user = currentUser();
$db   = getDB();
$uid  = (int)$user['id'];

// Handle Mark All as Read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_all_read') {
    $stmt = $db->prepare("UPDATE notifications SET is_read=1 WHERE user_id=? OR user_id IS NULL");
    $stmt->execute([$uid]);
    redirectWithMessage(APP_URL . '/passenger/notifications.php', 'success', 'All notifications marked as read.');
}

// Handle Mark Single as Read
if (isset($_GET['mark_read'])) {
    $notifId = (int)$_GET['mark_read'];
    $stmt = $db->prepare("UPDATE notifications SET is_read=1 WHERE notif_id=? AND (user_id=? OR user_id IS NULL)");
    $stmt->execute([$notifId, $uid]);
    header('Location: ' . APP_URL . '/passenger/notifications.php');
    exit;
}

// Filter handling
$typeFilter = sanitize($_GET['type'] ?? 'all');
$validTypes = ['delay','booking','pass','complaint','emergency','general'];

if (in_array($typeFilter, $validTypes)) {
    $stmt = $db->prepare("SELECT * FROM notifications WHERE (user_id=? OR user_id IS NULL) AND type=? ORDER BY created_at DESC");
    $stmt->execute([$uid, $typeFilter]);
} else {
    $stmt = $db->prepare("SELECT * FROM notifications WHERE (user_id=? OR user_id IS NULL) ORDER BY created_at DESC");
    $stmt->execute([$uid]);
}
$notifications = $stmt->fetchAll();

// Count unread
$stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE (user_id=? OR user_id IS NULL) AND is_read=0");
$stmt->execute([$uid]);
$unreadTotal = (int)$stmt->fetchColumn();

$flash = getFlash();
$pageTitle = 'Notifications Center – TNSTC Passenger Portal';
include __DIR__ . '/../includes/header.php';
?>

<div class="app-layout">
  <!-- Left Sidebar -->
  <?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>

  <!-- Main Content -->
  <div class="main-content">

    <!-- Topbar -->
    <div class="egov-topbar" style="background:#ffffff;border-bottom:1px solid #e2e8f0;padding:12px 24px;position:sticky;top:0;z-index:900;display:flex;align-items:center;justify-content:space-between">
      <div class="d-flex align-items-center gap-3">
        <button class="sidebar-toggle btn btn-sm btn-light d-lg-none" id="sidebarToggle"><i class="fa fa-bars"></i></button>
        <h5 class="m-0 fw-bold" style="color:#0f172a;font-size:16px"><i class="fa fa-bell text-primary me-2"></i>Notifications Center</h5>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
          <div style="width:36px;height:36px;background:linear-gradient(135deg,#0284c7,#0369a1);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px">
            <?= strtoupper(substr($user['name'],0,1)) ?>
          </div>
          <div class="d-none d-md-block">
            <div style="font-weight:700;font-size:13px;color:#0f172a;line-height:1.1"><?= htmlspecialchars($user['name']) ?></div>
            <div style="font-size:10.5px;color:#64748b">Citizen / Passenger</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Page Body -->
    <div class="page-content p-4" style="background:#f8fafc;flex:1">

      <?php if (!empty($flash['msg'])): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show shadow-sm" role="alert" style="border-radius:12px">
          <i class="fa fa-circle-check me-2"></i><?= htmlspecialchars($flash['msg']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Header Card -->
      <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius:16px;background:linear-gradient(135deg, #092047 0%, #0c336b 100%);color:#fff">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="badge bg-warning text-dark font-weight-bold" style="font-size:11px">TNSTC Alerts & Updates</span>
              <?php if ($unreadTotal > 0): ?>
                <span class="badge bg-danger" style="font-size:11px"><?= $unreadTotal ?> Unread</span>
              <?php endif; ?>
            </div>
            <h3 style="font-weight:800;margin:0 0 4px;font-size:22px">Notifications & System Alerts</h3>
            <p style="margin:0;font-size:13px;opacity:0.85">Stay updated on your ticket bookings, bus pass applications, schedule delays, and official announcements.</p>
          </div>

          <?php if ($unreadTotal > 0): ?>
            <form method="POST" class="m-0">
              <input type="hidden" name="action" value="mark_all_read">
              <button type="submit" class="btn btn-light btn-sm fw-bold px-3 py-2" style="border-radius:10px;color:#092047;font-size:12.5px">
                <i class="fa fa-check-double me-1 text-success"></i> Mark All as Read
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <!-- Filter Pills Bar -->
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div class="d-flex flex-wrap gap-2">
          <a href="notifications.php" class="btn btn-sm <?= $typeFilter === 'all' ? 'btn-primary fw-bold' : 'btn-white text-secondary' ?>" style="border-radius:20px;padding:6px 16px;font-size:12.5px;box-shadow:0 2px 6px rgba(0,0,0,0.03)">
            All Notifications
          </a>
          <a href="notifications.php?type=general" class="btn btn-sm <?= $typeFilter === 'general' ? 'btn-primary fw-bold' : 'btn-white text-secondary' ?>" style="border-radius:20px;padding:6px 16px;font-size:12.5px;box-shadow:0 2px 6px rgba(0,0,0,0.03)">
            📢 General
          </a>
          <a href="notifications.php?type=booking" class="btn btn-sm <?= $typeFilter === 'booking' ? 'btn-primary fw-bold' : 'btn-white text-secondary' ?>" style="border-radius:20px;padding:6px 16px;font-size:12.5px;box-shadow:0 2px 6px rgba(0,0,0,0.03)">
            🎟️ Bookings
          </a>
          <a href="notifications.php?type=pass" class="btn btn-sm <?= $typeFilter === 'pass' ? 'btn-primary fw-bold' : 'btn-white text-secondary' ?>" style="border-radius:20px;padding:6px 16px;font-size:12.5px;box-shadow:0 2px 6px rgba(0,0,0,0.03)">
            💳 Bus Passes
          </a>
          <a href="notifications.php?type=delay" class="btn btn-sm <?= $typeFilter === 'delay' ? 'btn-primary fw-bold' : 'btn-white text-secondary' ?>" style="border-radius:20px;padding:6px 16px;font-size:12.5px;box-shadow:0 2px 6px rgba(0,0,0,0.03)">
            ⏱️ Delays
          </a>
          <a href="notifications.php?type=complaint" class="btn btn-sm <?= $typeFilter === 'complaint' ? 'btn-primary fw-bold' : 'btn-white text-secondary' ?>" style="border-radius:20px;padding:6px 16px;font-size:12.5px;box-shadow:0 2px 6px rgba(0,0,0,0.03)">
            💬 Grievances
          </a>
          <a href="notifications.php?type=emergency" class="btn btn-sm <?= $typeFilter === 'emergency' ? 'btn-primary fw-bold' : 'btn-white text-secondary' ?>" style="border-radius:20px;padding:6px 16px;font-size:12.5px;box-shadow:0 2px 6px rgba(0,0,0,0.03)">
            🚨 Emergency
          </a>
        </div>
        <div style="font-size:12px;color:#64748b" class="fw-semibold">
          Showing <?= count($notifications) ?> notification(s)
        </div>
      </div>

      <!-- Notifications List -->
      <div class="card border-0 shadow-sm p-3" style="border-radius:16px">
        <?php if (!empty($notifications)): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($notifications as $n): 
              $isUnread = !(bool)$n['is_read'];
              $bgClass = $isUnread ? 'bg-light' : '';
              
              // Icon & color helper based on type
              $typeIcon = 'fa-info-circle';
              $iconBg   = '#eff6ff';
              $iconColor= '#2563eb';
              $actionUrl = '';
              $actionBtn = '';

              switch ($n['type']) {
                case 'booking':
                  $typeIcon  = 'fa-ticket';
                  $iconBg    = '#f0fdf4';
                  $iconColor = '#16a34a';
                  $actionUrl = 'my_tickets.php';
                  $actionBtn = 'View Tickets';
                  break;
                case 'pass':
                  $typeIcon  = 'fa-id-card';
                  $iconBg    = '#faf5ff';
                  $iconColor = '#9333ea';
                  $actionUrl = 'bus_pass.php';
                  $actionBtn = 'View Pass';
                  break;
                case 'complaint':
                  $typeIcon  = 'fa-comments';
                  $iconBg    = '#fef9c3';
                  $iconColor = '#854d0e';
                  $actionUrl = 'complaints.php';
                  $actionBtn = 'View Grievance';
                  break;
                case 'delay':
                  $typeIcon  = 'fa-clock';
                  $iconBg    = '#fff7ed';
                  $iconColor = '#ea580c';
                  $actionUrl = 'live_tracking.php';
                  $actionBtn = 'Live Track';
                  break;
                case 'emergency':
                  $typeIcon  = 'fa-triangle-exclamation';
                  $iconBg    = '#fef2f2';
                  $iconColor = '#ef4444';
                  $actionUrl = 'chatbot.php';
                  $actionBtn = 'Contact Helpdesk';
                  break;
                default:
                  $typeIcon  = 'fa-bullhorn';
                  $iconBg    = '#eff6ff';
                  $iconColor = '#2563eb';
                  break;
              }
            ?>
            <div class="list-group-item border-0 p-3 mb-2 rounded-3 <?= $bgClass ?>" style="transition:all 0.2s;border:1px solid <?= $isUnread ? '#bfdbfe' : '#f1f5f9' ?> !important">
              <div class="d-flex align-items-start gap-3">
                <div style="width:42px;height:42px;border-radius:12px;background:<?= $iconBg ?>;color:<?= $iconColor ?>;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
                  <i class="fa <?= $typeIcon ?>"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                    <div class="d-flex align-items-center gap-2">
                      <h6 class="m-0 fw-bold" style="color:#0f172a;font-size:14px"><?= htmlspecialchars($n['title']) ?></h6>
                      <?php if ($isUnread): ?>
                        <span class="badge bg-danger rounded-pill" style="font-size:9px">New</span>
                      <?php endif; ?>
                    </div>
                    <div style="font-size:11px;color:#94a3b8" class="fw-semibold">
                      <i class="fa fa-clock me-1"></i><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?>
                    </div>
                  </div>
                  <p class="m-0 text-secondary" style="font-size:12.5px;line-height:1.5"><?= htmlspecialchars($n['message']) ?></p>
                  
                  <?php if (!empty($actionUrl) || $isUnread): ?>
                    <div class="d-flex align-items-center gap-3 mt-2">
                      <?php if (!empty($actionUrl)): ?>
                        <a href="<?= $actionUrl ?>" class="btn btn-sm btn-outline-primary fw-bold py-1 px-3" style="border-radius:6px;font-size:11.5px">
                          <?= $actionBtn ?> &rarr;
                        </a>
                      <?php endif; ?>
                      <?php if ($isUnread): ?>
                        <a href="notifications.php?mark_read=<?= $n['notif_id'] ?>" class="text-decoration-none text-muted fw-semibold" style="font-size:11.5px">
                          <i class="fa fa-check me-1 text-success"></i>Mark as read
                        </a>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-5">
            <div class="mx-auto mb-3" style="width:64px;height:64px;border-radius:50%;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:28px">
              <i class="fa fa-bell-slash"></i>
            </div>
            <h5 class="fw-bold" style="color:#0f172a">No Notifications Found</h5>
            <p class="text-muted mb-3" style="font-size:13px;max-width:360px" class="mx-auto">You're all caught up! There are no notifications matching your selected filter at this time.</p>
            <a href="notifications.php" class="btn btn-sm btn-primary fw-bold px-3 py-2" style="border-radius:8px">View All Notifications</a>
          </div>
        <?php endif; ?>
      </div>

    </div><!-- /.page-content -->

    <!-- Footer -->
    <footer style="background:#ffffff;border-top:1px solid #e2e8f0;padding:16px 24px;font-size:12px;color:#64748b">
      <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <div>© 2026 Government of Tamil Nadu. All Rights Reserved.</div>
        <div class="d-flex gap-3">
          <a href="#" style="color:#64748b;text-decoration:none">Terms & Conditions</a>
          <a href="#" style="color:#64748b;text-decoration:none">Privacy Policy</a>
        </div>
      </div>
    </footer>

  </div><!-- /.main-content -->
</div><!-- /.app-layout -->

<?php include __DIR__ . '/../includes/footer.php'; ?>
