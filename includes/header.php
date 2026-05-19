<?php
// includes/header.php
// Usage: include __DIR__.'/../includes/header.php';
// Set $pageTitle before including.
if (!defined('APP_NAME')) require_once __DIR__ . '/../config/db.php';
$flash = getFlash();
$pageTitle = $pageTitle ?? 'TNSTC Smart Bus System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="TNSTC Smart Bus Management System – Tirunelveli District. Book tickets, track buses, apply passes online.">
  <title><?= htmlspecialchars($pageTitle) ?> | TNSTC Tirunelveli</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <!-- TNSTC Design System -->
  <link href="<?= APP_URL ?>/assets/css/tnstc.css" rel="stylesheet">

  <?php if (!empty($extraHead)) echo $extraHead; ?>
</head>
<body>

<?php if (!empty($flash['msg'])): ?>
  <div class="flash-banner flash-<?= htmlspecialchars($flash['type']) ?>" style="position:fixed;top:12px;right:16px;z-index:9999;min-width:300px;max-width:480px">
    <i class="fa fa-<?= $flash['type']==='success'?'check-circle':($flash['type']==='danger'?'times-circle':'info-circle') ?>"></i>
    <?= htmlspecialchars($flash['msg']) ?>
  </div>
<?php endif; ?>
