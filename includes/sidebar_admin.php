<?php
// includes/sidebar_admin.php
// Shared by: admin, depot_manager, minister roles
$user = currentUser();
$role = $user['role'];

$navItems = [];

if ($role === 'admin') {
  $base = APP_URL . '/admin';
  $navItems = [
    ['icon'=>'fa-gauge-high',   'label'=>'Dashboard',       'href'=>"$base/dashboard.php"],
    'sep1' => 'Manage',
    ['icon'=>'fa-building',     'label'=>'Depots',           'href'=>"$base/manage_depots.php"],
    ['icon'=>'fa-bus',          'label'=>'Buses',            'href'=>"$base/manage_buses.php"],
    ['icon'=>'fa-route',        'label'=>'Routes',           'href'=>"$base/manage_routes.php"],
    ['icon'=>'fa-location-dot', 'label'=>'Bus Stops',        'href'=>"$base/manage_stops.php"],
    ['icon'=>'fa-calendar-days','label'=>'Schedules',        'href'=>"$base/manage_schedules.php"],
    ['icon'=>'fa-users',        'label'=>'Users',            'href'=>"$base/manage_users.php"],
    'sep2' => 'Operations',
    ['icon'=>'fa-ticket',       'label'=>'Tickets',          'href'=>"$base/manage_tickets.php"],
    ['icon'=>'fa-id-card',      'label'=>'Bus Passes',       'href'=>"$base/manage_passes.php"],
    ['icon'=>'fa-comments',     'label'=>'Complaints',       'href'=>"$base/manage_complaints.php"],
    ['icon'=>'fa-box-open',     'label'=>'Lost & Found',     'href'=>"$base/manage_lost_found.php"],
    'sep3' => 'Reports',
    ['icon'=>'fa-chart-bar',    'label'=>'Reports',          'href'=>"$base/reports.php"],
  ];
} elseif ($role === 'depot_manager') {
  $base = APP_URL . '/depot_manager';
  $navItems = [
    ['icon'=>'fa-gauge-high',   'label'=>'Dashboard',       'href'=>"$base/dashboard.php"],
    'sep1' => 'My Depot',
    ['icon'=>'fa-bus',          'label'=>'Buses',            'href'=>"$base/buses.php"],
    ['icon'=>'fa-calendar-days','label'=>'Schedules',        'href'=>"$base/schedules.php"],
    ['icon'=>'fa-users',        'label'=>'Staff',            'href'=>"$base/staff.php"],
    ['icon'=>'fa-map',          'label'=>'Live Map',         'href'=>"$base/live_map.php"],
    'sep2' => 'Services',
    ['icon'=>'fa-comments',     'label'=>'Complaints',       'href'=>"$base/complaints.php"],
    ['icon'=>'fa-id-card',      'label'=>'Pass Verification','href'=>"$base/pass_verification.php"],
    ['icon'=>'fa-box-open',     'label'=>'Lost & Found',     'href'=>"$base/lost_found.php"],
    'sep3' => 'Reports',
    ['icon'=>'fa-chart-bar',    'label'=>'Revenue Report',   'href'=>"$base/reports.php"],
  ];
} elseif ($role === 'minister') {
  $base = APP_URL . '/minister';
  $navItems = [
    ['icon'=>'fa-gauge-high',     'label'=>'District Dashboard', 'href'=>"$base/dashboard.php"],
    'sep1' => 'Analytics',
    ['icon'=>'fa-building',       'label'=>'Depot Performance',  'href'=>"$base/depot_performance.php"],
    ['icon'=>'fa-comments',       'label'=>'Complaint Analytics','href'=>"$base/complaint_analytics.php"],
    ['icon'=>'fa-clock',          'label'=>'Delay Report',       'href'=>"$base/delay_report.php"],
    ['icon'=>'fa-triangle-exclamation','label'=>'Emergency Report','href'=>"$base/emergency_report.php"],
  ];
}
?>
<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="appSidebar">
  <div class="sidebar-brand">
    <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Tamil Nadu Government Emblem" style="object-fit: contain;">
    <div class="sidebar-brand-text">
      <div class="brand-name">TNSTC</div>
      <div class="brand-sub">Tirunelveli District</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <?php
    foreach ($navItems as $key => $item):
      if (is_string($item)):
    ?>
      <div class="sidebar-section-label"><?= htmlspecialchars($item) ?></div>
    <?php else: ?>
      <a href="<?= $item['href'] ?>" class="sidebar-link">
        <i class="fa <?= $item['icon'] ?>"></i>
        <?= htmlspecialchars($item['label']) ?>
      </a>
    <?php endif; endforeach; ?>

    <div class="sidebar-section-label">Account</div>
    <a href="<?= APP_URL ?>/auth/logout.php" class="sidebar-link">
      <i class="fa fa-right-from-bracket"></i> Logout
    </a>
  </nav>

  <div class="sidebar-footer">
    <strong><?= htmlspecialchars($user['name']) ?></strong><br>
    <?= ucfirst(str_replace('_',' ', $user['role'])) ?>
  </div>
</aside>
