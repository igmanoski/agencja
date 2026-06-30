<?php
/**
 * Template Name: Dashboard Page
 *
 * Autentykacja odbywa się po stronie PHP.
 * dashboard.js obsługuje tylko UI + pobieranie danych z REST API.
 */

// Sprawdź dostęp po stronie PHP (nie JS)
$is_logged_in = is_user_logged_in();
$user_id      = get_current_user_id();
$has_access   = $is_logged_in && aftermarket_user_has_access($user_id);

// auth_state: 'login' | 'no_access' | 'dashboard'
if (!$is_logged_in) {
    $auth_state = 'login';
} elseif (!$has_access) {
    $auth_state = 'no_access';
} else {
    $auth_state = 'dashboard';
}

// Dane użytkownika (do przekazania w HTML data-attributes)
$user        = $is_logged_in ? get_userdata($user_id) : null;
$ig_username = ($is_logged_in && function_exists('get_field'))
    ? (get_field('am_ig_username', 'user_' . $user_id) ?: '@twoja_marka')
    : '@twoja_marka';

get_header();
?>

<!-- ████████ LOGIN / NO-ACCESS GATE ████████ -->
<main id="view-login" class="pv <?php echo $auth_state !== 'dashboard' ? 'show' : ''; ?>" style="padding-top:80px;">
  <div class="scene">
    <div class="orb orb-pk" style="top:-100px;left:50%;width:800px;height:600px;transform:translateX(-50%);opacity:.26;"></div>
  </div>

  <section class="wrap" style="position:relative;z-index:1;max-width:520px;">
    <div class="card login-card tilt rev">
      <div class="ti">

        <?php if ($auth_state === 'no_access'): ?>
          <!-- ── Brak dostępu ── -->
          <div style="text-align:center;margin-bottom:28px;">
            <div class="chip" style="display:table;margin:0 auto 20px;">🔒 BRAK DOSTĘPU</div>
            <h2 style="font-size:1.9rem;text-transform:uppercase;margin-bottom:10px;">Brak aktywnej kampanii</h2>
            <p style="margin-bottom:28px;">Twoje konto nie ma przypisanego pakietu reklamowego. Kup pakiet i uzyskaj dostęp do panelu.</p>
            <a href="<?php echo esc_url(home_url('/#sponsors')); ?>" class="btn btn-p btn-lg" style="display:block;text-align:center;">Kup pakiet Sponsor</a>
            <div style="margin-top:20px;font-size:.8rem;color:var(--t3);">
              Jesteś zalogowany jako <strong style="color:var(--t1);"><?php echo esc_html($user->user_email); ?></strong>
              &nbsp;·&nbsp;
              <a href="<?php echo esc_url(home_url('/?am_logout=1')); ?>" style="color:var(--pink);font-weight:700;">Wyloguj się</a>
            </div>
          </div>

        <?php else: ?>
          <!-- ── Formularz logowania ── -->
          <div style="text-align:center;margin-bottom:28px;">
            <div class="chip" style="display:table;margin:0 auto 20px;">🔒 SECURE PARTNER PORTAL</div>
            <h2 style="font-size:1.9rem;text-transform:uppercase;margin-bottom:10px;">Panel Partnera</h2>
            <p>Zaloguj się emailem i hasłem które otrzymałeś po zakupie pakietu.</p>
          </div>

          <div id="login-err" class="err" style="display:none;">❌ Niepoprawny email lub hasło.</div>

          <form id="login-form" novalidate>
            <div class="fg">
              <label class="fl" for="l-email">Adres e-mail</label>
              <input type="email" id="l-email" class="fi" placeholder="np. kontakt@marka.pl" required autocomplete="email">
            </div>
            <div class="fg">
              <label class="fl" for="l-password">Hasło</label>
              <input type="password" id="l-password" class="fi" placeholder="Hasło z emaila powitalnego" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-p btn-block btn-lg" id="login-btn" style="margin-top:4px;">
              Zaloguj się do Panelu →
            </button>
          </form>

          <div style="margin-top:20px;text-align:center;font-size:.8rem;color:var(--t3);">
            Nie masz jeszcze dostępu?
            <a href="<?php echo esc_url(home_url('/#sponsors')); ?>" style="color:var(--t1);font-weight:700;">Kup pakiet sponsorski</a>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </section>
</main>


<!-- ████████ DASHBOARD PANEL ████████ -->
<main id="view-dash" class="pv <?php echo $auth_state === 'dashboard' ? 'show' : ''; ?>" style="padding-bottom:80px;"
  data-ig="<?php echo esc_attr($ig_username); ?>">
  <div class="scene">
    <div class="orb orb-bl" style="top:-80px;left:5%;width:650px;height:440px;opacity:.18;"></div>
    <div class="orb orb-pk" style="bottom:-60px;right:5%;width:750px;height:500px;opacity:.2;"></div>
  </div>

  <section class="wrap sec" style="position:relative;z-index:1;">

    <!-- Dashboard header -->
    <div class="dash-hdr rev">
      <div>
        <div class="chip"><span class="chip-dot"></span>PARTNER ANALYTICS HUB</div>
        <h1 style="font-size:clamp(1.8rem,4vw,2.6rem);text-transform:uppercase;margin:10px 0 8px;">Panel Statystyk Sponsora</h1>
        <p>Podgląd wzrostu profilu <strong id="dash-ig" style="color:var(--pink);"><?php echo esc_html($ig_username); ?></strong> w czasie rzeczywistym.</p>
      </div>
      <div class="dash-actions">
        <a href="<?php echo esc_url(home_url('/?am_logout=1')); ?>" id="btn-logout" class="btn btn-d">Wyloguj</a>
      </div>
    </div>

    <!-- KPI Row -->
    <div class="kpi-row">
      <!-- KPI 1: Obserwujący -->
      <div class="card kpi-pk tilt rev">
        <div class="ti">
          <div class="kpi-l">Obserwujący (aktualni)</div>
          <div class="kpi-v"><span id="dash-latest">—</span></div>
          <div class="kpi-t" id="kpi-growth-info">▲ Ładowanie…</div>
          <div class="kpi-spark">
            <svg viewBox="0 0 120 36" preserveAspectRatio="none" style="width:100%;height:100%">
              <defs>
                <linearGradient id="sg1" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#F43F5E" stop-opacity="0.4"/>
                  <stop offset="100%" stop-color="#F43F5E" stop-opacity="0"/>
                </linearGradient>
              </defs>
              <path d="M0 30 C15 28,30 26,45 20 C60 14,75 10,90 6 C105 2,115 1,120 0 L120 36 L0 36Z" fill="url(#sg1)"/>
              <path d="M0 30 C15 28,30 26,45 20 C60 14,75 10,90 6 C105 2,115 1,120 0" fill="none" stroke="#F43F5E" stroke-width="2"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- KPI 2: Przyrost -->
      <div class="card kpi-bl tilt rev d1">
        <div class="ti">
          <div class="kpi-l">Przyrost obserwujących</div>
          <div class="kpi-v" id="kpi-leads">—</div>
          <div class="kpi-t" id="kpi-leads-info">▲ Ładowanie…</div>
          <div class="kpi-spark" style="display:flex;align-items:flex-end;gap:3px;padding-top:6px;">
            <div style="background:rgba(59,130,246,.2);height:18px;width:14%;border-radius:3px;"></div>
            <div style="background:rgba(59,130,246,.3);height:24px;width:14%;border-radius:3px;"></div>
            <div style="background:rgba(59,130,246,.4);height:20px;width:14%;border-radius:3px;"></div>
            <div style="background:rgba(59,130,246,.55);height:28px;width:14%;border-radius:3px;"></div>
            <div style="background:rgba(59,130,246,.7);height:32px;width:14%;border-radius:3px;"></div>
            <div style="background:#3B82F6;height:36px;width:14%;border-radius:3px;box-shadow:0 0 10px rgba(59,130,246,.5);"></div>
          </div>
        </div>
      </div>

      <!-- KPI 3: Timer -->
      <div class="card kpi-gr tilt rev d2">
        <div class="ti">
          <div class="kpi-l">Status kampanii</div>
          <div class="kpi-v" style="color:var(--pink);">LIVE DRAWING</div>
          <div class="kpi-t neutral" id="kpi-timer">⏰ Ładowanie…</div>
          <div style="margin-top:12px;">
            <div class="live-badge"><div class="pulse-dot" style="width:7px;height:7px;"></div>Kampania aktywna</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart + Activity Feed -->
    <div class="dash-main rev d1">
      <div class="card chart-card tilt">
        <div class="ti">
          <div class="chart-hdr">
            <div>
              <h3 style="font-size:1.15rem;text-transform:uppercase;margin-bottom:6px;">Trajektoria wzrostu konta — Live</h3>
              <p style="font-size:.84rem;" id="chart-subtitle">Ładowanie danych…</p>
            </div>
            <div class="chart-cur">STAN: <span id="dash-followers-chart" style="color:var(--pink);">—</span></div>
          </div>
          <div class="chart-box"><canvas id="growthChart"></canvas></div>
        </div>
      </div>

      <div class="card" style="display:flex;flex-direction:column;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
          <h3 style="font-size:1.05rem;text-transform:uppercase;">Aktywność Live</h3>
          <div class="live-badge" style="font-size:.62rem;"><div class="pulse-dot" style="width:6px;height:6px;"></div>Na żywo</div>
        </div>
        <div class="act-feed" id="act-feed">
          <!-- wypełnia dashboard.js -->
        </div>
      </div>
    </div>

  </section>
</main>

<?php get_footer(); ?>
