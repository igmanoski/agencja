/* dashboard.js — Auth | REST API | Chart.js | CountUp | Timer | Feed */

document.addEventListener('DOMContentLoaded', () => {

  // Guard: uruchamiaj tylko na stronie dashboardu
  if (!document.getElementById('view-login') && !document.getElementById('view-dash')) return;
  if (typeof Chart === 'undefined') {
    console.error('[Aftermarket] Chart.js niedostępne!');
  }

  /* ─────────────────────────────────────────────
     1. ZMIENNE GLOBALNE
  ───────────────────────────────────────────── */
  let growthChart   = null;
  let timerInterval = null;
  let actInterval   = null;
  let statsData     = {};

  /* ─────────────────────────────────────────────
     2. SCROLL REVEAL
  ───────────────────────────────────────────── */
  function revealAll() {
    const io = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) { entry.target.classList.add('in'); obs.unobserve(entry.target); }
      });
    }, { threshold: 0.06, rootMargin: '0px 0px -30px 0px' });
    document.querySelectorAll('.rev:not(.in)').forEach(el => io.observe(el));
  }

  /* ─────────────────────────────────────────────
     3. TILT
  ───────────────────────────────────────────── */
  function initTilt() {
    document.querySelectorAll('.tilt').forEach(card => {
      let raf, tx = 0, ty = 0, cx = 0, cy = 0;
      function loop() {
        cx = cx + (tx - cx) * 0.12; cy = cy + (ty - cy) * 0.12;
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
     4. COUNTUP
  ───────────────────────────────────────────── */
  function countUp(el, target, duration = 900) {
    if (!el) return;
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
     5. AJAX LOGOWANIE
  ───────────────────────────────────────────── */
  const loginForm = document.getElementById('login-form');
  const loginErr  = document.getElementById('login-err');
  const loginBtn  = document.getElementById('login-btn');

  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email    = document.getElementById('l-email')?.value.trim()    || '';
      const password = document.getElementById('l-password')?.value         || '';

      if (loginErr)  loginErr.style.display = 'none';
      if (loginBtn) { loginBtn.disabled = true; loginBtn.textContent = 'Logowanie…'; }

      try {
        const body = new URLSearchParams({
          action:   'aftermarket_login',
          nonce:    AftermarketConfig?.loginNonce || '',
          email,
          password,
        });

        const res  = await fetch(AftermarketConfig?.ajaxUrl || '/wp-admin/admin-ajax.php', {
          method:      'POST',
          credentials: 'same-origin',
          headers:     { 'Content-Type': 'application/x-www-form-urlencoded' },
          body,
        });
        const data = await res.json();

        if (data.success) {
          // Przeładuj stronę — PHP zobaczy zalogowanego użytkownika i pokaże dashboard
          location.reload();
        } else {
          if (loginErr) { loginErr.style.display = 'block'; loginErr.textContent = '❌ ' + (data.data?.message || 'Niepoprawny email lub hasło.'); }
          if (loginBtn) { loginBtn.disabled = false; loginBtn.textContent = 'Zaloguj się do Panelu →'; }
        }
      } catch (err) {
        if (loginErr) { loginErr.style.display = 'block'; loginErr.textContent = '❌ Błąd połączenia. Spróbuj ponownie.'; }
        if (loginBtn) { loginBtn.disabled = false; loginBtn.textContent = 'Zaloguj się do Panelu →'; }
      }
    });
  }

  /* ─────────────────────────────────────────────
     6. INICJALIZACJA DASHBOARDU (tylko gdy PHP dał dostęp)
  ───────────────────────────────────────────── */
  const viewDash = document.getElementById('view-dash');

  if (viewDash?.classList.contains('show')) {
    setTimeout(revealAll, 80);
    setTimeout(initTilt,  80);
    loadDashboardData();
  } else {
    // Strona logowania — animacje
    setTimeout(revealAll, 80);
    setTimeout(initTilt,  80);
  }

  /* ─────────────────────────────────────────────
     7. POBIERZ DANE Z REST API
  ───────────────────────────────────────────── */
  async function loadDashboardData() {
    try {
      const res = await fetch('/wp-json/aftermarket/v1/my-stats', {
        credentials: 'same-origin',
        headers:     { 'Accept': 'application/json' },
      });

      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();

      if (!data.authenticated || !data.has_access) {
        console.warn('[Aftermarket] Brak dostępu z API.');
        return;
      }

      statsData = data;
      populateDashboard(data);

    } catch (err) {
      console.error('[Aftermarket] Błąd REST API:', err);
      // Wyświetl awaryjne dane
      populateDashboard({
        ig_username:       document.getElementById('view-dash')?.dataset.ig || '@twoja_marka',
        current_followers: 0,
        followers_start:   0,
        leads_generated:   0,
        campaign_end_date: new Date(Date.now() + 7 * 86400 * 1000).toISOString(),
        followers_history: [],
        activity_feed:     [],
      });
    }
  }

  /* ─────────────────────────────────────────────
     8. WYPEŁNIJ DASHBOARD DANYMI
  ───────────────────────────────────────────── */
  function populateDashboard(data) {
    // IG handle
    const dashIG = document.getElementById('dash-ig');
    if (dashIG && data.ig_username) dashIG.textContent = data.ig_username;

    // Obserwujący
    const dashLatest = document.getElementById('dash-latest');
    const dashChart  = document.getElementById('dash-followers-chart');
    if (data.current_followers) {
      if (dashLatest) countUp(dashLatest, data.current_followers, 1200);
      if (dashChart)  countUp(dashChart,  data.current_followers, 1200);
    }

    // Wzrost
    const growthInfo = document.getElementById('kpi-growth-info');
    if (growthInfo && data.current_followers && data.followers_start) {
      const gained = data.current_followers - data.followers_start;
      growthInfo.textContent = '▲ +' + gained.toLocaleString('pl-PL') + ' od startu kampanii';
    }

    // Leady
    const kpiLeads = document.getElementById('kpi-leads');
    if (kpiLeads && data.leads_generated) countUp(kpiLeads, data.leads_generated, 900);

    const leadsInfo = document.getElementById('kpi-leads-info');
    if (leadsInfo && data.leads_generated) {
      const today = Math.floor(data.leads_generated * 0.11);
      leadsInfo.textContent = '▲ +' + today.toLocaleString('pl-PL') + ' dzisiaj';
    }

    // Subtitle wykresu
    const chartSub = document.getElementById('chart-subtitle');
    if (chartSub && data.ig_username) {
      chartSub.textContent = data.ig_username + ' — wzrost vs. prognoza organiczna';
    }

    // Uruchom wykres i timer
    setTimeout(() => {
      initChart(data.followers_history, data.current_followers, data.followers_start);
    }, 200);

    if (data.campaign_end_date) {
      startCampaignTimer(new Date(data.campaign_end_date).getTime());
    }

    startActivityFeed(data.activity_feed);
  }

  /* ─────────────────────────────────────────────
     9. CHART.JS
  ───────────────────────────────────────────── */
  function initChart(history, currentFollowers, followersStart) {
    if (typeof Chart === 'undefined') return;
    const ctx = document.getElementById('growthChart');
    if (!ctx) return;
    if (growthChart) growthChart.destroy();

    const c2d  = ctx.getContext('2d');
    const pink = '#F43F5E';
    const blue = '#3B82F6';

    const gPink = c2d.createLinearGradient(0, 0, 0, 290);
    gPink.addColorStop(0, 'rgba(244,63,94,0.42)');
    gPink.addColorStop(1, 'rgba(244,63,94,0.00)');

    const gBlue = c2d.createLinearGradient(0, 0, 0, 290);
    gBlue.addColorStop(0, 'rgba(59,130,246,0.2)');
    gBlue.addColorStop(1, 'rgba(59,130,246,0.00)');

    // Dane z REST API
    let labels  = [];
    let giveaway = [];
    let organic  = [];

    if (history && history.length > 0) {
      history.forEach(point => {
        labels.push(point.label || point.date);
        giveaway.push(point.count);
      });
      // Prognoza organiczna (bez kampanii — liniowy wzrost ~1%)
      const startVal = followersStart || history[0]?.count || 0;
      const steps = history.length;
      for (let i = 0; i < steps; i++) {
        organic.push(Math.round(startVal * Math.pow(1.001, i)));
      }
    } else {
      // Fallback — brak danych
      labels  = ['Brak danych'];
      giveaway = [currentFollowers || 0];
      organic  = [currentFollowers || 0];
    }

    growthChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'Wzrost z Aftermarket',
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
            labels: { color: '#666680', font: { family: 'Inter', size: 11, weight: '700' }, boxWidth: 14, boxHeight: 2, padding: 24 },
          },
          tooltip: {
            backgroundColor: '#0E0E1A',
            borderColor: 'rgba(255,255,255,0.08)',
            borderWidth: 1,
            titleColor: '#fff',
            bodyColor: '#888899',
            padding: 14,
            cornerRadius: 14,
            callbacks: {
              label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.raw.toLocaleString('pl-PL'),
            },
          },
        },
        scales: {
          x: {
            grid:   { color: 'rgba(255,255,255,0.025)' },
            ticks:  { color: '#3E3E52', font: { family: 'Inter', size: 10 }, padding: 15 },
            border: { display: false },
          },
          y: {
            grid:   { color: 'rgba(255,255,255,0.025)' },
            ticks:  { color: '#3E3E52', font: { family: 'Inter', size: 10 }, padding: 10, callback: v => v.toLocaleString('pl-PL') },
            border: { display: false },
          },
        },
      },
    });
  }

  /* ─────────────────────────────────────────────
     10. TIMER
  ───────────────────────────────────────────── */
  function startCampaignTimer(targetTs) {
    const kpiTimer = document.getElementById('kpi-timer');
    if (!kpiTimer || !targetTs) return;
    if (timerInterval) clearInterval(timerInterval);

    function pad(n) { return String(Math.max(0, n)).padStart(2, '0'); }
    function tick() {
      const diff = Math.max(0, targetTs - Date.now());
      if (!diff) { kpiTimer.textContent = '🏁 Kampania zakończona'; clearInterval(timerInterval); return; }
      const d = Math.floor(diff / 86400000);
      const h = Math.floor((diff % 86400000) / 3600000);
      const m = Math.floor((diff % 3600000)  / 60000);
      const s = Math.floor((diff % 60000)    / 1000);
      kpiTimer.textContent = '⏰ Koniec za ' + pad(d) + 'd ' + pad(h) + 'h ' + pad(m) + 'm ' + pad(s) + 's';
    }
    tick();
    timerInterval = setInterval(tick, 1000);
  }

  /* ─────────────────────────────────────────────
     11. ACTIVITY FEED
  ───────────────────────────────────────────── */
  function timeStr() {
    const n = new Date();
    return String(n.getHours()).padStart(2, '0') + ':' + String(n.getMinutes()).padStart(2, '0') + ':' + String(n.getSeconds()).padStart(2, '0');
  }

  function addActivity(feed, act) {
    if (!feed) return;
    const div = document.createElement('div');
    div.className = 'act-item';
    div.style.opacity = '0';
    div.style.transform = 'translateY(8px)';
    div.innerHTML = `
      <div class="act-dot ${act.type || act.t || 'g'}"></div>
      <div>
        <div class="act-text"><strong>${act.text || act.tx || ''}</strong></div>
        <div class="act-text">${act.sub || ''}</div>
        <div class="act-time">${timeStr()}</div>
      </div>`;
    feed.prepend(div);
    requestAnimationFrame(() => {
      div.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      div.style.opacity = '1';
      div.style.transform = 'translateY(0)';
    });
    while (feed.children.length > 12) feed.removeChild(feed.lastChild);
  }

  function startActivityFeed(activities) {
    const feed = document.getElementById('act-feed');
    if (!feed) return;
    if (actInterval) clearInterval(actInterval);

    const defaultActs = [
      { type: 'g', text: 'Nowy obserwujący',      sub: 'Organiczny wzrost z kampanii' },
      { type: 'p', text: 'Rejestracja uczestnika', sub: 'Formularz lead' },
      { type: 'g', text: '+12 obserwujących',      sub: 'Burst z Instagram Reels' },
      { type: 'b', text: 'Webhook płatności',      sub: 'Kampania aktywna — 200 OK' },
      { type: 'g', text: 'Nowy obserwujący',       sub: 'Polecenie przez znajomych' },
      { type: 'p', text: 'Story view',             sub: '+840 wyświetleń w 5 min' },
    ];

    const list = (activities && activities.length > 0) ? [...activities, ...defaultActs] : defaultActs;
    let idx = 0;

    // Załaduj 5 początkowych
    for (let i = 0; i < Math.min(5, list.length); i++) {
      const act = list[idx++ % list.length];
      const div = document.createElement('div');
      div.className = 'act-item';
      div.innerHTML = `
        <div class="act-dot ${act.type || 'g'}"></div>
        <div>
          <div class="act-text"><strong>${act.text || ''}</strong></div>
          <div class="act-text">${act.sub || ''}</div>
          <div class="act-time">${timeStr()}</div>
        </div>`;
      feed.appendChild(div);
    }

    // Aktualizacje co 4-7 sekund
    function scheduleNext() {
      const delay = Math.random() * 3000 + 4000;
      actInterval = setTimeout(() => {
        addActivity(feed, list[idx++ % list.length]);
        scheduleNext();
      }, delay);
    }
    scheduleNext();
  }

  /* ─────────────────────────────────────────────
     12. ODŚWIEŻ
  ───────────────────────────────────────────── */
  const btnRefresh = document.getElementById('btn-refresh');
  if (btnRefresh) {
    btnRefresh.addEventListener('click', async () => {
      btnRefresh.disabled = true;
      btnRefresh.textContent = '⟳ Pobieranie…';

      await loadDashboardData();

      setTimeout(() => {
        btnRefresh.disabled = false;
        btnRefresh.textContent = '⟳ Odśwież';
      }, 1000);
    });
  }

});
