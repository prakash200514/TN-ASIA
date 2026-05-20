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
<nav class="navbar navbar-expand-lg bg-white px-4 py-3" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03); border-bottom: 1px solid var(--gray-200);">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= APP_URL ?>" style="color: var(--primary)">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-primary" style="color: var(--primary)">
        <rect x="4" y="4" width="16" height="16" rx="2" />
        <path d="M4 9h16" />
        <path d="M4 14h16" />
        <path d="M8 18H8.01" />
        <path d="M16 18H16.01" />
      </svg>
      <div>
        <div style="font-size:16px;line-height:1;font-weight:800;letter-spacing:-0.02em">TNSTC</div>
        <div style="font-size:9px;color:var(--gray-600);letter-spacing:1px;font-weight:600">TIRUNELVELI DISTRICT</div>
      </div>
    </a>
    
    <div class="collapse navbar-collapse justify-content-center" id="navbarLandingContent">
      <ul class="navbar-nav gap-4">
        <li class="nav-item"><a class="nav-link active fw-bold" style="font-size:14.5px; border-bottom: 2px solid var(--primary); padding-bottom: 4px; color: var(--primary) !important" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link text-muted fw-bold" style="font-size:14.5px;" href="#about">About Us</a></li>
        <li class="nav-item"><a class="nav-link text-muted fw-bold" style="font-size:14.5px;" href="#services">Services</a></li>
        <li class="nav-item"><a class="nav-link text-muted fw-bold" style="font-size:14.5px;" href="#routes">Routes</a></li>
        <li class="nav-item"><a class="nav-link text-muted fw-bold" style="font-size:14.5px;" href="#depots">Depots</a></li>
        <li class="nav-item"><a class="nav-link text-muted fw-bold" style="font-size:14.5px;" href="#contact">Contact</a></li>
      </ul>
    </div>
    
    <div class="d-flex gap-2">
      <a href="<?= APP_URL ?>/auth/register.php" class="btn btn-outline-primary px-3 fw-bold btn-sm" style="border-radius:8px; border-color:var(--primary); color:var(--primary)">Register</a>
      <a href="<?= APP_URL ?>/auth/login.php" class="btn btn-primary px-3 fw-bold btn-sm" style="border-radius:8px; background:var(--primary); border:none">Login</a>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="landing-hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 animate-fade-right">
        <div class="mb-2" style="display:inline-block;background:rgba(37,99,235,0.08);padding:6px 14px;border-radius:20px;font-size:12px;color:var(--primary);letter-spacing:1px;font-weight:700">
          🏛️ GOVERNMENT OF TAMIL NADU
        </div>
        <h1 style="font-size:clamp(32px,4.5vw,48px);font-weight:800;line-height:1.2;color:var(--gray-800);margin-top:12px;letter-spacing:-0.03em">
          TNSTC Smart Bus<br>Management System
        </h1>
        <p style="color:var(--gray-600);font-size:16px;margin:20px 0 32px;line-height:1.6">
          Tirunelveli District — 7 Depots · Book tickets, track buses live, apply passes, and more — all in one platform.
        </p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="<?= APP_URL ?>/auth/register.php" class="btn-primary-custom" style="background:var(--primary);color:#fff;font-weight:700;border-radius:8px">
            <i class="fa fa-user-plus"></i> Get Started
          </a>
          <a href="<?= APP_URL ?>/auth/login.php" class="btn-primary-custom" style="background:transparent;color:var(--primary);border:1px solid var(--gray-200);box-shadow:none;border-radius:8px">
            <i class="fa fa-right-to-bracket"></i> Login
          </a>
        </div>
        
        <!-- Vector Bus & City Illustration -->
        <div class="hero-illustration d-none d-lg-block mt-5" style="position:relative; height: 130px; opacity: 0.9">
          <svg viewBox="0 0 400 120" style="position:absolute; bottom:0; left:0; width:100%; height:auto">
            <!-- City skyline -->
            <path d="M 0 120 L 0 80 L 15 80 L 15 90 L 30 90 L 30 70 L 45 70 L 45 100 L 60 100 L 60 60 L 80 60 L 80 95 L 95 95 L 95 50 L 115 50 L 115 85 L 130 85 L 130 75 L 145 75 L 145 120 Z" fill="#e2e8f0" opacity="0.5" />
            <path d="M 120 120 L 120 70 L 140 70 L 140 85 L 160 85 L 160 65 L 180 65 L 180 95 L 195 95 L 195 40 L 225 40 L 225 80 L 245 80 L 245 70 L 270 70 L 270 120 Z" fill="#cbd5e1" opacity="0.6" />
            <path d="M 240 120 L 240 80 L 255 80 L 255 90 L 270 90 L 270 60 L 290 60 L 290 95 L 310 95 L 310 55 L 330 55 L 330 85 L 350 85 L 350 75 L 370 75 L 370 120 Z" fill="#cbd5e1" opacity="0.4" />
            <!-- Trees -->
            <circle cx="340" cy="100" r="12" fill="#94a3b8" opacity="0.6" />
            <rect x="338" y="108" width="4" height="12" fill="#64748b" opacity="0.6" />
            <circle cx="355" cy="105" r="8" fill="#94a3b8" opacity="0.5" />
            <rect x="353" y="110" width="3" height="10" fill="#64748b" opacity="0.5" />
          </svg>
          
          <svg viewBox="0 0 200 70" width="180" style="position:absolute; bottom:0; left:30px">
            <rect x="5" y="55" width="170" height="10" rx="3" fill="#cbd5e1" />
            <rect x="10" y="15" width="160" height="40" rx="8" fill="#2563eb" />
            <rect x="10" y="32" width="160" height="23" fill="#1d4ed8" />
            <path d="M 40 15 L 130 15 L 120 10 L 50 10 Z" fill="#1e40af" />
            <path d="M 10 20 Q 5 20 5 27 L 5 45 L 20 45 L 20 20 Z" fill="#0f172a" />
            <path d="M 7 23 L 18 23 L 18 42 L 7 42 Z" fill="#60a5fa" opacity="0.8" />
            <rect x="25" y="20" width="22" height="15" rx="2" fill="#60a5fa" opacity="0.8" />
            <rect x="52" y="20" width="22" height="15" rx="2" fill="#60a5fa" opacity="0.8" />
            <rect x="79" y="20" width="22" height="15" rx="2" fill="#60a5fa" opacity="0.8" />
            <rect x="106" y="20" width="22" height="15" rx="2" fill="#60a5fa" opacity="0.8" />
            <rect x="133" y="20" width="22" height="15" rx="2" fill="#60a5fa" opacity="0.8" />
            <circle cx="32" cy="27" r="3" fill="#1e40af" opacity="0.6" />
            <circle cx="64" cy="27" r="3" fill="#1e40af" opacity="0.6" />
            <circle cx="118" cy="27" r="3" fill="#1e40af" opacity="0.6" />
            <circle cx="5" cy="48" r="3" fill="#fbbf24" />
            <polygon points="5,46 -15,40 -15,56" fill="#fef08a" opacity="0.3" />
            <rect x="168" y="45" width="2" height="6" fill="#ef4444" />
            <circle cx="45" cy="55" r="11" fill="#1e293b" />
            <circle cx="45" cy="55" r="5" fill="#94a3b8" />
            <circle cx="135" cy="55" r="11" fill="#1e293b" />
            <circle cx="135" cy="55" r="5" fill="#94a3b8" />
            <rect x="25" y="40" width="130" height="2" fill="#ffffff" opacity="0.7" />
          </svg>
        </div>
      </div>
      <div class="col-lg-6 mt-4 mt-lg-0 animate-fade-left">
        <!-- Quick Search Widget -->
        <div class="search-widget" style="border: 1px solid var(--gray-200); box-shadow: 0 20px 40px rgba(0,0,0,0.04); border-radius: 16px; padding: 30px;">
          <h5 style="font-size:17px;font-weight:800;margin-bottom:24px;color:var(--primary);letter-spacing:-0.02em; display:flex; align-items:center; gap:8px">
            <i class="fa fa-magnifying-glass" style="color:var(--primary)"></i> Search Bus
          </h5>
          <form action="<?= APP_URL ?>/passenger/search_bus.php" method="GET">
            <div class="row g-3">
              <div class="col-6">
                <label class="form-label" style="font-size:12.5px;font-weight:700;color:var(--gray-600)">From</label>
                <div style="position:relative">
                  <i class="fa fa-location-dot" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--gray-400)"></i>
                  <input type="text" name="src" class="form-control-custom" style="padding-left:36px; border-radius:10px;" placeholder="Tirunelveli" required>
                </div>
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size:12.5px;font-weight:700;color:var(--gray-600)">To</label>
                <div style="position:relative">
                  <i class="fa fa-location-dot" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--gray-400)"></i>
                  <input type="text" name="dst" class="form-control-custom" style="padding-left:36px; border-radius:10px;" placeholder="Valliyoor" required>
                </div>
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size:12.5px;font-weight:700;color:var(--gray-600)">Date</label>
                <div style="position:relative">
                  <i class="fa fa-calendar-days" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--gray-400); pointer-events:none"></i>
                  <input type="date" name="date" class="form-control-custom" style="padding-left:36px; border-radius:10px;" value="<?= date('Y-m-d') ?>" required>
                </div>
              </div>
              <div class="col-6 d-flex align-items-end">
                <button type="submit" class="btn-primary-custom w-100 justify-content-center" style="height:44px; border-radius:10px; background:var(--primary); font-weight:700; gap:8px">
                  <i class="fa fa-search"></i> Search
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Hero Stats Cards -->
    <div class="row g-3 mt-5">
      <!-- Depots -->
      <div class="col-6 col-lg-3 animate-fade-up delay-1">
        <div class="card p-3 d-flex flex-row align-items-center gap-3" style="border: 1px solid var(--gray-200); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.02)">
          <div style="width:48px;height:48px;background:#eff6ff;color:#3b82f6;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">
            <i class="fa fa-building"></i>
          </div>
          <div>
            <div style="font-size:24px;font-weight:800;color:var(--gray-800);line-height:1.1"><?= $totalDepots ?></div>
            <div style="font-size:11px;color:var(--gray-600);text-transform:uppercase;letter-spacing:0.5px;font-weight:700;margin-top:2px">Depots</div>
          </div>
        </div>
      </div>
      
      <!-- Active Buses -->
      <div class="col-6 col-lg-3 animate-fade-up delay-2">
        <div class="card p-3 d-flex flex-row align-items-center gap-3" style="border: 1px solid var(--gray-200); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.02)">
          <div style="width:48px;height:48px;background:#ecfdf5;color:#10b981;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">
            <i class="fa fa-bus"></i>
          </div>
          <div>
            <div style="font-size:24px;font-weight:800;color:var(--gray-800);line-height:1.1"><?= $totalBuses ?></div>
            <div style="font-size:11px;color:var(--gray-600);text-transform:uppercase;letter-spacing:0.5px;font-weight:700;margin-top:2px">Active Buses</div>
          </div>
        </div>
      </div>
      
      <!-- Routes -->
      <div class="col-6 col-lg-3 animate-fade-up delay-3">
        <div class="card p-3 d-flex flex-row align-items-center gap-3" style="border: 1px solid var(--gray-200); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.02)">
          <div style="width:48px;height:48px;background:#faf5ff;color:#8b5cf6;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">
            <i class="fa fa-route"></i>
          </div>
          <div>
            <div style="font-size:24px;font-weight:800;color:var(--gray-800);line-height:1.1"><?= $totalRoutes ?></div>
            <div style="font-size:11px;color:var(--gray-600);text-transform:uppercase;letter-spacing:0.5px;font-weight:700;margin-top:2px">Routes</div>
          </div>
        </div>
      </div>
      
      <!-- Passengers -->
      <div class="col-6 col-lg-3 animate-fade-up delay-4">
        <div class="card p-3 d-flex flex-row align-items-center gap-3" style="border: 1px solid var(--gray-200); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.02)">
          <div style="width:48px;height:48px;background:#fff7ed;color:#f59e0b;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">
            <i class="fa fa-users"></i>
          </div>
          <div>
            <div style="font-size:24px;font-weight:800;color:var(--gray-800);line-height:1.1"><?= $totalUsers ?>+</div>
            <div style="font-size:11px;color:var(--gray-600);text-transform:uppercase;letter-spacing:0.5px;font-weight:700;margin-top:2px">Passengers</div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Curved transition wave at the bottom of the hero -->
    <div style="position: absolute; bottom: 0; left: 0; right: 0; width: 100%; overflow: hidden; line-height: 0; pointer-events: none;">
      <svg viewBox="0 0 1440 60" preserveAspectRatio="none" style="width: 100%; height: 20px; display: block;">
        <path d="M0,30 Q360,60 720,30 T1440,30 L1440,60 L0,60 Z" fill="var(--primary)" opacity="0.95"></path>
      </svg>
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
