// ============================================================
// TNSTC – main.js  (global utilities)
// ============================================================
<<<<<<<
document.addEventListener('DOMContentLoaded', () => {

  /* ── Sidebar toggle (mobile) ── */
  const toggle  = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('appSidebar');
  const overlay = document.getElementById('sidebarOverlay');

  function openSidebar() {
    sidebar?.classList.add('open');
    overlay?.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('show');
    document.body.style.overflow = '';
  }

  toggle?.addEventListener('click', openSidebar);
  overlay?.addEventListener('click', closeSidebar);

  /* ── Active sidebar link ── */
  const currentPath = window.location.pathname;
  document.querySelectorAll('.sidebar-link').forEach(link => {
    if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href'))) {
      link.classList.add('active');
    }
  });

  /* ── Auto-dismiss flash banners ── */
  document.querySelectorAll('.flash-banner').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity .5s ease';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    }, 4000);
  });

  /* ── Bootstrap tooltips ── */
  if (typeof bootstrap !== 'undefined') {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      new bootstrap.Tooltip(el);
    });
  }

  /* ── Confirm delete prompts ── */
  document.querySelectorAll('.confirm-delete').forEach(btn => {
    btn.addEventListener('click', e => {
      if (!confirm('Are you sure you want to delete this record? This cannot be undone.')) {
        e.preventDefault();
      }
    });
  });

  /* ── Scroll Reveal System ── */
  const revealElements = document.querySelectorAll('.animate-fade-up, .animate-fade-left, .animate-fade-right, .animate-scale-in, .animate-fade-in');
  
  if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('reveal');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.05,
      rootMargin: '0px 0px -20px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));
  } else {
    revealElements.forEach(el => el.classList.add('reveal'));
  }

  /* ── Ripple Click Effect ── */
  document.querySelectorAll('.btn-primary-custom, .btn-accent-custom, .btn-warning, .btn-primary').forEach(btn => {
    btn.addEventListener('click', function(e) {
      const rect = this.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      
      const ripple = document.createElement('span');
      ripple.className = 'btn-ripple-span';
      ripple.style.position = 'absolute';
      ripple.style.width = '60px';
      ripple.style.height = '60px';
      ripple.style.background = 'rgba(255, 255, 255, 0.35)';
      ripple.style.borderRadius = '50%';
      ripple.style.transform = 'translate(-50%, -50%) scale(0)';
      ripple.style.left = `${x}px`;
      ripple.style.top = `${y}px`;
      ripple.style.pointerEvents = 'none';
      ripple.style.transition = 'transform 0.4s cubic-bezier(0.1, 0.8, 0.3, 1), opacity 0.4s ease';
      
      const origPosition = window.getComputedStyle(this).position;
      if (origPosition === 'static') {
        this.style.position = 'relative';
      }
      this.style.overflow = 'hidden';
      
      this.appendChild(ripple);
      
      requestAnimationFrame(() => {
        ripple.style.transform = 'translate(-50%, -50%) scale(3.5)';
        ripple.style.opacity = '0';
      });
      
      setTimeout(() => {
        ripple.remove();
      }, 450);
    });
  });

});

// ============================================================
// Global AJAX helper
// ============================================================
async function apiPost(url, data) {
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify(data),
  });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

async function apiGet(url) {
  const res = await fetch(url, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
  });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

// ============================================================
// Notification badge refresh (every 30 s)
// ============================================================
async function refreshNotifBadge() {
  try {
    const data = await apiGet('/TNSTC/api/notify.php');
    const badge = document.querySelector('.notif-badge');
    if (badge) badge.style.display = data.unread > 0 ? 'block' : 'none';
  } catch (_) {}
}

setInterval(refreshNotifBadge, 30000);
refreshNotifBadge();
