<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$uid         = (int)currentUser()['id'];
$schedule_id = (int)($_GET['schedule_id'] ?? 0);
$date        = $_GET['date'] ?? date('Y-m-d');
if (!$schedule_id) { header('Location: search_bus.php'); exit; }

$db   = getDB();
$sched= $db->prepare("SELECT s.*,b.bus_number,b.bus_type,b.total_seats,r.source,r.destination,r.distance,r.route_number,d.depot_name FROM schedules s JOIN buses b ON b.bus_id=s.bus_id JOIN routes r ON r.route_id=s.route_id JOIN depots d ON d.depot_id=b.depot_id WHERE s.schedule_id=?");
$sched->execute([$schedule_id]);
$s = $sched->fetch();
if (!$s) { header('Location: search_bus.php'); exit; }

$bookedSeats = $db->prepare("SELECT seat_number FROM tickets WHERE schedule_id=? AND ticket_status='active'");
$bookedSeats->execute([$schedule_id]);
$booked = array_column($bookedSeats->fetchAll(), 'seat_number');
$fare   = round($s['distance'] * 0.60, 2);

$pageTitle = 'Book Ticket';
include __DIR__ . '/../includes/header.php';
?>
<div style="background:#f8f9fa;min-height:100vh">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div style="max-width:1100px;margin:0 auto;padding:24px 16px">

  <h1 style="font-size:20px;font-weight:700;margin-bottom:20px"><i class="fa fa-ticket me-2 text-success"></i>Book Ticket</h1>

  <div class="row g-4">
    <!-- Seat Map -->
    <div class="col-md-7">
      <div class="card">
        <div class="card-header-custom"><i class="fa fa-bus"></i> <?= htmlspecialchars($s['bus_number']) ?> – Seat Selection</div>
        <div class="card-body-custom">
          <div class="d-flex gap-3 mb-3 flex-wrap">
            <span style="font-size:12px"><span style="display:inline-block;width:16px;height:16px;background:#e8f5ee;border:2px solid #1a6b3c;border-radius:3px;vertical-align:middle"></span> Available</span>
            <span style="font-size:12px"><span style="display:inline-block;width:16px;height:16px;background:#e9ecef;border:2px solid #ced4da;border-radius:3px;vertical-align:middle"></span> Booked</span>
            <span style="font-size:12px"><span style="display:inline-block;width:16px;height:16px;background:#1a6b3c;border:2px solid #1a6b3c;border-radius:3px;vertical-align:middle"></span> Selected</span>
          </div>
          <!-- Seat grid -->
          <div class="seat-map" id="seatMap" style="grid-template-columns:repeat(4,1fr);max-width:100%">
            <?php for ($i=1; $i<=$s['total_seats']; $i++): $isBooked = in_array($i,$booked); ?>
            <div class="seat <?= $isBooked?'booked':'available' ?>" data-seat="<?= $i ?>"
                 <?= $isBooked?'':'onclick="selectSeat(this)"' ?>>
              <?= str_pad($i,2,'0',STR_PAD_LEFT) ?>
            </div>
            <?php endfor; ?>
          </div>
          <p class="text-muted mt-3" style="font-size:13px"><i class="fa fa-info-circle me-1"></i><?= ($s['total_seats']-count($booked)) ?> seats available out of <?= $s['total_seats'] ?></p>
        </div>
      </div>
    </div>

    <!-- Booking Summary -->
    <div class="col-md-5">
      <div class="card" style="position:sticky;top:80px">
        <div class="card-header-custom"><i class="fa fa-receipt"></i> Booking Summary</div>
        <div class="card-body-custom">
          <div class="ticket-row"><span class="label">Route</span><span class="value"><?= htmlspecialchars($s['route_number']) ?></span></div>
          <div class="ticket-row"><span class="label">From</span><span class="value"><?= htmlspecialchars($s['source']) ?></span></div>
          <div class="ticket-row"><span class="label">To</span><span class="value"><?= htmlspecialchars($s['destination']) ?></span></div>
          <div class="ticket-row"><span class="label">Date</span><span class="value"><?= htmlspecialchars($date) ?></span></div>
          <div class="ticket-row"><span class="label">Departs</span><span class="value"><?= htmlspecialchars(substr($s['departure_time'],0,5)) ?></span></div>
          <div class="ticket-row"><span class="label">Bus Type</span><span class="value"><?= ucfirst(str_replace('_',' ',$s['bus_type'])) ?></span></div>
          <div class="ticket-row"><span class="label">Depot</span><span class="value"><?= htmlspecialchars($s['depot_name']) ?></span></div>
          <div class="ticket-row"><span class="label">Selected Seat</span><span class="value" id="selectedSeatDisplay">—</span></div>
          <div class="divider-dashed"></div>
          <div class="ticket-row">
            <span class="label" style="font-size:15px;font-weight:700">Total Fare</span>
            <span class="value" style="font-size:22px;color:#1a6b3c">₹<span id="fareDisplay"><?= number_format($fare,2) ?></span></span>
          </div>
          <div class="mt-3">
            <label class="form-label">Board From Stop</label>
            <input type="text" id="sourceStop" class="form-control-custom" value="<?= htmlspecialchars($s['source']) ?>" placeholder="Boarding stop">
          </div>
          <div class="mt-2">
            <label class="form-label">Get Off At Stop</label>
            <input type="text" id="destStop" class="form-control-custom" value="<?= htmlspecialchars($s['destination']) ?>" placeholder="Alighting stop">
          </div>
          <button id="bookBtn" class="btn-primary-custom w-100 justify-content-center mt-4" disabled onclick="confirmBooking()">
            <i class="fa fa-lock"></i> Confirm & Book
          </button>
          <p style="font-size:11px;color:#6c757d;margin-top:8px;text-align:center">Secure payment – QR ticket issued instantly</p>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<!-- QR Success Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0" style="border-radius:16px">
      <div class="modal-body p-0">
        <div id="ticketResult"></div>
      </div>
      <div class="modal-footer border-0 justify-content-center gap-2">
        <button onclick="window.print()" class="btn-primary-custom btn-sm-custom"><i class="fa fa-print"></i> Print</button>
        <a href="my_tickets.php" class="btn-accent-custom btn-sm-custom"><i class="fa fa-ticket"></i> My Tickets</a>
      </div>
    </div>
  </div>
</div>

<script>
let selectedSeat = null;
const fare = <?= $fare ?>;

function selectSeat(el) {
  document.querySelectorAll('.seat.selected').forEach(s => s.classList.replace('selected','available'));
  el.classList.replace('available','selected');
  selectedSeat = parseInt(el.dataset.seat);
  document.getElementById('selectedSeatDisplay').textContent = 'Seat ' + String(selectedSeat).padStart(2,'0');
  document.getElementById('fareDisplay').textContent = fare.toFixed(2);
  document.getElementById('bookBtn').disabled = false;
}

async function confirmBooking() {
  if (!selectedSeat) return;
  const btn = document.getElementById('bookBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing…';
  try {
    const res = await apiPost('/TNSTC/api/book_ticket.php', {
      schedule_id: <?= $schedule_id ?>,
      seat_number: selectedSeat,
      source_stop: document.getElementById('sourceStop').value,
      dest_stop:   document.getElementById('destStop').value,
    });
    if (res.success) {
      document.getElementById('ticketResult').innerHTML = `
        <div class="ticket-card" style="max-width:100%">
          <div class="ticket-header">
            <div class="ticket-id">Ticket #${res.ticket_id}</div>
            <div class="ticket-route"><?= htmlspecialchars($s['source']) ?> → <?= htmlspecialchars($s['destination']) ?></div>
          </div>
          <div class="ticket-body">
            <div class="ticket-row"><span class="label">Bus</span><span class="value"><?= htmlspecialchars($s['bus_number']) ?></span></div>
            <div class="ticket-row"><span class="label">Date</span><span class="value"><?= htmlspecialchars($date) ?></span></div>
            <div class="ticket-row"><span class="label">Departure</span><span class="value"><?= htmlspecialchars(substr($s['departure_time'],0,5)) ?></span></div>
            <div class="ticket-row"><span class="label">Seat</span><span class="value">Seat ${String(selectedSeat).padStart(2,'0')}</span></div>
            <div class="ticket-row"><span class="label">Fare Paid</span><span class="value" style="color:#1a6b3c;font-size:18px">₹${parseFloat(res.fare).toFixed(2)}</span></div>
          </div>
          <div class="ticket-qr">
            <img src="${res.qr_url}" alt="QR Code">
            <div style="font-size:11px;color:#6c757d;margin-top:6px">${res.qr_data}</div>
          </div>
        </div>`;
      new bootstrap.Modal(document.getElementById('qrModal')).show();
    } else {
      alert('Booking failed: ' + res.message);
    }
  } catch(e) {
    alert('Error: ' + e.message);
  }
  btn.disabled = false;
  btn.innerHTML = '<i class="fa fa-lock"></i> Confirm & Book';
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
