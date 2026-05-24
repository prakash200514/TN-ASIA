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

<!-- ═══════════════════════════════════════════════════════
     BUS JOURNEY ANIMATION MODAL
═══════════════════════════════════════════════════════ -->
<div class="journey-overlay" id="journeyOverlay" role="dialog" aria-modal="true" aria-label="Bus Journey Preview">
  <div class="journey-modal" id="journeyModal">

    <!-- Header -->
    <div class="journey-modal-header">
      <div class="journey-header-row">
        <div class="journey-bus-badge">
          🚌 <span id="jBusNumber">TN67 1234</span>
          &nbsp;·&nbsp;
          <span id="jBusTypeBadge" class="jtype-ordinary" style="padding:2px 8px;border-radius:10px;font-size:10px"></span>
        </div>
        <button class="journey-close-btn" id="journeyCloseBtn" aria-label="Close">✕</button>
      </div>
      <div class="journey-route-display">
        <div class="journey-stop">
          <span class="stop-label">From</span>
          <span class="stop-name" id="jFrom">—</span>
        </div>
        <div class="journey-route-arrow">
          <div class="journey-route-line"></div>
          <div class="journey-route-duration" id="jDuration">— min</div>
        </div>
        <div class="journey-stop" style="text-align:right">
          <span class="stop-label">To</span>
          <span class="stop-name" id="jTo">—</span>
        </div>
      </div>
    </div>

    <!-- Animated Scene -->
    <div class="journey-scene" id="journeyScene">

      <!-- Mountains (slow parallax) -->
      <div class="scene-mountains">
        <svg width="700" height="80" viewBox="0 0 700 80" fill="none">
          <polygon points="0,80 80,20 160,80" fill="#94a3b8" opacity="0.5"/>
          <polygon points="60,80 160,10 260,80" fill="#7f8ea3" opacity="0.4"/>
          <polygon points="180,80 280,30 380,80" fill="#94a3b8" opacity="0.45"/>
          <polygon points="320,80 420,15 520,80" fill="#7f8ea3" opacity="0.35"/>
          <polygon points="450,80 550,25 650,80" fill="#94a3b8" opacity="0.5"/>
          <polygon points="580,80 680,10 780,80" fill="#7f8ea3" opacity="0.4"/>
        </svg>
        <svg width="700" height="80" viewBox="0 0 700 80" fill="none">
          <polygon points="0,80 80,20 160,80" fill="#94a3b8" opacity="0.5"/>
          <polygon points="60,80 160,10 260,80" fill="#7f8ea3" opacity="0.4"/>
          <polygon points="180,80 280,30 380,80" fill="#94a3b8" opacity="0.45"/>
          <polygon points="320,80 420,15 520,80" fill="#7f8ea3" opacity="0.35"/>
          <polygon points="450,80 550,25 650,80" fill="#94a3b8" opacity="0.5"/>
          <polygon points="580,80 680,10 780,80" fill="#7f8ea3" opacity="0.4"/>
        </svg>
        <svg width="700" height="80" viewBox="0 0 700 80" fill="none">
          <polygon points="0,80 80,20 160,80" fill="#94a3b8" opacity="0.5"/>
          <polygon points="60,80 160,10 260,80" fill="#7f8ea3" opacity="0.4"/>
          <polygon points="180,80 280,30 380,80" fill="#94a3b8" opacity="0.45"/>
          <polygon points="320,80 420,15 520,80" fill="#7f8ea3" opacity="0.35"/>
          <polygon points="450,80 550,25 650,80" fill="#94a3b8" opacity="0.5"/>
        </svg>
      </div>

      <!-- Clouds -->
      <div class="scene-clouds">
        <svg width="2100" height="50" viewBox="0 0 2100 50" fill="none">
          <ellipse cx="80" cy="30" rx="45" ry="20" fill="white" opacity="0.85"/>
          <ellipse cx="100" cy="24" rx="30" ry="18" fill="white" opacity="0.9"/>
          <ellipse cx="60" cy="26" rx="22" ry="14" fill="white" opacity="0.8"/>
          <ellipse cx="280" cy="22" rx="38" ry="16" fill="white" opacity="0.75"/>
          <ellipse cx="298" cy="16" rx="24" ry="15" fill="white" opacity="0.8"/>
          <ellipse cx="500" cy="34" rx="50" ry="18" fill="white" opacity="0.85"/>
          <ellipse cx="520" cy="26" rx="32" ry="18" fill="white" opacity="0.9"/>
          <ellipse cx="480" cy="28" rx="26" ry="16" fill="white" opacity="0.8"/>
          <ellipse cx="720" cy="18" rx="42" ry="17" fill="white" opacity="0.7"/>
          <ellipse cx="738" cy="12" rx="28" ry="14" fill="white" opacity="0.75"/>
          <ellipse cx="950" cy="28" rx="48" ry="19" fill="white" opacity="0.8"/>
          <ellipse cx="968" cy="20" rx="30" ry="17" fill="white" opacity="0.85"/>
          <ellipse cx="1180" cy="30" rx="45" ry="20" fill="white" opacity="0.85"/>
          <ellipse cx="1200" cy="24" rx="30" ry="18" fill="white" opacity="0.9"/>
          <ellipse cx="1380" cy="22" rx="38" ry="16" fill="white" opacity="0.75"/>
          <ellipse cx="1600" cy="34" rx="50" ry="18" fill="white" opacity="0.85"/>
          <ellipse cx="1820" cy="18" rx="42" ry="17" fill="white" opacity="0.7"/>
          <ellipse cx="2050" cy="28" rx="48" ry="19" fill="white" opacity="0.8"/>
        </svg>
      </div>

      <!-- Trees -->
      <div class="scene-trees">
        <svg width="700" height="55" viewBox="0 0 700 55">
          <rect x="30" y="30" width="6" height="20" fill="#5d4037"/>
          <polygon points="33,8 18,32 48,32" fill="#2e7d32"/>
          <polygon points="33,2 20,22 46,22" fill="#388e3c"/>
          <rect x="90" y="35" width="5" height="15" fill="#5d4037"/>
          <polygon points="92.5,18 80,37 105,37" fill="#1b5e20"/>
          <rect x="160" y="28" width="7" height="24" fill="#4e342e"/>
          <polygon points="163.5,5 146,30 181,30" fill="#2e7d32"/>
          <polygon points="163.5,0 148,18 179,18" fill="#43a047"/>
          <rect x="230" y="30" width="5" height="22" fill="#795548"/>
          <ellipse cx="232.5" cy="26" rx="18" ry="10" fill="#388e3c" opacity="0.9"/>
          <ellipse cx="220" cy="22" rx="14" ry="7" fill="#2e7d32" opacity="0.8" transform="rotate(-20,220,22)"/>
          <ellipse cx="245" cy="22" rx="14" ry="7" fill="#2e7d32" opacity="0.8" transform="rotate(20,245,22)"/>
          <rect x="310" y="33" width="5" height="18" fill="#5d4037"/>
          <polygon points="312.5,16 300,35 325,35" fill="#388e3c"/>
          <rect x="390" y="26" width="8" height="26" fill="#4e342e"/>
          <polygon points="394,3 375,28 413,28" fill="#1b5e20"/>
          <polygon points="394,-2 377,14 411,14" fill="#388e3c"/>
          <rect x="470" y="31" width="6" height="20" fill="#5d4037"/>
          <polygon points="473,12 459,33 487,33" fill="#388e3c"/>
          <rect x="560" y="34" width="5" height="17" fill="#795548"/>
          <ellipse cx="562.5" cy="30" rx="16" ry="8" fill="#43a047"/>
          <rect x="640" y="28" width="7" height="24" fill="#4e342e"/>
          <polygon points="643.5,6 628,30 659,30" fill="#2e7d32"/>
          <polygon points="643.5,0 630,18 657,18" fill="#66bb6a"/>
        </svg>
      </div>

      <!-- Road -->
      <div class="scene-road"></div>

      <!-- Bus -->
      <div class="scene-bus-wrap">
        <svg id="journeyBusSVG" width="200" height="90" viewBox="0 0 200 90" fill="none">
          <rect x="8" y="8" width="180" height="58" rx="10" fill="#1d4ed8"/>
          <rect x="8" y="42" width="180" height="24" rx="0" fill="#1e40af"/>
          <rect x="8" y="60" width="180" height="6" rx="0" fill="#1e3a8a"/>
          <rect x="12" y="8" width="172" height="5" rx="10" fill="#2563eb"/>
          <rect x="22" y="14" width="26" height="20" rx="4" fill="#93c5fd" opacity="0.9"/>
          <rect x="56" y="14" width="26" height="20" rx="4" fill="#93c5fd" opacity="0.9"/>
          <rect x="90" y="14" width="26" height="20" rx="4" fill="#93c5fd" opacity="0.9"/>
          <rect x="124" y="14" width="26" height="20" rx="4" fill="#93c5fd" opacity="0.9"/>
          <rect x="158" y="12" width="26" height="24" rx="4" fill="#60a5fa" opacity="0.85"/>
          <circle cx="185" cy="52" r="5" fill="#fbbf24" style="animation: busHeadlight 1.5s ease-in-out infinite"/>
          <circle cx="185" cy="52" r="3" fill="#fef08a"/>
          <rect x="8" y="48" width="5" height="8" rx="2" fill="#ef4444" opacity="0.9"/>
          <text x="68" y="56" font-family="Arial,sans-serif" font-size="10" font-weight="bold" fill="white" letter-spacing="2">TNSTC</text>
          <circle cx="45" cy="72" r="14" fill="#1f2937" style="transform-origin:45px 72px; animation: wheelSpin 0.6s linear infinite"/>
          <circle cx="155" cy="72" r="14" fill="#1f2937" style="transform-origin:155px 72px; animation: wheelSpin 0.6s linear infinite"/>
        </svg>
      </div>
    </div><!-- /.journey-scene -->

    <!-- Progress -->
    <div class="journey-progress-section">
      <div class="journey-progress-label">
        <span id="jProgressFrom">—</span>
        <span style="color:var(--primary);font-size:11px">🚌 In Journey</span>
        <span id="jProgressTo">—</span>
      </div>
      <div class="journey-progress-bar">
        <div class="journey-progress-fill" id="jProgressFill"></div>
      </div>
    </div>

    <!-- Info Grid -->
    <div class="journey-info-grid">
      <div class="journey-info-item">
        <div class="journey-info-value" id="jFare">₹0</div>
        <div class="journey-info-label">Fare</div>
      </div>
      <div class="journey-info-item">
        <div class="journey-info-value" id="jSeats">0</div>
        <div class="journey-info-label">Seats Left</div>
      </div>
      <div class="journey-info-item">
        <div class="journey-info-value" id="jDepart">--:--</div>
        <div class="journey-info-label">Departs</div>
      </div>
      <div class="journey-info-item">
        <div class="journey-info-value" id="jArrive">--:--</div>
        <div class="journey-info-label">Arrives</div>
      </div>
    </div>

    <!-- Footer Actions -->
    <div class="journey-modal-footer">
      <a href="#" class="journey-book-btn" id="jBookBtn">
        <i class="fa fa-ticket"></i> Book This Bus
      </a>
      <button class="journey-dismiss-btn" id="journeyDismissBtn">Close</button>
    </div>

  </div><!-- /.journey-modal -->
</div><!-- /.journey-overlay -->

<script>
const form        = document.getElementById('searchForm');
const resultsArea = document.getElementById('resultsArea');
const loadingState= document.getElementById('loadingState');

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
  const busData = encodeURIComponent(JSON.stringify({
    bus_number: b.bus_number,
    bus_type: b.bus_type,
    source: b.source,
    destination: b.destination,
    departure_time: b.departure_time,
    arrival_time: b.arrival_time,
    estimated_time: b.estimated_time,
    fare: b.fare,
    available_seats: b.available_seats,
    route_number: b.route_number,
    distance: b.distance,
    delay_minutes: b.delay_minutes,
    schedule_id: b.schedule_id,
    date: date
  }));
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
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn-primary-custom btn-sm-custom"
                  style="background:linear-gradient(135deg,#7c3aed,#2563eb);border:none"
                  onclick="openJourneyModal(${busData})"
                  title="View animated journey preview">
            <i class="fa fa-play-circle"></i> View Journey
          </button>
          ${avail > 0
            ? `<a href="book_ticket.php?schedule_id=${b.schedule_id}&date=${date}" class="btn-primary-custom btn-sm-custom"><i class="fa fa-ticket"></i> Book Now</a>`
            : `<span class="badge-custom badge-danger">Fully Booked</span>`}
        </div>
      </div>
    </div>
  </div>`;
}

/* ═══════════════════════════════════════════════════════
   JOURNEY MODAL LOGIC
═══════════════════════════════════════════════════════ */
const journeyOverlay = document.getElementById('journeyOverlay');
const journeyModal   = document.getElementById('journeyModal');

function openJourneyModal(b) {
  // Bus type badge
  const typeBadge = document.getElementById('jBusTypeBadge');
  typeBadge.textContent = b.bus_type.replace('_',' ').toUpperCase();
  typeBadge.className = 'jtype-' + b.bus_type;
  typeBadge.style.cssText = 'padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700';

  // Header
  document.getElementById('jBusNumber').textContent  = b.bus_number;
  document.getElementById('jFrom').textContent       = b.source;
  document.getElementById('jTo').textContent         = b.destination;
  document.getElementById('jDuration').textContent   = '~' + b.estimated_time + ' min';

  // Progress
  document.getElementById('jProgressFrom').textContent = b.source;
  document.getElementById('jProgressTo').textContent   = b.destination;

  // Reset + restart progress fill animation
  const fill = document.getElementById('jProgressFill');
  fill.style.animation = 'none';
  fill.offsetHeight;
  fill.style.animation = '';

  // Info grid
  document.getElementById('jFare').textContent   = '₹' + parseFloat(b.fare).toFixed(2);
  document.getElementById('jSeats').textContent  = parseInt(b.available_seats) > 0 ? b.available_seats : 'Full';
  document.getElementById('jDepart').textContent = b.departure_time.slice(0,5);
  document.getElementById('jArrive').textContent = b.arrival_time.slice(0,5);

  // Book button
  const bookBtn = document.getElementById('jBookBtn');
  if (parseInt(b.available_seats) > 0) {
    bookBtn.href = `book_ticket.php?schedule_id=${b.schedule_id}&date=${b.date}`;
    bookBtn.style.opacity = '1';
    bookBtn.style.pointerEvents = 'all';
    bookBtn.innerHTML = '<i class="fa fa-ticket"></i> Book This Bus';
  } else {
    bookBtn.href = '#';
    bookBtn.style.opacity = '0.5';
    bookBtn.style.pointerEvents = 'none';
    bookBtn.innerHTML = '<i class="fa fa-ban"></i> Fully Booked';
  }

  // Color bus body based on bus type
  const busSVG = document.getElementById('journeyBusSVG');
  const typeColors = {
    ordinary:      ['#14532d','#166534','#15803d'],
    express:       ['#1e40af','#1d4ed8','#2563eb'],
    super_express: ['#4c1d95','#6d28d9','#7c3aed'],
    ac:            ['#134e4a','#0f766e','#0d9488'],
    mini:          ['#713f12','#92400e','#b45309'],
  };
  const bodyColors = typeColors[b.bus_type] || typeColors.express;

  const bodyRects = busSVG.querySelectorAll('rect');
  if (bodyRects[0]) bodyRects[0].setAttribute('fill', bodyColors[0]);
  if (bodyRects[1]) bodyRects[1].setAttribute('fill', bodyColors[1]);
  if (bodyRects[2]) bodyRects[2].setAttribute('fill', bodyColors[2]);

  journeyModal.classList.remove('closing');
  journeyOverlay.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeJourneyModal() {
  journeyModal.classList.add('closing');
  setTimeout(() => {
    journeyOverlay.classList.remove('active');
    journeyModal.classList.remove('closing');
    document.body.style.overflow = '';
  }, 280);
}

document.getElementById('journeyCloseBtn').addEventListener('click', closeJourneyModal);
document.getElementById('journeyDismissBtn').addEventListener('click', closeJourneyModal);
journeyOverlay.addEventListener('click', function(e) {
  if (e.target === journeyOverlay) closeJourneyModal();
});
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && journeyOverlay.classList.contains('active')) closeJourneyModal();
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
