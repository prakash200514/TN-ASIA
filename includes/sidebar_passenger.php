<?php
// includes/sidebar_passenger.php
if (!defined('APP_NAME')) require_once __DIR__ . '/../config/db.php';
$user = currentUser();
$base = APP_URL . '/passenger';
$currentPage = basename($_SERVER['PHP_SELF']);

$links = [
  ['icon'=>'fa-gauge-high',      'label'=>'Dashboard',    'href'=>"$base/dashboard.php",    'file'=>'dashboard.php'],
  ['icon'=>'fa-magnifying-glass','label'=>'Search Bus',   'href'=>"$base/search_bus.php",   'file'=>'search_bus.php'],
  ['icon'=>'fa-ticket',          'label'=>'My Tickets',   'href'=>"$base/my_tickets.php",   'file'=>'my_tickets.php'],
  ['icon'=>'fa-id-card',         'label'=>'Bus Pass',     'href'=>"$base/bus_pass.php",     'file'=>'bus_pass.php'],
  ['icon'=>'fa-map-location-dot','label'=>'Live Track',   'href'=>"$base/live_tracking.php",'file'=>'live_tracking.php'],
  ['icon'=>'fa-comments',        'label'=>'Complaints',   'href'=>"$base/complaints.php",   'file'=>'complaints.php'],
  ['icon'=>'fa-box-open',        'label'=>'Lost & Found', 'href'=>"$base/lost_found.php",   'file'=>'lost_found.php'],
  ['icon'=>'fa-robot',           'label'=>'AI Assistant', 'href'=>"$base/chatbot.php",      'file'=>'chatbot.php'],
];
?>
<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Left Sidebar -->
<aside class="sidebar" id="appSidebar">
  <div class="sidebar-brand">
    <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Tamil Nadu Government Emblem" style="object-fit: contain; flex-shrink: 0;">
    <div class="sidebar-brand-text">
      <div class="brand-name">TNSTC</div>
      <div class="brand-sub">Passenger Portal</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="sidebar-section-label">Navigation</div>
    <?php foreach ($links as $l): ?>
    <a href="<?= $l['href'] ?>" class="sidebar-link <?= $currentPage === $l['file'] ? 'active' : '' ?>">
      <i class="fa <?= $l['icon'] ?>"></i>
      <?= $l['label'] ?>
    </a>
    <?php endforeach; ?>

    <div class="sidebar-section-label">Account</div>
    <a href="<?= APP_URL ?>/auth/logout.php" class="sidebar-link">
      <i class="fa fa-right-from-bracket"></i> Logout
    </a>
  </nav>

  <div class="sidebar-footer">
    <strong><?= htmlspecialchars($user['name']) ?></strong><br>
    <span style="opacity:.6">Passenger</span>
  </div>
</aside>
