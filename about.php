<?php
require_once __DIR__ . '/config/db.php';
startAppSession();

$loggedUser  = isLoggedIn() ? currentUser() : null;
$isPassenger = $loggedUser && $loggedUser['role'] === 'passenger';

$pageTitle = 'About Us – TNSTC Tirunelveli';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="About TNSTC Tirunelveli – History, mission, depots and official documents of Tamil Nadu State Transport Corporation Tirunelveli District.">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="<?= APP_URL ?>/assets/css/tnstc.css" rel="stylesheet">
  <style>

    /* ── Page Background ── */
    body { background: #f4f6fb; }

    /* ── Navbar ── */
    .about-navbar {
      background: #fff;
      box-shadow: 0 2px 16px rgba(0,0,0,.05);
      border-bottom: 1px solid #e2e8f0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    /* ── Hero Banner ── */
    .about-hero {
      background: linear-gradient(135deg, #0f2d5e 0%, #1a3a6b 45%, #1e4080 100%);
      position: relative;
      padding: 60px 0 48px;
      overflow: hidden;
    }
    .about-hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .about-hero::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 60px;
      background: #f4f6fb;
      clip-path: ellipse(55% 100% at 50% 100%);
    }
    .about-hero .container { position: relative; z-index: 2; }
    .about-hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(255,255,255,.12);
      border: 1px solid rgba(255,255,255,.2);
      backdrop-filter: blur(12px);
      color: #fbbf24;
      font-size: 11.5px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      padding: 6px 16px;
      border-radius: 30px;
      margin-bottom: 16px;
    }
    .about-hero h1 {
      font-size: clamp(28px, 4vw, 42px);
      font-weight: 800;
      color: #fff;
      letter-spacing: -0.03em;
      line-height: 1.2;
      margin-bottom: 12px;
    }
    .about-hero p {
      color: rgba(255,255,255,.78);
      font-size: 15.5px;
      max-width: 540px;
      line-height: 1.7;
    }
    .about-breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: rgba(255,255,255,.55);
      margin-bottom: 20px;
    }
    .about-breadcrumb a { color: rgba(255,255,255,.75); text-decoration: none; }
    .about-breadcrumb a:hover { color: #fbbf24; }
    .about-breadcrumb .sep { font-size: 11px; }

    /* ── Stats strip ── */
    .stats-strip {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0,0,0,.06);
      padding: 24px 0;
      margin: -24px 0 40px;
      position: relative;
      z-index: 5;
    }
    .stats-strip .stat-item {
      text-align: center;
      border-right: 1px solid #e2e8f0;
      padding: 8px 0;
    }
    .stats-strip .stat-item:last-child { border-right: none; }
    .stats-strip .stat-num {
      font-size: 30px;
      font-weight: 800;
      color: #1a3a6b;
      display: block;
      line-height: 1;
    }
    .stats-strip .stat-lbl {
      font-size: 11.5px;
      color: #64748b;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .5px;
      margin-top: 5px;
    }

    /* ── Section Head ── */
    .section-head {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 24px;
    }
    .section-head-icon {
      width: 46px; height: 46px;
      border-radius: 12px;
      background: linear-gradient(135deg, #1a3a6b, #2563eb);
      display: flex; align-items: center; justify-content: center;
      color: #fff;
      font-size: 20px;
      flex-shrink: 0;
      box-shadow: 0 6px 16px rgba(26,58,107,.25);
    }
    .section-head h2 {
      font-size: 22px;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -.03em;
      line-height: 1.2;
    }
    .section-head p {
      font-size: 13px;
      color: #64748b;
      margin: 2px 0 0;
    }

    /* ── Cards ── */
    .content-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 18px rgba(0,0,0,.05);
      border: 1px solid #e9eef5;
      overflow: hidden;
    }
    .content-card-body {
      padding: 32px;
    }

    /* ── Mission / Vision cards ── */
    .mv-card {
      border-radius: 16px;
      padding: 28px;
      height: 100%;
      position: relative;
      overflow: hidden;
    }
    .mv-card.mission {
      background: linear-gradient(135deg, #eff6ff, #dbeafe);
      border: 1px solid #bfdbfe;
    }
    .mv-card.vision {
      background: linear-gradient(135deg, #fefce8, #fef3c7);
      border: 1px solid #fde68a;
    }
    .mv-card.values {
      background: linear-gradient(135deg, #f0fdf4, #dcfce7);
      border: 1px solid #bbf7d0;
    }
    .mv-card-icon {
      font-size: 32px;
      margin-bottom: 12px;
      display: block;
    }
    .mv-card h3 {
      font-size: 17px;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 10px;
    }
    .mv-card p {
      font-size: 13.5px;
      color: #374151;
      line-height: 1.7;
    }

    /* ── Timeline ── */
    .timeline-wrap {
      position: relative;
      padding-left: 40px;
    }
    .timeline-wrap::before {
      content: '';
      position: absolute;
      left: 16px;
      top: 0; bottom: 0;
      width: 3px;
      background: linear-gradient(to bottom, #1a3a6b, #2563eb, #FF671F);
      border-radius: 3px;
    }
    .tl-item {
      position: relative;
      margin-bottom: 28px;
      padding-bottom: 4px;
    }
    .tl-item:last-child { margin-bottom: 0; }
    .tl-dot {
      position: absolute;
      left: -32px;
      top: 8px;
      width: 18px; height: 18px;
      border-radius: 50%;
      background: #FF671F;
      border: 3px solid #fff;
      box-shadow: 0 0 0 3px rgba(255,103,31,.25);
    }
    .tl-year {
      font-size: 12px;
      font-weight: 700;
      color: #FF671F;
      letter-spacing: .5px;
      text-transform: uppercase;
      margin-bottom: 4px;
    }
    .tl-title {
      font-size: 15px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 5px;
    }
    .tl-desc {
      font-size: 13.5px;
      color: #475569;
      line-height: 1.65;
    }

    /* ── PDF Documents Table ── */
    .pdf-header-bar {
      background: linear-gradient(90deg, #1a3a6b, #0f2d5e);
      color: #fff;
      padding: 16px 24px;
      border-radius: 14px 14px 0 0;
    }
    .pdf-header-bar h3 {
      font-size: 16px;
      font-weight: 700;
      margin: 0;
    }
    .pdf-header-bar p {
      font-size: 12.5px;
      color: rgba(255,255,255,.7);
      margin: 2px 0 0;
    }

    .pdf-col-header {
      display: grid;
      grid-template-columns: 50px 1fr 140px;
      gap: 12px;
      padding: 10px 24px;
      background: #f1f5f9;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .5px;
      color: #64748b;
      border-bottom: 1px solid #e2e8f0;
    }

    .pdf-row {
      display: grid;
      grid-template-columns: 50px 1fr 140px;
      gap: 12px;
      padding: 15px 24px;
      align-items: center;
      border-bottom: 1px solid #f1f5f9;
      transition: background .18s;
    }
    .pdf-row:last-child { border-bottom: none; }
    .pdf-row:hover { background: #f8fafc; }

    .pdf-num {
      font-size: 13px;
      font-weight: 700;
      color: #94a3b8;
    }
    .pdf-title-cell {}
    .pdf-title {
      font-size: 14px;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 3px;
    }
    .pdf-meta {
      font-size: 12px;
      color: #94a3b8;
    }

    .pdf-view-btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      background: #dc2626;
      color: #fff;
      font-size: 13px;
      font-weight: 600;
      padding: 8px 16px;
      border-radius: 8px;
      text-decoration: none;
      transition: background .18s, transform .18s, box-shadow .18s;
      box-shadow: 0 3px 10px rgba(220,38,38,.25);
    }
    .pdf-view-btn:hover {
      background: #b91c1c;
      color: #fff;
      transform: translateY(-1px);
      box-shadow: 0 5px 16px rgba(220,38,38,.35);
      text-decoration: none;
    }
    .pdf-view-btn .pdf-icon {
      font-size: 16px;
      line-height: 1;
    }

    /* Locked row */
    .pdf-row.locked .pdf-title { color: #94a3b8; }
    .pdf-coming-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      background: #f1f5f9;
      color: #94a3b8;
      font-size: 12px;
      font-weight: 600;
      padding: 8px 14px;
      border-radius: 8px;
      border: 1px dashed #cbd5e1;
    }

    /* ── Depot Cards ── */
    .depot-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 16px;
    }
    .depot-card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid #e2e8f0;
      padding: 20px;
      transition: transform .2s, box-shadow .2s;
    }
    .depot-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 28px rgba(0,0,0,.08);
    }
    .depot-badge {
      width: 40px; height: 40px;
      border-radius: 10px;
      background: linear-gradient(135deg, #1a3a6b, #2563eb);
      display: flex; align-items: center; justify-content: center;
      font-size: 18px;
      margin-bottom: 12px;
    }
    .depot-card h4 {
      font-size: 14px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 4px;
    }
    .depot-card p {
      font-size: 12.5px;
      color: #64748b;
      margin: 0;
    }

    /* ── Sidebar ── */
    .about-sidebar {}
    .sidebar-widget {
      background: #fff;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 4px 14px rgba(0,0,0,.04);
      overflow: hidden;
      margin-bottom: 20px;
    }
    .sw-head {
      background: linear-gradient(90deg, #1a3a6b, #0f2d5e);
      color: #fff;
      padding: 14px 20px;
      font-size: 14px;
      font-weight: 700;
    }
    .sw-body { padding: 16px 20px; }
    .quick-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 0;
      border-bottom: 1px solid #f1f5f9;
      color: #374151;
      font-size: 13.5px;
      text-decoration: none;
      transition: color .15s;
    }
    .quick-link:last-child { border-bottom: none; }
    .quick-link:hover { color: #1a3a6b; }
    .quick-link i { color: #FF671F; width: 18px; text-align: center; }

    .contact-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 10px 0;
      border-bottom: 1px solid #f1f5f9;
    }
    .contact-item:last-child { border-bottom: none; }
    .contact-item i { color: #1a3a6b; margin-top: 2px; font-size: 14px; }
    .contact-item .ci-label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .4px;
      color: #94a3b8;
      margin-bottom: 1px;
    }
    .contact-item .ci-val {
      font-size: 13px;
      color: #1e293b;
      font-weight: 600;
    }

    /* ── Leadership ── */
    .leader-card {
      background: #fff;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      padding: 22px;
      text-align: center;
      transition: transform .2s, box-shadow .2s;
    }
    .leader-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 28px rgba(0,0,0,.07);
    }
    .leader-avatar {
      width: 72px; height: 72px;
      border-radius: 50%;
      object-fit: cover;
      object-position: top center;
      border: 3px solid #1a3a6b;
      margin: 0 auto 12px;
      display: block;
    }
    .leader-initial {
      width: 72px; height: 72px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1a3a6b, #2563eb);
      display: flex; align-items: center; justify-content: center;
      font-size: 26px;
      font-weight: 800;
      color: #fff;
      margin: 0 auto 12px;
    }
    .leader-name {
      font-size: 15px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 3px;
    }
    .leader-role {
      font-size: 12px;
      color: #64748b;
      font-weight: 500;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .stats-strip .stat-item { border-right: none; border-bottom: 1px solid #e2e8f0; }
      .pdf-col-header { display: none; }
      .pdf-row { grid-template-columns: 36px 1fr; }
      .pdf-row .pdf-view-btn { display: none; }
      .pdf-row .pdf-title::after { content: ' – View PDF'; color: #dc2626; }
      .depot-grid { grid-template-columns: 1fr 1fr; }
    }
  </style>
</head>
<body>

<!-- ═══════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════ -->
<nav class="about-navbar px-4 py-3">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <a class="fw-bold d-flex align-items-center gap-2 text-decoration-none" href="<?= APP_URL ?>">
      <div class="emblem-brand-container">
        <img src="<?= APP_URL ?>/assets/images/cm_profile.jpg" alt="CM Profile" class="emblem-profile-bg" title="Chief Minister Office">
        <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Tamil Nadu Government Emblem" class="emblem-logo-main">
      </div>
      <div>
        <div style="font-size:16px;line-height:1;font-weight:800;letter-spacing:-0.02em;color:#1a3a6b">TNSTC</div>
        <div style="font-size:9px;color:#64748b;letter-spacing:1px;font-weight:600">TIRUNELVELI DISTRICT</div>
      </div>
    </a>

    <div class="d-flex gap-1">
      <a href="<?= APP_URL ?>" class="btn btn-sm text-muted fw-bold" style="font-size:14px">Home</a>
      <a href="<?= APP_URL ?>/about.php" class="btn btn-sm fw-bold" style="font-size:14px;color:#1a3a6b;border-bottom:2px solid #FF671F;border-radius:0">About Us</a>
      <?php if ($isPassenger): ?>
        <a href="<?= APP_URL ?>/passenger/search_bus.php" class="btn btn-sm fw-bold ms-2" style="font-size:14px;background:#FF671F;color:#fff;border-radius:8px;padding:6px 16px">Search Bus</a>
      <?php else: ?>
        <a href="<?= APP_URL ?>/auth/login.php" class="btn btn-sm fw-bold ms-2" style="font-size:14px;background:#1a3a6b;color:#fff;border-radius:8px;padding:6px 16px">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- ═══════════════════════════════════════════
     HERO BANNER
══════════════════════════════════════════════ -->
<section class="about-hero">
  <div class="container">
    <div class="about-breadcrumb">
      <a href="<?= APP_URL ?>"><i class="fa fa-house"></i> Home</a>
      <span class="sep"><i class="fa fa-chevron-right" style="font-size:9px"></i></span>
      <span>About Us</span>
    </div>
    <div class="about-hero-badge">
      <i class="fa fa-building-columns"></i> Government of Tamil Nadu Undertaking
    </div>
    <h1>About TNSTC<br>Tirunelveli District</h1>
    <p>Tamil Nadu State Transport Corporation – Tirunelveli. Serving millions of passengers across southern Tamil Nadu since 1972 with safe, affordable and reliable bus transport.</p>
  </div>
</section>

<!-- ═══════════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════════════ -->
<div class="container" style="padding-top: 10px; padding-bottom: 60px;">

  <!-- Stats Strip -->
  <div class="stats-strip">
    <div class="row g-0">
      <div class="col-6 col-md-2 stat-item">
        <span class="stat-num">1972</span>
        <div class="stat-lbl">Established</div>
      </div>
      <div class="col-6 col-md-2 stat-item">
        <span class="stat-num">7</span>
        <div class="stat-lbl">Depots</div>
      </div>
      <div class="col-6 col-md-2 stat-item">
        <span class="stat-num">800+</span>
        <div class="stat-lbl">Buses</div>
      </div>
      <div class="col-6 col-md-2 stat-item">
        <span class="stat-num">500+</span>
        <div class="stat-lbl">Routes</div>
      </div>
      <div class="col-6 col-md-2 stat-item">
        <span class="stat-num">2L+</span>
        <div class="stat-lbl">Daily Passengers</div>
      </div>
      <div class="col-6 col-md-2 stat-item">
        <span class="stat-num">5000+</span>
        <div class="stat-lbl">Employees</div>
      </div>
    </div>
  </div>

  <div class="row g-4">

    <!-- ── Left Main Column ── -->
    <div class="col-lg-8">

      <!-- Mission / Vision / Values -->
      <div class="content-card mb-4">
        <div class="content-card-body">
          <div class="section-head">
            <div class="section-head-icon"><i class="fa fa-bullseye"></i></div>
            <div>
              <h2>Our Mission & Vision</h2>
              <p>The pillars that guide every service we deliver</p>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <div class="mv-card mission">
                <span class="mv-card-icon">🎯</span>
                <h3>Mission</h3>
                <p>To provide safe, affordable, and reliable public bus transportation services to the citizens of Tirunelveli District and beyond, fostering connectivity and economic growth.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mv-card vision">
                <span class="mv-card-icon">🔭</span>
                <h3>Vision</h3>
                <p>To be the most trusted and preferred public transport provider in Southern Tamil Nadu, embracing sustainable technology and world-class service standards.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mv-card values">
                <span class="mv-card-icon">⭐</span>
                <h3>Core Values</h3>
                <p>Safety First · Passenger Respect · Punctuality · Transparency · Environmental Responsibility · Inclusive Service for All.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Journey Timeline -->
      <div class="content-card mb-4">
        <div class="content-card-body">
          <div class="section-head">
            <div class="section-head-icon"><i class="fa fa-timeline"></i></div>
            <div>
              <h2>Our Journey</h2>
              <p>Key milestones in our history since 1947</p>
            </div>
          </div>

          <div class="timeline-wrap">
            <div class="tl-item">
              <div class="tl-dot"></div>
              <div class="tl-year">1947</div>
              <div class="tl-title">Post-Independence Nationalization</div>
              <div class="tl-desc">Following Indian Independence, the Madras State Government initiated plans to nationalize private bus services for fair and universal access to public transport.</div>
            </div>
            <div class="tl-item">
              <div class="tl-dot"></div>
              <div class="tl-year">1972</div>
              <div class="tl-title">TNSTC Tirunelveli Established</div>
              <div class="tl-desc">Tamil Nadu State Transport Corporation (Tirunelveli) Limited was formally incorporated under the Companies Act, beginning operations with ~200 buses.</div>
            </div>
            <div class="tl-item">
              <div class="tl-dot"></div>
              <div class="tl-year">1975–1985</div>
              <div class="tl-title">Rapid Fleet & Depot Expansion</div>
              <div class="tl-desc">New depots at Cheranmahadevi, Valliyoor, and Thisayanvilai were established. Fleet grew from 200 to over 700 buses connecting every corner of Tirunelveli.</div>
            </div>
            <div class="tl-item">
              <div class="tl-dot"></div>
              <div class="tl-year">1986</div>
              <div class="tl-title">District Reorganization</div>
              <div class="tl-desc">When Thoothukudi was carved as a separate district, TNSTC Tirunelveli was reorganized while maintaining seamless service continuity for passengers.</div>
            </div>
            <div class="tl-item">
              <div class="tl-dot"></div>
              <div class="tl-year">2005</div>
              <div class="tl-title">Modernization & AC Services</div>
              <div class="tl-desc">Introduction of semi-luxury and air-conditioned buses on major routes. Computer-based ticketing and fleet management systems were adopted.</div>
            </div>
            <div class="tl-item">
              <div class="tl-dot"></div>
              <div class="tl-year">2022–Present</div>
              <div class="tl-title">Smart Mobility & Digital Era</div>
              <div class="tl-desc">Launch of the TNSTC Smart Bus System with online booking, real-time GPS tracking, QR ticketing, AI complaint management, and electric bus pilots.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- PDF Documents Table -->
      <div class="content-card mb-4">
        <div class="pdf-header-bar">
          <h3><i class="fa fa-folder-open me-2"></i>Official Documents & History</h3>
          <p>Click "View" to open the document in a PDF viewer</p>
        </div>
        <div class="pdf-col-header">
          <span>#</span>
          <span>About Us</span>
          <span>View</span>
        </div>

        <!-- Row 1 — AVAILABLE -->
        <div class="pdf-row">
          <div class="pdf-num">1</div>
          <div class="pdf-title-cell">
            <div class="pdf-title">History of TNSTC Tirunelveli District</div>
            <div class="pdf-meta"><i class="fa fa-clock me-1"></i>Establishment · Fleet · Depots · Timeline · 2026</div>
          </div>
          <div>
            <a href="<?= APP_URL ?>/assets/pdfs/history-tirunelveli-tnstc.php" target="_blank" class="pdf-view-btn">
              <span class="pdf-icon">📄</span> View
            </a>
          </div>
        </div>

        <!-- Row 2 — Coming soon -->
        <div class="pdf-row locked">
          <div class="pdf-num">2</div>
          <div class="pdf-title-cell">
            <div class="pdf-title">History of Tamil Nadu State Transport Undertakings</div>
            <div class="pdf-meta"><i class="fa fa-clock me-1"></i>State-wide historical overview</div>
          </div>
          <div>
            <span class="pdf-coming-badge"><i class="fa fa-lock"></i> Coming Soon</span>
          </div>
        </div>

        <!-- Row 3 -->
        <div class="pdf-row locked">
          <div class="pdf-num">3</div>
          <div class="pdf-title-cell">
            <div class="pdf-title">History of SETC (State Express Transport Corporation)</div>
            <div class="pdf-meta"><i class="fa fa-clock me-1"></i>Long-distance express services</div>
          </div>
          <div>
            <span class="pdf-coming-badge"><i class="fa fa-lock"></i> Coming Soon</span>
          </div>
        </div>

        <!-- Row 4 -->
        <div class="pdf-row locked">
          <div class="pdf-num">4</div>
          <div class="pdf-title-cell">
            <div class="pdf-title">Training for Heavy Vehicle Driving License</div>
            <div class="pdf-meta"><i class="fa fa-clock me-1"></i>Driver training & licensing guide</div>
          </div>
          <div>
            <span class="pdf-coming-badge"><i class="fa fa-lock"></i> Coming Soon</span>
          </div>
        </div>

        <!-- Row 5 -->
        <div class="pdf-row locked">
          <div class="pdf-num">5</div>
          <div class="pdf-title-cell">
            <div class="pdf-title">Annual Performance Report 2025–26</div>
            <div class="pdf-meta"><i class="fa fa-clock me-1"></i>Revenue · Ridership · Fleet utilisation</div>
          </div>
          <div>
            <span class="pdf-coming-badge"><i class="fa fa-lock"></i> Coming Soon</span>
          </div>
        </div>

        <!-- Row 6 -->
        <div class="pdf-row locked">
          <div class="pdf-num">6</div>
          <div class="pdf-title-cell">
            <div class="pdf-title">Passenger Charter & Rights</div>
            <div class="pdf-meta"><i class="fa fa-clock me-1"></i>Passenger rights & grievance redressal</div>
          </div>
          <div>
            <span class="pdf-coming-badge"><i class="fa fa-lock"></i> Coming Soon</span>
          </div>
        </div>

      </div><!-- /pdf card -->

      <!-- Depots -->
      <div class="content-card mb-4">
        <div class="content-card-body">
          <div class="section-head">
            <div class="section-head-icon"><i class="fa fa-building"></i></div>
            <div>
              <h2>Our 7 Depots</h2>
              <p>Serving every corner of Tirunelveli District</p>
            </div>
          </div>
          <div class="depot-grid">
            <?php
            $depots = [
              ['🏢', 'Thamirabarani Depot', 'Vannarpettai, Tirunelveli'],
              ['🚌', 'Bye-Pass Depot',      'Vannarpettai, Tirunelveli'],
              ['🏛️', 'Kattabomman Nagar Depot', 'KTC Nagar, Tirunelveli'],
              ['🌿', 'Cheranmahadevi Depot','Cheranmahadevi'],
              ['🌄', 'Valliyoor Depot',     'Valliyoor'],
              ['🌊', 'Thisayanvilai Depot', 'Thisayanvilai'],
              ['⛰️', 'Papanasam Depot',    'Papanasam'],
            ];
            foreach ($depots as $d): ?>
            <div class="depot-card">
              <div class="depot-badge"><?= $d[0] ?></div>
              <h4><?= htmlspecialchars($d[1]) ?></h4>
              <p><i class="fa fa-location-dot me-1" style="color:#FF671F"></i><?= htmlspecialchars($d[2]) ?></p>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    </div><!-- /col-lg-8 -->

    <!-- ── Right Sidebar ── -->
    <div class="col-lg-4 about-sidebar">

      <!-- Quick Links -->
      <div class="sidebar-widget">
        <div class="sw-head"><i class="fa fa-bolt me-2"></i>Quick Links</div>
        <div class="sw-body">
          <a href="<?= APP_URL ?>/passenger/search_bus.php" class="quick-link"><i class="fa fa-search"></i> Search Buses</a>
          <a href="<?= APP_URL ?>/passenger/bus_pass.php"   class="quick-link"><i class="fa fa-id-card"></i> Apply Bus Pass</a>
          <a href="<?= APP_URL ?>/passenger/my_tickets.php" class="quick-link"><i class="fa fa-ticket"></i> My Tickets</a>
          <a href="<?= APP_URL ?>/passenger/live_tracking.php" class="quick-link"><i class="fa fa-location-dot"></i> Live Bus Tracking</a>
          <a href="<?= APP_URL ?>/passenger/complaints.php" class="quick-link"><i class="fa fa-comment-dots"></i> Register Complaint</a>
          <a href="<?= APP_URL ?>/auth/register.php"        class="quick-link"><i class="fa fa-user-plus"></i> Create Account</a>
        </div>
      </div>

      <!-- Contact Info -->
      <div class="sidebar-widget">
        <div class="sw-head"><i class="fa fa-phone me-2"></i>Contact Information</div>
        <div class="sw-body">
          <div class="contact-item">
            <i class="fa fa-building"></i>
            <div>
              <div class="ci-label">Head Office</div>
              <div class="ci-val">TNSTC Tirunelveli<br><small class="text-muted">Tirunelveli – 627 002</small></div>
            </div>
          </div>
          <div class="contact-item">
            <i class="fa fa-phone"></i>
            <div>
              <div class="ci-label">Toll Free</div>
              <div class="ci-val">080 66006572<br>9513948001</div>
            </div>
          </div>
          <div class="contact-item">
            <i class="fab fa-whatsapp"></i>
            <div>
              <div class="ci-label">WhatsApp</div>
              <div class="ci-val">9445014448</div>
            </div>
          </div>
          <div class="contact-item">
            <i class="fa fa-envelope"></i>
            <div>
              <div class="ci-label">Email</div>
              <div class="ci-val"><a href="mailto:commercial@tnstc.org" style="color:#1a3a6b">commercial@tnstc.org</a></div>
            </div>
          </div>
          <div class="contact-item">
            <i class="fa fa-clock"></i>
            <div>
              <div class="ci-label">Office Hours</div>
              <div class="ci-val">Mon – Sat: 9 AM – 5 PM</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Leadership -->
      <div class="sidebar-widget">
        <div class="sw-head"><i class="fa fa-users me-2"></i>Leadership</div>
        <div class="sw-body">
          <div class="row g-3">
            <div class="col-6">
              <div class="leader-card">
                <img src="<?= APP_URL ?>/assets/images/cm_profile.jpg" alt="CM" class="leader-avatar">
                <div class="leader-name">Thiru. M.K. Stalin</div>
                <div class="leader-role">Chief Minister, Tamil Nadu</div>
              </div>
            </div>
            <div class="col-6">
              <div class="leader-card">
                <div class="leader-initial">MD</div>
                <div class="leader-name">Managing Director</div>
                <div class="leader-role">TNSTC Tirunelveli Ltd.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Achievements -->
      <div class="sidebar-widget">
        <div class="sw-head"><i class="fa fa-trophy me-2"></i>Achievements</div>
        <div class="sw-body">
          <div class="contact-item">
            <i class="fa fa-award" style="color:#f59e0b"></i>
            <div>
              <div class="ci-label">2024</div>
              <div class="ci-val">Best Regional Transport Award – TN Govt</div>
            </div>
          </div>
          <div class="contact-item">
            <i class="fa fa-leaf" style="color:#10b981"></i>
            <div>
              <div class="ci-label">2023</div>
              <div class="ci-val">BS-VI Fleet Transition Complete</div>
            </div>
          </div>
          <div class="contact-item">
            <i class="fa fa-mobile-screen" style="color:#3b82f6"></i>
            <div>
              <div class="ci-label">2022</div>
              <div class="ci-val">Smart Ticketing System Launched</div>
            </div>
          </div>
          <div class="contact-item">
            <i class="fa fa-shield-halved" style="color:#8b5cf6"></i>
            <div>
              <div class="ci-label">2021</div>
              <div class="ci-val">Zero Fatal Accident Year</div>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /sidebar -->
  </div><!-- /row -->
</div><!-- /container -->

<!-- ═══════════════════════════════════════════
     CONTACT FOOTER + MAIN FOOTER
══════════════════════════════════════════════ -->
<?php include __DIR__ . '/includes/contact_footer.php'; ?>

<footer style="background:#0f2d5e;color:rgba(255,255,255,.65);padding:28px 0;">
  <div class="container text-center">
    <div style="font-weight:700;color:#fff;font-size:15px;margin-bottom:4px">TNSTC Smart Bus System</div>
    <div style="font-size:12.5px">Tirunelveli District · Tamil Nadu State Transport Corporation</div>
    <div style="margin-top:12px;font-size:11.5px;opacity:.5">
      © <?= date('Y') ?> TNSTC Tirunelveli. All Rights Reserved. | Government of Tamil Nadu
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
