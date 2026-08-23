<?php
require_once __DIR__ . '/../config/db.php';
startAppSession();

// Already logged in as passenger → redirect to passenger dashboard or requested redirect URL
if (isLoggedIn()) {
    if ($_SESSION['role'] === 'passenger') {
        $redir = $_GET['redirect'] ?? (APP_URL . '/passenger/dashboard.php');
        header('Location: ' . $redir);
        exit;
    }
}

$err = $_GET['err'] ?? '';
$redirect = $_GET['redirect'] ?? '';
$pageTitle = 'Passenger Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Passenger Login | TNSTC Tirunelveli</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="<?= APP_URL ?>/assets/css/tnstc.css" rel="stylesheet">
  <style>
    body.passenger-auth-page {
      min-height: 100vh;
      background: url('<?= APP_URL ?>/assets/images/tirunelveli_bus_stand.jpg') no-repeat center center;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      position: relative;
      z-index: 1;
    }
    body.passenger-auth-page::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(15, 23, 42, 0.55);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      z-index: -1;
    }
    .passenger-card {
      width: 100%;
      max-width: 440px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-radius: 20px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.4);
      border: 1px solid rgba(255, 255, 255, 0.4);
      overflow: hidden;
    }
    .passenger-card-header {
      background: linear-gradient(135deg, #124d2b 0%, #1a6b3c 100%);
      color: #ffffff;
      padding: 30px 24px 24px;
      text-align: center;
    }
    .passenger-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(255, 255, 255, 0.15);
      color: #fde68a;
      border: 1px solid rgba(255, 255, 255, 0.25);
      padding: 4px 14px;
      border-radius: 20px;
      font-size: 11.5px;
      font-weight: 700;
      letter-spacing: 0.5px;
      margin-bottom: 12px;
      backdrop-filter: blur(4px);
    }
    .passenger-title {
      font-size: 22px;
      font-weight: 800;
      margin: 0;
      color: #ffffff;
    }
    .passenger-sub {
      font-size: 13px;
      color: rgba(255, 255, 255, 0.85);
      margin-top: 4px;
    }
    .passenger-card-body {
      padding: 28px 24px;
    }
    .btn-passenger-login {
      background: #1a6b3c;
      color: #ffffff;
      font-weight: 700;
      height: 46px;
      border-radius: 10px;
      border: none;
      transition: all 0.2s;
    }
    .btn-passenger-login:hover {
      background: #124d2b;
      color: #ffffff;
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(26, 107, 60, 0.35);
    }
  </style>
</head>
<body class="passenger-auth-page">

<div class="passenger-card animate-fade">
  <!-- Header -->
  <div class="passenger-card-header">
    <div class="passenger-badge">
      🧑‍💼 Passenger Portal
    </div>
    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
      <div class="emblem-brand-container">
        <img src="<?= APP_URL ?>/assets/images/cm_profile.jpg" alt="CM Profile" class="emblem-profile-bg" title="Chief Minister Office">
        <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Tamil Nadu Government Emblem" class="emblem-logo-main">
      </div>
    </div>
    <h1 class="passenger-title">TNSTC Passenger Login</h1>
    <div class="passenger-sub">Tirunelveli District Smart Bus Service</div>
  </div>

  <!-- Body -->
  <div class="passenger-card-body">
    <?php if ($err === 'unauthorized'): ?>
      <div class="flash-banner flash-danger mb-3"><i class="fa fa-lock"></i> Please login to access passenger services.</div>
    <?php endif; ?>
    <?php if ($err === 'invalid'): ?>
      <div class="flash-banner flash-danger mb-3"><i class="fa fa-times-circle"></i> Invalid email or password. Please try again.</div>
    <?php endif; ?>
    <?php if ($err === 'inactive'): ?>
      <div class="flash-banner flash-warning mb-3"><i class="fa fa-clock"></i> Account pending email verification.</div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/auth/login_process.php" id="passengerLoginForm">
      <input type="hidden" name="role_hint" value="passenger">
      <?php if (!empty($redirect)): ?>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <?php endif; ?>

      <div class="form-group mb-3">
        <label class="form-label" for="email" style="font-size:12.5px;font-weight:700;color:#334155">
          <i class="fa fa-envelope me-1" style="color:#1a6b3c"></i> Email Address
        </label>
        <input type="email" id="email" name="email" class="form-control-custom" placeholder="you@example.com" required autocomplete="username">
      </div>

      <div class="form-group mb-4">
        <label class="form-label" for="password" style="font-size:12.5px;font-weight:700;color:#334155">
          <i class="fa fa-lock me-1" style="color:#1a6b3c"></i> Password
        </label>
        <div style="position:relative">
          <input type="password" id="password" name="password" class="form-control-custom" placeholder="Enter password" required autocomplete="current-password">
          <button type="button" onclick="togglePwd()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:none;background:none;color:#6c757d;cursor:pointer">
            <i class="fa fa-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-passenger-login w-100 d-flex align-items-center justify-content-center gap-2">
        <i class="fa fa-right-to-bracket"></i> Passenger Login
      </button>
    </form>

    <div class="text-center mt-3" style="font-size:13px;color:#6c757d">
      <a href="<?= APP_URL ?>/auth/forgot_password.php" style="color:#1a6b3c">Forgot Password?</a>
    </div>

    <hr class="my-3">
    <div class="text-center" style="font-size:13.5px">
      New passenger? <a href="<?= APP_URL ?>/auth/register.php" style="color:#1a6b3c;font-weight:700">Create Account →</a>
    </div>

    <div class="text-center mt-3" style="font-size:12.5px">
      <a href="<?= APP_URL ?>/" style="color:#64748b;text-decoration:none">
        <i class="fa fa-arrow-left me-1"></i> Return to Home
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
