<?php
// includes/sidebar_passenger.php
if (!defined('APP_NAME')) require_once __DIR__ . '/../config/db.php';
$user = currentUser();
$base = APP_URL . '/passenger';
$currentPage = basename($_SERVER['PHP_SELF']);

$links = [
  ['icon'=>'fa-house',            'label'=>'Dashboard',         'href'=>"$base/dashboard.php",    'file'=>'dashboard.php'],
  ['icon'=>'fa-bus',              'label'=>'Bus Services',      'href'=>"$base/search_bus.php",   'file'=>'search_bus.php'],
  ['icon'=>'fa-ticket',           'label'=>'My Bookings',       'href'=>"$base/my_tickets.php",   'file'=>'my_tickets.php'],
  ['icon'=>'fa-id-card',          'label'=>'Bus Passes',        'href'=>"$base/bus_pass.php",     'file'=>'bus_pass.php'],
  ['icon'=>'fa-map-location-dot', 'label'=>'Live Bus Track',    'href'=>"$base/live_tracking.php",'file'=>'live_tracking.php'],
  ['icon'=>'fa-comments',         'label'=>'Grievances',        'href'=>"$base/complaints.php",   'file'=>'complaints.php'],
  ['icon'=>'fa-box-open',         'label'=>'Lost & Found',      'href'=>"$base/lost_found.php",   'file'=>'lost_found.php'],
  ['icon'=>'fa-robot',            'label'=>'AI Assistant',      'href'=>"$base/chatbot.php",      'file'=>'chatbot.php'],
  ['icon'=>'fa-bell',             'label'=>'Notifications',     'href'=>"$base/notifications.php",'file'=>'notifications.php'],
  ['icon'=>'fa-chart-pie',        'label'=>'Reports & Analytics','href'=>"$base/dashboard.php#analytics",'file'=>'analytics'],
  ['icon'=>'fa-hand-holding-heart','label'=>'Schemes',          'href'=>"$base/dashboard.php#schemes",'file'=>'schemes'],
  ['icon'=>'fa-credit-card',      'label'=>'e-Payments',        'href'=>"$base/my_tickets.php",   'file'=>'epayments'],
];
?>
<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Left Sidebar -->
<aside class="sidebar tn-egov-sidebar" id="appSidebar">
  <div class="sidebar-brand d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <div class="emblem-brand-container">
        <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Tamil Nadu Government Emblem" class="emblem-logo-main" style="width:36px;height:36px">
      </div>
      <div class="sidebar-brand-text">
        <div class="brand-name" style="font-size:13px;font-weight:800;letter-spacing:0.5px">GOVERNMENT OF</div>
        <div class="brand-name" style="font-size:14px;font-weight:800;color:#60a5fa">TAMIL NADU</div>
        <div class="brand-sub" style="font-size:8.5px;color:rgba(255,255,255,.6);letter-spacing:0.5px">TRUTH ALONE TRIUMPHS</div>
      </div>
    </div>
    <button class="sidebar-toggle-btn d-lg-none" id="sidebarCloseBtn" style="background:none;border:none;color:#fff;font-size:18px">
      <i class="fa fa-xmark"></i>
    </button>
  </div>

  <nav class="sidebar-nav">
    <?php foreach ($links as $l): ?>
    <a href="<?= $l['href'] ?>" class="sidebar-link <?= $currentPage === $l['file'] ? 'active' : '' ?>">
      <div class="sidebar-icon-box"><i class="fa <?= $l['icon'] ?>"></i></div>
      <span><?= $l['label'] ?></span>
      <?php if ($l['file'] === 'notifications.php'): ?>
        <?php
          if (!isset($myNotifsCount)) {
            $db_sb = getDB();
            $u_sb = currentUser();
            $uid_sb = (int)($u_sb['id'] ?? 0);
            $stmt_sb = $db_sb->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? OR user_id IS NULL");
            $stmt_sb->execute([$uid_sb]);
            $myNotifsCount = (int)$stmt_sb->fetchColumn();
          }
        ?>
        <?php if ($myNotifsCount > 0): ?>
        <span class="badge rounded-pill bg-danger ms-auto" style="font-size:10px"><?= $myNotifsCount ?></span>
        <?php endif; ?>
      <?php endif; ?>
    </a>
    <?php endforeach; ?>

    <div class="sidebar-section-label">Account</div>
    <a href="<?= APP_URL ?>/passenger/dashboard.php#profile" class="sidebar-link">
      <div class="sidebar-icon-box"><i class="fa fa-user"></i></div>
      <span>Profile & Settings</span>
    </a>
    <a href="<?= APP_URL ?>/auth/logout.php" class="sidebar-link sidebar-logout">
      <div class="sidebar-icon-box"><i class="fa fa-right-from-bracket"></i></div>
      <span>Logout</span>
    </a>
  </nav>

  <!-- Bottom Tamil Nadu Vector Temple Landmark Illustration -->
  <div class="sidebar-tn-vector p-3 text-center" style="position:relative;margin-top:auto;background:rgba(255,255,255,0.03);border-top:1px solid rgba(255,255,255,0.06)">
    <svg viewBox="0 0 300 80" style="width:100%;height:45px;opacity:0.35;fill:#ffffff">
      <!-- Temple Gopuram Outline -->
      <path d="M150 5 L160 25 L165 25 L170 45 L175 45 L180 70 L120 70 L125 45 L130 45 L135 25 L140 25 Z" />
      <path d="M145 10 L155 10 L153 20 L147 20 Z" />
      <path d="M80 35 L90 70 L70 70 Z" />
      <path d="M220 35 L230 70 L210 70 Z" />
      <rect x="10" y="65" width="280" height="5" rx="2" />
    </svg>
    <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
      <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Emblem" style="width:20px;height:20px">
      <div style="text-align:left">
        <div style="font-size:10px;font-weight:800;color:#fff;letter-spacing:0.5px">TAMIL NADU</div>
        <div style="font-size:7.5px;color:rgba(255,255,255,0.5);letter-spacing:0.3px">WHERE STORIES NEVER END</div>
      </div>
    </div>
  </div>
</aside>
