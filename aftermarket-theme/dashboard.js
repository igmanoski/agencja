/* dashboard.js — Auth | REST API | Chart.js | CountUp | Timer | Feed */

function initDashboard() {

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

    // Zegar startuje NATYCHMIAST z daty wpisanej w panelu WP — bez oczekiwania na AJAX
    const preloadTs = parseInt(AftermarketConfig?.campaignEndTs || '0', 10);
    if (preloadTs > Date.now()) {
      startCampaignTimer(preloadTs);
    }

    loadDashboardData();
  } else {
    // Strona logowania — animacje
    setTimeout(revealAll, 80);
    setTimeout(initTilt,  80);
  }

  /* ─────────────────────────────────────────────
     7. POBIERZ DANE Z REST API
  ───────────────────────────────────────────── */
  async function loadDashboardData(force = false) {
    try {
      const body = new URLSearchParams({
        action: 'aftermarket_get_stats',
        nonce:  AftermarketConfig?.statsNonce || '',
      });
      if (force) body.set('force', '1');

      const res = await fetch(AftermarketConfig?.ajaxUrl || '/wp-admin/admin-ajax.php', {
        method:      'POST',
        credentials: 'same-origin',
        headers:     { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:        body.toString(),
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
      console.error('[Aftermarket] Błąd AJAX:', err);
      // Awaryjnie pokaż co najmniej zegar z daty WP (już uruchomiony wyżej)
      const dashIG = document.getElementById('dash-ig');
      if (dashIG && dashIG.textContent === '') dashIG.textContent = '@twoja_marka';
    }
  }

  /* ─────────────────────────────────────────────
     8. WYPEŁNIJ DASHBOARD DANYMI
  ───────────────────────────────────────────── */
  function populateDashboard(data) {
    // Alert błędu Instagrama (typos, private profiles, API blocks)
    let alertEl = document.getElementById('am-ig-alert');
    if (data.ig_error) {
      if (!alertEl) {
        alertEl = document.createElement('div');
        alertEl.id = 'am-ig-alert';
        alertEl.style.background = 'rgba(244,63,94,0.08)';
        alertEl.style.border = '1px solid rgba(244,63,94,0.22)';
        alertEl.style.color = '#FCA5A5';
        alertEl.style.padding = '14px 18px';
        alertEl.style.borderRadius = '11px';
        alertEl.style.fontSize = '0.84rem';
        alertEl.style.fontWeight = '700';
        alertEl.style.display = 'flex';
        alertEl.style.alignItems = 'center';
        alertEl.style.gap = '8px';
        alertEl.style.marginBottom = '28px';
        
        const parent = document.querySelector('#view-dash .wrap');
        if (parent) {
          parent.insertBefore(alertEl, parent.firstChild);
        }
      }
      alertEl.innerHTML = `⚠️ <span>${data.ig_error}</span>`;
      alertEl.style.display = 'flex';
    } else if (alertEl) {
      alertEl.style.display = 'none';
    }

    // IG handle
    const dashIG = document.getElementById('dash-ig');
    if (dashIG && data.ig_username) dashIG.textContent = data.ig_username;

    // Obserwujący
    const dashLatest = document.getElementById('dash-latest');
    const dashChart  = document.getElementById('dash-followers-chart');
    if (data.current_followers !== undefined && data.current_followers !== null) {
      if (dashLatest) countUp(dashLatest, data.current_followers, 1200);
      if (dashChart)  countUp(dashChart,  data.current_followers, 1200);
    } else {
      if (dashLatest) dashLatest.textContent = '0';
      if (dashChart)  dashChart.textContent = '0';
    }

    // Zamiast powielać przyrost, pokazujemy datę ostatniej synchronizacji
    const growthInfo = document.getElementById('kpi-growth-info');
    if (growthInfo && data.last_update) {
      growthInfo.textContent = 'Ostatnia aktualizacja: ' + data.last_update;
    }

    // Przyrost (w miejsce leadów)
    const kpiLeads = document.getElementById('kpi-leads');
    const gained = (data.current_followers !== undefined && data.followers_start !== undefined) 
      ? Math.max(0, data.current_followers - data.followers_start) 
      : 0;

    if (kpiLeads) {
      countUp(kpiLeads, gained, 900);
    }

    const leadsInfo = document.getElementById('kpi-leads-info');
    if (leadsInfo && data.followers_start !== undefined && data.followers_start !== null) {
      leadsInfo.textContent = 'Start z poziomu: ' + data.followers_start.toLocaleString('pl-PL') + ' obserwujących';
    } else if (leadsInfo) {
      leadsInfo.textContent = 'Brak danych startowych';
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

    if (data.campaign_end_ts || data.campaign_end_date) {
      startCampaignTimer(data.campaign_end_ts || new Date(data.campaign_end_date).getTime());
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

    // Dane rzeczywistego przyrostu
    let labels  = [];
    let giveaway = [];

    if (history && history.length > 0) {
      history.forEach(point => {
        labels.push(point.label || point.date);
        giveaway.push(point.count);
      });
    } else {
      // Fallback — brak danych
      labels  = ['Brak danych'];
      giveaway = [currentFollowers || 0];
    }

    growthChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'Liczba obserwujących',
            data: giveaway,
            borderColor: pink,
            borderWidth: 4,
            backgroundColor: gPink,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#0F0F1A',
            pointBorderColor: pink,
            pointBorderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 9,
            pointHoverBackgroundColor: pink,
            pointHoverBorderWidth: 3,
            shadowColor: 'rgba(244,63,94,0.5)',
            shadowBlur: 10
          }
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: {
            display: false // Ukrywamy legendę bo jest tylko jedna linia
          },
          tooltip: {
            backgroundColor: '#0E0E1A',
            borderColor: 'rgba(255,255,255,0.08)',
            borderWidth: 1,
            titleColor: '#fff',
            bodyColor: '#fff',
            bodyFont: { weight: 'bold' },
            padding: 14,
            cornerRadius: 14,
            callbacks: {
              label: ctx => ' Obserwujący: ' + ctx.raw.toLocaleString('pl-PL'),
            },
          },
        },
        scales: {
          x: {
            grid:   { display: false }, // Brak pionowych linii siatki
            ticks:  { color: '#888899', font: { family: 'Inter', size: 11 }, padding: 12 },
            border: { display: false },
          },
          y: {
            grid:   { color: 'rgba(255,255,255,0.03)', drawTicks: false }, // Bardzo subtelna pozioma siatka
            ticks:  { color: '#888899', font: { family: 'Inter', size: 11 }, padding: 12, callback: v => v.toLocaleString('pl-PL') },
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
    // Zatrzymaj poprzedni interwał jeśli istnieje
    if (actInterval) { clearInterval(actInterval); actInterval = null; }

    // Wyczyszczenie feedu
    feed.innerHTML = '';

    // Wyświetl TYLKO zdarzenia z bazy — bez cycling, bez interwału
    const list = (activities && activities.length > 0) ? activities : [];

    if (list.length === 0) {
      feed.innerHTML = '<div class="act-item" style="color:rgba(255,255,255,0.35);font-size:.82rem;padding:12px 0;">Brak aktywności do wyświetlenia.</div>';
      return;
    }

    list.forEach(act => {
      const div = document.createElement('div');
      div.className = 'act-item';
      div.innerHTML = `
        <div class="act-dot ${act.type || act.t || 'g'}"></div>
        <div>
          <div class="act-text"><strong>${act.text || act.tx || ''}</strong></div>
          <div class="act-text">${act.sub || ''}</div>
          <div class="act-time">${timeStr()}</div>
        </div>`;
      feed.appendChild(div);
    });
  }

  /* ─────────────────────────────────────────────
     12. ODŚWIEŻ
  ───────────────────────────────────────────── */
  const btnRefresh = document.getElementById('btn-refresh');
  if (btnRefresh) {
    btnRefresh.addEventListener('click', async () => {
      btnRefresh.disabled = true;
      btnRefresh.textContent = '⟳ Pobieranie…';

      await loadDashboardData(true);

      setTimeout(() => {
        btnRefresh.disabled = false;
        btnRefresh.textContent = '⟳ Odśwież';
      }, 1000);
    });
  }

}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initDashboard);
} else {
  initDashboard();
}
