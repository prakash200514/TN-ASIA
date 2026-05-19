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
<div style="background:#f8f9fa;min-height:100vh">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>

<div style="max-width:1100px;margin:0 auto;padding:24px 16px">
  <!-- Welcome Banner -->
  <div class="mb-4 p-4 rounded-3" style="background:linear-gradient(135deg,#1a6b3c,#0d4a28);color:#fff">
    <h1 style="font-size:22px;font-weight:700">Welcome back, <?= htmlspecialchars($user['name']) ?>! 👋</h1>
    <p style="opacity:.8;margin:4px 0 0;font-size:14px">Tirunelveli District TNSTC – Passenger Dashboard</p>
  </div>

  <!-- Quick Actions -->
  <div class="row g-3 mb-4">
    <?php
    $actions = [
      ['🔍','Search Bus','Find and book a bus','search_bus.php','#1a6b3c'],
      ['🎫','My Tickets','View all your tickets','my_tickets.php','#1d4ed8'],
      ['🪪','Bus Pass','Apply or view pass status','bus_pass.php','#7c3aed'],
      ['📍','Track Bus','Live bus location map','live_tracking.php','#c2410c'],
      ['💬','Complaints','Submit or track complaints','complaints.php','#854d0e'],
      ['📦','Lost & Found','Report or claim items','lost_found.php','#0f766e'],
      ['🤖','AI Chatbot','Ask route questions','chatbot.php','#075985'],
    ];
    foreach ($actions as [$icon,$label,$desc,$href,$color]):
    ?>
    <div class="col-6 col-sm-4 col-md-3">
      <a href="<?= $href ?>" class="d-block text-center p-3 rounded-3 text-decoration-none animate-fade"
         style="background:#fff;border:1px solid #e9ecef;transition:all .2s"
         onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 20px rgba(0,0,0,.1)'"
         onmouseout="this.style.transform='';this.style.boxShadow=''">
        <div style="font-size:28px;margin-bottom:8px"><?= $icon ?></div>
        <div style="font-weight:700;font-size:13px;color:<?= $color ?>"><?= $label ?></div>
        <div style="font-size:11px;color:#6c757d;margin-top:2px"><?= $desc ?></div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- KPI Cards -->
  <div class="row g-3 mb-4">
    <div class="col-sm-4">
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fa fa-ticket"></i></div>
        <div><div class="stat-label">Active Tickets</div><div class="stat-value"><?= $myTickets ?></div></div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="stat-card">
        <div class="stat-icon purple"><i class="fa fa-id-card"></i></div>
        <div><div class="stat-label">Valid Pass</div><div class="stat-value"><?= $myPass ?></div></div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="stat-card">
        <div class="stat-icon orange"><i class="fa fa-comments"></i></div>
        <div><div class="stat-label">Open Complaints</div><div class="stat-value"><?= $myComplaints ?></div></div>
      </div>
    </div>
  </div>

  <!-- Recent Tickets -->
  <div class="table-card mb-4">
    <div class="table-header">
      <h5><i class="fa fa-clock me-2 text-success"></i>Recent Tickets</h5>
      <a href="my_tickets.php" style="font-size:13px;color:#1a6b3c">View All →</a>
    </div>
    <?php if (empty($recent)): ?>
      <div class="empty-state"><i class="fa fa-ticket"></i>No tickets booked yet.<br><a href="search_bus.php" style="color:#1a6b3c">Search & book a bus</a></div>
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
    <?php endif; ?>
  </div>
</div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
