<?php
require_once __DIR__ . '/config/db.php';
startAppSession();
if (isLoggedIn()) {
    $role = $_SESSION['role'];
    $map = ['passenger'=>'/passenger/dashboard.php','driver'=>'/driver/dashboard.php',
            'conductor'=>'/driver/dashboard.php','depot_manager'=>'/depot_manager/dashboard.php',
            'minister'=>'/minister/dashboard.php','admin'=>'/admin/dashboard.php'];
    header('Location: '.APP_URL.($map[$role]??'/'));
    exit;
}
// Quick stats for landing
$db = getDB();
$totalBuses  = $db->query('SELECT COUNT(*) FROM buses WHERE status="active"')->fetchColumn();
$totalRoutes = $db->query('SELECT COUNT(*) FROM routes')->fetchColumn();
$totalUsers  = $db->query('SELECT COUNT(*) FROM users WHERE role="passenger"')->fetchColumn();
$totalDepots = 7;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="TNSTC Smart Bus Management System for Tirunelveli District. Search routes, book tickets, track buses live.">
  <title>TNSTC Smart Bus System – Tirunelveli District</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="<?= APP_URL ?>/assets/css/tnstc.css" rel="stylesheet">
  <style>
    .hero-stat { text-align:center; color:#fff; }
    .hero-stat .num { font-size:36px; font-weight:800; display:block; }
    .hero-stat .lbl { font-size:12px; opacity:.7; text-transform:uppercase; letter-spacing:1px; }
    .navbar-landing { background:linear-gradient(90deg,#124d2b,#1a6b3c); }
    .wave { display:block; width:100%; height:60px; }
  </style>
</head>
<body style="background:#f8f9fa">

<!-- Navbar -->
<nav class="navbar navbar-landing navbar-expand-lg px-4 py-2">
  <a class="navbar-brand text-white fw-bold d-flex align-items-center gap-2" href="<?= APP_URL ?>">
    <span style="font-size:26px">🚌</span>
    <div><div style="font-size:15px;line-height:1">TNSTC</div><div style="font-size:10px;opacity:.7;letter-spacing:1px">TIRUNELVELI DISTRICT</div></div>
  </a>
  <div class="ms-auto d-flex gap-2">
    <a href="<?= APP_URL ?>/auth/register.php" class="btn btn-sm btn-outline-light fw-600">Register</a>
    <a href="<?= APP_URL ?>/auth/login.php" class="btn btn-sm btn-warning text-dark fw-bold">Login</a>
  </div>
</nav>

<!-- Hero -->
<section class="landing-hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 animate-fade-right">
        <div class="mb-2" style="display:inline-block;background:rgba(255,255,255,.12);padding:6px 14px;border-radius:20px;font-size:12px;color:rgba(255,255,255,.85);letter-spacing:1.5px;font-weight:600">
          🏛️ GOVERNMENT OF TAMIL NADU
        </div>
        <h1 style="font-size:clamp(28px,4.5vw,48px);font-weight:800;line-height:1.2;color:#fff;margin-top:12px;letter-spacing:-0.03em">
          TNSTC Smart Bus<br>Management System
        </h1>
        <p style="color:rgba(255,255,255,.8);font-size:16px;margin:20px 0 32px;line-height:1.6">
          Tirunelveli District — 7 Depots · Book tickets, track buses live, apply passes, and more — all in one platform.
        </p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="<?= APP_URL ?>/auth/register.php" class="btn-primary-custom" style="background:#ffc107;color:#1a1d23;font-weight:700">
            <i class="fa fa-user-plus"></i> Get Started
          </a>
          <a href="<?= APP_URL ?>/auth/login.php" class="btn-primary-custom" style="background:rgba(255,255,255,.15);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.1)">
            <i class="fa fa-right-to-bracket"></i> Login
          </a>
        </div>
      </div>
      <div class="col-lg-6 mt-4 mt-lg-0 animate-fade-left">
        <!-- Quick Search Widget -->
        <div class="search-widget">
          <h5 style="font-size:17px;font-weight:800;margin-bottom:20px;color:#14532d;letter-spacing:-0.02em">
            <i class="fa fa-magnifying-glass me-2" style="color:var(--primary)"></i>Search Bus
          </h5>
          <form action="<?= APP_URL ?>/passenger/search_bus.php" method="GET">
            <div class="row g-3">
              <div class="col-6">
                <label class="form-label" style="font-size:12px;font-weight:700;color:var(--gray-600)">From</label>
                <input type="text" name="src" class="form-control-custom" placeholder="Tirunelveli" required>
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size:12px;font-weight:700;color:var(--gray-600)">To</label>
                <input type="text" name="dst" class="form-control-custom" placeholder="Valliyoor" required>
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size:12px;font-weight:700;color:var(--gray-600)">Date</label>
                <input type="date" name="date" class="form-control-custom" value="<?= date('Y-m-d') ?>" required>
              </div>
              <div class="col-6 d-flex align-items-end">
                <button type="submit" class="btn-primary-custom w-100 justify-content-center" style="height:42px">
                  <i class="fa fa-search"></i> Search
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Hero Stats -->
    <div class="row g-4 mt-5">
      <div class="col-6 col-md-3 animate-fade-up delay-1"><div class="hero-stat"><span class="num"><?= $totalDepots ?></span><span class="lbl">Depots</span></div></div>
      <div class="col-6 col-md-3 animate-fade-up delay-2"><div class="hero-stat"><span class="num"><?= $totalBuses ?></span><span class="lbl">Active Buses</span></div></div>
      <div class="col-6 col-md-3 animate-fade-up delay-3"><div class="hero-stat"><span class="num"><?= $totalRoutes ?></span><span class="lbl">Routes</span></div></div>
      <div class="col-6 col-md-3 animate-fade-up delay-4"><div class="hero-stat"><span class="num"><?= $totalUsers ?>+</span><span class="lbl">Passengers</span></div></div>
    </div>
  </div>
</section>

<!-- Features -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5 animate-fade-up">
      <h2 style="font-size:28px;font-weight:800;letter-spacing:-0.03em">Everything You Need</h2>
      <p class="text-muted" style="font-size:15px">One platform for passengers, drivers, depot managers, and administration.</p>
    </div>
    <div class="row g-4">
      <?php
      $features = [
        ['🔍','Search Buses','Find routes by source and destination with real-time seat availability.','#e6f4ea','#14532d'],
        ['🎫','Book Tickets','Select seats, pay online, and download your QR code ticket instantly.','#eff6ff','#2563eb'],
        ['📍','Live Tracking','Track any TNSTC Tirunelveli bus live on Google Maps.','#fff7ed','#d97706'],
        ['🪪','Bus Pass','Apply for monthly or student bus pass online with document upload.','#faf5ff','#9333ea'],
        ['💬','Complaints','Submit complaints on delay, behavior, cleanliness and track resolution.','#fef9c3','#854d0e'],
        ['📦','Lost & Found','Report and claim lost items found in TNSTC Tirunelveli buses.','#f0fdfa','#0d9488'],
        ['🤖','AI Chatbot','Ask the AI assistant about routes, timings, and how to use the system.','#fef2f2','#ef4444'],
        ['📊','Analytics','Ministers and managers get full performance dashboards with charts.','#f0f9ff','#0284c7'],
      ];
      $idx = 0;
      foreach ($features as [$icon,$title,$desc,$bg,$color]):
        $delayClass = 'delay-' . (($idx % 4) + 1);
        $idx++;
      ?>
      <div class="col-sm-6 col-lg-3 animate-fade-up <?= $delayClass ?>">
        <div class="feature-card">
          <div class="feature-icon" style="background:<?= $bg ?>;color:<?= $color ?>"><?= $icon ?></div>
          <h3 style="font-size:16px;font-weight:700;margin-bottom:10px;letter-spacing:-0.01em"><?= $title ?></h3>
          <p style="font-size:13px;color:#6c757d;margin:0;line-height:1.5"><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Role Login Cards -->
<section class="py-5" style="background:#fff">
  <div class="container">
    <div class="text-center mb-5 animate-fade-up">
      <h2 style="font-size:28px;font-weight:800;letter-spacing:-0.03em">Login By Role</h2>
      <p class="text-muted" style="font-size:15px">Select your role to access the right panel.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <?php
      $roles = [
        ['🧑‍💼','Passenger','Register, book tickets, track buses, apply passes','passenger'],
        ['🚌','Driver / Conductor','View trips, scan QR tickets, report emergencies','driver'],
        ['🏢','Depot Manager','Manage depot buses, staff, schedules, and reports','depot_manager'],
        ['🏛️','TNSTC Minister','View district-wide analytics and performance reports','minister'],
        ['⚙️','Admin','Full system administration and management','admin'],
      ];
      $ridx = 0;
      foreach ($roles as [$icon,$name,$desc,$r]):
        $delayClass = 'delay-' . (($ridx % 5) + 1);
        $ridx++;
      ?>
      <div class="col-sm-6 col-md-4 col-lg-2-4 animate-scale-in <?= $delayClass ?>" style="flex:0 0 auto;width:210px">
        <a href="<?= APP_URL ?>/auth/login.php" class="role-card">
          <div style="font-size:40px;margin-bottom:12px"><?= $icon ?></div>
          <div style="font-weight:800;font-size:15px;color:#14532d;letter-spacing:-0.02em"><?= $name ?></div>
          <div style="font-size:12px;color:#6c757d;margin-top:6px;line-height:1.4"><?= $desc ?></div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Depots Section -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5 animate-fade-up">
      <h2 style="font-size:28px;font-weight:800;letter-spacing:-0.03em">Tirunelveli District Depots</h2>
    </div>
    <div class="row g-3">
      <?php
      $depots = [
        ['Thamirabarani Depot','Vannarpettai'],['Bye-Pass Depot','Vannarpettai'],
        ['Kattabomman Nagar Depot','KTC Nagar'],['Cheranmahadevi Depot','Cheranmahadevi'],
        ['Valliyoor Depot','Valliyoor'],['Thisayanvilai Depot','Thisayanvilai'],
        ['Papanasam Depot','Papanasam'],
      ];
      foreach ($depots as $i=>[$dname,$loc]):
        $delayClass = 'delay-' . (($i % 4) + 1);
      ?>
      <div class="col-sm-6 col-md-4 col-lg-3 animate-fade-up <?= $delayClass ?>">
        <div class="card p-3 d-flex flex-row align-items-center gap-3" style="border-left:4px solid var(--primary)">
          <div style="width:38px;height:38px;background:var(--primary-light);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">🏢</div>
          <div>
            <div style="font-weight:700;font-size:13.5px;color:var(--gray-800)"><?= htmlspecialchars($dname) ?></div>
            <div style="font-size:11.5px;color:#6c757d;margin-top:2px"><i class="fa fa-location-dot me-1" style="color:var(--primary)"></i><?= htmlspecialchars($loc) ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Footer -->
<footer style="background:#1a1d23;color:rgba(255,255,255,.7);padding:32px 0;margin-top:20px">
  <div class="container text-center">
    <div style="font-size:26px;margin-bottom:8px">🚌</div>
    <div style="font-weight:700;color:#fff;font-size:16px">TNSTC Smart Bus System</div>
    <div style="font-size:13px;margin-top:4px">Tirunelveli District · Tamil Nadu State Transport Corporation</div>
    <div style="margin-top:16px;font-size:12px;opacity:.5">
      © <?= date('Y') ?> TNSTC Tirunelveli. All Rights Reserved. | Government of Tamil Nadu
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
