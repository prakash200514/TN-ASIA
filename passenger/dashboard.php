<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$user = currentUser();
$db   = getDB();
$uid  = $user['id'];

$myTickets    = $db->prepare("SELECT COUNT(*) FROM tickets WHERE passenger_id=? AND ticket_status='active'")->execute([$uid]) ? $db->query("SELECT COUNT(*) FROM tickets WHERE passenger_id=$uid AND ticket_status='active'")->fetchColumn() : 0;
$myPass       = $db->query("SELECT COUNT(*) FROM bus_pass WHERE passenger_id=$uid AND status='approved' AND valid_to>=CURDATE()")->fetchColumn();
$myComplaints = $db->query("SELECT COUNT(*) FROM complaints WHERE passenger_id=$uid AND status='open'")->fetchColumn();
$recentTickets= $db->prepare("SELECT t.*,s.departure_time,s.travel_date,r.source,r.destination,b.bus_number FROM tickets t JOIN schedules s ON s.schedule_id=t.schedule_id JOIN routes r ON r.route_id=s.route_id JOIN buses b ON b.bus_id=s.bus_id WHERE t.passenger_id=? ORDER BY t.booking_date DESC LIMIT 5");
$recentTickets->execute([$uid]);
$recent = $recentTickets->fetchAll();

$pageTitle = 'Passenger Dashboard';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div class="main-content">

  <!-- Topbar -->
  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title">👋 Welcome, <?= htmlspecialchars($user['name']) ?></div>
    <a href="<?= APP_URL ?>/auth/logout.php" class="btn btn-sm btn-outline-secondary ms-auto" style="border-radius:8px;font-size:12px;">
      <i class="fa fa-right-from-bracket me-1"></i>Logout
    </a>
  </div>

  <div class="page-content">

  <div class="mb-4 p-4 rounded-3 animate-fade-right" style="background:linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 50%, var(--accent-dark) 100%);color:#fff;box-shadow:0 10px 30px rgba(var(--primary-rgb),0.15)">
    <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.02em">Welcome back, <?= htmlspecialchars($user['name']) ?>! 👋</h1>
    <p style="opacity:.85;margin:6px 0 0;font-size:14.5px;font-weight:500">Tirunelveli District TNSTC – Passenger Dashboard</p>
  </div>

  <!-- Quick Actions -->
  <div class="row g-3 mb-4">
    <?php
    $actions = [
      ['🔍','Search Bus','Find and book a bus','search_bus.php','var(--primary)'],
      ['🎫','My Tickets','View all your tickets','my_tickets.php','var(--accent)'],
      ['🪪','Bus Pass','Apply or view pass status','bus_pass.php','#7c3aed'],
      ['📍','Track Bus','Live bus location map','live_tracking.php','#d97706'],
      ['💬','Complaints','Submit or track complaints','complaints.php','#854d0e'],
      ['📦','Lost & Found','Report or claim items','lost_found.php','#0d9488'],
      ['🤖','AI Chatbot','Ask route questions','chatbot.php','#06038D'],
    ];
    $aidx = 0;
    foreach ($actions as [$icon,$label,$desc,$href,$color]):
      $delayClass = 'delay-' . (($aidx % 4) + 1);
      $aidx++;
    ?>
    <div class="col-6 col-sm-4 col-md-3 animate-scale-in <?= $delayClass ?>">
      <a href="<?= $href ?>" class="role-card d-block text-center p-3 text-decoration-none" style="border-radius:16px;border:1px solid var(--gray-200)">
        <div style="font-size:32px;margin-bottom:8px"><?= $icon ?></div>
        <div style="font-weight:800;font-size:14px;color:<?= $color ?>;letter-spacing:-0.01em"><?= $label ?></div>
        <div style="font-size:11.5px;color:#71717a;margin-top:4px;line-height:1.4"><?= $desc ?></div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- KPI Cards -->
  <div class="row g-3 mb-4">
    <div class="col-sm-4 animate-fade-up delay-1">
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fa fa-ticket"></i></div>
        <div><div class="stat-label">Active Tickets</div><div class="stat-value"><?= $myTickets ?></div></div>
      </div>
    </div>
    <div class="col-sm-4 animate-fade-up delay-2">
      <div class="stat-card">
        <div class="stat-icon purple"><i class="fa fa-id-card"></i></div>
        <div><div class="stat-label">Valid Pass</div><div class="stat-value"><?= $myPass ?></div></div>
      </div>
    </div>
    <div class="col-sm-4 animate-fade-up delay-3">
      <div class="stat-card">
        <div class="stat-icon orange"><i class="fa fa-comments"></i></div>
        <div><div class="stat-label">Open Complaints</div><div class="stat-value"><?= $myComplaints ?></div></div>
      </div>
    </div>
  </div>

  <!-- Recent Tickets -->
  <div class="table-card mb-4 animate-fade-up delay-4">
    <div class="table-header" style="padding:18px 24px">
      <h5 style="font-weight:800;letter-spacing:-0.02em;margin:0"><i class="fa fa-clock me-2 text-success"></i>Recent Tickets</h5>
      <a href="my_tickets.php" style="font-size:13.5px;color:var(--primary);font-weight:600">View All →</a>
    </div>
    <?php if (empty($recent)): ?>
      <div class="empty-state"><i class="fa fa-ticket"></i>No tickets booked yet.<br><a href="search_bus.php" style="color:var(--primary)">Search & book a bus</a></div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="data-table">
        <thead><tr><th>Ticket ID</th><th>Bus</th><th>Route</th><th>Date</th><th>Seat</th><th>Fare</th><th>Status</th><th>QR</th></tr></thead>
        <tbody>
          <?php foreach ($recent as $t): ?>
          <tr>
            <td><strong>#<?= $t['ticket_id'] ?></strong></td>
            <td><?= htmlspecialchars($t['bus_number']) ?></td>
            <td><?= htmlspecialchars($t['source'].' → '.$t['destination']) ?></td>
            <td><?= htmlspecialchars($t['travel_date']) ?></td>
            <td>Seat <?= $t['seat_number'] ?></td>
            <td>₹<?= number_format($t['fare'],2) ?></td>
            <td><span class="badge-custom badge-<?= $t['ticket_status']==='active'?'success':($t['ticket_status']==='used'?'secondary':'danger') ?>"><?= ucfirst($t['ticket_status']) ?></span></td>
            <td><a href="<?= htmlspecialchars(qrCodeUrl($t['qr_code'])) ?>" target="_blank" class="btn-icon view" title="View QR"><i class="fa fa-qrcode"></i></a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    </div>
    <?php endif; ?>
  </div><!-- /.table-card -->

  </div><!-- /.page-content -->
</div><!-- /.main-content -->
</div><!-- /.app-layout -->
<?php include __DIR__ . '/../includes/footer.php'; ?>
