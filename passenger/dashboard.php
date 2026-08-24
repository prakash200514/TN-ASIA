<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$user = currentUser();
$db   = getDB();
$uid  = (int)$user['id'];

// Fetch real database counts for current passenger
$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE passenger_id=? AND ticket_status='active'");
$stmt->execute([$uid]);
$myTickets = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE passenger_id=?");
$stmt->execute([$uid]);
$totalTickets = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM bus_pass WHERE passenger_id=? AND status='approved' AND (valid_to >= CURDATE() OR valid_to IS NULL)");
$stmt->execute([$uid]);
$myPass = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM bus_pass WHERE passenger_id=?");
$stmt->execute([$uid]);
$totalPasses = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT status, COUNT(*) as cnt FROM bus_pass WHERE passenger_id=? GROUP BY status");
$stmt->execute([$uid]);
$passStatusCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$passApproved  = (int)($passStatusCounts['approved'] ?? 0);
$passPending   = (int)($passStatusCounts['pending'] ?? 0);
$passRejected  = (int)($passStatusCounts['rejected'] ?? 0);

$stmt = $db->prepare("SELECT COUNT(*) FROM complaints WHERE passenger_id=?");
$stmt->execute([$uid]);
$myComplaints = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? OR user_id IS NULL");
$stmt->execute([$uid]);
$myNotifsCount = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT * FROM notifications WHERE user_id=? OR user_id IS NULL ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$uid]);
$userNotifications = $stmt->fetchAll();

$totalApplications = $totalTickets + $totalPasses + $myComplaints;

$recentTickets = $db->prepare("SELECT t.*,s.departure_time,s.travel_date,r.source,r.destination,b.bus_number FROM tickets t JOIN schedules s ON s.schedule_id=t.schedule_id JOIN routes r ON r.route_id=s.route_id JOIN buses b ON b.bus_id=s.bus_id WHERE t.passenger_id=? ORDER BY t.booking_date DESC LIMIT 5");
$recentTickets->execute([$uid]);
$recent = $recentTickets->fetchAll();

$pageTitle = 'Passenger Portal – Government of Tamil Nadu';
include __DIR__ . '/../includes/header.php';
?>
<style>
  /* Tamil Nadu e-Gov Theme Styling */
  .egov-topbar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 24px;
    position: sticky;
    top: 0;
    z-index: 900;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.03);
  }
  .egov-search-input {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    padding: 8px 16px 8px 38px;
    font-size: 13px;
    width: 340px;
    transition: all 0.2s;
  }
  .egov-search-input:focus {
    background: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    outline: none;
  }
  .btn-acc-toggle {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
  }
  .btn-acc-toggle:hover { background: #e2e8f0; }

  /* Hero Banner */
  .egov-hero-banner {
    background: linear-gradient(135deg, #092047 0%, #0c336b 60%, #114b99 100%);
    border-radius: 16px;
    color: #ffffff;
    padding: 32px 36px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(9,32,71,0.25);
  }
  .egov-hero-banner::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; width: 45%;
    background: url('<?= APP_URL ?>/assets/images/cm_profile.jpg') no-repeat right center;
    background-size: cover;
    opacity: 0.15;
    pointer-events: none;
    mask-image: linear-gradient(to right, transparent, black);
  }

  /* Metric Cards */
  .egov-metric-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.2s;
  }
  .egov-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    border-color: #cbd5e1;
  }
  .metric-icon-wrapper {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
  }

  /* Quick Access Cards */
  .quick-service-tile {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    padding: 16px 12px;
    text-align: center;
    text-decoration: none !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    height: 100%;
    min-height: 110px;
  }
  .quick-service-tile:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    border-color: #cbd5e1;
  }
  .service-tile-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; margin-bottom: 8px;
  }
  .service-tile-title {
    font-size: 12px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.3;
  }

  /* Notifications Card */
  .notif-item {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    gap: 12px;
  }
  .notif-item:last-child { border-bottom: none; }
</style>

<div class="app-layout">
  <!-- Left Sidebar Navigation -->
  <?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>

  <!-- Main Content -->
  <div class="main-content">

    <!-- Topbar -->
    <div class="egov-topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="sidebar-toggle btn btn-sm btn-light d-lg-none" id="sidebarToggle"><i class="fa fa-bars"></i></button>
        <div style="position:relative" class="d-none d-md-block">
          <i class="fa fa-magnifying-glass" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px"></i>
          <input type="text" class="egov-search-input" placeholder="Search for services, bus routes, tickets, passes...">
        </div>
      </div>

      <div class="d-flex align-items-center gap-3">
        <div class="d-none d-sm-flex align-items-center gap-1">
          <button class="btn-acc-toggle">A-</button>
          <button class="btn-acc-toggle">A</button>
          <button class="btn-acc-toggle">A+</button>
          <button class="btn-acc-toggle ms-1" title="Toggle Theme"><i class="fa fa-moon"></i></button>
        </div>

        <div style="position:relative">
          <a href="notifications.php" class="btn btn-light btn-sm position-relative" style="border-radius:50%;width:38px;height:38px;background:#f8fafc;border:1px solid #e2e8f0;display:inline-flex;align-items:center;justify-content:center">
            <i class="fa fa-bell" style="color:#64748b"></i>
            <?php if ($myNotifsCount > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:9px"><?= $myNotifsCount ?></span>
            <?php endif; ?>
          </a>
        </div>

        <div class="d-flex align-items-center gap-2 ps-2 border-start">
          <div style="width:36px;height:36px;background:linear-gradient(135deg,#0284c7,#0369a1);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px">
            <?= strtoupper(substr($user['name'],0,1)) ?>
          </div>
          <div class="d-none d-md-block">
            <div style="font-weight:700;font-size:13px;color:#0f172a;line-height:1.1"><?= htmlspecialchars($user['name']) ?></div>
            <div style="font-size:10.5px;color:#64748b">Citizen / Passenger</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Page Body Content -->
    <div class="page-content p-4" style="background:#f8fafc;flex:1">

      <!-- Hero Banner -->
      <div class="egov-hero-banner mb-4 animate-fade">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <div style="font-size:14px;opacity:0.85;font-weight:600">Welcome back,</div>
            <h1 style="font-size:28px;font-weight:800;margin:4px 0 8px;letter-spacing:-0.02em">
              <?= htmlspecialchars($user['name']) ?> 👋
            </h1>
            <p style="font-size:13.5px;opacity:0.9;margin:0;max-width:540px">
              Access Tamil Nadu State Transport Corporation services, search bus routes, book QR tickets, track buses live, and manage passes.
            </p>
          </div>
          <div class="col-lg-4 text-end d-none d-lg-block" style="position:relative;z-index:2">
            <svg viewBox="0 0 200 100" style="width:180px;height:90px;fill:none;stroke:#ffffff;stroke-opacity:0.25;stroke-width:1.5">
              <!-- Temple Silhouette Artwork -->
              <path d="M100 10 L110 30 L115 30 L120 60 L125 60 L130 90 L70 90 L75 60 L80 60 L85 30 L90 30 Z" />
              <path d="M40 50 L50 90 L30 90 Z" />
              <path d="M160 50 L170 90 L150 90 Z" />
            </svg>
            <div style="font-weight:800;font-size:12px;letter-spacing:1px;color:#fde68a;margin-top:4px">
              TAMIL NADU
            </div>
            <div style="font-size:9px;opacity:0.75;letter-spacing:0.5px">TRUTH ALONE TRIUMPHS</div>
          </div>
        </div>
      </div>

      <!-- Top 5 Metric Cards Row -->
      <div class="row g-3 mb-4">
        <!-- Metric 1: Applications / Bookings -->
        <div class="col-6 col-md-4 col-lg-2-4">
          <div class="egov-metric-card">
            <div class="metric-icon-wrapper" style="background:#eff6ff;color:#2563eb">
              <i class="fa fa-file-lines"></i>
            </div>
            <div>
              <div style="font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase">Applications</div>
              <div style="font-size:22px;font-weight:800;color:#0f172a;line-height:1.1"><?= $totalApplications ?></div>
              <a href="my_tickets.php" style="font-size:11px;color:#2563eb;font-weight:700;text-decoration:none">View all &rarr;</a>
            </div>
          </div>
        </div>

        <!-- Metric 2: Valid Passes -->
        <div class="col-6 col-md-4 col-lg-2-4">
          <div class="egov-metric-card">
            <div class="metric-icon-wrapper" style="background:#f0fdf4;color:#16a34a">
              <i class="fa fa-id-card"></i>
            </div>
            <div>
              <div style="font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase">Passes Issued</div>
              <div style="font-size:22px;font-weight:800;color:#0f172a;line-height:1.1"><?= $myPass ?></div>
              <a href="bus_pass.php" style="font-size:11px;color:#16a34a;font-weight:700;text-decoration:none">View all &rarr;</a>
            </div>
          </div>
        </div>

        <!-- Metric 3: Grievances -->
        <div class="col-6 col-md-4 col-lg-2-4">
          <div class="egov-metric-card">
            <div class="metric-icon-wrapper" style="background:#faf5ff;color:#9333ea">
              <i class="fa fa-comments"></i>
            </div>
            <div>
              <div style="font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase">Grievances</div>
              <div style="font-size:22px;font-weight:800;color:#0f172a;line-height:1.1"><?= $myComplaints ?></div>
              <a href="complaints.php" style="font-size:11px;color:#9333ea;font-weight:700;text-decoration:none">View all &rarr;</a>
            </div>
          </div>
        </div>

        <!-- Metric 4: Active Tickets -->
        <div class="col-6 col-md-4 col-lg-2-4">
          <div class="egov-metric-card">
            <div class="metric-icon-wrapper" style="background:#fff7ed;color:#ea580c">
              <i class="fa fa-clock"></i>
            </div>
            <div>
              <div style="font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase">Active Tickets</div>
              <div style="font-size:22px;font-weight:800;color:#0f172a;line-height:1.1"><?= $myTickets ?></div>
              <a href="my_tickets.php" style="font-size:11px;color:#ea580c;font-weight:700;text-decoration:none">View all &rarr;</a>
            </div>
          </div>
        </div>

        <!-- Metric 5: Notifications -->
        <div class="col-6 col-md-4 col-lg-2-4">
          <div class="egov-metric-card">
            <div class="metric-icon-wrapper" style="background:#fef2f2;color:#ef4444">
              <i class="fa fa-bell"></i>
            </div>
            <div>
              <div style="font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase">Notifications</div>
              <div style="font-size:22px;font-weight:800;color:#0f172a;line-height:1.1"><?= $myNotifsCount ?></div>
              <a href="notifications.php" style="font-size:11px;color:#ef4444;font-weight:700;text-decoration:none">View all &rarr;</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Middle Row: Quick Access Services + Notifications -->
      <div class="row g-4 mb-4">

        <!-- Quick Access (Left Column 8 Cols) -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm p-4" style="border-radius:16px">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <h5 style="font-weight:800;color:#0f172a;margin:0;font-size:17px">Quick Access</h5>
                <div style="font-size:12px;color:#64748b">Frequently used transport & passenger services</div>
              </div>
              <a href="search_bus.php" style="font-size:12.5px;font-weight:700;color:#2563eb;text-decoration:none">View All Services &rarr;</a>
            </div>

            <div class="row g-3">
              <!-- Tile 1: Search Bus -->
              <div class="col-6 col-sm-4 col-md-3">
                <a href="search_bus.php" class="quick-service-tile">
                  <div class="service-tile-icon" style="background:#eff6ff;color:#2563eb">
                    <i class="fa fa-magnifying-glass"></i>
                  </div>
                  <div class="service-tile-title">Search & Book Bus</div>
                </a>
              </div>

              <!-- Tile 2: My Tickets -->
              <div class="col-6 col-sm-4 col-md-3">
                <a href="my_tickets.php" class="quick-service-tile">
                  <div class="service-tile-icon" style="background:#f0fdf4;color:#16a34a">
                    <i class="fa fa-ticket"></i>
                  </div>
                  <div class="service-tile-title">My Tickets & QR</div>
                </a>
              </div>

              <!-- Tile 3: Bus Pass -->
              <div class="col-6 col-sm-4 col-md-3">
                <a href="bus_pass.php" class="quick-service-tile">
                  <div class="service-tile-icon" style="background:#faf5ff;color:#9333ea">
                    <i class="fa fa-id-card"></i>
                  </div>
                  <div class="service-tile-title">Bus Pass Application</div>
                </a>
              </div>

              <!-- Tile 4: Live Track -->
              <div class="col-6 col-sm-4 col-md-3">
                <a href="live_tracking.php" class="quick-service-tile">
                  <div class="service-tile-icon" style="background:#fff7ed;color:#ea580c">
                    <i class="fa fa-map-location-dot"></i>
                  </div>
                  <div class="service-tile-title">Live Bus Tracking</div>
                </a>
              </div>

              <!-- Tile 5: Complaints -->
              <div class="col-6 col-sm-4 col-md-3">
                <a href="complaints.php" class="quick-service-tile">
                  <div class="service-tile-icon" style="background:#fef9c3;color:#854d0e">
                    <i class="fa fa-comments"></i>
                  </div>
                  <div class="service-tile-title">Grievance Portal</div>
                </a>
              </div>

              <!-- Tile 6: Lost & Found -->
              <div class="col-6 col-sm-4 col-md-3">
                <a href="lost_found.php" class="quick-service-tile">
                  <div class="service-tile-icon" style="background:#fff1f2;color:#e11d48">
                    <i class="fa fa-box-open"></i>
                  </div>
                  <div class="service-tile-title">Lost & Found Claim</div>
                </a>
              </div>

              <!-- Tile 7: AI Assistant -->
              <div class="col-6 col-sm-4 col-md-3">
                <a href="chatbot.php" class="quick-service-tile">
                  <div class="service-tile-icon" style="background:#f0f9ff;color:#0284c7">
                    <i class="fa fa-robot"></i>
                  </div>
                  <div class="service-tile-title">AI Chat Assistant</div>
                </a>
              </div>

              <!-- Tile 8: Govt Schemes -->
              <div class="col-6 col-sm-4 col-md-3">
                <a href="schemes.php" class="quick-service-tile">
                  <div class="service-tile-icon" style="background:#f5f3ff;color:#6d28d9">
                    <i class="fa fa-hand-holding-heart"></i>
                  </div>
                  <div class="service-tile-title">TN Govt Schemes</div>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Latest Notifications (Right Column 4 Cols) -->
        <div class="col-lg-4" id="notifications">
          <div class="card border-0 shadow-sm p-4 h-100" style="border-radius:16px">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 style="font-weight:800;color:#0f172a;margin:0;font-size:16px">Latest Notifications</h5>
              <a href="notifications.php" style="font-size:12px;font-weight:700;color:#2563eb;text-decoration:none">View All &rarr;</a>
            </div>

            <div class="notif-list">
              <?php if (!empty($userNotifications)): ?>
                <?php foreach ($userNotifications as $n): ?>
                <div class="notif-item">
                  <div style="width:36px;height:36px;border-radius:10px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fa fa-info-circle"></i>
                  </div>
                  <div>
                    <div style="font-weight:700;font-size:12.5px;color:#0f172a"><?= htmlspecialchars($n['title']) ?></div>
                    <div style="font-size:11.5px;color:#64748b;margin-top:2px"><?= htmlspecialchars($n['message']) ?></div>
                    <div style="font-size:10px;color:#94a3b8;margin-top:4px"><?= date('d M Y', strtotime($n['created_at'])) ?></div>
                  </div>
                </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-center py-4 text-muted">
                  <i class="fa fa-bell-slash fa-2x mb-2" style="opacity:0.4;color:#94a3b8"></i>
                  <div style="font-size:13px;font-weight:700;color:#475569">No Notifications</div>
                  <div style="font-size:11px;color:#94a3b8" class="mt-1">You have no new alerts or announcements.</div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Analytics & Profile Overview Row -->
      <div class="row g-4 mb-4">

        <!-- Applications Breakdown Donut Chart -->
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm p-4 h-100" style="border-radius:16px">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 style="font-weight:800;color:#0f172a;margin:0;font-size:16px">Applications Overview</h5>
              <a href="my_tickets.php" style="font-size:12px;font-weight:700;color:#2563eb;text-decoration:none">View All &rarr;</a>
            </div>

            <?php if ($totalApplications > 0): 
              $approvedCount = $passApproved + $myTickets;
              $approvedPct   = round(($approvedCount / $totalApplications) * 100, 1);
              $pendingPct    = round(($passPending / $totalApplications) * 100, 1);
              $complaintPct  = round(($myComplaints / $totalApplications) * 100, 1);
              $rejectedPct   = round(($passRejected / $totalApplications) * 100, 1);
            ?>
            <div class="d-flex align-items-center justify-content-center my-3" style="position:relative">
              <svg viewBox="0 0 100 100" style="width:150px;height:150px;transform:rotate(-90deg)">
                <circle cx="50" cy="50" r="38" stroke="#e2e8f0" stroke-width="14" fill="none" />
                <circle cx="50" cy="50" r="38" stroke="#16a34a" stroke-width="14" fill="none" stroke-dasharray="<?= ($approvedPct * 2.38) ?> 238" stroke-dashoffset="0" />
              </svg>
              <div style="position:absolute;text-align:center">
                <div style="font-size:24px;font-weight:800;color:#0f172a;line-height:1"><?= $totalApplications ?></div>
                <div style="font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase">Total</div>
              </div>
            </div>

            <div style="font-size:12px;display:grid;grid-template-columns:1fr 1fr;gap:8px" class="mt-2">
              <div><span style="display:inline-block;width:10px;height:10px;background:#16a34a;border-radius:50%" class="me-1"></span> Approved: <strong><?= $approvedCount ?> (<?= $approvedPct ?>%)</strong></div>
              <div><span style="display:inline-block;width:10px;height:10px;background:#ea580c;border-radius:50%" class="me-1"></span> Pending: <strong><?= $passPending ?> (<?= $pendingPct ?>%)</strong></div>
              <div><span style="display:inline-block;width:10px;height:10px;background:#9333ea;border-radius:50%" class="me-1"></span> Grievances: <strong><?= $myComplaints ?> (<?= $complaintPct ?>%)</strong></div>
              <div><span style="display:inline-block;width:10px;height:10px;background:#ef4444;border-radius:50%" class="me-1"></span> Rejected: <strong><?= $passRejected ?> (<?= $rejectedPct ?>%)</strong></div>
            </div>
            <?php else: ?>
            <div class="text-center py-4 my-auto">
              <div class="mx-auto mb-3" style="width:48px;height:48px;border-radius:50%;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fa fa-folder-open"></i>
              </div>
              <div style="font-weight:700;font-size:13.5px;color:#0f172a">No Applications Yet</div>
              <div style="font-size:11.5px;color:#64748b;margin-top:4px">Your application breakdown will appear here once you book tickets or apply for passes.</div>
              <a href="search_bus.php" class="btn btn-sm btn-primary mt-3 fw-bold" style="border-radius:8px;font-size:12px">Book First Ticket</a>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Monthly Applications Trend Chart -->
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm p-4 h-100" style="border-radius:16px">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 style="font-weight:800;color:#0f172a;margin:0;font-size:16px">Applications Activity</h5>
            </div>

            <?php if ($totalApplications > 0): ?>
            <div class="my-2" style="position:relative">
              <svg viewBox="0 0 300 120" style="width:100%;height:140px">
                <defs>
                  <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#2563eb" stop-opacity="0.3" />
                    <stop offset="100%" stop-color="#2563eb" stop-opacity="0.0" />
                  </linearGradient>
                </defs>
                <line x1="0" y1="20" x2="300" y2="20" stroke="#f1f5f9" stroke-dasharray="4" />
                <line x1="0" y1="60" x2="300" y2="60" stroke="#f1f5f9" stroke-dasharray="4" />
                <line x1="0" y1="100" x2="300" y2="100" stroke="#f1f5f9" stroke-dasharray="4" />
                <path d="M 10 100 L 290 40 L 290 110 L 10 110 Z" fill="url(#chartGrad)" />
                <path d="M 10 100 L 290 40" fill="none" stroke="#2563eb" stroke-width="3.5" stroke-linecap="round" />
                <circle cx="10" cy="100" r="4" fill="#2563eb" />
                <circle cx="290" cy="40" r="4" fill="#2563eb" />
              </svg>
            </div>
            <div class="d-flex justify-content-between text-muted" style="font-size:10px;font-weight:600">
              <span>Account Created</span>
              <span>Total Submissions: <?= $totalApplications ?></span>
            </div>
            <?php else: ?>
            <div class="text-center py-4 my-auto">
              <div class="mx-auto mb-3" style="width:48px;height:48px;border-radius:50%;background:#f0fdf4;color:#16a34a;display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fa fa-chart-line"></i>
              </div>
              <div style="font-weight:700;font-size:13.5px;color:#0f172a">No Activity Trends</div>
              <div style="font-size:11.5px;color:#64748b;margin-top:4px">Submit your first booking or bus pass request to view your monthly activity trend.</div>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Profile Summary Card -->
        <div class="col-lg-4" id="profile">
          <div class="card border-0 shadow-sm p-4 h-100" style="border-radius:16px">
            <h5 style="font-weight:800;color:#0f172a;margin:0 0 16px;font-size:16px">Profile Summary</h5>

            <div class="row g-2">
              <div class="col-6">
                <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #dcfce7">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fa fa-user-check text-success" style="font-size:16px"></i>
                    <span style="font-size:11px;font-weight:700;color:#166534">Account Role</span>
                  </div>
                  <div style="font-size:13px;font-weight:800;color:#15803d">Passenger</div>
                  <div style="font-size:10px;color:#166534">Verified Citizen</div>
                </div>
              </div>

              <div class="col-6">
                <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #dcfce7">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fa fa-mobile-screen text-success" style="font-size:16px"></i>
                    <span style="font-size:11px;font-weight:700;color:#166534">Phone Number</span>
                  </div>
                  <div style="font-size:12px;font-weight:800;color:#15803d" class="text-truncate"><?= htmlspecialchars(!empty($user['phone']) ? $user['phone'] : 'Registered') ?></div>
                  <div style="font-size:10px;color:#166534"><?= !empty($user['phone']) ? 'Verified' : 'Active' ?></div>
                </div>
              </div>

              <div class="col-6">
                <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #dcfce7">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fa fa-envelope text-success" style="font-size:16px"></i>
                    <span style="font-size:11px;font-weight:700;color:#166534">Email Address</span>
                  </div>
                  <div style="font-size:11px;font-weight:800;color:#15803d" class="text-truncate"><?= htmlspecialchars($user['email']) ?></div>
                  <div style="font-size:10px;color:#166534">Verified</div>
                </div>
              </div>

              <div class="col-6">
                <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #dcfce7">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fa fa-shield-halved text-success" style="font-size:16px"></i>
                    <span style="font-size:11px;font-weight:700;color:#166534">Account Status</span>
                  </div>
                  <div style="font-size:12px;font-weight:800;color:#15803d">Active</div>
                  <div style="font-size:10px;color:#166534">e-Gov Portal</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Row: Important Links & Support Card -->
      <div class="row g-4 mb-4">
        <!-- Important Links Grid (8 Cols) -->
        <div class="col-lg-8" id="schemes">
          <div class="card border-0 shadow-sm p-4" style="border-radius:16px">
            <h6 style="font-weight:800;color:#0f172a;margin:0 0 14px;font-size:15px">Important Government Portals & Schemes</h6>

            <div class="row g-3">
              <div class="col-6 col-sm-3">
                <a href="https://tn.gov.in" target="_blank" class="d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none" style="background:#f8fafc;border:1px solid #e2e8f0;color:#0f172a">
                  <div style="width:32px;height:32px;border-radius:8px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa fa-globe"></i></div>
                  <div>
                    <div style="font-size:12px;font-weight:700">TN Govt. Portal</div>
                    <div style="font-size:10px;color:#64748b">tn.gov.in</div>
                  </div>
                </a>
              </div>

              <div class="col-6 col-sm-3">
                <a href="https://cm.tn.gov.in" target="_blank" class="d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none" style="background:#f8fafc;border:1px solid #e2e8f0;color:#0f172a">
                  <div style="width:32px;height:32px;border-radius:8px;background:#f0fdf4;color:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa fa-user-tie"></i></div>
                  <div>
                    <div style="font-size:12px;font-weight:700">Chief Minister's Portal</div>
                    <div style="font-size:10px;color:#64748b">cm.tn.gov.in</div>
                  </div>
                </a>
              </div>

              <div class="col-6 col-sm-3">
                <a href="schemes.php" class="d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none" style="background:#f8fafc;border:1px solid #e2e8f0;color:#0f172a">
                  <div style="width:32px;height:32px;border-radius:8px;background:#faf5ff;color:#9333ea;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa fa-hand-holding-heart"></i></div>
                  <div>
                    <div style="font-size:12px;font-weight:700">TN State Schemes</div>
                    <div style="font-size:10px;color:#64748b">View Schemes</div>
                  </div>
                </a>
              </div>

              <div class="col-6 col-sm-3">
                <a href="complaints.php" class="d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none" style="background:#f8fafc;border:1px solid #e2e8f0;color:#0f172a">
                  <div style="width:32px;height:32px;border-radius:8px;background:#fff7ed;color:#ea580c;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa fa-comments"></i></div>
                  <div>
                    <div style="font-size:12px;font-weight:700">Grievance Portal</div>
                    <div style="font-size:10px;color:#64748b">Register Complaint</div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Need Help Support Card (4 Cols) -->
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm p-4 d-flex flex-row align-items-center justify-content-between" style="border-radius:16px;background:#ffffff">
            <div class="d-flex align-items-center gap-3">
              <div style="width:44px;height:44px;border-radius:50%;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">
                <i class="fa fa-headset"></i>
              </div>
              <div>
                <div style="font-weight:800;font-size:14px;color:#0f172a">Need Help?</div>
                <div style="font-size:11.5px;color:#64748b">Raise a request or get help from support team.</div>
              </div>
            </div>
            <a href="chatbot.php" class="btn btn-primary btn-sm px-3 py-2 fw-bold" style="border-radius:8px;font-size:12px;background:#092047;border:none;flex-shrink:0">
              Contact Support
            </a>
          </div>
        </div>
      </div>

    </div><!-- /.page-content -->

    <!-- Footer Bar -->
    <footer style="background:#ffffff;border-top:1px solid #e2e8f0;padding:16px 24px;font-size:12px;color:#64748b">
      <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <div>© 2026 Government of Tamil Nadu. All Rights Reserved.</div>
        <div class="d-flex gap-3">
          <a href="#" style="color:#64748b;text-decoration:none">Terms & Conditions</a>
          <a href="#" style="color:#64748b;text-decoration:none">Privacy Policy</a>
          <a href="#" style="color:#64748b;text-decoration:none">Accessibility</a>
          <a href="#" style="color:#64748b;text-decoration:none">Feedback</a>
        </div>
      </div>
    </footer>

  </div><!-- /.main-content -->
</div><!-- /.app-layout -->

<?php include __DIR__ . '/../includes/footer.php'; ?>
