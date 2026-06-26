<?php
require_once __DIR__ . '/../config/db.php';
startAppSession();
if (isLoggedIn()) { header('Location:'.APP_URL.'/passenger/dashboard.php'); exit; }
$email = $_SESSION['reg_email'] ?? '';
if (!$email) { header('Location:'.APP_URL.'/auth/register.php'); exit; }

$flash = getFlash();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    $db  = getDB();
    $stmt= $db->prepare('SELECT * FROM users WHERE email=? AND otp=? AND status=? LIMIT 1');
    $stmt->execute([$email,$otp,'pending']);
    $user = $stmt->fetch();
    if (!$user) {
        $error = 'Invalid OTP. Please check and try again.';
    } elseif (strtotime($user['otp_expiry']) < time()) {
        $error = 'OTP expired. Please register again.';
    } else {
        $db->prepare('UPDATE users SET status=?,otp=NULL,otp_expiry=NULL WHERE email=?')
           ->execute(['active',$email]);
        unset($_SESSION['reg_email'],$_SESSION['dev_otp']);
        redirectWithMessage(APP_URL.'/auth/login.php','success','Email verified! You can now login.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>OTP Verification | TNSTC</title>
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
    <h1>Email Verification</h1>
    <p>Enter the 6-digit OTP sent to <strong><?= htmlspecialchars($email) ?></strong></p>
  </div>
  <div class="auth-card-body">
    <?php if (!empty($flash['msg'])): ?>
      <div class="flash-banner flash-<?= $flash['type'] ?> mb-3"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="flash-banner flash-danger mb-3"><i class="fa fa-times-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Dev mode OTP hint -->
    <?php if (!empty($_SESSION['dev_otp'])): ?>
    <div class="flash-banner flash-info mb-3" style="flex-direction:column;align-items:flex-start">
      <strong>🔧 Dev Mode – OTP:</strong>
      <span style="font-size:22px;font-weight:700;letter-spacing:6px;color:#075985"><?= htmlspecialchars($_SESSION['dev_otp']) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group text-center">
        <label class="form-label">Enter 6-Digit OTP</label>
        <input type="text" name="otp" id="otpInput" class="form-control-custom text-center"
               maxlength="6" pattern="\d{6}" placeholder="• • • • • •"
               style="font-size:28px;font-weight:700;letter-spacing:8px;text-align:center" required autofocus>
      </div>
      <button type="submit" class="btn-primary-custom w-100 justify-content-center">
        <i class="fa fa-check-circle"></i> Verify OTP
      </button>
    </form>
    <div class="text-center mt-3" style="font-size:13px;color:#6c757d">
      Didn't receive OTP? <a href="<?= APP_URL ?>/auth/register.php" style="color:#1a6b3c">Resend</a>
    </div>
  </div>
</div>
<script>
  document.getElementById('otpInput').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g,'');
  });
</script>
</body>
</html>
