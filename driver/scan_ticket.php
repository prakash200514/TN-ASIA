<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('driver_conductor');
$pageTitle = 'Scan QR Ticket';
include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar_driver.php'; ?>
<div class="main-content">
  <div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
    <div class="topbar-title">📷 QR Ticket Scanner</div>
  </div>
  <div class="page-content">
    <div class="row g-4 justify-content-center">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header-custom"><i class="fa fa-qrcode"></i> Scan Passenger QR Ticket</div>
          <div class="card-body-custom text-center">
            <div id="scannerArea" style="width:100%;max-width:320px;margin:0 auto 16px;border-radius:12px;overflow:hidden;border:3px solid #1a6b3c;position:relative">
              <video id="qrVideo" style="width:100%;display:block" autoplay muted playsinline></video>
              <canvas id="qrCanvas" style="display:none"></canvas>
              <div id="scanOverlay" style="position:absolute;inset:0;border:3px solid rgba(26,107,60,.4);pointer-events:none">
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:160px;height:160px;border:3px solid #1a6b3c;border-radius:8px;box-shadow:0 0 0 2000px rgba(0,0,0,.3)"></div>
              </div>
            </div>
            <button id="startScan" class="btn-primary-custom justify-content-center mb-3" onclick="startScanner()">
              <i class="fa fa-camera"></i> Start Camera Scan
            </button>
            <hr class="divider-dashed">
            <p style="font-size:13px;color:#6c757d">Or enter QR code manually:</p>
            <div class="d-flex gap-2">
              <input type="text" id="manualQR" class="form-control-custom flex-1" placeholder="TNSTC-TKT-...">
              <button class="btn-accent-custom btn-sm-custom" onclick="validateManual()"><i class="fa fa-check"></i> Validate</button>
            </div>
          </div>
        </div>

        <!-- Result -->
        <div id="scanResult" class="card mt-4 d-none">
          <div class="card-body-custom" id="scanResultBody"></div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let scanning = false;
let animFrame;

async function startScanner() {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
    const video = document.getElementById('qrVideo');
    video.srcObject = stream;
    scanning = true;
    document.getElementById('startScan').innerHTML = '<i class="fa fa-stop"></i> Stop Camera';
    document.getElementById('startScan').onclick = stopScanner;
    tick();
  } catch(e) {
    alert('Camera access denied or not available on this device.');
  }
}

function stopScanner() {
  scanning = false;
  cancelAnimationFrame(animFrame);
  const video = document.getElementById('qrVideo');
  if (video.srcObject) video.srcObject.getTracks().forEach(t => t.stop());
  document.getElementById('startScan').innerHTML = '<i class="fa fa-camera"></i> Start Camera Scan';
  document.getElementById('startScan').onclick = startScanner;
}

function tick() {
  const video  = document.getElementById('qrVideo');
  const canvas = document.getElementById('qrCanvas');
  if (video.readyState === video.HAVE_ENOUGH_DATA) {
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video,0,0,canvas.width,canvas.height);
    const img = ctx.getImageData(0,0,canvas.width,canvas.height);
    const code = jsQR(img.data, img.width, img.height, { inversionAttempts: 'dontInvert' });
    if (code) { stopScanner(); validateQR(code.data); return; }
  }
  if (scanning) animFrame = requestAnimationFrame(tick);
}

async function validateManual() {
  const qr = document.getElementById('manualQR').value.trim();
  if (qr) validateQR(qr);
}

async function validateQR(qrCode) {
  const resultDiv  = document.getElementById('scanResult');
  const resultBody = document.getElementById('scanResultBody');
  resultDiv.classList.remove('d-none');
  resultBody.innerHTML = '<div class="text-center p-3"><div class="spinner-border text-success"></div><p class="mt-2">Validating…</p></div>';
  try {
    const res = await apiPost('/TNSTC/api/validate_ticket.php', { qr_code: qrCode });
    if (res.valid) {
      resultBody.innerHTML = `
        <div class="text-center mb-3">
          <div style="width:60px;height:60px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto">✅</div>
          <h4 style="color:#166534;margin-top:10px">Valid Ticket</h4>
        </div>
        <div class="ticket-row"><span class="label">Passenger</span><span class="value">${res.ticket.passenger}</span></div>
        <div class="ticket-row"><span class="label">Seat</span><span class="value">Seat ${res.ticket.seat}</span></div>
        <div class="ticket-row"><span class="label">From</span><span class="value">${res.ticket.from}</span></div>
        <div class="ticket-row"><span class="label">To</span><span class="value">${res.ticket.to}</span></div>
        <div class="ticket-row"><span class="label">Fare</span><span class="value">${res.ticket.fare}</span></div>`;
    } else {
      resultBody.innerHTML = `
        <div class="text-center">
          <div style="width:60px;height:60px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto">❌</div>
          <h4 style="color:#991b1b;margin-top:10px">${res.message}</h4>
        </div>`;
    }
  } catch(e) {
    resultBody.innerHTML = `<div class="flash-banner flash-danger">Error: ${e.message}</div>`;
  }
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
