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

<!-- ══════════════════════════════════════════════════════════
     ALL JOURNEY ANIMATION STYLES  (defined here so keyframes
     are always in scope when the modal opens)
══════════════════════════════════════════════════════════ -->
<style>
/* ── Overlay & modal ── */
.journey-overlay {
  position: fixed; inset: 0;
  background: rgba(2,6,23,.75);
  backdrop-filter: blur(6px);
  z-index: 9999;
  display: flex; align-items: center; justify-content: center;
  padding: 16px;
  opacity: 0; pointer-events: none;
  transition: opacity .3s ease;
}
.journey-overlay.active { opacity:1; pointer-events:all; }

.journey-modal {
  background:#fff;
  border-radius: 22px;
  width: 100%; max-width: 680px;
  overflow: hidden;
  box-shadow: 0 40px 80px -20px rgba(0,0,0,.45);
  transform: scale(.93) translateY(24px);
  transition: transform .38s cubic-bezier(.16,1,.3,1), opacity .3s ease;
  opacity: 0;
}
.journey-overlay.active .journey-modal {
  transform: scale(1) translateY(0);
  opacity: 1;
}

/* ── Modal header ── */
.jm-header {
  background: linear-gradient(135deg,#FF671F,#06038D 55%,#046A38);
  color:#fff; padding:18px 22px 14px; position:relative; overflow:hidden;
}
.jm-header::before {
  content:''; position:absolute; top:-50px; right:-50px;
  width:180px; height:180px;
  background:rgba(255,255,255,.06); border-radius:50%;
}
.jm-badge-row {
  display:flex; align-items:center; justify-content:space-between;
  position:relative; z-index:1;
}
.jm-bus-badge {
  display:inline-flex; align-items:center; gap:6px;
  background:rgba(255,255,255,.14);
  border:1px solid rgba(255,255,255,.25);
  border-radius:20px; padding:4px 14px;
  font-size:12px; font-weight:700;
}
.jm-type-chip {
  padding:2px 9px; border-radius:10px;
  font-size:10px; font-weight:800; letter-spacing:.4px;
}
.jm-close {
  width:32px; height:32px;
  background:rgba(255,255,255,.15);
  border:1px solid rgba(255,255,255,.2);
  border-radius:50%; color:#fff;
  display:flex; align-items:center; justify-content:center;
  cursor:pointer; font-size:15px; line-height:1;
  transition: background .2s;
}
.jm-close:hover { background:rgba(255,255,255,.3); }
.jm-route {
  display:flex; align-items:center; gap:10px;
  margin-top:12px; position:relative; z-index:1;
}
.jm-stop { display:flex; flex-direction:column; gap:2px; }
.jm-stop-label { font-size:10px; font-weight:600; letter-spacing:1px; opacity:.6; text-transform:uppercase; }
.jm-stop-name  { font-size:19px; font-weight:800; letter-spacing:-.03em; }
.jm-arrow {
  flex:1; display:flex; flex-direction:column; align-items:center; gap:4px;
}
.jm-line {
  width:100%; height:2px; background:rgba(255,255,255,.3); border-radius:2px;
  position:relative;
}
.jm-line::after {
  content:'🚌'; font-size:14px; position:absolute; top:50%; left:50%;
  transform:translate(-50%,-50%);
  background: var(--primary-dark); padding:0 4px; border-radius:4px;
}
.jm-dur { font-size:11px; opacity:.65; font-weight:600; }

/* ════════════════════════════════════════════
   SCENE  (the animated canvas)
════════════════════════════════════════════ */
.jm-scene {
  height:190px;
  background: linear-gradient(180deg,
    #5bb3d6 0%, #87ceeb 30%, #b8e4f5 45%,
    #c8e6b0 60%, #8fbe6b 72%, #6b9b42 78%, #a0785a 100%
  );
  position:relative; overflow:hidden;
}

/* ── Sun ── */
.scene-sun {
  position:absolute; top:14px; right:60px;
  width:34px; height:34px;
  background:radial-gradient(circle,#fff9c4,#fbbf24);
  border-radius:50%;
  box-shadow: 0 0 20px 8px rgba(251,191,36,.35);
}

/* ── Clouds ── */
@keyframes cloudMove {
  from { transform:translateX(0); }
  to   { transform:translateX(-50%); }
}
.scene-clouds-wrap {
  position:absolute; top:0; left:0; height:60px;
  width:200%; /* two copies side by side for seamless loop */
  animation: cloudMove 18s linear infinite;
  white-space:nowrap;
}
.scene-cloud {
  position:absolute; background:#fff; border-radius:50px;
}
.scene-cloud::before, .scene-cloud::after {
  content:''; position:absolute; background:#fff; border-radius:50%;
}

/* Cloud positions (set A) */
.c1 { width:70px; height:22px; top:12px; left:60px; opacity:.88; }
.c1::before { width:36px; height:30px; top:-14px; left:10px; }
.c1::after  { width:28px; height:22px; top:-8px;  left:34px; }

.c2 { width:55px; height:18px; top:6px;  left:220px; opacity:.72; }
.c2::before { width:28px; height:24px; top:-12px; left:8px; }
.c2::after  { width:22px; height:18px; top:-6px;  left:28px; }

.c3 { width:80px; height:24px; top:18px; left:400px; opacity:.8; }
.c3::before { width:40px; height:32px; top:-16px; left:12px; }
.c3::after  { width:32px; height:24px; top:-10px; left:42px; }

.c4 { width:60px; height:20px; top:8px;  left:580px; opacity:.65; }
.c4::before { width:30px; height:26px; top:-13px; left:9px; }

.c5 { width:75px; height:22px; top:22px; left:760px; opacity:.78; }
.c5::before { width:38px; height:30px; top:-15px; left:11px; }
.c5::after  { width:30px; height:22px; top:-8px;  left:40px; }

/* Cloud set B — offset 50% for seamless loop */
.c1b { width:70px; height:22px; top:10px; left:910px;  opacity:.85; }
.c1b::before { width:36px; height:30px; top:-14px; left:10px; }
.c1b::after  { width:28px; height:22px; top:-8px;  left:34px; }

.c2b { width:50px; height:17px; top:5px;  left:1080px; opacity:.70; }
.c2b::before { width:26px; height:22px; top:-11px; left:7px; }

.c3b { width:82px; height:26px; top:20px; left:1230px; opacity:.82; }
.c3b::before { width:42px; height:34px; top:-18px; left:12px; }
.c3b::after  { width:34px; height:26px; top:-12px; left:44px; }

.c4b { width:62px; height:20px; top:7px;  left:1420px; opacity:.68; }
.c4b::before { width:31px; height:26px; top:-13px; left:9px; }

.c5b { width:72px; height:22px; top:18px; left:1610px; opacity:.75; }
.c5b::before { width:36px; height:30px; top:-15px; left:11px; }
.c5b::after  { width:28px; height:22px; top:-8px;  left:38px; }

/* ── Mountains ── */
@keyframes mountainMove {
  from { transform:translateX(0); }
  to   { transform:translateX(-50%); }
}
.scene-mountains-wrap {
  position:absolute; bottom:58px; left:0;
  width:200%; height:80px;
  animation: mountainMove 14s linear infinite;
}
.scene-mountains-wrap svg { display:inline-block; vertical-align:bottom; }

/* ── Trees (fast foreground) ── */
@keyframes treeMove {
  from { transform:translateX(0); }
  to   { transform:translateX(-50%); }
}
.scene-trees-wrap {
  position:absolute; bottom:56px; left:0;
  width:200%; height:60px; display:flex; align-items:flex-end;
  animation: treeMove 4s linear infinite;
}
.scene-trees-wrap svg { display:block; flex-shrink:0; }

/* ── Road ── */
.scene-road {
  position:absolute; bottom:0; left:0; right:0;
  height:58px; background:#4a4a4a;
}
.scene-road::before {
  content:''; position:absolute;
  top:26px; left:0; right:0; height:5px;
  background: repeating-linear-gradient(
    90deg,
    #fbbf24 0 40px, transparent 40px 80px
  );
  animation: roadLine 1.2s linear infinite;
}
.scene-road::after {
  content:''; position:absolute;
  bottom:0; left:0; right:0; height:7px; background:#333;
}
@keyframes roadLine {
  from { background-position:0 0; }
  to   { background-position:-80px 0; }
}
/* Road edge lines */
.scene-road-edge {
  position:absolute; bottom:56px; left:0; right:0;
  height:3px; background:#e5e7eb; opacity:.6;
}

/* ── Bus (stays in center, world scrolls past) ── */
@keyframes busBounce {
  0%,100% { transform: translateX(-50%) translateY(0px); }
  25%      { transform: translateX(-50%) translateY(-4px); }
  75%      { transform: translateX(-50%) translateY(3px); }
}
.scene-bus-wrap {
  position:absolute; bottom:52px; left:50%;
  transform:translateX(-50%);
  animation: busBounce .55s ease-in-out infinite;
  z-index:20;
  filter: drop-shadow(0 10px 18px rgba(0,0,0,.35));
}

/* ── Spinning wheels (applied to <g> groups via class) ── */
@keyframes wheelSpin {
  from { transform:rotate(0deg); }
  to   { transform:rotate(360deg); }
}
.wheel-spin {
  animation: wheelSpin .55s linear infinite;
}

/* ── Exhaust ── */
@keyframes puff {
  0%   { opacity:.75; transform:scale(.5) translate(0,0); }
  100% { opacity:0;   transform:scale(2.2) translate(-24px,-22px); }
}
.exhaust-wrap {
  position:absolute; bottom:22px; right:100%;
  width:28px; height:20px;
}
.puff {
  position:absolute; width:13px; height:13px;
  background:rgba(210,210,210,.85); border-radius:50%;
  animation: puff 1.1s ease-out infinite;
}
.puff:nth-child(1) { animation-delay:0s; }
.puff:nth-child(2) { animation-delay:.37s; }
.puff:nth-child(3) { animation-delay:.74s; }

/* ── Headlight flicker ── */
@keyframes headlight {
  0%,100% { opacity:.45; }
  50%      { opacity:1;   }
}
.headlight { animation: headlight 1.4s ease-in-out infinite; }

/* ── Progress bar ── */
@keyframes progressFill {
  from { width:0%; }
  to   { width:100%; }
}
.jm-progress-fill {
  height:100%;
  background: linear-gradient(90deg,var(--primary),var(--accent));
  border-radius:99px;
  width:0%;
  /* animation applied via JS so it restarts each open */
}
.jm-progress-fill.running {
  animation: progressFill 3.5s cubic-bezier(.4,0,.2,1) forwards;
}

/* ── Info grid ── */
.jm-info-grid {
  display:grid; grid-template-columns:repeat(4,1fr);
  gap:1px; background:var(--gray-100); margin-top:14px;
}
.jm-info-cell {
  background:#fff; padding:13px 14px; text-align:center;
}
.jm-info-val   { font-size:17px; font-weight:800; color:var(--gray-800); line-height:1; }
.jm-info-label { font-size:10px; color:var(--gray-600); font-weight:600;
                  text-transform:uppercase; letter-spacing:.6px; margin-top:3px; }

/* ── Footer ── */
.jm-footer {
  display:flex; gap:10px; padding:14px 20px 18px; align-items:center;
}
.jm-book-btn {
  flex:1; display:flex; align-items:center; justify-content:center; gap:8px;
  background:linear-gradient(135deg,var(--primary-dark),var(--primary));
  color:#fff; border:none; padding:11px 18px;
  border-radius:12px; font-size:14px; font-weight:700;
  cursor:pointer; transition:all .2s;
  box-shadow:0 4px 14px rgba(var(--primary-rgb),.3);
  text-decoration:none;
}
.jm-book-btn:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(var(--primary-rgb),.4); color:#fff; }
.jm-close-btn2 {
  padding:11px 16px; border-radius:12px;
  border:1px solid var(--gray-200); background:#fff;
  color:var(--gray-600); font-size:13px; font-weight:600;
  cursor:pointer; transition:all .2s;
}
.jm-close-btn2:hover { background:var(--gray-100); }

/* ── progress label row ── */
.jm-progress-section { padding:14px 20px 0; }
.jm-progress-label {
  display:flex; justify-content:space-between; align-items:center;
  font-size:11.5px; font-weight:600; color:var(--gray-600); margin-bottom:7px;
}
.jm-progress-bar {
  height:8px; background:var(--gray-100); border-radius:99px; overflow:hidden;
}
</style>

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
          <input type="text" name="src" id="srcInput" list="bus-stops" class="form-control-custom"
                 placeholder="Tirunelveli" value="<?= htmlspecialchars($src) ?>" required autocomplete="off">
        </div>
        <div class="col-sm-4">
          <label class="form-label">To</label>
          <input type="text" name="dst" id="dstInput" list="bus-stops" class="form-control-custom"
                 placeholder="Valliyoor" value="<?= htmlspecialchars($dst) ?>" required autocomplete="off">
        </div>
        <div class="col-sm-2">
          <label class="form-label">Date</label>
          <input type="date" name="date" id="dateInput" class="form-control-custom"
                 value="<?= htmlspecialchars($date) ?>" min="<?= date('Y-m-d') ?>" required>
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

  <div id="loadingState" class="text-center py-5 d-none">
    <div class="spinner-border text-success" role="status"></div>
    <p class="mt-2 text-muted">Searching available buses…</p>
  </div>

  <div id="resultsArea"></div>

  </div>
</div>
</div>

<!-- ══════════════════════════════════════════════════════════
     BUS JOURNEY ANIMATION MODAL
══════════════════════════════════════════════════════════ -->
<div class="journey-overlay" id="journeyOverlay">
  <div class="journey-modal" id="journeyModal">

    <!-- ── Header ── -->
    <div class="jm-header">
      <div class="jm-badge-row">
        <div class="jm-bus-badge">
          🚌 <span id="jBusNum">—</span>
          &nbsp;·&nbsp;
          <span class="jm-type-chip" id="jTypeChip">EXPRESS</span>
        </div>
        <button class="jm-close" id="jCloseBtn">✕</button>
      </div>
      <div class="jm-route">
        <div class="jm-stop">
          <span class="jm-stop-label">From</span>
          <span class="jm-stop-name" id="jFrom">—</span>
        </div>
        <div class="jm-arrow">
          <div class="jm-line"></div>
          <div class="jm-dur" id="jDur">— min</div>
        </div>
        <div class="jm-stop" style="text-align:right">
          <span class="jm-stop-label">To</span>
          <span class="jm-stop-name" id="jTo">—</span>
        </div>
      </div>
    </div>

    <!-- ── Animated Scene ── -->
    <div class="jm-scene">

      <!-- Sun -->
      <div class="scene-sun"></div>

      <!-- Clouds (two identical sets for seamless loop) -->
      <div class="scene-clouds-wrap">
        <div class="scene-cloud c1"></div>
        <div class="scene-cloud c2"></div>
        <div class="scene-cloud c3"></div>
        <div class="scene-cloud c4"></div>
        <div class="scene-cloud c5"></div>
        <!-- Set B -->
        <div class="scene-cloud c1b"></div>
        <div class="scene-cloud c2b"></div>
        <div class="scene-cloud c3b"></div>
        <div class="scene-cloud c4b"></div>
        <div class="scene-cloud c5b"></div>
      </div>

      <!-- Mountains — two side-by-side SVG tiles, container width=200% -->
      <div class="scene-mountains-wrap">
        <!-- tile A -->
        <svg width="900" height="80" viewBox="0 0 900 80" preserveAspectRatio="none">
          <polygon points="0,80 70,18 140,80"    fill="#94a3b8" opacity=".55"/>
          <polygon points="50,80 140,8  230,80"  fill="#7f8ea3" opacity=".45"/>
          <polygon points="160,80 260,28 360,80" fill="#94a3b8" opacity=".5"/>
          <polygon points="290,80 400,12 510,80" fill="#7f8ea3" opacity=".38"/>
          <polygon points="430,80 540,22 650,80" fill="#94a3b8" opacity=".52"/>
          <polygon points="580,80 680,6  780,80" fill="#7f8ea3" opacity=".42"/>
          <polygon points="720,80 820,20 920,80" fill="#94a3b8" opacity=".48"/>
        </svg>
        <!-- tile B (identical, placed right after A) -->
        <svg width="900" height="80" viewBox="0 0 900 80" preserveAspectRatio="none">
          <polygon points="0,80 70,18 140,80"    fill="#94a3b8" opacity=".55"/>
          <polygon points="50,80 140,8  230,80"  fill="#7f8ea3" opacity=".45"/>
          <polygon points="160,80 260,28 360,80" fill="#94a3b8" opacity=".5"/>
          <polygon points="290,80 400,12 510,80" fill="#7f8ea3" opacity=".38"/>
          <polygon points="430,80 540,22 650,80" fill="#94a3b8" opacity=".52"/>
          <polygon points="580,80 680,6  780,80" fill="#7f8ea3" opacity=".42"/>
          <polygon points="720,80 820,20 920,80" fill="#94a3b8" opacity=".48"/>
        </svg>
      </div>

      <!-- Trees — two tile set for seamless loop -->
      <div class="scene-trees-wrap">
        <!-- Tile A -->
        <svg width="900" height="58" viewBox="0 0 900 58">
          <!-- tree 1 -->
          <rect x="20"  y="32" width="6"  height="22" fill="#5d4037"/>
          <polygon points="23,10 8,34 38,34"    fill="#2e7d32"/>
          <polygon points="23,4  10,22 36,22"   fill="#388e3c"/>
          <!-- tree 2 -->
          <rect x="80"  y="36" width="5"  height="18" fill="#5d4037"/>
          <polygon points="82.5,20 70,38 95,38" fill="#1b5e20"/>
          <!-- tree 3 (tall) -->
          <rect x="150" y="26" width="7"  height="28" fill="#4e342e"/>
          <polygon points="153.5,4 136,28 171,28"  fill="#2e7d32"/>
          <polygon points="153.5,0 138,16 169,16"  fill="#43a047"/>
          <!-- tree 4 palm -->
          <rect x="220" y="30" width="5"  height="24" fill="#795548"/>
          <ellipse cx="222.5" cy="26" rx="18" ry="10" fill="#388e3c" opacity=".9"/>
          <ellipse cx="210"   cy="21" rx="14" ry="7"  fill="#2e7d32" opacity=".8" transform="rotate(-22,210,21)"/>
          <ellipse cx="236"   cy="21" rx="14" ry="7"  fill="#2e7d32" opacity=".8" transform="rotate(22,236,21)"/>
          <!-- tree 5 -->
          <rect x="300" y="34" width="5"  height="20" fill="#5d4037"/>
          <polygon points="302.5,18 290,36 315,36" fill="#388e3c"/>
          <!-- tree 6 -->
          <rect x="380" y="24" width="8"  height="30" fill="#4e342e"/>
          <polygon points="384,2 365,26 403,26"  fill="#1b5e20"/>
          <polygon points="384,-4 367,12 401,12" fill="#2e7d32"/>
          <!-- tree 7 -->
          <rect x="460" y="32" width="6"  height="22" fill="#5d4037"/>
          <polygon points="463,12 449,34 477,34" fill="#388e3c"/>
          <!-- tree 8 round -->
          <rect x="550" y="36" width="5"  height="18" fill="#795548"/>
          <ellipse cx="552.5" cy="30" rx="17" ry="9" fill="#43a047"/>
          <ellipse cx="552.5" cy="26" rx="12" ry="7" fill="#66bb6a"/>
          <!-- tree 9 -->
          <rect x="630" y="28" width="7"  height="26" fill="#4e342e"/>
          <polygon points="633.5,6 618,30 649,30"  fill="#2e7d32"/>
          <polygon points="633.5,0 620,16 647,16"  fill="#66bb6a"/>
          <!-- tree 10 -->
          <rect x="720" y="34" width="5"  height="20" fill="#5d4037"/>
          <polygon points="722.5,18 710,36 735,36" fill="#2e7d32"/>
          <!-- tree 11 -->
          <rect x="800" y="26" width="7"  height="28" fill="#4e342e"/>
          <polygon points="803.5,4 786,28 821,28"  fill="#1b5e20"/>
          <polygon points="803.5,0 788,16 819,16"  fill="#43a047"/>
          <!-- tree 12 -->
          <rect x="870" y="32" width="6"  height="22" fill="#5d4037"/>
          <polygon points="873,10 859,34 887,34"   fill="#388e3c"/>
        </svg>
        <!-- Tile B (identical for seamless loop) -->
        <svg width="900" height="58" viewBox="0 0 900 58">
          <rect x="20"  y="32" width="6"  height="22" fill="#5d4037"/>
          <polygon points="23,10 8,34 38,34"    fill="#2e7d32"/>
          <polygon points="23,4  10,22 36,22"   fill="#388e3c"/>
          <rect x="80"  y="36" width="5"  height="18" fill="#5d4037"/>
          <polygon points="82.5,20 70,38 95,38" fill="#1b5e20"/>
          <rect x="150" y="26" width="7"  height="28" fill="#4e342e"/>
          <polygon points="153.5,4 136,28 171,28"  fill="#2e7d32"/>
          <polygon points="153.5,0 138,16 169,16"  fill="#43a047"/>
          <rect x="220" y="30" width="5"  height="24" fill="#795548"/>
          <ellipse cx="222.5" cy="26" rx="18" ry="10" fill="#388e3c" opacity=".9"/>
          <ellipse cx="210"   cy="21" rx="14" ry="7"  fill="#2e7d32" opacity=".8" transform="rotate(-22,210,21)"/>
          <ellipse cx="236"   cy="21" rx="14" ry="7"  fill="#2e7d32" opacity=".8" transform="rotate(22,236,21)"/>
          <rect x="300" y="34" width="5"  height="20" fill="#5d4037"/>
          <polygon points="302.5,18 290,36 315,36" fill="#388e3c"/>
          <rect x="380" y="24" width="8"  height="30" fill="#4e342e"/>
          <polygon points="384,2 365,26 403,26"  fill="#1b5e20"/>
          <polygon points="384,-4 367,12 401,12" fill="#2e7d32"/>
          <rect x="460" y="32" width="6"  height="22" fill="#5d4037"/>
          <polygon points="463,12 449,34 477,34" fill="#388e3c"/>
          <rect x="550" y="36" width="5"  height="18" fill="#795548"/>
          <ellipse cx="552.5" cy="30" rx="17" ry="9" fill="#43a047"/>
          <ellipse cx="552.5" cy="26" rx="12" ry="7" fill="#66bb6a"/>
          <rect x="630" y="28" width="7"  height="26" fill="#4e342e"/>
          <polygon points="633.5,6 618,30 649,30"  fill="#2e7d32"/>
          <polygon points="633.5,0 620,16 647,16"  fill="#66bb6a"/>
          <rect x="720" y="34" width="5"  height="20" fill="#5d4037"/>
          <polygon points="722.5,18 710,36 735,36" fill="#2e7d32"/>
          <rect x="800" y="26" width="7"  height="28" fill="#4e342e"/>
          <polygon points="803.5,4 786,28 821,28"  fill="#1b5e20"/>
          <polygon points="803.5,0 788,16 819,16"  fill="#43a047"/>
          <rect x="870" y="32" width="6"  height="22" fill="#5d4037"/>
          <polygon points="873,10 859,34 887,34"   fill="#388e3c"/>
        </svg>
      </div>

      <!-- Road edge line -->
      <div class="scene-road-edge"></div>

      <!-- Road -->
      <div class="scene-road"></div>

      <!-- Bus (stays fixed in center; environment scrolls around it) -->
      <div class="scene-bus-wrap">
        <!-- Exhaust puffs behind the bus -->
        <div class="exhaust-wrap">
          <div class="puff"></div>
          <div class="puff"></div>
          <div class="puff"></div>
        </div>

        <!--
          The SVG bus. Wheel spin is done by wrapping each wheel group
          in a <g> with class="wheel-spin" and the correct transform-origin
          set via a style attribute on the <g>.
        -->
        <svg id="busSVG" width="220" height="90" viewBox="0 0 220 90" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- ── Body ── -->
          <rect id="busBody1" x="8"  y="6"  width="200" height="62" rx="10" fill="#FF671F"/>
          <rect id="busBody2" x="8"  y="40" width="200" height="28" rx="0"  fill="#FFFFFF"/>
          <rect id="busBody3" x="8"  y="59" width="200" height="9"  rx="0"  fill="#046A38"/>
          <!-- Roof highlight -->
          <rect x="14" y="6"  width="190" height="5"  rx="5"  fill="#FFFFFF" opacity=".4"/>
          <!-- Undercarriage -->
          <rect x="14" y="66" width="188" height="6"  rx="0"  fill="#0f172a" opacity=".8"/>

          <!-- ── Windows ── -->
          <rect x="20"  y="12" width="28" height="22" rx="4" fill="#bfdbfe" opacity=".95"/>
          <rect x="57"  y="12" width="28" height="22" rx="4" fill="#bfdbfe" opacity=".95"/>
          <rect x="94"  y="12" width="28" height="22" rx="4" fill="#bfdbfe" opacity=".95"/>
          <rect x="131" y="12" width="28" height="22" rx="4" fill="#bfdbfe" opacity=".95"/>
          <!-- Glare on windows -->
          <rect x="22"  y="14" width="10" height="6" rx="2" fill="#fff" opacity=".5"/>
          <rect x="59"  y="14" width="10" height="6" rx="2" fill="#fff" opacity=".5"/>
          <rect x="96"  y="14" width="10" height="6" rx="2" fill="#fff" opacity=".5"/>
          <rect x="133" y="14" width="10" height="6" rx="2" fill="#fff" opacity=".5"/>

          <!-- ── Front face ── -->
          <!-- Windshield -->
          <rect x="172" y="10" width="30" height="26" rx="4" fill="#60a5fa" opacity=".9"/>
          <rect x="174" y="12" width="12" height="9"  rx="2" fill="#fff"    opacity=".35"/>
          <!-- Wiper -->
          <line x1="175" y1="34" x2="198" y2="30" stroke="#fff" stroke-width="1.2" opacity=".4"/>
          <!-- Headlight -->
          <ellipse cx="206" cy="54" rx="7" ry="5" fill="#fef3c7" class="headlight"/>
          <ellipse cx="206" cy="54" rx="4" ry="3" fill="#fbbf24" class="headlight"/>
          <!-- Front bumper -->
          <rect x="204" y="62" width="8" height="8" rx="3" fill="#374151"/>
          <!-- Grille lines -->
          <rect x="182" y="44" width="20" height="2" rx="1" fill="#93c5fd" opacity=".5"/>
          <rect x="182" y="49" width="20" height="2" rx="1" fill="#93c5fd" opacity=".5"/>
          <rect x="182" y="54" width="20" height="2" rx="1" fill="#93c5fd" opacity=".5"/>

          <!-- ── Rear face ── -->
          <!-- Tail lights -->
          <rect x="8" y="44" width="6" height="12" rx="2" fill="#ef4444" opacity=".9"/>
          <rect x="8" y="58" width="6" height="5"  rx="1" fill="#f97316" opacity=".7"/>
          <!-- Rear bumper -->
          <rect x="4"  y="62" width="8" height="8"  rx="3" fill="#374151"/>

          <!-- ── Door ── -->
          <rect x="162" y="44" width="10" height="24" rx="2" fill="none" stroke="#60a5fa" stroke-width="1.2" opacity=".7"/>

          <!-- ── TNSTC label ── -->
          <rect x="60" y="41" width="82" height="14" rx="3" fill="#fff" opacity=".12"/>
          <text x="101" y="53" text-anchor="middle"
                font-family="Arial,sans-serif" font-size="10" font-weight="bold"
                fill="#fff" letter-spacing="2.5">TNSTC</text>

          <!-- ── Wheel 1 (rear) — spinning group ── -->
          <ellipse cx="52" cy="74" rx="20" ry="20" fill="#111827"/>
          <g class="wheel-spin" style="transform-origin:52px 74px">
            <circle cx="52" cy="74" r="16" fill="#1f2937"/>
            <circle cx="52" cy="74" r="9"  fill="#374151"/>
            <circle cx="52" cy="74" r="3.5" fill="#9ca3af"/>
            <!-- spokes -->
            <line x1="52" y1="60" x2="52" y2="88" stroke="#6b7280" stroke-width="2"/>
            <line x1="38" y1="74" x2="66" y2="74" stroke="#6b7280" stroke-width="2"/>
            <line x1="42" y1="63" x2="62" y2="85" stroke="#6b7280" stroke-width="1.5"/>
            <line x1="62" y1="63" x2="42" y2="85" stroke="#6b7280" stroke-width="1.5"/>
          </g>

          <!-- ── Wheel 2 (front) — spinning group ── -->
          <ellipse cx="168" cy="74" rx="20" ry="20" fill="#111827"/>
          <g class="wheel-spin" style="transform-origin:168px 74px">
            <circle cx="168" cy="74" r="16" fill="#1f2937"/>
            <circle cx="168" cy="74" r="9"  fill="#374151"/>
            <circle cx="168" cy="74" r="3.5" fill="#9ca3af"/>
            <!-- spokes -->
            <line x1="168" y1="60" x2="168" y2="88" stroke="#6b7280" stroke-width="2"/>
            <line x1="154" y1="74" x2="182" y2="74" stroke="#6b7280" stroke-width="2"/>
            <line x1="158" y1="63" x2="178" y2="85" stroke="#6b7280" stroke-width="1.5"/>
            <line x1="178" y1="63" x2="158" y2="85" stroke="#6b7280" stroke-width="1.5"/>
          </g>
        </svg>
      </div><!-- /.scene-bus-wrap -->

    </div><!-- /.jm-scene -->

    <!-- ── Journey Progress ── -->
    <div class="jm-progress-section">
      <div class="jm-progress-label">
        <span id="jPFrom">—</span>
        <span style="color:var(--primary);font-size:10.5px;font-weight:700">🚌 En Route</span>
        <span id="jPTo">—</span>
      </div>
      <div class="jm-progress-bar">
        <div class="jm-progress-fill" id="jProgressFill"></div>
      </div>
    </div>

    <!-- ── Info Grid ── -->
    <div class="jm-info-grid">
      <div class="jm-info-cell">
        <div class="jm-info-val"  id="jFare">₹0</div>
        <div class="jm-info-label">Fare</div>
      </div>
      <div class="jm-info-cell">
        <div class="jm-info-val"  id="jSeats">0</div>
        <div class="jm-info-label">Seats Left</div>
      </div>
      <div class="jm-info-cell">
        <div class="jm-info-val"  id="jDepart">--:--</div>
        <div class="jm-info-label">Departs</div>
      </div>
      <div class="jm-info-cell">
        <div class="jm-info-val"  id="jArrive">--:--</div>
        <div class="jm-info-label">Arrives</div>
      </div>
    </div>

    <!-- ── Footer ── -->
    <div class="jm-footer">
      <a href="#" class="jm-book-btn" id="jBookBtn">
        <i class="fa fa-ticket"></i> Book This Bus
      </a>
      <button class="jm-close-btn2" id="jDismissBtn">Close</button>
    </div>

  </div><!-- /.journey-modal -->
</div><!-- /.journey-overlay -->

<!-- ══════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════ -->
<script>
/* ── Search logic ── */
const form         = document.getElementById('searchForm');
const resultsArea  = document.getElementById('resultsArea');
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
    const data = await apiGet(
      `/TNSTC/api/search.php?src=${encodeURIComponent(src)}&dst=${encodeURIComponent(dst)}&date=${date}`
    );
    loadingState.classList.add('d-none');
    if (!data.length) {
      resultsArea.innerHTML = `<div class="empty-state"><i class="fa fa-bus"></i>No buses found for this route on ${date}.<br>Try different dates or nearby stops.</div>`;
      return;
    }
    resultsArea.innerHTML =
      `<p class="text-muted mb-3" style="font-size:13px">${data.length} bus(es) found for <strong>${src} → ${dst}</strong> on ${date}</p>` +
      data.map(b => busCard(b, date)).join('');
  } catch(err) {
    loadingState.classList.add('d-none');
    resultsArea.innerHTML = `<div class="flash-banner flash-danger">Error fetching results. Please try again.</div>`;
  }
});

if ('<?= $src ?>' && '<?= $dst ?>') form.dispatchEvent(new Event('submit'));

/* ── Global bus data store (avoids quote-escaping in onclick attrs) ── */
const _busData = [];

/* ── Bus card builder ── */
function busCard(b, date) {
  const typeColors = {
    ordinary:     '#046A38', express: '#06038D',
    super_express:'#FF671F', ac: '#046A38', mini:'#FF671F'
  };
  const tc    = typeColors[b.bus_type] || '#6c757d';
  const avail = parseInt(b.available_seats);
  const ac    = avail > 10 ? '#166534' : avail > 0 ? '#854d0e' : '#991b1b';

  /* Store bus data safely in an array; pass only index to onclick */
  const idx = _busData.push({
    bus_number: b.bus_number, bus_type: b.bus_type,
    source: b.source, destination: b.destination,
    departure_time: b.departure_time, arrival_time: b.arrival_time,
    estimated_time: b.estimated_time, fare: b.fare,
    available_seats: b.available_seats, schedule_id: b.schedule_id,
    delay_minutes: b.delay_minutes, date: date
  }) - 1;

  return `
  <div class="card mb-3 animate-fade" style="border-left:4px solid ${tc}">
    <div class="card-body-custom">
      <div class="d-flex flex-wrap gap-3 align-items-start justify-content-between">
        <div>
          <div class="d-flex align-items-center gap-2 mb-1">
            <span style="font-size:18px">🚌</span>
            <strong style="font-size:16px">${b.bus_number}</strong>
            <span class="badge-custom badge-primary" style="background:${tc}22;color:${tc}">
              ${b.bus_type.replace('_',' ').toUpperCase()}
            </span>
          </div>
          <div style="font-size:14px;color:#333;margin-bottom:4px">
            Route ${b.route_number}: ${b.source} → ${b.destination}
          </div>
          <div style="font-size:12px;color:#6c757d">
            ${b.depot_name} &nbsp;|&nbsp; ${b.distance} km &nbsp;|&nbsp; ~${b.estimated_time} min
          </div>
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
          <div style="font-weight:700;color:${ac}">
            ${avail > 0 ? avail + ' seats available' : 'FULL'}
          </div>
          <div style="font-size:11px;color:#6c757d">
            ${b.delay_minutes > 0 ? '⚠ Delayed ' + b.delay_minutes + ' min' : 'On time'}
          </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn-primary-custom btn-sm-custom"
            style="background:linear-gradient(135deg,var(--primary),var(--accent));border:none;cursor:pointer"
            onclick="openJourney(${idx})"
            title="Animated journey preview">
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

/* ══════════════════════════════════════════════════════
   JOURNEY MODAL
══════════════════════════════════════════════════════ */
const overlay  = document.getElementById('journeyOverlay');
const modal    = document.getElementById('journeyModal');

/* Bus type → body colour palette [dark, mid, light] */
const BUS_COLORS = {
  ordinary:     ['#FF671F','#FFFFFF','#046A38'],
  express:      ['#FF671F','#FFFFFF','#046A38'],
  super_express:['#FF671F','#FFFFFF','#046A38'],
  ac:           ['#FF671F','#FFFFFF','#046A38'],
  mini:         ['#FF671F','#FFFFFF','#046A38'],
};
const TYPE_CHIP_STYLE = {
  ordinary:     'background:#e6f4ea;color:#046A38',
  express:      'background:#ebebff;color:#06038D',
  super_express:'background:#fff2eb;color:#FF671F',
  ac:           'background:#e0f2fe;color:#06038D',
  mini:         'background:#fff7ed;color:#FF671F',
};

function openJourney(idx) {
  const b = _busData[idx];

  /* header */
  document.getElementById('jBusNum').textContent = b.bus_number;
  document.getElementById('jFrom').textContent   = b.source;
  document.getElementById('jTo').textContent     = b.destination;
  document.getElementById('jDur').textContent    = '~' + b.estimated_time + ' min';

  /* type chip */
  const chip = document.getElementById('jTypeChip');
  chip.textContent  = b.bus_type.replace(/_/g,' ').toUpperCase();
  chip.style.cssText = (TYPE_CHIP_STYLE[b.bus_type] || 'background:#e2e8f0;color:#475569') +
                       ';padding:2px 9px;border-radius:10px;font-size:10px;font-weight:800';

  /* progress labels */
  document.getElementById('jPFrom').textContent = b.source;
  document.getElementById('jPTo').textContent   = b.destination;

  /* restart progress animation */
  const fill = document.getElementById('jProgressFill');
  fill.classList.remove('running');
  void fill.offsetWidth;          /* force reflow */
  fill.classList.add('running');

  /* info cells */
  document.getElementById('jFare').textContent   = '₹' + parseFloat(b.fare).toFixed(2);
  document.getElementById('jSeats').textContent  = parseInt(b.available_seats) > 0 ? b.available_seats : 'Full';
  document.getElementById('jDepart').textContent = b.departure_time.slice(0,5);
  document.getElementById('jArrive').textContent = b.arrival_time.slice(0,5);

  /* book button */
  const bookBtn = document.getElementById('jBookBtn');
  bookBtn.innerHTML = '<i class="fa fa-ticket"></i> Book This Bus';
  if (parseInt(b.available_seats) > 0) {
    bookBtn.href = `book_ticket.php?schedule_id=${b.schedule_id}&date=${b.date}`;
    bookBtn.style.opacity = '1';
    bookBtn.style.pointerEvents = 'auto';
  } else {
    bookBtn.href = '#';
    bookBtn.style.opacity = '.45';
    bookBtn.style.pointerEvents = 'none';
    bookBtn.innerHTML = '<i class="fa fa-ban"></i> Fully Booked';
  }

  /* recolour bus body */
  const pal = BUS_COLORS[b.bus_type] || BUS_COLORS.express;
  const svg = document.getElementById('busSVG');
  svg.querySelector('#busBody1').setAttribute('fill', pal[0]);
  svg.querySelector('#busBody2').setAttribute('fill', pal[1]);
  svg.querySelector('#busBody3').setAttribute('fill', pal[2]);

  /* show */
  overlay.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeJourney() {
  overlay.classList.remove('active');
  document.body.style.overflow = '';
}

document.getElementById('jCloseBtn').addEventListener('click',   closeJourney);
document.getElementById('jDismissBtn').addEventListener('click', closeJourney);
overlay.addEventListener('click', e => { if (e.target === overlay) closeJourney(); });
document.addEventListener('keydown', e => { if (e.key==='Escape') closeJourney(); });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
