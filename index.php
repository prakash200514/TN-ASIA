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
      <div class="col-lg-6">
        <div class="mb-2" style="display:inline-block;background:rgba(255,255,255,.12);padding:6px 14px;border-radius:20px;font-size:12px;color:rgba(255,255,255,.85);letter-spacing:1px">
          🏛️ GOVERNMENT OF TAMIL NADU
        </div>
        <h1 style="font-size:clamp(26px,4vw,44px);font-weight:800;line-height:1.2;color:#fff;margin-top:8px">
          TNSTC Smart Bus<br>Management System
        </h1>
        <p style="color:rgba(255,255,255,.75);font-size:15px;margin:16px 0 28px">
          Tirunelveli District — 7 Depots · Book tickets, track buses live, apply passes, and more — all in one platform.
        </p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="<?= APP_URL ?>/auth/register.php" class="btn-primary-custom" style="background:#ffc107;color:#1a1d23;font-weight:700">
            <i class="fa fa-user-plus"></i> Get Started
          </a>
          <a href="<?= APP_URL ?>/auth/login.php" class="btn-primary-custom" style="background:rgba(255,255,255,.15);backdrop-filter:blur(8px)">
            <i class="fa fa-right-to-bracket"></i> Login
          </a>
        </div>
      </div>
      <div class="col-lg-6 mt-4 mt-lg-0">
        <!-- Quick Search Widget -->
        <div class="search-widget">
          <h5 style="font-size:16px;font-weight:700;margin-bottom:16px;color:#1a6b3c">
            <i class="fa fa-magnifying-glass me-2"></i>Search Bus
          </h5>
          <form action="<?= APP_URL ?>/passenger/search_bus.php" method="GET">
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label" style="font-size:12px">From</label>
                <input type="text" name="src" class="form-control" placeholder="Tirunelveli" required>
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size:12px">To</label>
                <input type="text" name="dst" class="form-control" placeholder="Valliyoor" required>
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size:12px">Date</label>
                <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
              </div>
              <div class="col-6 d-flex align-items-end">
                <button type="submit" class="btn w-100 fw-bold" style="background:#1a6b3c;color:#fff;height:38px">
                  <i class="fa fa-search me-1"></i> Search
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Hero Stats -->
    <div class="row g-4 mt-4">
      <div class="col-6 col-md-3"><div class="hero-stat"><span class="num"><?= $totalDepots ?></span><span class="lbl">Depots</span></div></div>
      <div class="col-6 col-md-3"><div class="hero-stat"><span class="num"><?= $totalBuses ?></span><span class="lbl">Active Buses</span></div></div>
      <div class="col-6 col-md-3"><div class="hero-stat"><span class="num"><?= $totalRoutes ?></span><span class="lbl">Routes</span></div></div>
      <div class="col-6 col-md-3"><div class="hero-stat"><span class="num"><?= $totalUsers ?>+</span><span class="lbl">Passengers</span></div></div>
    </div>
  </div>
</section>

<!-- Features -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h2 style="font-size:26px;font-weight:700">Everything You Need</h2>
      <p class="text-muted">One platform for passengers, drivers, depot managers, and administration.</p>
    </div>
    <div class="row g-4">
      <?php
      $features = [
        ['🔍','Search Buses','Find routes by source and destination with real-time seat availability.','#e8f5ee','#1a6b3c'],
        ['🎫','Book Tickets','Select seats, pay online, and download your QR code ticket instantly.','#dbeafe','#1d4ed8'],
        ['📍','Live Tracking','Track any TNSTC Tirunelveli bus live on Google Maps.','#fff7ed','#c2410c'],
        ['🪪','Bus Pass','Apply for monthly or student bus pass online with document upload.','#f3e8ff','#7c3aed'],
        ['💬','Complaints','Submit complaints on delay, behavior, cleanliness and track resolution.','#fef9c3','#854d0e'],
        ['📦','Lost & Found','Report and claim lost items found in TNSTC Tirunelveli buses.','#ccfbf1','#0f766e'],
        ['🤖','AI Chatbot','Ask the AI assistant about routes, timings, and how to use the system.','#fee2e2','#b91c1c'],
        ['📊','Analytics','Ministers and managers get full performance dashboards with charts.','#e0f2fe','#075985'],
      ];
      foreach ($features as [$icon,$title,$desc,$bg,$color]):
      ?>
      <div class="col-sm-6 col-lg-3">
        <div class="feature-card">
          <div class="feature-icon" style="background:<?= $bg ?>;color:<?= $color ?>"><?= $icon ?></div>
          <h3 style="font-size:15px;font-weight:700;margin-bottom:8px"><?= $title ?></h3>
          <p style="font-size:13px;color:#6c757d;margin:0"><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Role Login Cards -->
<section class="py-5" style="background:#fff">
  <div class="container">
    <div class="text-center mb-4">
      <h2 style="font-size:24px;font-weight:700">Login By Role</h2>
      <p class="text-muted">Select your role to access the right panel.</p>
    </div>
    <div class="row g-3 justify-content-center">
      <?php
      $roles = [
        ['🧑‍💼','Passenger','Register, book tickets, track buses, apply passes','passenger'],
        ['🚌','Driver / Conductor','View trips, scan QR tickets, report emergencies','driver'],
        ['🏢','Depot Manager','Manage depot buses, staff, schedules, and reports','depot_manager'],
        ['🏛️','TNSTC Minister','View district-wide analytics and performance reports','minister'],
        ['⚙️','Admin','Full system administration and management','admin'],
      ];
      foreach ($roles as [$icon,$name,$desc,$r]):
      ?>
      <div class="col-sm-6 col-md-4 col-lg-2-4" style="flex:0 0 auto;width:200px">
        <a href="<?= APP_URL ?>/auth/login.php" class="role-card">
          <div style="font-size:36px;margin-bottom:10px"><?= $icon ?></div>
          <div style="font-weight:700;font-size:14px;color:#1a6b3c"><?= $name ?></div>
          <div style="font-size:11px;color:#6c757d;margin-top:4px"><?= $desc ?></div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Depots Section -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h2 style="font-size:24px;font-weight:700">Tirunelveli District Depots</h2>
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
      ?>
      <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="card p-3 d-flex flex-row align-items-center gap-3" style="border-left:4px solid #1a6b3c">
          <div style="width:36px;height:36px;background:#e8f5ee;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">🏢</div>
          <div>
            <div style="font-weight:600;font-size:13px"><?= htmlspecialchars($dname) ?></div>
            <div style="font-size:11px;color:#6c757d"><i class="fa fa-location-dot me-1"></i><?= htmlspecialchars($loc) ?></div>
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
</body>
</html>
