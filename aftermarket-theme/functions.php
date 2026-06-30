<?php
/**
 * Aftermarket Theme Functions
 */

// Globalny rejestrator błędów zapisujący błędy do pliku
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        $log_message = "[" . date('Y-m-d H:i:s') . "] FATAL ERROR: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'] . "\n";
        // Zapis do głównego katalogu wp-content (zazwyczaj ma prawa zapisu)
        $wp_content_dir = dirname(dirname(__DIR__));
        file_put_contents($wp_content_dir . '/error_log_aftermarket.txt', $log_message, FILE_APPEND);
    }
});

/* ═══════════════════════════════════════════
   1. THEME SETUP
═══════════════════════════════════════════ */
function aftermarket_setup() {
    add_theme_support('woocommerce');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'gallery', 'caption', 'style', 'script'));
    register_nav_menus(array('primary' => __('Menu Główne', 'aftermarket')));
}
add_action('after_setup_theme', 'aftermarket_setup');

/* ═══════════════════════════════════════════
   2. SCRIPTS & STYLES
═══════════════════════════════════════════ */
function aftermarket_enqueue_scripts() {
    $version = time(); // Dynamiczna wersja eliminująca cache
    wp_enqueue_style('aftermarket-style', get_stylesheet_uri(), array(), $version);
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js', array(), '4.4.3', true);
    wp_enqueue_script('aftermarket-app', get_template_directory_uri() . '/app.js', array(), $version, true);
    wp_enqueue_script('aftermarket-dashboard', get_template_directory_uri() . '/dashboard.js', array('chart-js'), $version, true);

    $dashboard_url = home_url('/dashboard/');
    if (function_exists('get_page_by_path')) {
        $dashboard_page = get_page_by_path('dashboard');
        if ($dashboard_page) {
            $dashboard_url = get_permalink($dashboard_page);
        }
    }

    // Data końca kampanii — do zegara na stronie i w dashboardzie
    $campaign_end_raw = get_option('am_global_campaign_end', '');
    $campaign_end_ts  = 0;
    if (!empty($campaign_end_raw)) {
        $ts = strtotime(str_replace('T', ' ', $campaign_end_raw));
        if ($ts !== false) $campaign_end_ts = $ts * 1000;
    }

    $config = array(
        'dashboardUrl'    => $dashboard_url,
        'homeUrl'         => home_url('/'),
        'ajaxUrl'         => admin_url('admin-ajax.php'),
        'loginNonce'      => wp_create_nonce('aftermarket_login'),
        'statsNonce'      => wp_create_nonce('aftermarket_stats'),
        'isLoggedIn'      => is_user_logged_in() ? 'true' : 'false',
        'restUrl'         => esc_url_raw(rest_url('aftermarket/v1/my-stats')),
        'campaignEndTs'   => $campaign_end_ts,
    );
    if ( wp_script_is('aftermarket-app', 'registered') ) {
        wp_localize_script('aftermarket-app', 'AftermarketConfig', $config);
    }
    if ( wp_script_is('aftermarket-dashboard', 'registered') ) {
        wp_localize_script('aftermarket-dashboard', 'AftermarketConfig', $config);
    }
}
add_action('wp_enqueue_scripts', 'aftermarket_enqueue_scripts');

/* ═══════════════════════════════════════════
   3. WYLOGOWANIE PRZEZ URL
═══════════════════════════════════════════ */
add_action('init', function () {
    if (isset($_GET['am_logout']) && is_user_logged_in()) {
        wp_logout();
        $dashboard_url = home_url('/dashboard/');
        if (function_exists('get_page_by_path')) {
            $dashboard_page = get_page_by_path('dashboard');
            if ($dashboard_page) {
                $dashboard_url = get_permalink($dashboard_page);
            }
        }
        wp_redirect($dashboard_url);
        exit;
    }
});

/* ═══════════════════════════════════════════
   4. ACF: POLA UŻYTKOWNIKA (Dashboard)
═══════════════════════════════════════════ */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    acf_add_local_field_group(array(
        'key'    => 'group_am_client_data',
        'title'  => 'Dane Klienta Aftermarket',
        'fields' => array(
            array(
                'key'          => 'field_am_ig_username',
                'label'        => 'Instagram (@handle)',
                'name'         => 'am_ig_username',
                'type'         => 'text',
                'instructions' => 'Np. @marka.pl — pojawi się w dashboardzie klienta',
                'placeholder'  => '@marka.pl',
            ),
            array(
                'key'           => 'field_am_package',
                'label'         => 'Pakiet',
                'name'          => 'am_package',
                'type'          => 'select',
                'choices'       => array('starter' => 'Starter (2000 zł)', 'professional' => 'Professional (3000 zł)'),
                'default_value' => 'starter',
            ),
            array(
                'key'           => 'field_am_current_followers',
                'label'         => 'Obserwujący — aktualna liczba',
                'name'          => 'am_current_followers',
                'type'          => 'number',
                'instructions'  => 'Aktualizuj ręcznie. Wykres buduje się automatycznie.',
                'default_value' => 0,
            ),
            array(
                'key'           => 'field_am_followers_start',
                'label'         => 'Obserwujący — przed kampanią (start)',
                'name'          => 'am_followers_start',
                'type'          => 'number',
                'default_value' => 0,
            ),
            array(
                'key'           => 'field_am_leads',
                'label'         => 'Leady wygenerowane',
                'name'          => 'am_leads_generated',
                'type'          => 'number',
                'default_value' => 0,
            ),
            array(
                'key'            => 'field_am_campaign_end',
                'label'          => 'Data końca kampanii',
                'name'           => 'am_campaign_end',
                'type'           => 'date_time_picker',
                'display_format' => 'd/m/Y H:i',
                'return_format'  => 'Y-m-d H:i:s',
                'instructions'   => 'Używana do odliczania czasu w dashboardzie',
            ),
            array(
                'key'           => 'field_am_access_granted',
                'label'         => 'Dostęp do dashboardu',
                'name'          => 'am_access_granted',
                'type'          => 'true_false',
                'message'       => 'Klient ma aktywny dostęp do panelu',
                'default_value' => 0,
                'instructions'  => 'Zaznacz po opłaceniu. WooCommerce robi to automatycznie po zakupie.',
            ),
        ),
        'location' => array(array(array(
            'param'    => 'user_form',
            'operator' => '==',
            'value'    => 'all',
        ))),
        'active' => true,
    ));
});

/* ═══════════════════════════════════════════
   5. STRONA USTAWIEŃ W WP ADMIN (Aftermarket)
═══════════════════════════════════════════ */
add_action('admin_menu', function () {
    add_menu_page(
        'Ustawienia Aftermarket',
        'Aftermarket',
        'manage_options',
        'aftermarket-settings',
        'aftermarket_settings_page',
        'dashicons-chart-line',
        30
    );
    add_submenu_page(
        'aftermarket-settings',
        'Sponsorzy — Dane',
        'Sponsorzy',
        'manage_options',
        'aftermarket-sponsors',
        'aftermarket_sponsors_page'
    );
});

/* Obsługa POST dla strony Sponsorzy */
add_action('admin_init', function () {
    if (!isset($_POST['am_save_sponsor'])) return;
    if (!current_user_can('manage_options')) return;
    check_admin_referer('am_save_sponsor_nonce');

    $uid = (int)$_POST['am_sponsor_uid'];
    if (!$uid) return;

    update_user_meta($uid, 'am_current_followers', absint($_POST['am_current_followers']));
    update_user_meta($uid, 'am_followers_start',   absint($_POST['am_followers_start']));
    update_user_meta($uid, 'am_ig_username',       sanitize_text_field($_POST['am_ig_username']));
    update_user_meta($uid, 'am_package',           sanitize_text_field($_POST['am_package']));
    delete_user_meta($uid, 'am_ig_error');
    delete_user_meta($uid, 'am_ig_last_update'); // wymuś re-scrap przy kolejnym wejściu

    // Zapisz też w historii wykresu
    $history = get_user_meta($uid, 'am_followers_history', true);
    if (!is_array($history)) $history = array();
    $history[date('Y-m-d')] = absint($_POST['am_current_followers']);
    update_user_meta($uid, 'am_followers_history', $history);

    wp_redirect(admin_url('admin.php?page=aftermarket-sponsors&saved=1&uid=' . $uid));
    exit;
});

function aftermarket_sponsors_page() {
    if (!current_user_can('manage_options')) return;

    $saved_uid = isset($_GET['saved']) ? (int)$_GET['uid'] : 0;

    // Pobierz użytkowników z dostępem (mają meta am_package)
    $sponsors = get_users(array(
        'meta_key'   => 'am_package',
        'meta_value' => '',
        'meta_compare' => '!=',
        'number'     => 100,
    ));

    $edit_uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
    $edit_user = $edit_uid ? get_userdata($edit_uid) : null;
    ?>
    <div class="wrap">
        <h1>👥 Sponsorzy — Zarządzanie Danymi</h1>
        <?php if ($saved_uid): ?>
            <div class="notice notice-success"><p>✓ Dane sponsora zostały zaktualizowane.</p></div>
        <?php endif; ?>

        <?php if ($edit_user): ?>
        <h2>Edycja: <?php echo esc_html($edit_user->display_name); ?> (ID: <?php echo $edit_uid; ?>)</h2>
        <form method="post">
            <?php wp_nonce_field('am_save_sponsor_nonce'); ?>
            <input type="hidden" name="am_save_sponsor" value="1">
            <input type="hidden" name="am_sponsor_uid" value="<?php echo $edit_uid; ?>">
            <table class="form-table">
                <tr><th>Profil Instagram</th><td>
                    <input type="text" name="am_ig_username" value="<?php echo esc_attr(get_user_meta($edit_uid, 'am_ig_username', true)); ?>" class="regular-text" placeholder="np. allan_inclusive">
                </td></tr>
                <tr><th>Aktualna liczba obserwujących</th><td>
                    <input type="number" name="am_current_followers" value="<?php echo (int)get_user_meta($edit_uid, 'am_current_followers', true); ?>" class="regular-text">
                    <p class="description">Wpisz ręcznie jeśli skrobak Instagrama nie działa.</p>
                </td></tr>
                <tr><th>Obserwujący na starcie kampanii</th><td>
                    <input type="number" name="am_followers_start" value="<?php echo (int)get_user_meta($edit_uid, 'am_followers_start', true); ?>" class="regular-text">
                </td></tr>
                <tr><th>Pakiet</th><td>
                    <select name="am_package">
                        <?php foreach (['starter','pro','enterprise'] as $pkg): ?>
                            <option value="<?php echo $pkg; ?>" <?php selected(get_user_meta($edit_uid, 'am_package', true), $pkg); ?>><?php echo ucfirst($pkg); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td></tr>
            </table>
            <?php submit_button('Zapisz dane sponsora'); ?>
        </form>
        <hr>
        <?php endif; ?>

        <h2>Lista sponsorów</h2>
        <table class="widefat striped">
            <thead><tr><th>ID</th><th>Użytkownik</th><th>Instagram</th><th>Obserwujący</th><th>Pakiet</th><th>Akcja</th></tr></thead>
            <tbody>
            <?php foreach ($sponsors as $u): ?>
                <tr>
                    <td><?php echo $u->ID; ?></td>
                    <td><?php echo esc_html($u->display_name); ?></td>
                    <td>@<?php echo esc_html(get_user_meta($u->ID, 'am_ig_username', true)); ?></td>
                    <td><?php echo number_format((int)get_user_meta($u->ID, 'am_current_followers', true)); ?></td>
                    <td><?php echo esc_html(get_user_meta($u->ID, 'am_package', true)); ?></td>
                    <td><a href="<?php echo admin_url('admin.php?page=aftermarket-sponsors&uid=' . $u->ID); ?>" class="button">Edytuj</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($sponsors)): ?>
                <tr><td colspan="6">Brak sponsorów.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}


add_action('admin_init', function () {
    register_setting('aftermarket_options', 'am_starter_product_id',  array('type' => 'integer', 'sanitize_callback' => 'absint'));
    register_setting('aftermarket_options', 'am_pro_product_id',      array('type' => 'integer', 'sanitize_callback' => 'absint'));
    register_setting('aftermarket_options', 'am_global_campaign_end', array('type' => 'string',  'sanitize_callback' => 'sanitize_text_field'));
    register_setting('aftermarket_options', 'am_rapidapi_key',        array('type' => 'string',  'sanitize_callback' => 'sanitize_text_field'));

    // Edycja Konkursu i Nagród
    register_setting('aftermarket_options', 'prize_name_1',     array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
    register_setting('aftermarket_options', 'prize_name_2',     array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
    register_setting('aftermarket_options', 'g_hero_subtitle',  array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
    
    // Edycja Zasad (Kroki)
    register_setting('aftermarket_options', 'g_step_1_title',   array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
    register_setting('aftermarket_options', 'g_step_1_desc',    array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
    register_setting('aftermarket_options', 'g_step_2_title',   array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
    register_setting('aftermarket_options', 'g_step_2_desc',    array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));

    // FOMO Miejsca
    register_setting('aftermarket_options', 'fomo_free_slots',     array('type' => 'integer', 'sanitize_callback' => 'absint'));
    register_setting('aftermarket_options', 'fomo_reserved_slots', array('type' => 'integer', 'sanitize_callback' => 'absint'));
    register_setting('aftermarket_options', 'fomo_total_slots',    array('type' => 'integer', 'sanitize_callback' => 'absint'));
});

function am_field($key, $fallback = '') {
    $val = get_option($key);
    if ($val !== false && $val !== '') {
        return $val;
    }
    return $fallback;
}

function aftermarket_settings_page() {
    if (!current_user_can('manage_options')) return;
    $saved = isset($_GET['settings-updated']) ? '<div class="notice notice-success"><p>&#10003; Zapisano zmiany w konkursie.</p></div>' : '';
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-admin-customizer" style="font-size:32px;height:32px;width:32px;color:#F43F5E;"></span>
            Konfiguracja Aktywnego Konkursu
        </h1>
        <?php echo $saved; ?>

        <form method="post" action="options.php">
            <?php settings_fields('aftermarket_options'); ?>

            <div style="display:grid;grid-template-columns: 2fr 1fr; gap:20px; max-width:1100px;">
                <!-- Kolumna lewa -->
                <div>
                    <!-- NAGRODY I PODTYTUŁ -->
                    <div style="background:#fff; padding:24px; border-radius:8px; border:1px solid #ccd0d4; margin-bottom:20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <h2 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:12px; color:#23282d; display:flex; align-items:center; gap:8px;">
                            <span class="dashicons dashicons-awards" style="color:#F43F5E;"></span> Nagrody w Konkursie (Strona Główna)
                        </h2>
                        <table class="form-table">
                            <tr>
                                <th>Pierwsza Nagroda (Różowa)</th>
                                <td>
                                    <input type="text" name="prize_name_1" value="<?php echo esc_attr(get_option('prize_name_1', 'iPhone 17 Pro')); ?>" class="large-text" placeholder="np. iPhone 17 Pro">
                                </td>
                            </tr>
                            <tr>
                                <th>Druga Nagroda (Niebieska)</th>
                                <td>
                                    <input type="text" name="prize_name_2" value="<?php echo esc_attr(get_option('prize_name_2', 'MacBook Air')); ?>" class="large-text" placeholder="np. MacBook Air">
                                </td>
                            </tr>
                            <tr>
                                <th>Podtytuł sekcji konkursowej</th>
                                <td>
                                    <textarea name="g_hero_subtitle" class="large-text" rows="3"><?php echo esc_textarea(get_option('g_hero_subtitle', 'Dołącz do prestiżowego rozdania Aftermarket. Spełnij proste warunki w 15 sekund i zgarnij nagrodę z puli o łącznej wartości przekraczającej 10 000 PLN.')); ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- ZEGAR ODLICZAJĄCY -->
                    <div style="background:#fff; padding:24px; border-radius:8px; border:1px solid #ccd0d4; margin-bottom:20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <h2 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:12px; color:#23282d; display:flex; align-items:center; gap:8px;">
                            <span class="dashicons dashicons-clock" style="color:#3B82F6;"></span> Zegar Odliczający (Giveaway Timer)
                        </h2>
                        <table class="form-table">
                            <tr>
                                <th>Data i godzina zakończenia</th>
                                <td>
                                    <input type="datetime-local" name="am_global_campaign_end"
                                        value="<?php echo esc_attr(str_replace(' ', 'T', get_option('am_global_campaign_end', ''))); ?>"
                                        class="regular-text" style="padding:5px;">
                                    <p class="description">Zegar na stronie głównej będzie odliczał dokładnie do tej daty.</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- AUTOMATYZACJA INSTAGRAMA -->
                    <div style="background:#fff; padding:24px; border-radius:8px; border:1px solid #ccd0d4; margin-bottom:20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <h2 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:12px; color:#23282d; display:flex; align-items:center; gap:8px;">
                            <span class="dashicons dashicons-networking" style="color:#10B981;"></span> Automatyzacja Instagrama (RapidAPI)
                        </h2>
                        <table class="form-table">
                            <tr>
                                <th>Klucz RapidAPI Key</th>
                                <td>
                                    <input type="text" name="am_rapidapi_key"
                                        value="<?php echo esc_attr(get_option('am_rapidapi_key', '')); ?>"
                                        class="large-text" placeholder="x-rapidapi-key">
                                    <p class="description">
                                        Wymagane do automatycznego pobierania followersów. Załóż darmowe konto na <strong>RapidAPI.com</strong>, 
                                        subskrybuj darmowy pakiet API np. <strong>"Instagram Data 12"</strong> i wklej klucz powyżej.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- GITHUB ACTIONS SYNC -->
                    <div style="background:#fff; padding:24px; border-radius:8px; border:1px solid #ccd0d4; margin-bottom:20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <h2 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:12px; color:#23282d; display:flex; align-items:center; gap:8px;">
                            <span class="dashicons dashicons-html" style="color:#24292e;"></span> DARMOWA Synchronizacja (GitHub Actions)
                        </h2>
                        <table class="form-table">
                            <tr>
                                <th>Adres URL do pobierania kont</th>
                                <td>
                                    <code><?php echo esc_url(admin_url('admin-ajax.php?action=am_get_ig_handles&token=' . get_option('am_cron_token'))); ?></code>
                                </td>
                            </tr>
                            <tr>
                                <th>Adres URL do wysyłania wyników</th>
                                <td>
                                    <code><?php echo esc_url(admin_url('admin-ajax.php')); ?></code> (akcja: <code>am_update_ig_followers</code>)
                                </td>
                            </tr>
                            <tr>
                                <th>Token bezpieczeństwa</th>
                                <td>
                                    <input type="text" readonly value="<?php echo esc_attr(get_option('am_cron_token')); ?>" class="regular-text" style="background:#f0f0f1; font-family:monospace;">
                                    <p class="description">Użyj tego tokenu w skrypcie GitHub Actions jako parametru uwierzytelniającego.</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- EDYCJA KROKÓW (ZASAD) -->
                    <div style="background:#fff; padding:24px; border-radius:8px; border:1px solid #ccd0d4; margin-bottom:20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <h2 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:12px; color:#23282d; display:flex; align-items:center; gap:8px;">
                            <span class="dashicons dashicons-editor-ol" style="color:#8B5CF6;"></span> Zasady Konkursu (Kroki)
                        </h2>
                        <table class="form-table">
                            <tr>
                                <th colspan="2" style="padding:10px 0; border-bottom:1px solid #fafafa;"><strong>KROK 1</strong></th>
                            </tr>
                            <tr>
                                <th>Tytuł Kroku 1</th>
                                <td><input type="text" name="g_step_1_title" value="<?php echo esc_attr(get_option('g_step_1_title', 'Zaobserwuj profile')); ?>" class="large-text"></td>
                            </tr>
                            <tr>
                                <th>Opis Kroku 1</th>
                                <td><textarea name="g_step_1_desc" class="large-text" rows="2"><?php echo esc_textarea(get_option('g_step_1_desc', 'Wejdź na profil @aftermarket.ag i zaobserwuj konta z naszej listy obserwowanych.')); ?></textarea></td>
                            </tr>
                            <tr>
                                <th colspan="2" style="padding:20px 0 10px 0; border-bottom:1px solid #fafafa;"><strong>KROK 2</strong></th>
                            </tr>
                            <tr>
                                <th>Tytuł Kroku 2</th>
                                <td><input type="text" name="g_step_2_title" value="<?php echo esc_attr(get_option('g_step_2_title', 'Oczekuj losowania na żywo')); ?>" class="large-text"></td>
                            </tr>
                            <tr>
                                <th>Opis Kroku 2</th>
                                <td><textarea name="g_step_2_desc" class="large-text" rows="2"><?php echo esc_textarea(get_option('g_step_2_desc', 'Transmisja live wyłoni zwycięzcę. Wyślemy Ci e-mail 1 godzinę przed losowaniem.')); ?></textarea></td>
                            </tr>
                        </table>
                    </div>

                    <!-- SPONSORZY FOMO MIEJSCA -->
                    <div style="background:#fff; padding:24px; border-radius:8px; border:1px solid #ccd0d4; margin-bottom:20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <h2 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:12px; color:#23282d; display:flex; align-items:center; gap:8px;">
                            <span class="dashicons dashicons-groups" style="color:#10B981;"></span> Wolne Miejsca dla Sponsorów (Pasek FOMO)
                        </h2>
                        <table class="form-table">
                            <tr>
                                <th>Wolne miejsca</th>
                                <td><input type="number" name="fomo_free_slots" value="<?php echo esc_attr(get_option('fomo_free_slots', '8')); ?>" class="small-text"></td>
                            </tr>
                            <tr>
                                <th>Zarezerwowane miejsca</th>
                                <td><input type="number" name="fomo_reserved_slots" value="<?php echo esc_attr(get_option('fomo_reserved_slots', '72')); ?>" class="small-text"></td>
                            </tr>
                            <tr>
                                <th>Suma wszystkich miejsc</th>
                                <td><input type="number" name="fomo_total_slots" value="<?php echo esc_attr(get_option('fomo_total_slots', '80')); ?>" class="small-text"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Kolumna prawa -->
                <div>
                    <div style="background:#fff; padding:20px; border-radius:8px; border:1px solid #ccd0d4; margin-bottom:20px;">
                        <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px; color:#23282d; display:flex; align-items:center; gap:6px;">
                            <span class="dashicons dashicons-cart"></span> WooCommerce ID
                        </h3>
                        <table class="form-table" style="margin-top:0;">
                            <tr>
                                <th style="width:100%; display:block; padding:10px 0 5px 0;">ID produktu Starter</th>
                                <td><input type="number" name="am_starter_product_id" value="<?php echo esc_attr(get_option('am_starter_product_id', '')); ?>" style="width:100%;"></td>
                            </tr>
                            <tr>
                                <th style="width:100%; display:block; padding:10px 0 5px 0;">ID produktu Professional</th>
                                <td><input type="number" name="am_pro_product_id" value="<?php echo esc_attr(get_option('am_pro_product_id', '')); ?>" style="width:100%;"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <?php submit_button('Zapisz wszystkie zmiany', 'primary', 'submit', true, array('style' => 'font-size:16px; padding:12px 36px; height:auto;')); ?>
        </form>
    </div>
    <?php
}

/* ═══════════════════════════════════════════
   6. AJAX — dane klienta (dashboard stats)
   Używamy admin-ajax.php zamiast REST API,
   bo REST na LH.pl nie zawsze przenosi sesję.
═══════════════════════════════════════════ */
function aftermarket_ajax_get_stats() {
    check_ajax_referer('aftermarket_stats', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json(array('authenticated' => false, 'has_access' => false));
        return;
    }

    $user_id = get_current_user_id();

    if (!aftermarket_user_has_access($user_id)) {
        wp_send_json(array('authenticated' => true, 'has_access' => false));
        return;
    }

    $ig_username       = get_user_meta($user_id, 'am_ig_username', true);
    $current_followers = (int)get_user_meta($user_id, 'am_current_followers', true);
    $ig_error          = get_user_meta($user_id, 'am_ig_error', true);
    $force             = !empty($_POST['force']) && current_user_can('manage_options');
    $last_update       = (int)get_user_meta($user_id, 'am_ig_last_update', true);

    if (!empty($ig_username) && $force) {
        $scraped = aftermarket_scrape_instagram_followers($ig_username);
        if ($scraped !== false && $scraped > 0) {
            update_user_meta($user_id, 'am_current_followers', $scraped);
            update_user_meta($user_id, 'am_ig_last_update', time());
            delete_user_meta($user_id, 'am_ig_error');
            $current_followers = $scraped;
            $ig_error = '';

            if ((int)get_user_meta($user_id, 'am_followers_start', true) === 0) {
                update_user_meta($user_id, 'am_followers_start', $scraped);
            }
            $hist = get_user_meta($user_id, 'am_followers_history', true);
            if (!is_array($hist)) $hist = array();
            $hist[date('Y-m-d')] = $scraped;
            if (count($hist) > 30) $hist = array_slice($hist, -30, null, true);
            update_user_meta($user_id, 'am_followers_history', $hist);
        } else {
            $ig_error = 'Nie udało się pobrać danych Instagrama dla @' . ltrim($ig_username, '@') . '. Sprawdź czy profil jest publiczny i nazwa jest poprawna.';
            update_user_meta($user_id, 'am_ig_error', $ig_error);
        }
    }

    $followers_start = (int)get_user_meta($user_id, 'am_followers_start', true);
    $leads_generated = (int)get_user_meta($user_id, 'am_leads_generated', true);
    $package         = get_user_meta($user_id, 'am_package', true) ?: 'starter';
    $user            = get_userdata($user_id);

    // Data końca — ZAWSZE z globalnej opcji
    $campaign_end     = get_option('am_global_campaign_end', '');
    $campaign_end_ts  = 0;
    if (!empty($campaign_end)) {
        $ts = strtotime(str_replace('T', ' ', $campaign_end));
        if ($ts) $campaign_end_ts = $ts * 1000;
    }

    // Historia obserwujących
    $hist_meta = get_user_meta($user_id, 'am_followers_history', true);
    $followers_history = array();
    if (is_array($hist_meta) && !empty($hist_meta)) {
        ksort($hist_meta);
        foreach ($hist_meta as $d => $c) {
            $followers_history[] = array('date' => $d, 'count' => (int)$c, 'label' => date('d.m', strtotime($d)));
        }
    } else {
        $s = $followers_start ?: $current_followers;
        $followers_history[] = array('date' => date('Y-m-d', strtotime('-1 day')), 'count' => $s,                  'label' => 'Start');
        $followers_history[] = array('date' => date('Y-m-d'),                      'count' => $current_followers,   'label' => 'Dziś');
    }

    $activity_feed = array(
        array('type' => 'b', 'text' => 'Aktywacja pakietu ' . ucfirst($package), 'sub' => 'Dostęp do panelu aktywny'),
        array('type' => 'g', 'text' => 'Profil @' . ltrim((string)$ig_username, '@'), 'sub' => 'Konto Instagram połączone'),
        array('type' => 'p', 'text' => 'Monitorowanie aktywne', 'sub' => 'Dane są aktualizowane co godzinę'),
    );

    $last_update_ts = (int)get_user_meta($user_id, 'am_ig_last_update', true);
    $last_update_str = $last_update_ts ? date('d.m.Y H:i', $last_update_ts) : 'brak danych';

    wp_send_json(array(
        'authenticated'     => true,
        'has_access'        => true,
        'ig_username'       => $ig_username ?: ('@' . $user->user_login),
        'current_followers' => $current_followers,
        'followers_start'   => $followers_start,
        'leads_generated'   => $leads_generated,
        'campaign_end_date' => $campaign_end,
        'campaign_end_ts'   => $campaign_end_ts,
        'package'           => $package,
        'followers_history' => $followers_history,
        'activity_feed'     => $activity_feed,
        'ig_error'          => $ig_error ?: '',
        'last_update'       => $last_update_str,
    ));
}
add_action('wp_ajax_aftermarket_get_stats',        'aftermarket_ajax_get_stats');
add_action('wp_ajax_nopriv_aftermarket_get_stats', 'aftermarket_ajax_get_stats');

/* ═══════════════════════════════════════════
   7. AJAX — LOGOWANIE
═══════════════════════════════════════════ */
function aftermarket_ajax_login() {
    check_ajax_referer('aftermarket_login', 'nonce');

    $email    = sanitize_email(wp_unslash($_POST['email']    ?? ''));
    $password = wp_unslash($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        wp_send_json_error(array('message' => 'Uzupelnij email i haslo.'));
    }

    $user = get_user_by('email', $email);
    if (!$user) {
        wp_send_json_error(array('message' => 'Niepoprawny email lub haslo.'));
    }

    $result = wp_signon(array(
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => true,
    ), is_ssl());

    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => 'Niepoprawny email lub haslo.'));
    }

    wp_send_json_success(array('message' => 'OK'));
}
add_action('wp_ajax_nopriv_aftermarket_login', 'aftermarket_ajax_login');
add_action('wp_ajax_aftermarket_login',        'aftermarket_ajax_login');

/* ═══════════════════════════════════════════
   7b. AJAX — REJESTRACJA
═══════════════════════════════════════════ */
function aftermarket_ajax_register() {
    check_ajax_referer('aftermarket_register', 'nonce');

    $email    = sanitize_email(wp_unslash($_POST['email']    ?? ''));
    $password = wp_unslash($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        wp_send_json_error(array('message' => 'Uzupelnij email i haslo.'));
    }

    if (strlen($password) < 8) {
        wp_send_json_error(array('message' => 'Haslo musi miec minimum 8 znakow.'));
    }

    if (email_exists($email)) {
        wp_send_json_error(array('message' => 'Konto z tym adresem email juz istnieje. Zaloguj sie.'));
    }

    $user_id = wp_create_user($email, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(array('message' => 'Nie udalo sie utworzyc konta: ' . $user_id->get_error_message()));
    }

    // Zaloguj automatycznie po rejestracji
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);

    wp_send_json_success(array('message' => 'Konto zostalo utworzone.'));
}
add_action('wp_ajax_nopriv_aftermarket_register', 'aftermarket_ajax_register');
add_action('wp_ajax_aftermarket_register',        'aftermarket_ajax_register');

/* ═══════════════════════════════════════════
   8. WOOCOMMERCE — po zakupie daj dostęp
═══════════════════════════════════════════ */
add_action('woocommerce_order_status_completed',  'aftermarket_on_order_complete');
add_action('woocommerce_order_status_processing', 'aftermarket_on_order_complete');

function aftermarket_on_order_complete($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;

    $user_id = $order->get_user_id();
    if (!$user_id) return;

    $starter_pid = (int)get_option('am_starter_product_id', 0);
    $pro_pid     = (int)get_option('am_pro_product_id',     0);
    $our_pids    = array_filter(array($starter_pid, $pro_pid));

    $is_our_order = false;
    $package      = 'starter';

    foreach ($order->get_items() as $item) {
        $pid = $item->get_product_id();
        if (empty($our_pids) || in_array($pid, $our_pids, true)) {
            $is_our_order = true;
            $package      = ($pid === $pro_pid) ? 'professional' : 'starter';
            break;
        }
    }

    if (!$is_our_order && !empty($our_pids)) return;

    update_user_meta($user_id, 'am_access_granted', 1);
    update_user_meta($user_id, 'am_package',        $package);

    // Kopiowanie nazwy Instagrama z zamówienia do profilu użytkownika
    $ig_from_order = $order->get_meta('_billing_ig_username') ?: $order->get_meta('billing_ig_username');
    if (!empty($ig_from_order)) {
        if (strpos($ig_from_order, '@') !== 0) {
            $ig_from_order = '@' . $ig_from_order;
        }
        update_user_meta($user_id, 'am_ig_username', $ig_from_order);

        // Automatyczne pobranie startowej liczby obserwujących przy zakupie
        $start_followers = aftermarket_scrape_instagram_followers($ig_from_order);
        if ($start_followers !== false && $start_followers > 0) {
            update_user_meta($user_id, 'am_current_followers', $start_followers);
            update_user_meta($user_id, 'am_followers_start',   $start_followers);
            update_user_meta($user_id, 'am_ig_last_update',    time());

            // Zapisz punkt początkowy w historii wykresu
            $history = array(
                date('Y-m-d') => $start_followers
            );
            update_user_meta($user_id, 'am_followers_history', $history);
        }
    }

    $dashboard_url = home_url('/dashboard/');
    if (function_exists('get_page_by_path')) {
        $dashboard_page = get_page_by_path('dashboard');
        if ($dashboard_page) {
            $dashboard_url = get_permalink($dashboard_page);
        }
    }
    $user          = get_userdata($user_id);
    $customer_name  = $order->get_billing_first_name() ?: $user->display_name;

    $subject = 'Twój Panel Aftermarket jest gotowy! 🚀';
    
    // Budujemy luksusowy szablon HTML zgodny z marką Aftermarket
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Twój Panel Aftermarket jest gotowy!</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #0B0B14; color: #E4E4E7; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
            .wrapper { width: 100%; table-layout: fixed; background-color: #0B0B14; padding: 40px 20px; }
            .container { max-width: 600px; margin: 0 auto; background-color: #121221; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.08); overflow: hidden; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5); }
            .header { background: linear-gradient(135deg, #1E1B4B 0%, #0B0B14 100%); padding: 40px 30px; text-align: center; border-bottom: 1px solid rgba(255, 255, 255, 0.06); }
            .logo { font-size: 24px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.5px; text-decoration: none; }
            .logo span { color: #F43F5E; }
            .body { padding: 40px 30px; line-height: 1.6; font-size: 15px; color: #D4D4D8; }
            h1 { color: #FFFFFF; font-size: 22px; font-weight: 700; margin-top: 0; margin-bottom: 16px; letter-spacing: -0.5px; }
            p { margin-top: 0; margin-bottom: 20px; }
            .highlight { color: #F43F5E; font-weight: 600; }
            .btn-container { text-align: center; margin: 30px 0; }
            .btn { display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #F43F5E 0%, #A855F7 100%); color: #FFFFFF !important; text-decoration: none; font-weight: 700; font-size: 14px; border-radius: 9999px; box-shadow: 0 10px 20px rgba(244, 63, 94, 0.3); border: 1px solid rgba(255, 255, 255, 0.15); transition: transform 0.2s; }
            .credentials-card { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 12px; padding: 20px; margin: 25px 0; }
            .credentials-title { font-size: 13px; text-transform: uppercase; color: #888899; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 10px; }
            .credential-row { margin-bottom: 8px; font-size: 14px; }
            .credential-row:last-child { margin-bottom: 0; }
            .credential-label { color: #A1A1AA; display: inline-block; width: 80px; }
            .credential-value { color: #FFFFFF; font-weight: 600; font-family: monospace; font-size: 15px; }
            .footer { padding: 30px; background-color: #09090F; border-top: 1px solid rgba(255, 255, 255, 0.06); text-align: center; font-size: 12px; color: #52525B; }
            .footer a { color: #888899; text-decoration: none; }
            .footer a:hover { color: #FFFFFF; }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <div class="container">
                <div class="header">
                    <a href="' . home_url() . '" class="logo">Aftermarket<span>.</span></a>
                </div>
                <div class="body">
                    <h1>Cześć ' . esc_html($customer_name) . '! 👋</h1>
                    <p>Dziękujemy za zaufanie i wybór pakietu <span class="highlight">' . esc_html(ucfirst($package)) . '</span> w naszej kampanii promocyjnej Aftermarket.</p>
                    <p>Twój dedykowany panel sponsora jest już aktywny. Wygodne śledzenie statystyk, przyrostów na żywo oraz postępu kampanii znajdziesz pod poniższym linkiem:</p>
                    
                    <div class="btn-container">
                        <a href="' . esc_url($dashboard_url) . '" class="btn" target="_blank">Przejdź do Panelu</a>
                    </div>
                    
                    <div class="credentials-card">
                        <div class="credentials-title">Dane Logowania do Twojego Konta</div>
                        <div class="credential-row">
                            <span class="credential-label">Login (email):</span>
                            <span class="credential-value">' . esc_html($user->user_email) . '</span>
                        </div>
                        <div class="credential-row">
                            <span class="credential-label">Hasło:</span>
                            <span class="credential-value" style="font-family: inherit; font-size: 14px;">Użyj hasła podanego przy składaniu zamówienia</span>
                        </div>
                    </div>
                    
                    <p style="margin-bottom: 0;">W razie pytań, Twój opiekun kampanii jest do Twojej dyspozycji pod adresem <a href="mailto:kontakt@aftermarket.pl" style="color: #F43F5E; text-decoration: none;">kontakt@aftermarket.pl</a>.</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' Aftermarket.ag. Wszelkie prawa zastrzeżone.</p>
                    <p><a href="' . home_url() . '">Strona Główna</a> &bull; <a href="' . esc_url($dashboard_url) . '">Panel Sponsora</a></p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';

    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($user->user_email, $subject, $message, $headers);
}

/* ═══════════════════════════════════════════
   9. HELPER — sprawdzenie dostępu
═══════════════════════════════════════════ */
function aftermarket_user_has_access($user_id = null) {
    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id) return false;

    if (user_can($user_id, 'manage_options')) return true;

    $granted = get_user_meta($user_id, 'am_access_granted', true);
    if ($granted) return true;

    if (function_exists('wc_customer_bought_product')) {
        $user        = get_userdata($user_id);
        $starter_pid = (int)get_option('am_starter_product_id', 0);
        $pro_pid     = (int)get_option('am_pro_product_id',     0);
        foreach (array_filter(array($starter_pid, $pro_pid)) as $pid) {
            if (wc_customer_bought_product($user->user_email, $user_id, $pid)) {
                return true;
            }
        }
    }

    return false;
}

/* ═══════════════════════════════════════════
   10. WOOCOMMERCE USTAWIENIA I PRZEKIEROWANIA
═══════════════════════════════════════════ */

// A. Oczyszczanie koszyka przed dodaniem pakietu (zapobiega sumowaniu)
add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id, $quantity) {
    $starter_pid = (int)get_option('am_starter_product_id', 0);
    $pro_pid     = (int)get_option('am_pro_product_id',     0);
    $our_pids    = array_filter(array($starter_pid, $pro_pid));

    if (in_array($product_id, $our_pids, true)) {
        WC()->cart->empty_cart();
    }
    return $passed;
}, 10, 3);

// B. Wymuszenie tworzenia konta w kasie (wyłączenie zakupów gościnnych)
add_filter('woocommerce_checkout_registration_enabled', '__return_true');
add_filter('woocommerce_checkout_registration_required', '__return_true');
add_filter('woocommerce_enable_guest_checkout', function($value) {
    return 'no';
});

// C. Dodanie pola Instagram do formularza zamówienia
add_filter('woocommerce_billing_fields', function ($fields) {
    $fields['billing_ig_username'] = array(
        'type'        => 'text',
        'label'       => 'Nazwa profilu Instagram do promocji',
        'placeholder' => 'np. @twoja_marka',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
    );
    // Usuwamy wymaganie i samo pole telefonu z WooCommerce
    if (isset($fields['billing_phone'])) {
        unset($fields['billing_phone']);
    }
    return $fields;
});

// D. Zapis nazwy Instagrama do metadanych użytkownika przy rejestracji
add_action('woocommerce_checkout_update_user_meta', function ($user_id, $posted) {
    if (!empty($posted['billing_ig_username'])) {
        $ig = sanitize_text_field($posted['billing_ig_username']);
        if (strpos($ig, '@') !== 0) {
            $ig = '@' . $ig;
        }
        update_user_meta($user_id, 'am_ig_username', $ig);
    }
}, 10, 2);

// E. Przekierowanie prosto do kasy po dodaniu do koszyka
add_filter('woocommerce_add_to_cart_redirect', function () {
    return wc_get_checkout_url();
});

// F. Debug koszyka w stopce
add_action('wp_footer', function() {
    if (function_exists('WC') && WC()->cart) {
        $cart_count = WC()->cart->get_cart_contents_count();
        echo "\n<!-- Aftermarket Woo Debug: Cart Count = " . intval($cart_count) . " -->\n";
    }
});

// G. Ukrywanie i automatyczne czyszczenie komunikatów "dodano do koszyka" w kasie
add_filter('woocommerce_add_to_cart_message', '__return_empty_string');
add_action('template_redirect', function() {
    if (is_checkout() && function_exists('wc_clear_notices')) {
        wc_clear_notices();
    }
});

/* ═══════════════════════════════════════════
   11. INSTAGRAM FOLLOWER SCRAPER (Statystyki)
═══════════════════════════════════════════ */

// A. Pomocnicza funkcja parsująca skrócone liczby (np. 12.3k, 12 tys., 1.5M)
function aftermarket_parse_short_number($string) {
    $string = str_replace(array(' ', ','), '', $string);
    $string = str_replace('tys.', 'k', $string);
    $string = str_replace('mln', 'm', $string);
    
    $last_char = strtolower(substr($string, -1));
    $number = (float) $string;
    
    if ($last_char === 'k') {
        $number *= 1000;
    } elseif ($last_char === 'm') {
        $number *= 1000000;
    }
    return (int) $number;
}

function aftermarket_scrape_instagram_followers($username) {
    $username = ltrim(trim($username), '@');
    if (empty($username)) return false;

    $api_key = get_option('am_rapidapi_key', '');
    if (empty($api_key)) {
        return false;
    }

    // Odpytujemy bezpośrednio z PHP do RapidAPI (Host: instagram-data19)
    // Próbujemy najpopularniejszy endpoint user-info
    $url = 'https://instagram-data19.p.rapidapi.com/user-info/?username_or_id_or_url=' . urlencode($username);
    $args = array(
        'timeout' => 15,
        'headers' => array(
            'x-rapidapi-key'  => $api_key,
            'x-rapidapi-host' => 'instagram-data19.p.rapidapi.com'
        )
    );

    $response = wp_remote_get($url, $args);
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        $followers = null;
        if (is_array($data)) {
            $followers = (
                $data['followers'] ??
                $data['follower_count'] ??
                ($data['data']['followers'] ?? null) ??
                ($data['data']['follower_count'] ?? null) ??
                ($data['data']['user']['edge_followed_by']['count'] ?? null) ??
                ($data['data']['user']['follower_count'] ?? null)
            );
        }

        if ($followers !== null && (int)$followers > 0) {
            return (int)$followers;
        }
    }

    // Jeśli user-info zawiedzie, próbujemy zapasowo endpoint info
    $url = 'https://instagram-data19.p.rapidapi.com/info/?username_or_id_or_url=' . urlencode($username);
    $response = wp_remote_get($url, $args);
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        $followers = null;
        if (is_array($data)) {
            $followers = (
                $data['followers'] ??
                $data['follower_count'] ??
                ($data['data']['followers'] ?? null) ??
                ($data['data']['follower_count'] ?? null) ??
                ($data['data']['user']['edge_followed_by']['count'] ?? null) ??
                ($data['data']['user']['follower_count'] ?? null)
            );
        }

        if ($followers !== null && (int)$followers > 0) {
            return (int)$followers;
        }
    }

    return false;
}

// C. Zadanie Crona aktualizujące wszystkich aktywnych użytkowników
add_action('aftermarket_cron_update_followers', 'aftermarket_run_followers_update');
function aftermarket_run_followers_update() {
    $users = get_users(array(
        'meta_key'     => 'am_access_granted',
        'meta_value'   => '1',
        'fields'       => 'ID',
    ));

    foreach ($users as $user_id) {
        $username = get_user_meta($user_id, 'am_ig_username', true);
        if (empty($username)) continue;

        $followers = aftermarket_scrape_instagram_followers($username);
        if ($followers !== false && $followers > 0) {
            update_user_meta($user_id, 'am_current_followers', $followers);
            update_user_meta($user_id, 'am_ig_last_update', time());
            
            $start = get_user_meta($user_id, 'am_followers_start', true);
            if (empty($start) || (int)$start === 0) {
                update_user_meta($user_id, 'am_followers_start', $followers);
            }
        }
        sleep(2); // Odstęp w celu uniknięcia blokad IP
    }
}

// D. Rejestracja cyklicznego zadania crona (dwa razy dziennie)
add_action('wp', function() {
    if (!wp_next_scheduled('aftermarket_cron_update_followers')) {
        wp_schedule_event(time(), 'twicedaily', 'aftermarket_cron_update_followers');
    }
});

/* ═══════════════════════════════════════════
   11. SYNC ENDPOINTS DLA GITHUB ACTIONS
═══════════════════════════════════════════ */
add_action('admin_init', function() {
    if (!get_option('am_cron_token')) {
        update_option('am_cron_token', bin2hex(random_bytes(16)));
    }
});

// GET: Pobierz listę loginów IG do zaktualizowania
function am_api_get_ig_handles() {
    $token = sanitize_text_field($_GET['token'] ?? '');
    $correct = get_option('am_cron_token');
    if (empty($correct) || $token !== $correct) {
        wp_send_json_error('Access denied', 403);
    }

    $sponsors = get_users(array(
        'meta_key'   => 'am_package',
        'meta_value' => '',
        'meta_compare' => '!=',
        'number'     => 1000,
    ));

    $handles = array();
    foreach ($sponsors as $u) {
        $user_id = $u->ID;
        $username = get_user_meta($user_id, 'am_ig_username', true);
        if (!empty($username)) {
            $handles[] = ltrim(trim($username), '@');
        }
    }
    
    $api_key = get_option('am_rapidapi_key', '');

    wp_send_json_success(array(
        'handles' => array_values(array_unique($handles)),
        'api_key' => $api_key
    ));
}
add_action('wp_ajax_am_get_ig_handles',        'am_api_get_ig_handles');
add_action('wp_ajax_nopriv_am_get_ig_handles', 'am_api_get_ig_handles');

// POST: Zapisz pobrane z zewnątrz dane
function am_api_update_ig_followers() {
    $token = sanitize_text_field($_POST['token'] ?? '');
    $correct = get_option('am_cron_token');
    if (empty($correct) || $token !== $correct) {
        wp_send_json_error('Access denied', 403);
    }

    $data = json_decode(stripslashes($_POST['data'] ?? '[]'), true);
    if (!is_array($data)) {
        wp_send_json_error('Invalid payload format', 400);
    }

    $updated = 0;
    foreach ($data as $username => $count) {
        $count = (int)$count;
        if ($count <= 0) continue;

        $clean_username = ltrim(trim($username), '@');

        // Znajdź użytkownika po IG username
        $users = get_users(array(
            'meta_key'   => 'am_ig_username',
            'meta_value' => $clean_username,
            'number'     => 1,
        ));
        if (empty($users)) {
            $users = get_users(array(
                'meta_key'   => 'am_ig_username',
                'meta_value' => '@' . $clean_username,
                'number'     => 1,
            ));
        }

        if (!empty($users)) {
            $uid = $users[0]->ID;
            update_user_meta($uid, 'am_current_followers', $count);
            update_user_meta($uid, 'am_ig_last_update', time());
            delete_user_meta($uid, 'am_ig_error');

            $start = (int)get_user_meta($uid, 'am_followers_start', true);
            if ($start === 0) {
                update_user_meta($uid, 'am_followers_start', $count);
            }

            // Zapis w historii
            $hist = get_user_meta($uid, 'am_followers_history', true);
            if (!is_array($hist)) $hist = array();
            $hist[date('Y-m-d')] = $count;
            if (count($hist) > 30) $hist = array_slice($hist, -30, null, true);
            update_user_meta($uid, 'am_followers_history', $hist);

            $updated++;
        }
    }
    wp_send_json_success(array('updated' => $updated));
}
add_action('wp_ajax_am_update_ig_followers',        'am_api_update_ig_followers');
add_action('wp_ajax_nopriv_am_update_ig_followers', 'am_api_update_ig_followers');


