<?php
// admin/manage_tickets.php
require_once __DIR__ . '/../config/db.php';
requireLogin('admin'); $db=getDB(); $flash=getFlash();
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['cancel_ticket'])) {
    $db->prepare("UPDATE tickets SET ticket_status='cancelled' WHERE ticket_id=?")->execute([(int)$_POST['ticket_id']]);
    redirectWithMessage(APP_URL.'/admin/manage_tickets.php','success','Ticket cancelled.');
}
$dateFilter = $_GET['date'] ?? '';
$where = $dateFilter ? "WHERE DATE(t.booking_date)='" . $db->quote($dateFilter) . "'" : '';
$tickets=$db->query("SELECT t.*,u.name passenger_name,s.travel_date,r.source,r.destination,b.bus_number FROM tickets t JOIN users u ON u.user_id=t.passenger_id JOIN schedules s ON s.schedule_id=t.schedule_id JOIN routes r ON r.route_id=s.route_id JOIN buses b ON b.bus_id=s.bus_id ORDER BY t.booking_date DESC LIMIT 200")->fetchAll();
$pageTitle='Manage Tickets'; include __DIR__.'/../includes/header.php';
?>
<div class="app-layout"><?php include __DIR__.'/../includes/sidebar_admin.php'; ?>
<div class="main-content">
  <div class="topbar"><button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button><div class="topbar-title">🎫 Manage Tickets</div></div>
  <div class="page-content">
    <?php if(!empty($flash['msg'])): ?><div class="flash-banner flash-<?=$flash['type']?> mb-3"><?=htmlspecialchars($flash['msg'])?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header"><h5><i class="fa fa-ticket me-2 text-success"></i>All Bookings (<?=count($tickets)?>)</h5></div>
      <div class="table-responsive"><table class="data-table">
        <thead><tr><th>#</th><th>Passenger</th><th>Bus</th><th>Route</th><th>Date</th><th>Seat</th><th>Fare</th><th>Payment</th><th>Status</th><th>QR</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach($tickets as $t): ?>
          <tr>
            <td><?=$t['ticket_id']?></td>
            <td><?=htmlspecialchars($t['passenger_name'])?></td>
            <td><?=htmlspecialchars($t['bus_number'])?></td>
            <td><?=htmlspecialchars($t['source'].'→'.$t['destination'])?></td>
            <td style="font-size:12px"><?=$t['travel_date']?></td>
            <td>S<?=str_pad($t['seat_number'],2,'0',STR_PAD_LEFT)?></td>
            <td>₹<?=number_format($t['fare'],2)?></td>
            <td><span class="badge-custom badge-<?=$t['payment_status']==='paid'?'success':'warning'?>"><?=ucfirst($t['payment_status'])?></span></td>
            <td><span class="badge-custom badge-<?=$t['ticket_status']==='active'?'success':($t['ticket_status']==='used'?'secondary':'danger')?>"><?=ucfirst($t['ticket_status'])?></span></td>
            <td><a href="<?=qrCodeUrl($t['qr_code'])?>" target="_blank" class="btn-icon view"><i class="fa fa-qrcode"></i></a></td>
            <td>
              <?php if($t['ticket_status']==='active'): ?>
              <form method="POST" style="display:inline" onsubmit="return confirm('Cancel ticket?')">
                <input type="hidden" name="ticket_id" value="<?=$t['ticket_id']?>">
                <button type="submit" name="cancel_ticket" class="btn-icon delete"><i class="fa fa-times"></i></button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
