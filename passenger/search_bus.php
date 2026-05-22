<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$db = getDB();
$stops = $db->query('SELECT stop_name FROM bus_stops ORDER BY stop_name ASC')->fetchAll(PDO::FETCH_COLUMN);

$src  = sanitize($_GET['src']  ?? '');
$dst  = sanitize($_GET['dst']  ?? '');
$date = $_GET['date'] ?? date('Y-m-d');
$pageTitle = 'Search Bus';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div class="main-content">

  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title"><i class="fa fa-search me-2" style="color:var(--primary)"></i>Search Bus</div>
  </div>

  <div class="page-content">

  <!-- Search Form -->
  <div class="card mb-4">
    <div class="card-body-custom">
      <form id="searchForm" class="row g-3 align-items-end">
        <div class="col-sm-4">
          <label class="form-label">From</label>
          <input type="text" name="src" id="srcInput" list="bus-stops" class="form-control-custom" placeholder="Tirunelveli" value="<?= htmlspecialchars($src) ?>" required autocomplete="off">
        </div>
        <div class="col-sm-4">
          <label class="form-label">To</label>
          <input type="text" name="dst" id="dstInput" list="bus-stops" class="form-control-custom" placeholder="Valliyoor" value="<?= htmlspecialchars($dst) ?>" required autocomplete="off">
        </div>
        <div class="col-sm-2">
          <label class="form-label">Date</label>
          <input type="date" name="date" id="dateInput" class="form-control-custom" value="<?= htmlspecialchars($date) ?>" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-sm-2">
          <button type="submit" class="btn-primary-custom w-100 justify-content-center">
            <i class="fa fa-search"></i> Search
          </button>
        </div>
        
        <datalist id="bus-stops">
          <?php foreach ($stops as $stop): ?>
            <option value="<?= htmlspecialchars($stop) ?>"></option>
          <?php endforeach; ?>
        </datalist>
      </form>
    </div>
  </div>

  <!-- Loading -->
  <div id="loadingState" class="text-center py-5 d-none">
    <div class="spinner-border text-success" role="status"></div>
    <p class="mt-2 text-muted">Searching available buses…</p>
  </div>

  <!-- Results -->
  <div id="resultsArea"></div>

  </div><!-- /.page-content -->
</div><!-- /.main-content -->
</div><!-- /.app-layout -->

<script>
const form = document.getElementById('searchForm');
const resultsArea = document.getElementById('resultsArea');
const loadingState = document.getElementById('loadingState');

form.addEventListener('submit', async e => {
  e.preventDefault();
  const src  = document.getElementById('srcInput').value.trim();
  const dst  = document.getElementById('dstInput').value.trim();
  const date = document.getElementById('dateInput').value;
  if (!src || !dst) return;

  resultsArea.innerHTML = '';
  loadingState.classList.remove('d-none');

  try {
    const data = await apiGet(`/TNSTC/api/search.php?src=${encodeURIComponent(src)}&dst=${encodeURIComponent(dst)}&date=${date}`);
    loadingState.classList.add('d-none');
    if (!data.length) {
      resultsArea.innerHTML = `<div class="empty-state"><i class="fa fa-bus"></i>No buses found for this route on ${date}.<br>Try different dates or nearby stops.</div>`;
      return;
    }
    resultsArea.innerHTML = `<p class="text-muted mb-3" style="font-size:13px">${data.length} bus(es) found for <strong>${src} → ${dst}</strong> on ${date}</p>` +
      data.map(b => busCard(b, date)).join('');
  } catch(err) {
    loadingState.classList.add('d-none');
    resultsArea.innerHTML = `<div class="flash-banner flash-danger">Error fetching results. Please try again.</div>`;
  }
});

// Auto-search if params in URL
if ('<?= $src ?>' && '<?= $dst ?>') form.dispatchEvent(new Event('submit'));

function busCard(b, date) {
  const typeColors = {ordinary:'#1a6b3c',express:'#1d4ed8',super_express:'#7c3aed',ac:'#0f766e',mini:'#854d0e'};
  const typeColor  = typeColors[b.bus_type] || '#6c757d';
  const avail = parseInt(b.available_seats);
  const availColor = avail > 10 ? '#166534' : avail > 0 ? '#854d0e' : '#991b1b';
  return `
  <div class="card mb-3 animate-fade" style="border-left:4px solid ${typeColor}">
    <div class="card-body-custom">
      <div class="d-flex flex-wrap gap-3 align-items-start justify-content-between">
        <div>
          <div class="d-flex align-items-center gap-2 mb-1">
            <span style="font-size:18px">🚌</span>
            <strong style="font-size:16px">${b.bus_number}</strong>
            <span class="badge-custom badge-primary" style="background:${typeColor}22;color:${typeColor}">${b.bus_type.replace('_',' ').toUpperCase()}</span>
          </div>
          <div style="font-size:14px;color:#333;margin-bottom:4px">Route ${b.route_number}: ${b.source} → ${b.destination}</div>
          <div style="font-size:12px;color:#6c757d">${b.depot_name} &nbsp;|&nbsp; ${b.distance} km &nbsp;|&nbsp; ~${b.estimated_time} min</div>
        </div>
        <div class="text-end">
          <div style="font-size:24px;font-weight:800;color:#1a6b3c">₹${parseFloat(b.fare).toFixed(2)}</div>
          <div style="font-size:11px;color:#6c757d">per ticket</div>
        </div>
      </div>
      <hr style="margin:12px 0;border-color:#f1f3f5">
      <div class="d-flex flex-wrap gap-4 align-items-center">
        <div class="text-center">
          <div style="font-size:20px;font-weight:700">${b.departure_time.slice(0,5)}</div>
          <div style="font-size:11px;color:#6c757d">Departs</div>
        </div>
        <div style="flex:1;text-align:center;color:#6c757d;font-size:12px">
          ──── ${b.estimated_time} min ────
        </div>
        <div class="text-center">
          <div style="font-size:20px;font-weight:700">${b.arrival_time.slice(0,5)}</div>
          <div style="font-size:11px;color:#6c757d">Arrives</div>
        </div>
        <div>
          <div style="font-weight:700;color:${availColor}">${avail > 0 ? avail + ' seats available' : 'FULL'}</div>
          <div style="font-size:11px;color:#6c757d">${b.delay_minutes>0?'⚠ Delayed '+b.delay_minutes+' min':'On time'}</div>
        </div>
        <div>
          ${avail > 0
            ? `<a href="book_ticket.php?schedule_id=${b.schedule_id}&date=${date}" class="btn-primary-custom btn-sm-custom"><i class="fa fa-ticket"></i> Book Now</a>`
            : `<span class="badge-custom badge-danger">Fully Booked</span>`}
        </div>
      </div>
    </div>
  </div>`;
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
