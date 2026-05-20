<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$uid = (int)currentUser()['id'];
$db  = getDB();

// Handle cancel
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['cancel_ticket'])) {
    $tid = (int)$_POST['ticket_id'];
    $db->prepare("UPDATE tickets SET ticket_status='cancelled' WHERE ticket_id=? AND passenger_id=?")->execute([$tid,$uid]);
    redirectWithMessage(APP_URL.'/passenger/my_tickets.php','success','Ticket cancelled successfully.');
}

$page  = max(1,(int)($_GET['p']??1));
$limit = 10;
$offset= ($page-1)*$limit;
$total = $db->query("SELECT COUNT(*) FROM tickets WHERE passenger_id=$uid")->fetchColumn();
$pages = ceil($total/$limit);

$stmt = $db->prepare("SELECT t.*,s.departure_time,s.arrival_time,s.travel_date,r.source,r.destination,b.bus_number FROM tickets t JOIN schedules s ON s.schedule_id=t.schedule_id JOIN routes r ON r.route_id=s.route_id JOIN buses b ON b.bus_id=s.bus_id WHERE t.passenger_id=? ORDER BY t.booking_date DESC LIMIT $limit OFFSET $offset");
$stmt->execute([$uid]);
$tickets = $stmt->fetchAll();

$pageTitle = 'My Tickets';
include __DIR__ . '/../includes/header.php';
?>
<div style="background:#f8f9fa;min-height:100vh">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div style="max-width:1100px;margin:0 auto;padding:24px 16px">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 style="font-size:20px;font-weight:700"><i class="fa fa-ticket me-2 text-success"></i>My Tickets</h1>
    <a href="search_bus.php" class="btn-primary-custom btn-sm-custom"><i class="fa fa-plus"></i> Book New</a>
  </div>

  <?php if (empty($tickets)): ?>
    <div class="empty-state card"><i class="fa fa-ticket"></i>No tickets booked yet.<br><a href="search_bus.php" style="color:var(--primary)">Book your first bus →</a></div>
  <?php else: ?>
  <div class="row g-3">
    <?php foreach ($tickets as $t): $color=['active'=>'var(--primary)','used'=>'#6c757d','cancelled'=>'#dc3545'][$t['ticket_status']]??'#6c757d'; ?>
    <div class="col-md-6">
      <div class="ticket-card">
        <div class="ticket-header" style="background:linear-gradient(135deg,<?= $color ?>,<?= $color ?>dd)">
          <div class="ticket-id">Ticket #<?= $t['ticket_id'] ?> · <?= ucfirst($t['ticket_status']) ?></div>
          <div class="ticket-route"><?= htmlspecialchars($t['source'].' → '.$t['destination']) ?></div>
        </div>
        <div class="ticket-body">
          <div class="ticket-row"><span class="label">Bus</span><span class="value"><?= htmlspecialchars($t['bus_number']) ?></span></div>
          <div class="ticket-row"><span class="label">Travel Date</span><span class="value"><?= htmlspecialchars($t['travel_date']) ?></span></div>
          <div class="ticket-row"><span class="label">Departs</span><span class="value"><?= htmlspecialchars(substr($t['departure_time'],0,5)) ?></span></div>
          <div class="ticket-row"><span class="label">Seat</span><span class="value">Seat <?= str_pad($t['seat_number'],2,'0',STR_PAD_LEFT) ?></span></div>
          <div class="ticket-row"><span class="label">Fare</span><span class="value" style="color:var(--primary)">₹<?= number_format($t['fare'],2) ?></span></div>
        </div>
        <div class="ticket-qr">
          <img src="<?= qrCodeUrl($t['qr_code']) ?>" alt="QR" style="width:120px;height:120px">
          <div style="font-size:10px;color:#6c757d;margin-top:4px;word-break:break-all"><?= htmlspecialchars($t['qr_code']) ?></div>
        </div>
        <?php if ($t['ticket_status']==='active'): ?>
        <div style="padding:12px 16px;border-top:1px solid #e9ecef;display:flex;gap:8px;justify-content:flex-end">
          <a href="<?= qrCodeUrl($t['qr_code']) ?>" download="ticket_<?= $t['ticket_id'] ?>.png" class="btn-icon view" title="Download QR"><i class="fa fa-download"></i></a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Cancel this ticket?')">
            <input type="hidden" name="ticket_id" value="<?= $t['ticket_id'] ?>">
            <button type="submit" name="cancel_ticket" class="btn-icon delete" title="Cancel Ticket"><i class="fa fa-times"></i></button>
          </form>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
  <nav class="mt-4 d-flex justify-content-center gap-2">
    <?php for ($i=1;$i<=$pages;$i++): ?>
    <a href="?p=<?= $i ?>" class="btn btn-sm <?= $i==$page?'btn-primary':'btn-outline-secondary' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </nav>
  <?php endif; ?>
  <?php endif; ?>
</div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
