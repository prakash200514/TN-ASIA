<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$db     = getDB();
$routes = $db->query("SELECT route_number,source,destination,distance,estimated_time FROM routes ORDER BY route_number")->fetchAll();
$pageTitle = 'AI Route Assistant';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div class="main-content">

  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title"><i class="fa fa-robot me-2" style="color:var(--primary)"></i>AI Route Assistant</div>
  </div>

  <div class="page-content">

  <div class="chatbot-wrap">
    <div class="chatbot-header">
      <div class="chatbot-avatar">🤖</div>
      <div>
        <div style="font-weight:700">TNSTC Route Assistant</div>
        <div style="font-size:12px;opacity:.75">Tirunelveli District · Always Online</div>
      </div>
      <span class="ms-auto badge bg-success" style="font-size:11px">● Live</span>
    </div>

    <div class="chatbot-messages" id="chatMessages"></div>

    <div class="chat-input-row">
      <input type="text" id="chatInput" placeholder="Ask me about routes, bus timings, passes…" autocomplete="off">
      <button class="chat-send-btn" id="chatSend"><i class="fa fa-paper-plane"></i></button>
    </div>
  </div><!-- /.chatbot-wrap -->

  </div><!-- /.page-content -->
</div><!-- /.main-content -->
</div><!-- /.app-layout -->

<script>
window.TNSTC_ROUTES = <?= json_encode($routes) ?>;
</script>
<script src="<?= APP_URL ?>/assets/js/chatbot.js"></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
