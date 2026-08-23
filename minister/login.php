<?php
require_once __DIR__ . '/../config/db.php';
startAppSession();

// If already logged in as minister, go straight to dashboard
if (isLoggedIn()) {
    if ($_SESSION['role'] === 'minister') {
        header('Location: ' . APP_URL . '/minister/dashboard.php');
        exit;
    }
}

$err = $_GET['err'] ?? '';
$pageTitle = 'Minister Login Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Minister Executive Portal | TNSTC Tirunelveli</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="<?= APP_URL ?>/assets/css/tnstc.css" rel="stylesheet">
  <style>
    body.minister-auth-page {
      min-height: 100vh;
      background: url('<?= APP_URL ?>/assets/images/thalamai_seyalagam.jpg') no-repeat center center;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      position: relative;
      z-index: 1;
      font-family: system-ui, -apple-system, sans-serif;
    }
    body.minister-auth-page::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(10, 25, 50, 0.65);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      z-index: -1;
    }
    .minister-card {
      width: 100%;
      max-width: 440px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.4);
      overflow: hidden;
    }
    .minister-card-header {
      background: linear-gradient(135deg, #0f2b48 0%, #1a365d 100%);
      color: #ffffff;
      padding: 32px 24px 24px;
      text-align: center;
      position: relative;
    }
    .minister-card-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #d97706, #f59e0b, #d97706);
    }
    .badge-security {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(245, 158, 11, 0.15);
      color: #fbbf24;
      border: 1px solid rgba(245, 158, 11, 0.3);
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 14px;
    }
    .minister-title {
      font-size: 20px;
      font-weight: 800;
      margin: 0;
      color: #ffffff;
      letter-spacing: -0.02em;
    }
    .minister-sub {
      font-size: 12.5px;
      color: rgba(255, 255, 255, 0.75);
      margin-top: 4px;
    }
    .minister-card-body {
      padding: 28px 24px;
    }
    .btn-minister-login {
      background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
      color: #ffffff;
      font-weight: 700;
      height: 46px;
      border-radius: 10px;
      border: none;
      transition: all 0.2s;
    }
    .btn-minister-login:hover {
      background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
      color: #ffffff;
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(30, 58, 138, 0.35);
    }
    .security-notice {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 12px;
      font-size: 11.5px;
      color: #64748b;
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>
<body class="minister-auth-page">

<div class="minister-card animate-fade">
  <!-- Header -->
  <div class="minister-card-header">
    <div class="badge-security">
      <i class="fa fa-shield-halved"></i> Executive Portal
    </div>
    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
      <div class="emblem-brand-container">
        <img src="<?= APP_URL ?>/assets/images/cm_profile.jpg" alt="CM Profile" class="emblem-profile-bg" title="Chief Minister Office">
        <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Tamil Nadu Government Emblem" class="emblem-logo-main">
      </div>
    </div>
    <h1 class="minister-title">TNSTC Minister Portal</h1>
    <div class="minister-sub">Government of Tamil Nadu — Transport Department</div>
  </div>

  <!-- Body -->
  <div class="minister-card-body">
    <?php if ($err === 'unauthorized'): ?>
      <div class="flash-banner flash-danger mb-3"><i class="fa fa-lock"></i> Minister authentication required to access executive dashboard.</div>
    <?php endif; ?>
    <?php if ($err === 'invalid'): ?>
      <div class="flash-banner flash-danger mb-3"><i class="fa fa-times-circle"></i> Invalid official email or password. Please try again.</div>
    <?php endif; ?>
    <?php if ($err === 'inactive'): ?>
      <div class="flash-banner flash-warning mb-3"><i class="fa fa-clock"></i> Account inactive or restricted.</div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/auth/login_process.php" id="ministerLoginForm">
      <input type="hidden" name="role_hint" value="minister">

      <div class="form-group mb-3">
        <label class="form-label" for="email" style="font-size:12.5px;font-weight:700;color:#334155">
          <i class="fa fa-user-shield me-1" style="color:#1e40af"></i> Official Email
        </label>
        <input type="email" id="email" name="email" class="form-control-custom" placeholder="minister@tnstc.tn.gov.in" required autocomplete="username">
      </div>

      <div class="form-group mb-4">
        <label class="form-label" for="password" style="font-size:12.5px;font-weight:700;color:#334155">
          <i class="fa fa-key me-1" style="color:#1e40af"></i> Executive Security Key
        </label>
        <div style="position:relative">
          <input type="password" id="password" name="password" class="form-control-custom" placeholder="Enter password" required autocomplete="current-password">
          <button type="button" onclick="togglePwd()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:none;background:none;color:#6c757d;cursor:pointer">
            <i class="fa fa-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-minister-login w-100 d-flex align-items-center justify-content-center gap-2">
        <i class="fa fa-right-to-bracket"></i> Secure Minister Login
      </button>
    </form>

    <div class="security-notice">
      <i class="fa fa-lock text-warning me-1"></i> Authorized Executive Personnel Only.<br>
      All access attempts are logged and monitored by TNSTC Secretariat.
    </div>

    <div class="text-center mt-3" style="font-size:12.5px">
      <a href="<?= APP_URL ?>/" style="color:#64748b;text-decoration:none">
        <i class="fa fa-arrow-left me-1"></i> Return to Main Website
      </a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function togglePwd() {
    const p = document.getElementById('password');
    const i = document.getElementById('eyeIcon');
    if (p.type === 'password') { p.type = 'text'; i.className = 'fa fa-eye-slash'; }
    else { p.type = 'password'; i.className = 'fa fa-eye'; }
  }
</script>
</body>
</html>
