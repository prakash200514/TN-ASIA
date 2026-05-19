<?php
// includes/sidebar_passenger.php
if (!defined('APP_NAME')) require_once __DIR__ . '/../config/db.php';
$user = currentUser();
$base = APP_URL . '/passenger';
$links = [
  ['icon'=>'fa-gauge-high',    'label'=>'Dashboard',    'href'=>"$base/dashboard.php"],
  ['icon'=>'fa-magnifying-glass','label'=>'Search Bus', 'href'=>"$base/search_bus.php"],
  ['icon'=>'fa-ticket',        'label'=>'My Tickets',   'href'=>"$base/my_tickets.php"],
  ['icon'=>'fa-id-card',       'label'=>'Bus Pass',     'href'=>"$base/bus_pass.php"],
  ['icon'=>'fa-map-location-dot','label'=>'Live Track', 'href'=>"$base/live_tracking.php"],
  ['icon'=>'fa-comments',      'label'=>'Complaints',   'href'=>"$base/complaints.php"],
  ['icon'=>'fa-box-open',      'label'=>'Lost & Found', 'href'=>"$base/lost_found.php"],
  ['icon'=>'fa-robot',         'label'=>'AI Assistant', 'href'=>"$base/chatbot.php"],
];
?>
<!-- Top Navbar (mobile-friendly passenger nav) -->
<nav class="navbar navbar-expand-lg" style="background:#1a6b3c;padding:0 20px;height:56px;position:sticky;top:0;z-index:900">
  <a class="navbar-brand text-white fw-bold d-flex align-items-center gap-2" href="<?= APP_URL ?>">
    <span style="font-size:22px">🚌</span> TNSTC
  </a>
  <button class="navbar-toggler border-0 text-white ms-auto me-2" type="button" id="sidebarToggle">
    <i class="fa fa-bars"></i>
  </button>
  <div class="d-none d-lg-flex align-items-center gap-1 ms-auto">
    <?php foreach ($links as $l): ?>
    <a href="<?= $l['href'] ?>" class="text-white px-2 py-2 rounded" style="font-size:13px;display:flex;align-items:center;gap:6px;transition:.2s" onmouseover="this.style.background='rgba(255,255,255,.15)'" onmouseout="this.style.background='transparent'">
      <i class="fa <?= $l['icon'] ?>" style="font-size:12px"></i><?= $l['label'] ?>
    </a>
    <?php endforeach; ?>
    <a href="<?= APP_URL ?>/auth/logout.php" class="btn btn-sm btn-warning text-dark fw-bold ms-2">Logout</a>
  </div>
</nav>

<!-- Mobile Sidebar overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="appSidebar">
  <div class="sidebar-brand">
    <div style="width:40px;height:40px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px">🚌</div>
    <div class="sidebar-brand-text">
      <div class="brand-name">TNSTC</div>
      <div class="brand-sub">Passenger Portal</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <?php foreach ($links as $l): ?>
    <a href="<?= $l['href'] ?>" class="sidebar-link"><i class="fa <?= $l['icon'] ?>"></i> <?= $l['label'] ?></a>
    <?php endforeach; ?>
    <a href="<?= APP_URL ?>/auth/logout.php" class="sidebar-link"><i class="fa fa-right-from-bracket"></i> Logout</a>
  </nav>
  <div class="sidebar-footer"><?= htmlspecialchars($user['name']) ?> &nbsp;|&nbsp; Passenger</div>
</aside>
