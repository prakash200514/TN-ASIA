// ============================================================
// TNSTC – main.js  (global utilities)
// ============================================================

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

  /* ── Ultra-Smooth Inertia Momentum Scroll Engine ── */
  let lenis = null;
  if (typeof Lenis !== 'undefined') {
    lenis = new Lenis({
      duration: 1.25,
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Smooth exponential decay curve
      direction: 'vertical',
      gestureDirection: 'vertical',
      smoothWheel: true,
      smoothTouch: false,
      touchMultiplier: 1.5,
    });

    function lenisRaf(time) {
      lenis.raf(time);
      requestAnimationFrame(lenisRaf);
    }
    requestAnimationFrame(lenisRaf);

    lenis.on('scroll', () => {
      onWindowScroll();
    });
  }

  /* ── Smooth Scroll & Progress Bar Setup ── */
  let scrollProgressBar = document.getElementById('scrollProgressBar');
  if (!scrollProgressBar) {
    scrollProgressBar = document.createElement('div');
    scrollProgressBar.id = 'scrollProgressBar';
    scrollProgressBar.className = 'scroll-progress-bar';
    document.body.appendChild(scrollProgressBar);
  }

  let scrollToTopBtn = document.getElementById('scrollToTopBtn');
  if (!scrollToTopBtn) {
    scrollToTopBtn = document.createElement('button');
    scrollToTopBtn.id = 'scrollToTopBtn';
    scrollToTopBtn.className = 'scroll-to-top-btn';
    scrollToTopBtn.setAttribute('aria-label', 'Scroll to top');
    scrollToTopBtn.innerHTML = '<i class="fa fa-chevron-up"></i>';
    document.body.appendChild(scrollToTopBtn);

    scrollToTopBtn.addEventListener('click', () => {
      if (lenis) {
        lenis.scrollTo(0, { duration: 1.2 });
      } else {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      }
    });
  }

  /* ── Smooth Scrolling for Hash Links ── */
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId && targetId !== '#' && targetId.length > 1) {
        const targetEl = document.querySelector(targetId);
        if (targetEl) {
          e.preventDefault();
          if (lenis) {
            lenis.scrollTo(targetEl, { offset: -80, duration: 1.2 });
          } else {
            const navOffset = 80;
            const elementPosition = targetEl.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - navOffset;

            window.scrollTo({
              top: offsetPosition,
              behavior: 'smooth'
            });
          }
        }
      }
    });
  });

  /* ── Scroll Handler: Progress Bar, Top Btn & Smart Moving Navbar ── */
  let lastScrollTop = 0;
  const navbars = document.querySelectorAll('.landing-nav, .top-navbar, .navbar, .about-navbar');

  function onWindowScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrollPercent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

    if (scrollProgressBar) {
      scrollProgressBar.style.width = scrollPercent + '%';
    }

    if (scrollToTopBtn) {
      if (scrollTop > 250) {
        scrollToTopBtn.classList.add('visible');
      } else {
        scrollToTopBtn.classList.remove('visible');
      }
    }

    // Sticky Fixed Glassmorphic Navbar on Scroll
    navbars.forEach(nav => {
      if (scrollTop > 20) {
        nav.classList.add('navbar-scrolled');
      } else {
        nav.classList.remove('navbar-scrolled');
      }
    });
  }

  window.addEventListener('scroll', onWindowScroll, { passive: true });
  onWindowScroll();

  /* ── Automatic Scroll-Reveal & Stagger Assignment ── */
  const autoAnimateSelectors = [
    '.card:not(.animate-fade-up):not(.animate-fade-left):not(.animate-fade-right):not(.animate-scale-in)',
    '.depot-card:not(.animate-fade-up)',
    '.feature-card:not(.animate-fade-up)',
    '.stat-card:not(.animate-fade-up)',
    '.timeline-item:not(.animate-fade-up)',
    '.service-card:not(.animate-fade-up)',
    '.route-card:not(.animate-fade-up)',
    '.step-card:not(.animate-fade-up)',
    '.role-card'
  ];

  document.querySelectorAll(autoAnimateSelectors.join(',')).forEach((el) => {
    el.classList.add('animate-fade-up');
  });

  // Assign sequential auto-stagger delay (delay-1 to delay-10) to all grid row children
  document.querySelectorAll('.row').forEach(row => {
    const animChildren = row.querySelectorAll('.animate-fade-up, .animate-scale-in, .animate-zoom-in, .animate-fade-left, .animate-fade-right, .scroll-reveal, [class*="col-"]');
    animChildren.forEach((child, idx) => {
      const hasDelay = Array.from(child.classList).some(c => c.startsWith('delay-'));
      if (!hasDelay) {
        child.classList.add(`delay-${(idx % 10) + 1}`);
      }
    });
  });

  /* ── Scroll Reveal IntersectionObserver ── */
  const revealElements = document.querySelectorAll(
    '.animate-fade-up, .animate-fade-down, .animate-fade-left, .animate-fade-right, .animate-scale-in, .animate-zoom-in, .animate-flip-up, .animate-fade-in, .scroll-reveal'
  );
  
  function triggerCounterAnimation(target) {
    const counters = target.querySelectorAll('.counter-val, [data-count]');
    counters.forEach(counter => {
      const targetVal = parseInt(counter.getAttribute('data-count') || counter.innerText.replace(/[^0-9]/g, ''));
      if (isNaN(targetVal) || counter.dataset.animated) return;
      counter.dataset.animated = 'true';
      let startVal = 0;
      const duration = 1200;
      const startTime = performance.now();

      function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeProgress = 1 - Math.pow(1 - progress, 3); // cubic ease out
        const currentCount = Math.floor(easeProgress * targetVal);
        counter.innerText = currentCount.toLocaleString();
        if (progress < 1) {
          requestAnimationFrame(updateCounter);
        } else {
          counter.innerText = targetVal.toLocaleString();
        }
      }
      requestAnimationFrame(updateCounter);
    });
  }

  if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('reveal');
          triggerCounterAnimation(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.08,
      rootMargin: '0px 0px -30px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));
  } else {
    revealElements.forEach(el => {
      el.classList.add('reveal');
      triggerCounterAnimation(el);
    });
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
