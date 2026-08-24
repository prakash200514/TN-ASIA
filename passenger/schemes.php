<?php
require_once __DIR__ . '/../config/db.php';
requireLogin('passenger');
$user = currentUser();
$db   = getDB();

$pageTitle = 'TN State Schemes & Concessions – Government of Tamil Nadu';
include __DIR__ . '/../includes/header.php';
?>

<div class="app-layout">
  <!-- Left Sidebar Navigation -->
  <?php include __DIR__ . '/../includes/sidebar_passenger.php'; ?>

  <!-- Main Content -->
  <div class="main-content">

    <!-- Topbar -->
    <div class="egov-topbar" style="background:#ffffff;border-bottom:1px solid #e2e8f0;padding:12px 24px;position:sticky;top:0;z-index:900;display:flex;align-items:center;justify-content:space-between">
      <div class="d-flex align-items-center gap-3">
        <button class="sidebar-toggle btn btn-sm btn-light d-lg-none" id="sidebarToggle"><i class="fa fa-bars"></i></button>
        <h5 class="m-0 fw-bold" style="color:#0f172a;font-size:16px"><i class="fa fa-hand-holding-heart text-purple me-2"></i>TN State Schemes & Welfare Concessions</h5>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
          <div style="width:36px;height:36px;background:linear-gradient(135deg,#0284c7,#0369a1);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px">
            <?= strtoupper(substr($user['name'],0,1)) ?>
          </div>
          <div class="d-none d-md-block">
            <div style="font-weight:700;font-size:13px;color:#0f172a;line-height:1.1"><?= htmlspecialchars($user['name']) ?></div>
            <div style="font-size:10.5px;color:#64748b">Citizen / Passenger</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Page Body Content -->
    <div class="page-content p-4" style="background:#f8fafc;flex:1">

      <!-- Hero Banner -->
      <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius:16px;background:linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);color:#fff">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2">
              <span class="badge bg-warning text-dark font-weight-bold" style="font-size:11px">Government of Tamil Nadu Welfare Schemes</span>
              <span class="badge bg-success" style="font-size:11px">Active Public Schemes</span>
            </div>
            <h2 style="font-weight:800;margin:0 0 8px;font-size:24px">TN Transport Welfare Schemes & Concessions</h2>
            <p style="font-size:13.5px;opacity:0.9;margin:0;max-width:640px">
              Explore 100% free travel schemes, student concessions, senior citizen travel benefits, differently abled passes, and official state government public welfare initiatives.
            </p>
          </div>
          <div class="col-lg-4 text-end d-none d-lg-block">
            <div style="font-size:48px;opacity:0.25">🏛️</div>
            <div style="font-weight:800;font-size:11px;letter-spacing:1px;color:#fde68a">TAMIL NADU STATE TRANSPORT</div>
            <div style="font-size:9px;opacity:0.75">TRUTH ALONE TRIUMPHS</div>
          </div>
        </div>
      </div>

      <!-- Filter Buttons -->
      <div class="d-flex flex-wrap gap-2 mb-4">
        <button class="btn btn-sm btn-primary fw-bold rounded-pill px-3 py-2 scheme-filter-btn active" onclick="filterSchemes('all', this)">All Schemes</button>
        <button class="btn btn-sm btn-white text-secondary fw-semibold rounded-pill px-3 py-2 scheme-filter-btn" onclick="filterSchemes('women', this)">👩 Women Free Travel</button>
        <button class="btn btn-sm btn-white text-secondary fw-semibold rounded-pill px-3 py-2 scheme-filter-btn" onclick="filterSchemes('student', this)">🎓 Student Passes</button>
        <button class="btn btn-sm btn-white text-secondary fw-semibold rounded-pill px-3 py-2 scheme-filter-btn" onclick="filterSchemes('senior', this)">👴 Senior Citizens</button>
        <button class="btn btn-sm btn-white text-secondary fw-semibold rounded-pill px-3 py-2 scheme-filter-btn" onclick="filterSchemes('disabled', this)">♿ Differently Abled</button>
        <button class="btn btn-sm btn-white text-secondary fw-semibold rounded-pill px-3 py-2 scheme-filter-btn" onclick="filterSchemes('special', this)">🎖️ Special Passes</button>
      </div>

      <!-- Schemes Grid -->
      <div class="row g-4 mb-4" id="schemesGrid">

        <!-- Scheme 1: Vidiyal Payanam (Women Free Travel) -->
        <div class="col-md-6 col-lg-4 scheme-card-item" data-category="women">
          <div class="card border-0 shadow-sm h-100 p-4" style="border-radius:16px;background:#ffffff">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div style="width:46px;height:46px;border-radius:12px;background:#fce7f3;color:#db2777;display:flex;align-items:center;justify-content:center;font-size:22px">
                <i class="fa fa-person-dress"></i>
              </div>
              <span class="badge bg-danger rounded-pill" style="font-size:10px">100% Free Travel</span>
            </div>
            <h5 style="font-weight:800;color:#0f172a;margin:0 0 4px;font-size:16px">Vidiyal Payanam Scheme</h5>
            <div style="font-size:12px;color:#db2777;font-weight:700" class="mb-2">மகளிர் இலவச பேருந்து பயணத் திட்டம்</div>
            <p style="font-size:12.5px;color:#64748b;line-height:1.5" class="flex-grow-1">
              Provides 100% free travel for all women, trans-gender citizens, and persons with disabilities in TNSTC Ordinary Town Buses across Tirunelveli district and Tamil Nadu.
            </p>
            <div class="p-2 rounded mb-3" style="background:#fdf2f8;border:1px solid #fbcfe8;font-size:11.5px">
              <b>Eligibility:</b> All women, trans-gender persons & disability pass holders.<br>
              <b>Required Document:</b> No pass needed — board any town bus directly!
            </div>
            <a href="search_bus.php" class="btn btn-sm btn-outline-danger fw-bold w-100 py-2" style="border-radius:8px;font-size:12px">
              Search Town Buses &rarr;
            </a>
          </div>
        </div>

        <!-- Scheme 2: Student Free Bus Pass -->
        <div class="col-md-6 col-lg-4 scheme-card-item" data-category="student">
          <div class="card border-0 shadow-sm h-100 p-4" style="border-radius:16px;background:#ffffff">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div style="width:46px;height:46px;border-radius:12px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:22px">
                <i class="fa fa-graduation-cap"></i>
              </div>
              <span class="badge bg-primary rounded-pill" style="font-size:10px">Free Concession</span>
            </div>
            <h5 style="font-weight:800;color:#0f172a;margin:0 0 4px;font-size:16px">Free Student Bus Pass Scheme</h5>
            <div style="font-size:12px;color:#2563eb;font-weight:700" class="mb-2">மாணவர்களுக்கான இலவசப் பேருந்து அட்டை</div>
            <p style="font-size:12.5px;color:#64748b;line-height:1.5" class="flex-grow-1">
              Provides 100% free bus travel for school students (Classes 1 to 12) and government/aided college students between their residence and institution.
            </p>
            <div class="p-2 rounded mb-3" style="background:#eff6ff;border:1px solid #bfdbfe;font-size:11.5px">
              <b>Eligibility:</b> Accredited school and college students in Tirunelveli.<br>
              <b>Required Documents:</b> College ID card & Bonafide Certificate.
            </div>
            <a href="bus_pass.php" class="btn btn-sm btn-primary fw-bold w-100 py-2" style="border-radius:8px;font-size:12px">
              Apply Student Bus Pass &rarr;
            </a>
          </div>
        </div>

        <!-- Scheme 3: Senior Citizens Travel Concession -->
        <div class="col-md-6 col-lg-4 scheme-card-item" data-category="senior">
          <div class="card border-0 shadow-sm h-100 p-4" style="border-radius:16px;background:#ffffff">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div style="width:46px;height:46px;border-radius:12px;background:#f0fdf4;color:#16a34a;display:flex;align-items:center;justify-content:center;font-size:22px">
                <i class="fa fa-users-line"></i>
              </div>
              <span class="badge bg-success rounded-pill" style="font-size:10px">10 Tokens / Month</span>
            </div>
            <h5 style="font-weight:800;color:#0f172a;margin:0 0 4px;font-size:16px">Senior Citizen Free Travel Scheme</h5>
            <div style="font-size:12px;color:#16a34a;font-weight:700" class="mb-2">மூத்த குடிமக்களுக்கான பேருந்து திட்டம்</div>
            <p style="font-size:12.5px;color:#64748b;line-height:1.5" class="flex-grow-1">
              Offers 10 free bus travel tokens per month to senior citizens aged 60 and above in non-AC TNSTC buses operating within Tamil Nadu.
            </p>
            <div class="p-2 rounded mb-3" style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:11.5px">
              <b>Eligibility:</b> Senior citizens aged 60+ residing in Tamil Nadu.<br>
              <b>Required Documents:</b> Age proof (Aadhaar / Voter ID / Ration Card).
            </div>
            <a href="chatbot.php" class="btn btn-sm btn-outline-success fw-bold w-100 py-2" style="border-radius:8px;font-size:12px">
              Inquire at Depot / Helpdesk &rarr;
            </a>
          </div>
        </div>

        <!-- Scheme 4: Differently Abled & Escort Concession -->
        <div class="col-md-6 col-lg-4 scheme-card-item" data-category="disabled">
          <div class="card border-0 shadow-sm h-100 p-4" style="border-radius:16px;background:#ffffff">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div style="width:46px;height:46px;border-radius:12px;background:#faf5ff;color:#9333ea;display:flex;align-items:center;justify-content:center;font-size:22px">
                <i class="fa fa-wheelchair"></i>
              </div>
              <span class="badge bg-purple text-white rounded-pill" style="font-size:10px;background:#9333ea">100% Free + Companion</span>
            </div>
            <h5 style="font-weight:800;color:#0f172a;margin:0 0 4px;font-size:16px">Differently Abled Concession</h5>
            <div style="font-size:12px;color:#9333ea;font-weight:700" class="mb-2">மாற்றுத்திறனாளிகள் பேருந்து சலுகை</div>
            <p style="font-size:12.5px;color:#64748b;line-height:1.5" class="flex-grow-1">
              Free travel pass for persons with 40% or more disability, along with free travel allowance for one accompanying escort or companion.
            </p>
            <div class="p-2 rounded mb-3" style="background:#faf5ff;border:1px solid #e9d5ff;font-size:11.5px">
              <b>Eligibility:</b> Disability ID card issued by Welfare Dept.<br>
              <b>Coverage:</b> Ordinary & Express town & mofussil buses.
            </div>
            <a href="bus_pass.php" class="btn btn-sm btn-outline-purple fw-bold w-100 py-2" style="border-radius:8px;font-size:12px;color:#9333ea;border-color:#9333ea">
              Apply Disability Pass &rarr;
            </a>
          </div>
        </div>

        <!-- Scheme 5: Freedom Fighters & Martyrs Family Scheme -->
        <div class="col-md-6 col-lg-4 scheme-card-item" data-category="special">
          <div class="card border-0 shadow-sm h-100 p-4" style="border-radius:16px;background:#ffffff">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div style="width:46px;height:46px;border-radius:12px;background:#fff7ed;color:#ea580c;display:flex;align-items:center;justify-content:center;font-size:22px">
                <i class="fa fa-flag"></i>
              </div>
              <span class="badge bg-warning text-dark rounded-pill" style="font-size:10px">Honor Scheme</span>
            </div>
            <h5 style="font-weight:800;color:#0f172a;margin:0 0 4px;font-size:16px">Freedom Fighters Honor Pass</h5>
            <div style="font-size:12px;color:#ea580c;font-weight:700" class="mb-2">தியாகிகள் இலவசப் பேருந்து திட்டம்</div>
            <p style="font-size:12.5px;color:#64748b;line-height:1.5" class="flex-grow-1">
              Free travel passes for recognized Freedom Fighters, widows, and eligible dependents across all TNSTC state transport bus routes.
            </p>
            <div class="p-2 rounded mb-3" style="background:#fff7ed;border:1px solid #ffedd5;font-size:11.5px">
              <b>Eligibility:</b> Freedom Fighter pension certificate holders.<br>
              <b>Benefit:</b> 100% Free travel in all state buses.
            </div>
            <a href="chatbot.php" class="btn btn-sm btn-outline-warning text-dark fw-bold w-100 py-2" style="border-radius:8px;font-size:12px">
              Contact Collectorate / Helpdesk &rarr;
            </a>
          </div>
        </div>

        <!-- Scheme 6: Accredited Press & Media Pass -->
        <div class="col-md-6 col-lg-4 scheme-card-item" data-category="special">
          <div class="card border-0 shadow-sm h-100 p-4" style="border-radius:16px;background:#ffffff">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div style="width:46px;height:46px;border-radius:12px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:22px">
                <i class="fa fa-newspaper"></i>
              </div>
              <span class="badge bg-info text-white rounded-pill" style="font-size:10px">Media Concession</span>
            </div>
            <h5 style="font-weight:800;color:#0f172a;margin:0 0 4px;font-size:16px">Accredited Journalists Pass</h5>
            <div style="font-size:12px;color:#0d9488;font-weight:700" class="mb-2">செய்தியாளர்கள் இலவசப் பேருந்து அட்டை</div>
            <p style="font-size:12.5px;color:#64748b;line-height:1.5" class="flex-grow-1">
              Free and concessional bus travel passes for government-accredited journalists, press reporters, and media photojournalists.
            </p>
            <div class="p-2 rounded mb-3" style="background:#f0fdfa;border:1px solid #ccfbf1;font-size:11.5px">
              <b>Eligibility:</b> DIPR accredited media personnel.<br>
              <b>Benefit:</b> Free travel across state express & town buses.
            </div>
            <a href="bus_pass.php" class="btn btn-sm btn-outline-info fw-bold w-100 py-2" style="border-radius:8px;font-size:12px">
              Apply Media Pass &rarr;
            </a>
          </div>
        </div>

      </div><!-- /#schemesGrid -->

      <!-- Official Government Portals Section -->
      <div class="card border-0 shadow-sm p-4" style="border-radius:16px;background:#ffffff">
        <h5 style="font-weight:800;color:#0f172a;margin:0 0 16px;font-size:16px"><i class="fa fa-building-columns text-primary me-2"></i>Official Government Portals & e-Services</h5>
        
        <div class="row g-3">
          <div class="col-6 col-md-3">
            <a href="https://tn.gov.in" target="_blank" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8fafc;border:1px solid #e2e8f0;color:#0f172a;transition:all 0.2s">
              <div style="width:38px;height:38px;border-radius:10px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"><i class="fa fa-globe"></i></div>
              <div>
                <div style="font-size:13px;font-weight:800">TN Govt. Portal</div>
                <div style="font-size:10.5px;color:#64748b">tn.gov.in</div>
              </div>
            </a>
          </div>

          <div class="col-6 col-md-3">
            <a href="https://cm.tn.gov.in" target="_blank" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8fafc;border:1px solid #e2e8f0;color:#0f172a;transition:all 0.2s">
              <div style="width:38px;height:38px;border-radius:10px;background:#f0fdf4;color:#16a34a;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"><i class="fa fa-user-tie"></i></div>
              <div>
                <div style="font-size:13px;font-weight:800">Chief Minister's Cell</div>
                <div style="font-size:10.5px;color:#64748b">cm.tn.gov.in</div>
              </div>
            </a>
          </div>

          <div class="col-6 col-md-3">
            <a href="https://tnegov.tn.gov.in" target="_blank" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8fafc;border:1px solid #e2e8f0;color:#0f172a;transition:all 0.2s">
              <div style="width:38px;height:38px;border-radius:10px;background:#faf5ff;color:#9333ea;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"><i class="fa fa-laptop-code"></i></div>
              <div>
                <div style="font-size:13px;font-weight:800">TN e-Governance Agency</div>
                <div style="font-size:10.5px;color:#64748b">tnegov.tn.gov.in</div>
              </div>
            </a>
          </div>

          <div class="col-6 col-md-3">
            <a href="https://edistrict.tn.gov.in" target="_blank" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8fafc;border:1px solid #e2e8f0;color:#0f172a;transition:all 0.2s">
              <div style="width:38px;height:38px;border-radius:10px;background:#fff7ed;color:#ea580c;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"><i class="fa fa-id-card"></i></div>
              <div>
                <div style="font-size:13px;font-weight:800">TN e-District Services</div>
                <div style="font-size:10.5px;color:#64748b">edistrict.tn.gov.in</div>
              </div>
            </a>
          </div>
        </div>
      </div>

    </div><!-- /.page-content -->

    <!-- Footer -->
    <footer style="background:#ffffff;border-top:1px solid #e2e8f0;padding:16px 24px;font-size:12px;color:#64748b">
      <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <div>© 2026 Government of Tamil Nadu. All Rights Reserved.</div>
        <div class="d-flex gap-3">
          <a href="#" style="color:#64748b;text-decoration:none">Terms & Conditions</a>
          <a href="#" style="color:#64748b;text-decoration:none">Privacy Policy</a>
        </div>
      </div>
    </footer>

  </div><!-- /.main-content -->
</div><!-- /.app-layout -->

<script>
function filterSchemes(category, btnElement) {
  document.querySelectorAll('.scheme-filter-btn').forEach(btn => {
    btn.classList.remove('btn-primary', 'fw-bold', 'active');
    btn.classList.add('btn-white', 'text-secondary', 'fw-semibold');
  });
  btnElement.classList.remove('btn-white', 'text-secondary', 'fw-semibold');
  btnElement.classList.add('btn-primary', 'fw-bold', 'active');

  const items = document.querySelectorAll('.scheme-card-item');
  items.forEach(item => {
    if (category === 'all' || item.dataset.category === category) {
      item.style.display = 'block';
    } else {
      item.style.display = 'none';
    }
  });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
