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

    <?php if (current_user_can('manage_options')) : 
        $sponsors = get_users(array(
            'meta_key'     => 'am_ig_username',
            'meta_compare' => 'EXISTS',
            'number'       => 100,
        ));
    ?>
    <!-- ════ ADMIN MONITORING PANEL ════ -->
    <style>
        .am-spinner {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 2px solid rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            border-top-color: var(--pink);
            animation: am-spin 0.8s linear infinite;
            vertical-align: middle;
        }
        @keyframes am-spin {
            to { transform: rotate(360deg); }
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
    <div class="card rev d2" style="margin-top: 30px; padding: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 15px; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div>
                <div class="chip" style="background: rgba(244,63,94,0.1); color: var(--pink); font-weight: 700;">🛡️ Panel Administratora</div>
                <h3 style="font-size: 1.25rem; text-transform: uppercase; margin-top: 5px; margin-bottom: 0;">Monitoring Kont Sponsorskich</h3>
            </div>
            <div style="font-size: 0.8rem; color: rgba(255,255,255,0.45);">
                Wszystkich profili: <strong style="color: #fff;"><?php echo count($sponsors); ?></strong>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem; text-align: left; min-width: 800px;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.45);">
                        <th style="padding: 10px 5px;">Nazwa / Email</th>
                        <th style="padding: 10px 5px;">Profil Instagram</th>
                        <th style="padding: 10px 5px; text-align: center;">Start</th>
                        <th style="padding: 10px 5px; text-align: center;">Aktualnie</th>
                        <th style="padding: 10px 5px; text-align: center;">Przyrost</th>
                        <th style="padding: 10px 5px; text-align: center;">Ostatnia synchronizacja</th>
                        <th style="padding: 10px 5px; text-align: center;">Błędy / Status</th>
                        <th style="padding: 10px 5px; text-align: right;">Akcja</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($sponsors as $s) :
                        $s_id = $s->ID;
                        $s_email = $s->user_email;
                        $s_ig = get_user_meta($s_id, 'am_ig_username', true);
                        $s_start = (int)get_user_meta($s_id, 'am_followers_start', true);
                        $s_current = (int)get_user_meta($s_id, 'am_current_followers', true);
                        $s_last_update = get_user_meta($s_id, 'am_ig_last_update', true);
                        $s_error = get_user_meta($s_id, 'am_ig_error', true);
                        
                        $s_growth = $s_current - $s_start;
                        $s_time_formatted = $s_last_update ? date('d.m.Y H:i', $s_last_update) : 'Nigdy';
                    ?>
                        <tr id="admin-user-row-<?php echo $s_id; ?>" style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.2s;">
                            <td style="padding: 12px 5px;">
                                <strong><?php echo esc_html($s->display_name); ?></strong><br>
                                <span style="font-size: 0.75rem; color: rgba(255,255,255,0.4);"><?php echo esc_html($s_email); ?></span>
                            </td>
                            <td style="padding: 12px 5px; color: var(--pink); font-weight: 700;">
                                <a href="https://instagram.com/<?php echo esc_attr(ltrim($s_ig, '@')); ?>" target="_blank" style="color: inherit; text-decoration: none;">
                                    <?php echo esc_html($s_ig); ?> ↗
                                </a>
                            </td>
                            <td style="padding: 12px 5px; text-align: center; font-weight: 600;">
                                <?php echo number_format($s_start, 0, ',', ' '); ?>
                            </td>
                            <td style="padding: 12px 5px; text-align: center; font-weight: 700; color: #fff;" id="admin-curr-<?php echo $s_id; ?>">
                                <?php echo number_format($s_current, 0, ',', ' '); ?>
                            </td>
                            <td style="padding: 12px 5px; text-align: center; color: #10B981; font-weight: 700;" id="admin-growth-<?php echo $s_id; ?>">
                                <?php echo ($s_growth >= 0 ? '+' : '') . number_format($s_growth, 0, ',', ' '); ?>
                            </td>
                            <td style="padding: 12px 5px; text-align: center; color: rgba(255,255,255,0.6);" id="admin-time-<?php echo $s_id; ?>">
                                <?php echo esc_html($s_time_formatted); ?>
                            </td>
                            <td style="padding: 12px 5px; text-align: center;" id="admin-status-<?php echo $s_id; ?>">
                                <?php if ($s_error) : ?>
                                    <span style="color: #EF4444; font-size: 0.78rem; display: inline-block; background: rgba(239, 68, 68, 0.1); padding: 2px 6px; border-radius: 4px; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo esc_attr($s_error); ?>">
                                        ⚠️ Błąd API
                                    </span>
                                <?php else : ?>
                                    <span style="color: #10B981; font-size: 0.78rem; display: inline-block; background: rgba(16, 185, 129, 0.1); padding: 2px 6px; border-radius: 4px;">
                                        ✓ OK
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px 5px; text-align: right;">
                                <button class="btn btn-d btn-sm" onclick="adminForceRefresh(<?php echo $s_id; ?>)" id="btn-admin-refresh-<?php echo $s_id; ?>" style="padding: 6px 12px; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 8px;">
                                    <span>Odśwież API</span>
                                    <div class="am-spinner" id="sp-admin-refresh-<?php echo $s_id; ?>" style="width: 10px; height: 10px; border-width: 2px; display: none;"></div>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Skrypt obsługujący AJAX odświeżania -->
    <script>
    function adminForceRefresh(userId) {
        const btn = document.getElementById('btn-admin-refresh-' + userId);
        const spinner = document.getElementById('sp-admin-refresh-' + userId);
        const btnText = btn.querySelector('span');
        
        // Blokujemy przycisk i pokazujemy kręciołek
        btn.disabled = true;
        spinner.style.display = 'inline-block';
        btnText.textContent = 'Pobieranie...';
        
        const formData = new FormData();
        formData.append('action', 'am_admin_force_refresh_user');
        formData.append('user_id', userId);
        
        fetch('<?php echo esc_js(admin_url("admin-ajax.php")); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            spinner.style.display = 'none';
            btnText.textContent = 'Odśwież API';
            
            if (data.success) {
                // Aktualizujemy komórki w tabeli
                const followers = data.data.followers.toLocaleString('pl-PL');
                const growth = (data.data.growth >= 0 ? '+' : '') + data.data.growth.toLocaleString('pl-PL');
                
                document.getElementById('admin-curr-' + userId).textContent = followers;
                document.getElementById('admin-growth-' + userId).textContent = growth;
                document.getElementById('admin-time-' + userId).textContent = data.data.updated;
                
                // Aktualizujemy status na OK
                document.getElementById('admin-status-' + userId).innerHTML = `
                    <span style="color: #10B981; font-size: 0.78rem; display: inline-block; background: rgba(16, 185, 129, 0.1); padding: 2px 6px; border-radius: 4px;">
                        ✓ OK
                    </span>
                `;
                
                // Mrugnięcie wiersza na zielono
                const row = document.getElementById('admin-user-row-' + userId);
                row.style.background = 'rgba(16, 185, 129, 0.1)';
                setTimeout(() => { row.style.background = 'transparent'; }, 800);
            } else {
                alert('Błąd: ' + data.data);
                // Pokazujemy błąd w statusie
                document.getElementById('admin-status-' + userId).innerHTML = `
                    <span style="color: #EF4444; font-size: 0.78rem; display: inline-block; background: rgba(239, 68, 68, 0.1); padding: 2px 6px; border-radius: 4px;" title="${data.data}">
                        ⚠️ Błąd API
                    </span>
                `;
            }
        })
        .catch(err => {
            btn.disabled = false;
            spinner.style.display = 'none';
            btnText.textContent = 'Odśwież API';
            alert('Wystąpił błąd sieci: ' + err.message);
        });
    }
    </script>
    <?php endif; ?>

  </section>
</main>

<?php get_footer(); ?>
