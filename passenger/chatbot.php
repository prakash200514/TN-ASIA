<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$db = getDB();

// Rich database knowledge base for AI Assistant
$routes    = $db->query("SELECT route_id, route_number, source, destination, distance, estimated_time FROM routes ORDER BY route_number")->fetchAll();
$schedules = $db->query("SELECT s.schedule_id, s.route_id, s.departure_time, s.arrival_time, r.route_number, r.source, r.destination, b.bus_number, b.bus_type FROM schedules s JOIN routes r ON r.route_id=s.route_id JOIN buses b ON b.bus_id=s.bus_id ORDER BY s.departure_time")->fetchAll();
$depots    = $db->query("SELECT depot_name, location, district FROM depots ORDER BY depot_name")->fetchAll();

$pageTitle = 'AI Route & Transport Assistant';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>
<div class="main-content">

  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title"><i class="fa fa-robot me-2" style="color:var(--primary)"></i>AI Route & Transport Assistant</div>
  </div>

  <div class="page-content">

  <div class="chatbot-wrap">
    <div class="chatbot-header d-flex align-items-center gap-3">
      <div class="chatbot-avatar" style="width:42px;height:42px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px">🤖</div>
      <div>
        <div style="font-weight:800;font-size:15px;color:#0f172a">TNSTC AI Smart Assistant</div>
        <div style="font-size:12px;color:#64748b">Tirunelveli District · Powered by TNSTC Live Data</div>
      </div>
      <span class="ms-auto badge bg-success px-2 py-1" style="font-size:11px"><i class="fa fa-circle me-1" style="font-size:8px"></i> AI Active</span>
    </div>

    <div class="chatbot-messages" id="chatMessages"></div>

    <div class="chat-input-row">
      <input type="text" id="chatInput" placeholder="Ask me anything: routes, bus timings, fares, passes, tracking..." autocomplete="off">
      <button class="chat-send-btn" id="chatSend"><i class="fa fa-paper-plane"></i></button>
    </div>
  </div><!-- /.chatbot-wrap -->

  <!-- Quick Info & Contact Help Section -->
  <div class="card border-0 shadow-sm p-4 mt-4" style="border-radius:16px;background:#ffffff">
    <h6 style="font-weight:800;color:#0f172a;margin-bottom:14px;font-size:14px"><i class="fa fa-headset me-2 text-primary"></i>TNSTC Contact Us & Support Enquiries</h6>
    <div class="table-responsive">
      <table class="table table-sm table-borderless align-middle mb-0" style="font-size:12.5px">
        <tbody>
          <tr>
            <td style="font-weight:700;color:#334155"><i class="fa fa-phone me-2 text-success"></i>Tirunelveli Control Room:</td>
            <td><a href="tel:04622579801" class="text-decoration-none fw-bold" style="color:#2563eb">0462-2579801</a></td>
            <td style="font-weight:700;color:#334155"><i class="fa fa-envelope me-2 text-danger"></i>Support Email:</td>
            <td><a href="mailto:contact@tnstctirunelveli.in" class="text-decoration-none fw-bold" style="color:#2563eb">contact@tnstctirunelveli.in</a></td>
          </tr>
          <tr>
            <td style="font-weight:700;color:#334155"><i class="fa fa-clock me-2 text-warning"></i>Working Hours:</td>
            <td>24x7 Operations Support</td>
            <td style="font-weight:700;color:#334155"><i class="fa fa-building-columns me-2 text-purple"></i>Central Depot:</td>
            <td>Thamirabarani, Vannarpettai</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  </div><!-- /.page-content -->
</div><!-- /.main-content -->
</div><!-- /.app-layout -->

<script>
window.TNSTC_ROUTES    = <?= json_encode($routes) ?>;
window.TNSTC_SCHEDULES = <?= json_encode($schedules) ?>;
window.TNSTC_DEPOTS    = <?= json_encode($depots) ?>;
</script>
<script src="<?= APP_URL ?>/assets/js/chatbot.js"></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
