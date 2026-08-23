<?php
// includes/sidebar_driver.php
if (!defined('APP_NAME')) require_once __DIR__ . '/../config/db.php';
$user = currentUser();
$base = APP_URL . '/driver';
$links = [
  ['icon'=>'fa-gauge-high',        'label'=>'Dashboard',       'href'=>"$base/dashboard.php"],
  ['icon'=>'fa-calendar-check',    'label'=>'Assigned Trips',  'href'=>"$base/assigned_trips.php"],
  ['icon'=>'fa-users',             'label'=>'Passenger List',  'href'=>"$base/passenger_list.php"],
  ['icon'=>'fa-qrcode',            'label'=>'Scan Ticket',     'href'=>"$base/scan_ticket.php"],
  ['icon'=>'fa-clock-rotate-left', 'label'=>'Delay Update',    'href'=>"$base/delay_update.php"],
  ['icon'=>'fa-triangle-exclamation','label'=>'Emergency',     'href'=>"$base/emergency.php"],
  ['icon'=>'fa-box-open',          'label'=>'Lost Item Report','href'=>"$base/lost_item_report.php"],
];
?>
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="appSidebar">
  <div class="sidebar-brand">
    <div class="emblem-brand-container">
      <img src="<?= APP_URL ?>/assets/images/cm_profile.jpg" alt="CM Profile" class="emblem-profile-bg" title="Chief Minister Office">
      <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Tamil Nadu Government Emblem" class="emblem-logo-main">
    </div>
    <div class="sidebar-brand-text">
      <div class="brand-name">TNSTC</div>
      <div class="brand-sub"><?= ucfirst($user['role']) ?> Panel</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="sidebar-section-label">Navigation</div>
    <?php foreach ($links as $l):
      $isActive = ($currentPage === basename($l['href'])) ? 'active' : '';
    ?>
    <a href="<?= $l['href'] ?>" class="sidebar-link <?= $isActive ?>">
      <div class="sidebar-icon-box"><i class="fa <?= $l['icon'] ?>"></i></div>
      <span><?= $l['label'] ?></span>
    </a>
    <?php endforeach; ?>

    <div class="sidebar-section-label">Account</div>
    <a href="<?= APP_URL ?>/auth/logout.php" class="sidebar-link sidebar-logout">
      <div class="sidebar-icon-box"><i class="fa fa-right-from-bracket"></i></div>
      <span>Logout</span>
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card-footer d-flex align-items-center gap-2">
      <div class="user-avatar-sm">
        <?= strtoupper(substr($user['name'], 0, 1)) ?>
      </div>
      <div class="user-info-sm text-truncate">
        <div class="user-name-sm text-truncate"><?= htmlspecialchars($user['name']) ?></div>
        <div class="user-role-sm"><span class="online-indicator"></span> <?= ucfirst($user['role']) ?></div>
      </div>
    </div>
  </div>
</aside>
