/* app.js — Router | Scroll Reveal | CountUp | Calculator | Countdown | Tilt | Forms */

document.addEventListener('DOMContentLoaded', () => {

  /* ─────────────────────────────────────────────
     1. ROUTER
  ───────────────────────────────────────────── */
  const views   = { '#giveaway': document.getElementById('view-g'), '#sponsors': document.getElementById('view-s') };
  const navEls  = { '#giveaway': document.getElementById('nav-g'),  '#sponsors': document.getElementById('nav-s') };

  function routeTo(hash) {
    if (!hash || hash === '#') hash = '#giveaway';
    
    let targetEl = null;
    let pageHash = hash;
    
    if (hash === '#lead-anchor') {
      pageHash = '#giveaway';
      targetEl = document.getElementById('lead-anchor');
    } else if (hash === '#checkout-s' || hash === '#s-pricing') {
      pageHash = '#sponsors';
      targetEl = document.getElementById('s-pricing');
    }
    
    if (!views[pageHash]) pageHash = '#giveaway';
    
    const viewChanged = !views[pageHash].classList.contains('show');
    
    Object.values(views).forEach(v  => v  && v.classList.remove('show'));
    Object.values(navEls).forEach(n => n  && n.classList.remove('on'));
    
    if (views[pageHash])  views[pageHash].classList.add('show');
    if (navEls[pageHash]) navEls[pageHash].classList.add('on');
    
    if (targetEl) {
      setTimeout(() => {
        targetEl.scrollIntoView({ behavior: 'smooth' });
      }, viewChanged ? 180 : 0);
    } else {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // Re-trigger scroll reveal for the newly shown page
    setTimeout(() => revealAll(), 100);
    
    // Initialize or refresh the floating side navigation sections
    if (typeof initSideNav === 'function') {
      initSideNav(hash);
    }
  }

  window.addEventListener('hashchange', () => routeTo(window.location.hash));
  routeTo(window.location.hash);

  const logoLink = document.getElementById('logo-link');
  if (logoLink) logoLink.addEventListener('click', e => { e.preventDefault(); window.location.hash = '#giveaway'; });


  /* ─────────────────────────────────────────────
     2. SCROLL REVEAL (IntersectionObserver)
  ───────────────────────────────────────────── */
  function revealAll() {
    const io = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          obs.unobserve(entry.target);
          // Trigger countUp if element has data-count
          const countEl = entry.target.querySelector('[data-count]');
          if (countEl) countUp(countEl, +countEl.dataset.count, 1200);
        }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.rev:not(.in)').forEach(el => io.observe(el));
  }

  revealAll();


  /* ─────────────────────────────────────────────
     3. COUNTUP ANIMATION
  ───────────────────────────────────────────── */
  function countUp(el, target, duration = 1100, suffix = '') {
    const start = performance.now();
    const prefix = el.dataset.prefix || '';
    const suf    = suffix || el.dataset.suffix || '';

    function step(now) {
      const p = Math.min((now - start) / duration, 1);
      const e = 1 - Math.pow(1 - p, 4); // ease-out quart
      const v = Math.round(e * target);
      el.textContent = prefix + v.toLocaleString('pl-PL') + suf;
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }


  /* ─────────────────────────────────────────────
     4. SMOOTH 3-D TILT (lerp-based)
  ───────────────────────────────────────────── */
  document.querySelectorAll('.tilt').forEach(card => {
    let raf, tx = 0, ty = 0, cx = 0, cy = 0;

    function lerp(a, b, t) { return a + (b - a) * t; }

    function loop() {
      cx = lerp(cx, tx, 0.12);
      cy = lerp(cy, ty, 0.12);
      card.style.transform = `perspective(900px) rotateX(${cy}deg) rotateY(${cx}deg)`;
      raf = requestAnimationFrame(loop);
    }

    card.addEventListener('mouseenter', () => { loop(); });
    card.addEventListener('mousemove', e => {
      const r = card.getBoundingClientRect();
      tx = ((e.clientX - r.left) / r.width  - 0.5) *  9;
      ty = ((e.clientY - r.top)  / r.height - 0.5) * -9;
    });
    card.addEventListener('mouseleave', () => {
      tx = 0; ty = 0;
      setTimeout(() => { cancelAnimationFrame(raf); card.style.transform = ''; }, 600);
    });
  });


  /* ─────────────────────────────────────────────
     5. FOMO BAR ANIMATION
  ───────────────────────────────────────────── */
  setTimeout(() => {
    const bar = document.getElementById('slots-bar');
    if (bar) bar.style.width = '90%';
  }, 500);


  /* ─────────────────────────────────────────────
     6. RANGE SLIDER / CALCULATOR
  ───────────────────────────────────────────── */
  const slider  = document.getElementById('follower-range');
  const display = document.getElementById('calc-display');
  const gEl     = document.getElementById('calc-growth');
  const rEl     = document.getElementById('calc-reach');
  const cEl     = document.getElementById('calc-conv');

  let lastGrowth = 14500;

  function animateVal(el, target, isMoney) {
    const start = performance.now();
    const from  = lastGrowth;
    function step(now) {
      const p = Math.min((now - start) / 400, 1);
      const e = 1 - Math.pow(1 - p, 3);
      const v = Math.round(from + (target - from) * e);
      el.textContent = '+' + v.toLocaleString('pl-PL');
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  function updateCalc() {
    if (!slider) return;
    const val = +slider.value;
    const pct = ((val - +slider.min) / (+slider.max - +slider.min)) * 100;
    slider.style.setProperty('--pct', pct + '%');

    if (display) display.textContent = val.toLocaleString('pl-PL') + ' obserwujących';

    const growth = Math.min(85000, 10000 + Math.round(val * 0.14));
    if (gEl) {
      gEl.textContent = '+' + growth.toLocaleString('pl-PL');
    }
    lastGrowth = growth;

    const reach = Math.round(growth * 17.5);
    if (rEl) rEl.textContent = reach.toLocaleString('pl-PL') + '+';

    let conv;
    if (val < 25000)       conv = (3.9 + (val / 25000) * 1.6).toFixed(1);
    else if (val < 180000) conv = (5.5 - ((val - 25000) / 155000) * 1.4).toFixed(1);
    else                   conv = (4.1 - ((val - 180000) / 320000) * 1.0).toFixed(1);
    if (cEl) cEl.textContent = conv + '%';
  }

  if (slider) { slider.addEventListener('input', updateCalc); updateCalc(); }


  /* ─────────────────────────────────────────────
     7. COUNTDOWN TIMER (to giveaway end)
  ───────────────────────────────────────────── */
  const cdD = document.getElementById('cd-d');
  const cdH = document.getElementById('cd-h');
  const cdM = document.getElementById('cd-m');
  const cdS = document.getElementById('cd-s');

  if (cdD) {
    // Target: 4 days 12 hours from now (or a fixed date)
    const target = Date.now() + (4 * 86400 + 12 * 3600 + 47 * 60 + 33) * 1000;

    function pad(n) { return String(Math.max(0, n)).padStart(2, '0'); }

    function tick() {
      const diff = Math.max(0, target - Date.now());
      const d = Math.floor(diff / 86400000);
      const h = Math.floor((diff % 86400000) / 3600000);
      const m = Math.floor((diff % 3600000)  / 60000);
      const s = Math.floor((diff % 60000)    / 1000);
      cdD.textContent = pad(d);
      cdH.textContent = pad(h);
      cdM.textContent = pad(m);
      cdS.textContent = pad(s);
    }

    tick();
    setInterval(tick, 1000);
  }


  /* ─────────────────────────────────────────────
     8. SOCIAL PROOF COUNTER (animated)
  ───────────────────────────────────────────── */
  const spCount = document.getElementById('sp-count');
  if (spCount) {
    // Simulate live updates
    let count = 12840;
    setInterval(() => {
      count += Math.floor(Math.random() * 3);
      spCount.textContent = count.toLocaleString('pl-PL');
    }, 4000);
  }


  /* ─────────────────────────────────────────────
     9. CHECKOUT FORM (multi-step)
  ───────────────────────────────────────────── */
  const fs1 = document.getElementById('fs1');
  const fs2 = document.getElementById('fs2');
  const fs3 = document.getElementById('fs3');
  const ss1 = document.getElementById('ss1');
  const ss2 = document.getElementById('ss2');
  const ss3 = document.getElementById('ss3');

  const btnBack   = document.getElementById('btn-back');
  const btnPay    = document.getElementById('btn-pay');
  const btnDash   = document.getElementById('btn-dash');
  const payLoading = document.getElementById('pay-loading');
  const paySuccess = document.getElementById('pay-success');
  const whLog      = document.getElementById('wh-log');
  const txName     = document.getElementById('tx-name');
  const txIG       = document.getElementById('tx-ig');

  let sponsor = {};

  if (fs1) {
    fs1.addEventListener('submit', e => {
      e.preventDefault();
      sponsor.name  = document.getElementById('s-name')?.value.trim()  || '';
      sponsor.email = document.getElementById('s-email')?.value.trim() || '';
      let ig = document.getElementById('s-ig')?.value.trim() || '';
      if (ig && !ig.startsWith('@')) ig = '@' + ig;
      sponsor.ig = ig;

      fs1.classList.remove('on');
      fs2.classList.add('on');
      ss2.classList.add('on');
    });
  }

  if (btnBack) btnBack.addEventListener('click', () => {
    fs2.classList.remove('on'); fs1.classList.add('on'); ss2.classList.remove('on');
  });

  if (btnPay) btnPay.addEventListener('click', () => {
    const sel = document.querySelector('input[name="pay"]:checked');
    sponsor.method = sel ? sel.value.toUpperCase() : 'BLIK';
    fs2.classList.remove('on'); fs3.classList.add('on'); ss3.classList.add('on');
    runWebhook();
  });

  function runWebhook() {
    if (!whLog) return;
    whLog.innerHTML = '';
    if (payLoading) payLoading.style.display = 'flex';
    if (paySuccess) paySuccess.style.display = 'none';

    const logs = [
      { t:    0, html: `🚀 [0.0s] Inicjowanie sesji ${sponsor.method}...` },
      { t:  420, html: `📡 [0.4s] POST /webhooks/payment-received → <span class="wp">PENDING</span>` },
      { t:  900, html: `🔑 [0.9s] Uwierzytelnianie tokenu sesji...` },
      { t: 1350, html: `💳 [1.3s] Bramka: <span class="wg">SUCCESS 200 OK</span>` },
      { t: 1800, html: `🛠️ [1.8s] Rejestracja nowej marki Aftermarket...` },
      { t: 2250, html: `💾 [2.2s] Zapis rekordu <span class="wb">${sponsor.ig}</span> w PostgreSQL...` },
      { t: 2700, html: `📊 [2.7s] Inicjalizacja klucza API panelu live...` },
      { t: 3100, html: `✉️ [3.1s] Faktura VAT → <span class="wp">${sponsor.email}</span>` },
      { t: 3500, html: `🎉 [3.5s] Webhook zakończony pomyślnie.` },
    ];

    logs.forEach(({ t, html }) => {
      setTimeout(() => {
        const d = document.createElement('div');
        d.className = 'wl';
        d.innerHTML = html;
        whLog.appendChild(d);
        whLog.scrollTop = whLog.scrollHeight;
      }, t);
    });

    setTimeout(() => {
      if (txName) txName.textContent = sponsor.name;
      if (txIG)   txIG.textContent   = sponsor.ig;
      if (payLoading) payLoading.style.display = 'none';
      if (paySuccess) paySuccess.style.display  = 'block';
    }, 4000);
  }

  if (btnDash) btnDash.addEventListener('click', () => {
    window.location.href = `dashboard.html?login=success&ig=${encodeURIComponent(sponsor.ig)}&email=${encodeURIComponent(sponsor.email)}`;
  });


  /* ─────────────────────────────────────────────
     10. LEAD FORM
  ───────────────────────────────────────────── */
  const leadForm    = document.getElementById('lead-form');
  const leadSuccess = document.getElementById('lead-success');
  const btnReset    = document.getElementById('btn-lead-reset');

  if (leadForm) {
    leadForm.addEventListener('submit', e => {
      e.preventDefault();
      
      const nameVal = document.getElementById('l-name')?.value.trim() || 'Uczestnik';
      const emailVal = document.getElementById('l-email')?.value.trim() || '';
      const phoneVal = document.getElementById('l-phone')?.value.trim() || '';
      
      // Save registration to localStorage
      const regs = JSON.parse(localStorage.getItem('am_registrations') || '[]');
      const cities = ['Warszawa', 'Kraków', 'Wrocław', 'Poznań', 'Gdańsk', 'Katowice', 'Łódź', 'Szczecin'];
      const city = cities[Math.floor(Math.random() * cities.length)];
      
      // Generate a mock IG handle
      const emailUser = emailVal.split('@')[0] || 'user';
      const igHandle = '@' + emailUser.replace(/[^a-zA-Z0-9_.]/g, '_');
      
      const newReg = {
        name: nameVal,
        email: emailVal,
        phone: phoneVal,
        ig: igHandle,
        city: city,
        time: new Date().toLocaleTimeString()
      };
      regs.unshift(newReg);
      localStorage.setItem('am_registrations', JSON.stringify(regs));
      
      // Increment total count
      const localCount = parseInt(localStorage.getItem('am_total_subscribers') || '12840');
      localStorage.setItem('am_total_subscribers', localCount + 1);

      leadForm.style.display   = 'none';
      if (leadSuccess) leadSuccess.style.display = 'block';
    });
  }

  /* ─────────────────────────────────────────────
     11. 3D AMBIENT PARALLAX & STARFIELD DUST
  ───────────────────────────────────────────── */
  // 1. Mouse Coordinates & Parallax Targets
  let mx = 0, my = 0;   // Normalized coordinates (-1 to 1)
  let mRawX = 0, mRawY = 0; // Raw client coordinates
  let cx = 0, cy = 0;   // Lerped normalized coords
  let cgX = 0, cgY = 0; // Lerped raw coords for glow torch
  
  const orbs = [
    { el: document.getElementById('orb-1'), fx: -0.06, fy: -0.06 },
    { el: document.getElementById('orb-2'), fx: 0.05, fy: 0.05 },
    { el: document.getElementById('orb-3'), fx: -0.03, fy: 0.03 },
    { el: document.getElementById('orb-4'), fx: 0.03, fy: -0.03 }
  ];

  const gridBack = document.getElementById('bg-grid-back');
  const gridFront = document.getElementById('bg-grid-front');
  const cursorGlow = document.getElementById('bg-cursor-glow');

  window.addEventListener('mousemove', e => {
    mx = (e.clientX - window.innerWidth / 2) / (window.innerWidth / 2);
    my = (e.clientY - window.innerHeight / 2) / (window.innerHeight / 2);
    mRawX = e.clientX;
    mRawY = e.clientY;
  });

  // Initialize cursor glow positioning on first movement or center it initially
  mRawX = window.innerWidth / 2;
  mRawY = window.innerHeight / 2;

  function animateAmbientElements() {
    // Smooth lerping
    cx += (mx - cx) * 0.06;
    cy += (my - cy) * 0.06;
    cgX += (mRawX - cgX) * 0.08;
    cgY += (mRawY - cgY) * 0.08;

    // 1. Move Colored Ambient Orbs
    orbs.forEach(orb => {
      if (orb.el) {
        const x = cx * orb.fx * 160;
        const y = cy * orb.fy * 160;
        orb.el.style.transform = `translate(${x}px, ${y}px)`;
      }
    });

    // 2. Multi-layered Parallax Grid Shift
    if (gridBack) {
      const bx = cx * -12;
      const by = cy * -12;
      gridBack.style.transform = `translate(${bx}px, ${by}px)`;
    }
    if (gridFront) {
      const fx = cx * -32;
      const fy = cy * -32;
      gridFront.style.transform = `translate(${fx}px, ${fy}px)`;
    }

    // 3. Interactive Torch Glow Aura
    if (cursorGlow) {
      cursorGlow.style.left = `${cgX}px`;
      cursorGlow.style.top = `${cgY}px`;
    }

    requestAnimationFrame(animateAmbientElements);
  }
  animateAmbientElements();

  // 2. 3D Projected Canvas Floating Starfield
  const canvas = document.getElementById('bg-particles');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    let w = canvas.width = window.innerWidth;
    let h = canvas.height = window.innerHeight;

    window.addEventListener('resize', () => {
      w = canvas.width = window.innerWidth;
      h = canvas.height = window.innerHeight;
    });

    const particles = [];
    const count = 55;

    // Create 3D particles (x, y, z where z controls depth: 0.1 is close, 1.0 is far)
    for (let i = 0; i < count; i++) {
      particles.push({
        x: Math.random() * w,
        y: Math.random() * h,
        z: Math.random() * 0.9 + 0.1, // 0.1 to 1.0 depth
        speedY: Math.random() * 0.25 + 0.1,
        waveFreq: Math.random() * 0.02 + 0.005,
        waveAmp: Math.random() * 0.4 + 0.1
      });
    }

    function draw3DStarfield() {
      ctx.clearRect(0, 0, w, h);
      
      particles.forEach(p => {
        // Calculate speed, size, opacity based on depth (z)
        // Foreground particles (smaller z) are larger, brighter, and shift more with cursor
        const invZ = 1.0 - (p.z * 0.7); // scale depth effect
        const radius = invZ * 2.2;
        const opacity = invZ * 0.55;
        
        // Parallax displacement based on normalized cursor position and depth
        const parallaxX = cx * (invZ * 60);
        const parallaxY = cy * (invZ * 60);
        
        // Final screen position (projected)
        const renderX = p.x + parallaxX;
        const renderY = p.y + parallaxY;

        ctx.beginPath();
        ctx.arc(renderX, renderY, radius, 0, Math.PI * 2);
        
        // Gradient color for a premium touch: mix white and a slight neon blue/pink tint depending on depth
        if (p.z < 0.4) {
          // foreground: warm glow
          ctx.fillStyle = `rgba(244, 63, 94, ${opacity})`;
        } else if (p.z < 0.7) {
          // middleground: cool cyan glow
          ctx.fillStyle = `rgba(59, 130, 246, ${opacity})`;
        } else {
          // background: pure white stars
          ctx.fillStyle = `rgba(255, 255, 255, ${opacity * 0.8})`;
        }
        ctx.fill();

        // Particle floating physics
        p.y -= p.speedY * (invZ * 1.5);
        p.x += Math.sin(p.y * p.waveFreq) * p.waveAmp;

        // Wrap around limits
        if (p.y < -20) {
          p.y = h + 20;
          p.x = Math.random() * w;
          p.z = Math.random() * 0.9 + 0.1;
        }
        if (p.x < -20) p.x = w + 20;
        if (p.x > w + 20) p.x = -20;
      });

      requestAnimationFrame(draw3DStarfield);
    }
    draw3DStarfield();
  }

  if (btnReset) btnReset.addEventListener('click', () => {
    leadForm && (leadForm.reset(), leadForm.style.display = 'block');
    if (leadSuccess) leadSuccess.style.display = 'none';
  });

  /* ─────────────────────────────────────────────
     12. VERTICAL SCROLL NAVIGATION & PROGRESS SPY
  ───────────────────────────────────────────── */
  const sideNavDotsContainer = document.getElementById('side-nav-dots');
  const sideProgressBar = document.getElementById('side-progress-bar');

  const giveawaySections = [
    { id: 'g-hero', label: 'Start' },
    { id: 'g-steps', label: 'Zasady' }
  ];

  const sponsorsSections = [
    { id: 's-hero', label: 'Start B2B' },
    { id: 's-pricing', label: 'Cennik' }
  ];

  let currentSections = [];

  function initSideNav(hash) {
    if (!sideNavDotsContainer) return;
    sideNavDotsContainer.innerHTML = '';
    
    const pageHash = (!hash || hash === '#') ? '#giveaway' : hash;
    let viewHash = pageHash;
    
    if (pageHash === '#lead-anchor') {
      viewHash = '#giveaway';
    }
    
    if (viewHash.startsWith('#sponsors')) {
      currentSections = sponsorsSections;
    } else {
      currentSections = giveawaySections;
    }

    currentSections.forEach((sec, idx) => {
      const dot = document.createElement('div');
      dot.className = `side-dot-item${idx === 0 ? ' active' : ''}`;
      dot.dataset.target = sec.id;
      
      const tooltip = document.createElement('div');
      tooltip.className = 'side-dot-tooltip';
      tooltip.textContent = sec.label;
      
      dot.appendChild(tooltip);
      
      dot.addEventListener('click', () => {
        const el = document.getElementById(sec.id);
        if (el) {
          el.scrollIntoView({ behavior: 'smooth' });
        }
      });
      
      sideNavDotsContainer.appendChild(dot);
    });
    
    updateScrollSpy();
  }

  function updateScrollSpy() {
    if (!currentSections || !currentSections.length) return;
    
    // Calculate page scroll progress percentage
    const scrollPos = window.scrollY;
    const windowHeight = window.innerHeight;
    const docHeight = document.documentElement.scrollHeight - windowHeight;
    const scrollPercent = docHeight > 0 ? (scrollPos / docHeight) * 100 : 0;
    
    if (sideProgressBar) {
      sideProgressBar.style.height = `${scrollPercent}%`;
    }
    
    // Find active section
    let activeId = '';
    currentSections.forEach(sec => {
      const el = document.getElementById(sec.id);
      if (el) {
        const rect = el.getBoundingClientRect();
        // Section is active if it starts near or above the middle of viewport
        if (rect.top <= windowHeight * 0.45) {
          activeId = sec.id;
        }
      }
    });

    if (!activeId && currentSections.length > 0) {
      activeId = currentSections[0].id;
    }

    // Update active class on dots
    const dots = sideNavDotsContainer.querySelectorAll('.side-dot-item');
    dots.forEach(dot => {
      if (dot.dataset.target === activeId) {
        dot.classList.add('active');
      } else {
        dot.classList.remove('active');
      }
    });
  }

  // Monitor window scroll
  window.addEventListener('scroll', updateScrollSpy);
  window.addEventListener('resize', updateScrollSpy);

  // ── INSTAGRAM FOLLOWER GROWTH SIMULATION ──
  function initFollowerSimulator() {
    const countEl = document.getElementById('phone-followers-count');
    const badgeEl = document.getElementById('phone-followers-badge');
    if (!countEl || !badgeEl) return;

    let baseCount = 55300;
    
    function formatCount(num) {
      if (num >= 10000) {
        return (num / 1000).toFixed(1).replace('.', ',') + ' tys.';
      }
      return num.toLocaleString('pl-PL');
    }
    
    // Set initial value formatted
    countEl.textContent = formatCount(baseCount);

    function simulateGrowth() {
      // Generate a random increase between 40 and 150 followers
      const increase = Math.floor(Math.random() * 110) + 40;
      baseCount += increase;
      
      // Update the main counter text
      countEl.textContent = formatCount(baseCount);
      
      // Update badge text and trigger visual animation
      badgeEl.textContent = `+${increase}`;
      badgeEl.style.opacity = '1';
      badgeEl.style.transform = 'translateX(-50%) scale(1.1)';
      badgeEl.style.boxShadow = '0 0 10px var(--green)';

      // Trigger counter scale effect
      countEl.style.transform = 'scale(1.08)';
      countEl.style.color = 'var(--green)';

      // Reset style and fade out badge after 1.2s
      setTimeout(() => {
        badgeEl.style.opacity = '0';
        badgeEl.style.transform = 'translateX(-50%) scale(0.8)';
        badgeEl.style.boxShadow = 'none';
        countEl.style.transform = 'scale(1)';
        countEl.style.color = '#fff';
      }, 1200);

      // Loop back if it gets too high (e.g. > 98,000)
      if (baseCount > 98000) {
        baseCount = 55300;
      }
    }

    // Run every 3-5 seconds randomly
    function scheduleNext() {
      const delay = Math.random() * 2000 + 2500; // between 2.5 and 4.5 seconds
      setTimeout(() => {
        simulateGrowth();
        scheduleNext();
      }, delay);
    }
    
    scheduleNext();
  }

  // Initialize follower simulator
  initFollowerSimulator();

  // Expose to DOMContentLoaded scope so routeTo can call it
  window.initSideNav = initSideNav;
  
  // Toggle features lists
  window.toggleFeats = function(tier) {
    const list = document.getElementById(tier === 'starter' ? 'feats-starter' : 'feats-prof');
    if (!list) return;
    
    const btn = list.nextElementSibling;
    const isCollapsed = list.classList.toggle('collapsed');
    
    if (btn && btn.classList.contains('feats-toggle-btn')) {
      const label = btn.querySelector('span');
      if (label) {
        label.textContent = isCollapsed ? 'Pokaż więcej' : 'Pokaż mniej';
      }
      btn.classList.toggle('expanded', !isCollapsed);
    }
  };

  // Initialize on load
  initSideNav(window.location.hash);
});

