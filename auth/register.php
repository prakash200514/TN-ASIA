<?php
require_once __DIR__ . '/../config/db.php';
startAppSession();
if (isLoggedIn()) { header('Location: '.APP_URL.'/passenger/dashboard.php'); exit; }

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $cpass = $_POST['confirm_password'] ?? '';

    if (!$name || strlen($name) < 3) $errors[] = 'Full name must be at least 3 characters.';
    if (!preg_match('/^[6-9]\d{9}$/', $phone)) $errors[] = 'Enter a valid 10-digit Indian mobile number.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($pass) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($pass !== $cpass) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $db = getDB();
        $chk = $db->prepare('SELECT user_id FROM users WHERE email = ?');
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $errors[] = 'This email is already registered. Please login.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $otp  = generateOTP();
            $exp  = date('Y-m-d H:i:s', time() + 600); // 10 min

            $ins = $db->prepare('INSERT INTO users (name,email,phone,password,role,status,otp,otp_expiry) VALUES (?,?,?,?,?,?,?,?)');
            $ins->execute([$name,$email,$phone,$hash,'passenger','pending',$otp,$exp]);

            // Store for OTP page
            $_SESSION['reg_email'] = $email;
            $_SESSION['dev_otp']   = $otp; // shown in dev mode

            // TODO: send real email via SMTP when configured
            redirectWithMessage(APP_URL.'/auth/otp_verify.php', 'success', "OTP sent to $email (Dev mode: $otp)");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register | TNSTC Tirunelveli</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="<?= APP_URL ?>/assets/css/tnstc.css" rel="stylesheet">
</head>
<body class="auth-page">
<div class="auth-card animate-fade" style="max-width:500px">
  <div class="auth-card-header">
    <div class="auth-logo" style="width:auto;height:auto;background:transparent;">
      <div class="emblem-brand-container">
        <img src="<?= APP_URL ?>/assets/images/cm_profile.jpg" alt="CM Profile" class="emblem-profile-bg" title="Chief Minister Office">
        <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Tamil Nadu Government Emblem" class="emblem-logo-main">
      </div>
    </div>
    <h1>Create Passenger Account</h1>
    <p>TNSTC Tirunelveli – Passenger Self-Registration</p>
  </div>
  <div class="auth-card-body">
    <?php foreach ($errors as $e): ?>
      <div class="flash-banner flash-danger mb-2"><i class="fa fa-times-circle"></i> <?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control-custom" placeholder="Arun Kumar" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
      </div>
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="tel" name="phone" class="form-control-custom" placeholder="9876543210" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
          </div>
        </div>
        <div class="col-6">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control-custom" placeholder="you@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control-custom" placeholder="Min 8 chars" required>
          </div>
        </div>
        <div class="col-6">
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control-custom" placeholder="Repeat password" required>
          </div>
        </div>
      </div>
      <button type="submit" class="btn-primary-custom w-100 justify-content-center">
        <i class="fa fa-user-plus"></i> Register & Send OTP
      </button>
    </form>
    <hr class="my-3">
    <div class="text-center" style="font-size:13px">
      Already have an account? <a href="<?= APP_URL ?>/passenger/login.php" style="color:#1a6b3c;font-weight:600">Login →</a>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
