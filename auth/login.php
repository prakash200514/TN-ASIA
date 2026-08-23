<?php
require_once __DIR__ . '/../config/db.php';
startAppSession();
// Already logged in → redirect to appropriate dashboard
if (isLoggedIn()) {
    $role = $_SESSION['role'];
    $map = ['passenger'=>'/passenger/dashboard.php','driver'=>'/driver/dashboard.php',
            'conductor'=>'/driver/dashboard.php','depot_manager'=>'/depot_manager/dashboard.php',
            'minister'=>'/minister/dashboard.php','admin'=>'/admin/dashboard.php'];
    header('Location: ' . APP_URL . ($map[$role] ?? '/'));
    exit;
}
$err = $_GET['err'] ?? '';
$selectedRole = $_GET['role'] ?? 'passenger';
if ($selectedRole === 'minister') {
    header('Location: ' . APP_URL . '/minister/login.php');
    exit;
}
$allowedRoles = ['passenger', 'driver', 'depot_manager', 'admin'];
if (!in_array($selectedRole, $allowedRoles, true)) {
    $selectedRole = 'passenger';
}
$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login | TNSTC Tirunelveli</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="<?= APP_URL ?>/assets/css/tnstc.css" rel="stylesheet">
</head>
<body class="auth-page">

<div class="auth-card animate-fade">
  <!-- Header -->
  <div class="auth-card-header">
    <div class="auth-logo" style="width:auto;height:auto;background:transparent;">
      <div class="emblem-brand-container">
        <img src="<?= APP_URL ?>/assets/images/cm_profile.jpg" alt="CM Profile" class="emblem-profile-bg" title="Chief Minister Office">
        <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Tamil Nadu Government Emblem" class="emblem-logo-main">
      </div>
    </div>
    <h1>TNSTC Smart Bus System</h1>
    <p>Tirunelveli District — Secure Login</p>
  </div>

  <!-- Body -->
  <div class="auth-card-body">
    <?php if ($err === 'unauthorized'): ?>
      <div class="flash-banner flash-danger mb-3"><i class="fa fa-lock"></i> You are not authorized to access that page.</div>
    <?php endif; ?>
    <?php if ($err === 'invalid'): ?>
      <div class="flash-banner flash-danger mb-3"><i class="fa fa-times-circle"></i> Invalid email or password. Please try again.</div>
    <?php endif; ?>
    <?php if ($err === 'inactive'): ?>
      <div class="flash-banner flash-warning mb-3"><i class="fa fa-clock"></i> Your account is pending email verification.</div>
    <?php endif; ?>

    <!-- Role Tabs -->
    <div class="auth-tabs mb-3" id="roleTabs">
      <button class="auth-tab <?= $selectedRole === 'passenger' ? 'active' : '' ?>" data-role="passenger">Passenger</button>
      <button class="auth-tab <?= $selectedRole === 'driver' ? 'active' : '' ?>" data-role="driver">Driver/Conductor</button>
      <button class="auth-tab <?= $selectedRole === 'depot_manager' ? 'active' : '' ?>" data-role="depot_manager">Depot Mgr</button>
      <button class="auth-tab <?= $selectedRole === 'admin' ? 'active' : '' ?>" data-role="admin">Admin</button>
    </div>

    <form method="POST" action="<?= APP_URL ?>/auth/login_process.php" id="loginForm">
      <input type="hidden" name="role_hint" id="roleHint" value="<?= htmlspecialchars($selectedRole) ?>">

      <div class="form-group">
        <label class="form-label" for="email"><i class="fa fa-envelope me-1"></i>Email Address</label>
        <input type="email" id="email" name="email" class="form-control-custom" placeholder="you@example.com" required autocomplete="username">
      </div>

      <div class="form-group">
        <label class="form-label" for="password"><i class="fa fa-lock me-1"></i>Password</label>
        <div style="position:relative">
          <input type="password" id="password" name="password" class="form-control-custom" placeholder="Enter password" required autocomplete="current-password">
          <button type="button" onclick="togglePwd()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:none;background:none;color:#6c757d;cursor:pointer">
            <i class="fa fa-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary-custom w-100 justify-content-center mt-1" id="loginBtn">
        <i class="fa fa-right-to-bracket"></i> Login
      </button>
    </form>

    <div class="text-center mt-3" style="font-size:13px;color:#6c757d">
      <a href="<?= APP_URL ?>/auth/forgot_password.php" style="color:#1a6b3c">Forgot Password?</a>
    </div>

    <hr class="my-3">
    <div class="text-center" style="font-size:13px">
      New passenger? <a href="<?= APP_URL ?>/auth/register.php" style="color:#1a6b3c;font-weight:600">Create Account →</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('.auth-tab').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.auth-tab').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById('roleHint').value = btn.dataset.role;
    });
  });
  function togglePwd() {
    const p = document.getElementById('password');
    const i = document.getElementById('eyeIcon');
    if (p.type === 'password') { p.type = 'text'; i.className = 'fa fa-eye-slash'; }
    else { p.type = 'password'; i.className = 'fa fa-eye'; }
  }
</script>
</body>
</html>
