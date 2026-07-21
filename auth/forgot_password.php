<?php
<<<<<<<<<<
require_once __DIR__ . '/../config/db.php';
startAppSession();
$flash = getFlash();
$error = '';
$step  = 1; // 1=enter email, 2=enter new password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    if ($_POST['step'] == '1') {
        $email = trim($_POST['email'] ?? '');
        $stmt  = $db->prepare('SELECT * FROM users WHERE email=? AND status=? LIMIT 1');
        $stmt->execute([$email,'active']);
        $user = $stmt->fetch();
        if (!$user) {
            $error = 'No active account found with this email.';
        } else {
            $otp = generateOTP();
            $exp = date('Y-m-d H:i:s', time() + 600);
            $db->prepare('UPDATE users SET otp=?,otp_expiry=? WHERE email=?')->execute([$otp,$exp,$email]);
            $_SESSION['fp_email']  = $email;
            $_SESSION['fp_dev_otp']= $otp;
            $step = 2;
        }
    } elseif ($_POST['step'] == '2') {
        $email = $_SESSION['fp_email'] ?? '';
        $otp   = trim($_POST['otp'] ?? '');
        $np    = $_POST['new_password'] ?? '';
        $cp    = $_POST['confirm_password'] ?? '';
        if (strlen($np) < 8) { $error = 'Password must be at least 8 characters.'; $step = 2; }
        elseif ($np !== $cp) { $error = 'Passwords do not match.'; $step = 2; }
        else {
            $stmt = $db->prepare('SELECT * FROM users WHERE email=? AND otp=? LIMIT 1');
            $stmt->execute([$email,$otp]);
            $user = $stmt->fetch();
            if (!$user || strtotime($user['otp_expiry']) < time()) {
                $error = 'Invalid or expired OTP.'; $step = 2;
            } else {
                $db->prepare('UPDATE users SET password=?,otp=NULL,otp_expiry=NULL WHERE email=?')
                   ->execute([password_hash($np,PASSWORD_DEFAULT),$email]);
                unset($_SESSION['fp_email'],$_SESSION['fp_dev_otp']);
                redirectWithMessage(APP_URL.'/auth/login.php','success','Password reset successful. Please login.');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Forgot Password | TNSTC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="<?= APP_URL ?>/assets/css/tnstc.css" rel="stylesheet">
</head>
<body class="auth-page">
<div class="auth-card animate-fade">
  <div class="auth-card-header">
    <div class="auth-logo">
      <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="Tamil Nadu Government Emblem" style="width: 48px; height: 48px; object-fit: contain;">
    </div>
    <h1>Reset Password</h1>
    <p>TNSTC Tirunelveli – Account Recovery</p>
  </div>
  <div class="auth-card-body">
    <?php if ($error): ?>
      <div class="flash-banner flash-danger mb-3"><i class="fa fa-times-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($step === 1): ?>
    <form method="POST">
      <input type="hidden" name="step" value="1">
      <div class="form-group">
        <label class="form-label">Your Registered Email</label>
        <input type="email" name="email" class="form-control-custom" placeholder="you@gmail.com" required>
      </div>
      <button type="submit" class="btn-primary-custom w-100 justify-content-center">
        <i class="fa fa-paper-plane"></i> Send OTP
      </button>
    </form>
    <?php else: ?>
    <?php if (!empty($_SESSION['fp_dev_otp'])): ?>
    <div class="flash-banner flash-info mb-3">
      <strong>🔧 Dev OTP:</strong>&nbsp;
      <span style="font-size:20px;font-weight:700;letter-spacing:4px"><?= htmlspecialchars($_SESSION['fp_dev_otp']) ?></span>
    </div>
    <?php endif; ?>
    <form method="POST">
      <input type="hidden" name="step" value="2">
      <div class="form-group">
        <label class="form-label">Enter OTP</label>
        <input type="text" name="otp" class="form-control-custom" maxlength="6" placeholder="6-digit OTP" required>
      </div>
      <div class="form-group">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control-custom" placeholder="Min 8 chars" required>
      </div>
      <div class="form-group">
        <label class="form-label">Confirm New Password</label>
        <input type="password" name="confirm_password" class="form-control-custom" placeholder="Repeat password" required>
      </div>
      <button type="submit" class="btn-primary-custom w-100 justify-content-center">
        <i class="fa fa-lock"></i> Reset Password
      </button>
    </form>
    <?php endif; ?>
    <div class="text-center mt-3" style="font-size:13px">
      <a href="<?= APP_URL ?>/auth/login.php" style="color:#1a6b3c">← Back to Login</a>
    </div>
  </div>
</div>
</body>
</html>
