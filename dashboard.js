/* dashboard.js — Auth | Activity Feed | Chart.js Premium | CountUp | Refresh */

document.addEventListener('DOMContentLoaded', () => {

  const viewLogin = document.getElementById('view-login');
  const viewDash  = document.getElementById('view-dash');

  const loginForm = document.getElementById('login-form');
  const loginErr  = document.getElementById('login-err');
  const lEmail    = document.getElementById('l-email');
  const lToken    = document.getElementById('l-token');

  const dashIG    = document.getElementById('dash-ig');
  const kpiLeads  = document.getElementById('kpi-leads');
  const dashLatest = document.getElementById('dash-latest');
  const kpiTimer   = document.getElementById('kpi-timer');
  const actFeed    = document.getElementById('act-feed');

  const btnLogout  = document.getElementById('btn-logout');
  const btnRefresh = document.getElementById('btn-refresh');

  let growthChart = null;
  let actInterval = null;
  let timerInterval = null;

  /* ─────────────────────────────────────────────
     1. SCROLL REVEAL
  ───────────────────────────────────────────── */
  function revealAll() {
    const io = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.06, rootMargin: '0px 0px -30px 0px' });
    document.querySelectorAll('.rev:not(.in)').forEach(el => io.observe(el));
  }

  /* ─────────────────────────────────────────────
     2. LERP TILT
  ───────────────────────────────────────────── */
  function initTilt() {
    document.querySelectorAll('.tilt').forEach(card => {
      let raf, tx = 0, ty = 0, cx = 0, cy = 0;
      function loop() {
        cx = cx + (tx - cx) * 0.12;
        cy = cy + (ty - cy) * 0.12;
        card.style.transform = `perspective(900px) rotateX(${cy}deg) rotateY(${cx}deg)`;
        raf = requestAnimationFrame(loop);
      }
      card.addEventListener('mouseenter', () => loop());
      card.addEventListener('mousemove', e => {
        const r = card.getBoundingClientRect();
        tx = ((e.clientX - r.left) / r.width  - 0.5) *  8;
        ty = ((e.clientY - r.top)  / r.height - 0.5) * -8;
      });
      card.addEventListener('mouseleave', () => {
        tx = 0; ty = 0;
        setTimeout(() => { cancelAnimationFrame(raf); card.style.transform = ''; }, 600);
      });
    });
  }

  /* ─────────────────────────────────────────────
     3. COUNTUP
  ───────────────────────────────────────────── */
  function countUp(el, target, duration = 900) {
    const start = performance.now();
    const from  = parseInt(el.textContent.replace(/\D/g, '')) || 0;
    function step(now) {
      const p = Math.min((now - start) / duration, 1);
      const e = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(from + (target - from) * e).toLocaleString('pl-PL');
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  /* ─────────────────────────────────────────────
     4. AUTH
  ───────────────────────────────────────────── */
  function showDash(ig) {
    viewLogin.classList.remove('show');
    viewDash.classList.add('show');
    if (dashIG) dashIG.textContent = ig || '@twoja_marka';

    const totalSubs = localStorage.getItem('am_total_subscribers') || '12840';
    if (kpiLeads) kpiLeads.textContent = parseInt(totalSubs).toLocaleString('pl-PL');
    
    const extraSubs = parseInt(totalSubs) - 12840;
    const todayLeads = 1420 + (extraSubs > 0 ? extraSubs : 0);
    const leadsInfo = document.querySelector('.kpi-bl .kpi-t');
    if (leadsInfo) leadsInfo.textContent = `▲ +${todayLeads.toLocaleString('pl-PL')} dzisiaj`;

    setTimeout(() => {
      revealAll();
      initTilt();
      initChart();
      startActivityFeed();
      startCampaignTimer();
    }, 150);
  }

  function showLogin() {
    viewDash.classList.remove('show');
    viewLogin.classList.add('show');
    if (loginForm) loginForm.reset();
    if (loginErr) loginErr.style.display = 'none';
    clearInterval(actInterval);
    clearInterval(timerInterval);
    setTimeout(revealAll, 100);
    setTimeout(initTilt,  100);
  }

  function checkAuth() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('login') === 'success') {
      const ig = params.get('ig') || '@twoja_marka';
      sessionStorage.setItem('am_auth', 'true');
      sessionStorage.setItem('am_ig',   ig);
      window.history.replaceState({}, '', window.location.pathname);
    }
    if (sessionStorage.getItem('am_auth') === 'true') {
      showDash(sessionStorage.getItem('am_ig'));
    } else {
      showLogin();
    }
  }

  if (loginForm) {
    loginForm.addEventListener('submit', e => {
      e.preventDefault();
      if (lToken?.value.trim() === 'AFTERMARKET-2026') {
        sessionStorage.setItem('am_auth', 'true');
        sessionStorage.setItem('am_ig',   '@twoja_marka');
        if (loginErr) loginErr.style.display = 'none';
        showDash('@twoja_marka');
      } else {
        if (loginErr) loginErr.style.display = 'block';
        lToken?.focus();
      }
    });
  }

  if (btnLogout) btnLogout.addEventListener('click', () => {
    sessionStorage.clear();
    if (growthChart) { growthChart.destroy(); growthChart = null; }
    showLogin();
  });

  checkAuth();

  /* ─────────────────────────────────────────────
     5. CAMPAIGN COUNTDOWN (KPI card 3)
  ───────────────────────────────────────────── */
  function startCampaignTimer() {
    if (!kpiTimer) return;
    const target = Date.now() + (4 * 86400 + 12 * 3600 + 47 * 60 + 33) * 1000;

    function pad(n) { return String(Math.max(0, n)).padStart(2, '0'); }
    function tick() {
      const diff = Math.max(0, target - Date.now());
      if (!diff) { kpiTimer.textContent = '⏰ Losowanie zakończone'; return; }
      const d = Math.floor(diff / 86400000);
      const h = Math.floor((diff % 86400000) / 3600000);
      const m = Math.floor((diff % 3600000)  / 60000);
      const s = Math.floor((diff % 60000)    / 1000);
      kpiTimer.textContent = `⏰ Koniec za ${pad(d)}d ${pad(h)}h ${pad(m)}m ${pad(s)}s`;
    }
    tick();
    timerInterval = setInterval(tick, 1000);
  }

  /* ─────────────────────────────────────────────
     6. ACTIVITY FEED
  ───────────────────────────────────────────── */
  const localRegs = JSON.parse(localStorage.getItem('am_registrations') || '[]');
  const activities = [];
  localRegs.forEach(reg => {
    activities.push({
      t: 'p',
      tx: 'Prawdziwy zapis',
      sub: `${reg.name} (${reg.ig}) • ${reg.city}`
    });
  });
  
  const defaultActivities = [
    { t:'g', tx:'Nowy obserwujący', sub:'@marta_kowalska • Wrocław' },
    { t:'p', tx:'Rejestracja uczestnika', sub:'Formularz lead — 14:42:07' },
    { t:'g', tx:'+12 obserwujących', sub:'Burst z Instagram Reels' },
    { t:'b', tx:'Webhook płatności', sub:'Nowy sponsor • Starter 999 PLN' },
    { t:'g', tx:'Nowy obserwujący', sub:'@jan_nowak_foto • Kraków' },
    { t:'p', tx:'Udostępnienie posta', sub:'Zasięg +3 200 unikalnych' },
    { t:'g', tx:'+8 obserwujących', sub:'Polecenie przez znajomych' },
    { t:'b', tx:'API — Status Check', sub:'Webhook 200 OK' },
    { t:'g', tx:'Nowy obserwujący', sub:'@agnieszka.fit • Warszawa' },
    { t:'p', tx:'Story view', sub:'+840 wyświetleń w ciągu 5 min' },
    { t:'g', tx:'+21 obserwujących', sub:'Spike po publikacji lista' },
    { t:'b', tx:'Raport dobowy', sub:'Wygenerowany — pobierz PDF' },
  ];
  activities.push(...defaultActivities);

  let actIdx = 0;

  function timeStr() {
    const now = new Date();
    return `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}:${String(now.getSeconds()).padStart(2,'0')}`;
  }

  function addActivity(act) {
    if (!actFeed) return;
    const div = document.createElement('div');
    div.className = 'act-item';
    div.style.opacity = '0';
    div.style.transform = 'translateY(8px)';
    div.innerHTML = `
      <div class="act-dot ${act.t}"></div>
      <div>
        <div class="act-text"><strong>${act.tx}</strong></div>
        <div class="act-text">${act.sub}</div>
        <div class="act-time">${timeStr()}</div>
      </div>`;
    actFeed.prepend(div);
    requestAnimationFrame(() => {
      div.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      div.style.opacity = '1';
      div.style.transform = 'translateY(0)';
    });
    // Keep max 12 items
    while (actFeed.children.length > 12) actFeed.removeChild(actFeed.lastChild);
  }

  function startActivityFeed() {
    if (!actFeed) return;
    // Load initial items
    for (let i = 0; i < 6; i++) {
      const act = activities[(actIdx++) % activities.length];
      const div = document.createElement('div');
      div.className = 'act-item';
      div.innerHTML = `
        <div class="act-dot ${act.t}"></div>
        <div>
          <div class="act-text"><strong>${act.tx}</strong></div>
          <div class="act-text">${act.sub}</div>
          <div class="act-time">${timeStr()}</div>
        </div>`;
      actFeed.appendChild(div);
    }

    // Live updates every 3-6 seconds
    actInterval = setInterval(() => {
      const act = activities[(actIdx++) % activities.length];
      addActivity(act);
    }, Math.random() * 3000 + 3000);
  }

  /* ─────────────────────────────────────────────
     7. CHART.JS PREMIUM
  ───────────────────────────────────────────── */
  function initChart() {
    const ctx = document.getElementById('growthChart');
    if (!ctx) return;
    if (growthChart) growthChart.destroy();

    const c2d   = ctx.getContext('2d');
    const pink  = '#F43F5E';
    const blue  = '#3B82F6';

    const gPink = c2d.createLinearGradient(0, 0, 0, 290);
    gPink.addColorStop(0, 'rgba(244,63,94,0.42)');
    gPink.addColorStop(0.6, 'rgba(244,63,94,0.08)');
    gPink.addColorStop(1, 'rgba(244,63,94,0.00)');

    const gBlue = c2d.createLinearGradient(0, 0, 0, 290);
    gBlue.addColorStop(0, 'rgba(59,130,246,0.2)');
    gBlue.addColorStop(1, 'rgba(59,130,246,0.00)');

    const totalSubs = parseInt(localStorage.getItem('am_total_subscribers') || '12840');
    const extraSubs = totalSubs - 12840;
    const currentFollowers = 34250 + (extraSubs > 0 ? extraSubs : 0);

    const labels   = ['Dzień -3','Dzień -2','Dzień -1','Start','Dzień 2','Dzień 3','Live'];
    const organic  = [10000, 10050, 10110, 10160, 10210, 10255, 10305];
    const giveaway = [10000, 10050, 10110, 15400, 22100, 29800, currentFollowers];

    const dashLatest = document.getElementById('dash-latest');
    if (dashLatest) dashLatest.textContent = currentFollowers.toLocaleString('pl-PL');

    growthChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'Aftermarket Giveaway',
            data: giveaway,
            borderColor: pink,
            borderWidth: 3,
            backgroundColor: gPink,
            fill: true,
            tension: 0.48,
            pointBackgroundColor: '#0F0F1A',
            pointBorderColor: pink,
            pointBorderWidth: 2.5,
            pointRadius: 5,
            pointHoverRadius: 8,
            pointHoverBackgroundColor: pink,
          },
          {
            label: 'Prognoza organiczna',
            data: organic,
            borderColor: blue,
            borderWidth: 2,
            borderDash: [6, 4],
            backgroundColor: gBlue,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#0F0F1A',
            pointBorderColor: blue,
            pointBorderWidth: 2,
            pointRadius: 3,
            pointHoverRadius: 6,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              color: '#666680',
              font: { family: 'Inter', size: 11, weight: '700' },
              boxWidth: 14,
              boxHeight: 2,
              padding: 24,
              usePointStyle: false,
            },
          },
          tooltip: {
            backgroundColor: '#0E0E1A',
            borderColor: 'rgba(255,255,255,0.08)',
            borderWidth: 1,
            titleColor: '#fff',
            bodyColor: '#888899',
            titleFont: { family: 'Inter', weight: '800', size: 12 },
            bodyFont:  { family: 'Inter', size: 11 },
            padding: 14,
            cornerRadius: 14,
            callbacks: {
              label: ctx => ` ${ctx.dataset.label}: ${ctx.raw.toLocaleString('pl-PL')}`,
            },
          },
        },
        scales: {
          x: {
            grid:  { color: 'rgba(255,255,255,0.025)', drawBorder: false },
            ticks: { color: '#3E3E52', font: { family: 'Inter', size: 10 }, padding: 15 },
            border: { display: false },
          },
          y: {
            suggestedMin: 8000,
            grid:  { color: 'rgba(255,255,255,0.025)', drawBorder: false },
            ticks: { color: '#3E3E52', font: { family: 'Inter', size: 10 }, padding: 10,
                     callback: v => v.toLocaleString('pl-PL') },
            border: { display: false },
          },
        },
      },
    });
  }

  /* ─────────────────────────────────────────────
     8. REFRESH STATS
  ───────────────────────────────────────────── */
  if (btnRefresh) {
    btnRefresh.addEventListener('click', () => {
      btnRefresh.disabled = true;
      btnRefresh.textContent = '⟳ Pobieranie…';

      setTimeout(() => {
        const totalSubs = parseInt(localStorage.getItem('am_total_subscribers') || '12840');
        
        if (kpiLeads) {
          countUp(kpiLeads, totalSubs);
        }

        const extraSubs = totalSubs - 12840;
        const currentFollowers = 34250 + (extraSubs > 0 ? extraSubs : 0);

        if (dashLatest && growthChart) {
          countUp(dashLatest, currentFollowers, 800);
          growthChart.data.datasets[0].data[6] = currentFollowers;
          growthChart.update('active');
        }

        // Reload real-time registrations in feed
        const updatedRegs = JSON.parse(localStorage.getItem('am_registrations') || '[]');
        if (actFeed && updatedRegs.length > 0) {
          actFeed.innerHTML = '';
          updatedRegs.forEach(reg => {
            const div = document.createElement('div');
            div.className = 'act-item';
            div.innerHTML = `
              <div class="act-dot p"></div>
              <div>
                <div class="act-text"><strong>Prawdziwy zapis</strong></div>
                <div class="act-text">${reg.name} (${reg.ig}) • ${reg.city}</div>
                <div class="act-time">${reg.time}</div>
              </div>`;
            actFeed.appendChild(div);
          });
        }

        btnRefresh.disabled = false;
        btnRefresh.textContent = '⟳ Odśwież';
      }, 950);
    });
  }

});
