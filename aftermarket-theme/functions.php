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

    $config = array(
        'dashboardUrl' => $dashboard_url,
        'homeUrl'      => home_url('/'),
        'ajaxUrl'      => admin_url('admin-ajax.php'),
        'loginNonce'   => wp_create_nonce('aftermarket_login'),
        'isLoggedIn'   => is_user_logged_in() ? 'true' : 'false',
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
});

add_action('admin_init', function () {
    // WooCommerce i Zegar
    register_setting('aftermarket_options', 'am_starter_product_id',  array('type' => 'integer', 'sanitize_callback' => 'absint'));
    register_setting('aftermarket_options', 'am_pro_product_id',      array('type' => 'integer', 'sanitize_callback' => 'absint'));
    register_setting('aftermarket_options', 'am_global_campaign_end', array('type' => 'string',  'sanitize_callback' => 'sanitize_text_field'));

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
   6. REST API — dane klienta
═══════════════════════════════════════════ */
add_action('rest_api_init', function () {
    register_rest_route('aftermarket/v1', '/my-stats', array(
        'methods'             => 'GET',
        'callback'            => 'aftermarket_rest_my_stats',
        'permission_callback' => '__return_true',
    ));
});

function aftermarket_rest_my_stats() {
    if (!is_user_logged_in()) {
        return new WP_REST_Response(array('authenticated' => false), 401);
    }

    $user_id = get_current_user_id();

    if (!aftermarket_user_has_access($user_id)) {
        return new WP_REST_Response(array('authenticated' => true, 'has_access' => false), 403);
    }

    $acf = function_exists('get_field');

    $ig_username       = $acf ? get_field('am_ig_username',       'user_' . $user_id) : get_user_meta($user_id, 'am_ig_username',       true);
    $current_followers = (int)($acf ? get_field('am_current_followers', 'user_' . $user_id) : get_user_meta($user_id, 'am_current_followers', true));
    $followers_start   = (int)($acf ? get_field('am_followers_start',   'user_' . $user_id) : get_user_meta($user_id, 'am_followers_start',   true));
    $leads_generated   = (int)($acf ? get_field('am_leads_generated',   'user_' . $user_id) : get_user_meta($user_id, 'am_leads_generated',   true));
    $campaign_end      = $acf ? get_field('am_campaign_end', 'user_' . $user_id) : get_user_meta($user_id, 'am_campaign_end', true);
    $package           = $acf ? get_field('am_package',      'user_' . $user_id) : get_user_meta($user_id, 'am_package',      true);

    if (!$campaign_end) {
        $campaign_end = get_option('am_global_campaign_end', '');
    }
    if (!$campaign_end) {
        $campaign_end = date('Y-m-d H:i:s', strtotime('+7 days'));
    }

    $followers_history = array();
    if ($current_followers > 0) {
        $start = $followers_start ?: max(0, $current_followers - 20000);
        $days  = 7;
        for ($i = $days; $i >= 0; $i--) {
            $progress          = ($days - $i) / $days;
            $count             = (int)($start + ($current_followers - $start) * pow($progress, 0.7));
            $date              = date('Y-m-d', strtotime("-{$i} days"));
            $followers_history[] = array(
                'date'  => $date,
                'count' => $count,
                'label' => $i === $days ? 'Start' : ($i === 0 ? 'Dzis' : 'Dzien ' . ($days - $i)),
            );
        }
    }

    $activity_feed = array(
        array('type' => 'g', 'text' => 'Nowy obserwujacy',       'sub' => 'Organiczny wzrost z kampanii'),
        array('type' => 'p', 'text' => 'Rejestracja uczestnika', 'sub' => 'Formularz lead'),
        array('type' => 'g', 'text' => '+12 obserwujacych',      'sub' => 'Burst z Instagram Reels'),
        array('type' => 'b', 'text' => 'Webhook platnosci',      'sub' => 'Kampania active — 200 OK'),
        array('type' => 'g', 'text' => 'Nowy obserwujacy',       'sub' => 'Polecenie przez znajomych'),
        array('type' => 'p', 'text' => 'Story view',             'sub' => '+840 wyswietlen w 5 min'),
    );

    $user = get_userdata($user_id);

    return new WP_REST_Response(array(
        'authenticated'     => true,
        'has_access'        => true,
        'ig_username'       => $ig_username ?: ('@' . $user->user_login),
        'current_followers' => $current_followers,
        'followers_start'   => $followers_start,
        'leads_generated'   => $leads_generated,
        'campaign_end_date' => $campaign_end,
        'package'           => $package ?: 'starter',
        'followers_history' => $followers_history,
        'activity_feed'     => $activity_feed,
    ), 200);
}

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

    $dashboard_url = home_url('/dashboard/');
    if (function_exists('get_page_by_path')) {
        $dashboard_page = get_page_by_path('dashboard');
        if ($dashboard_page) {
            $dashboard_url = get_permalink($dashboard_page);
        }
    }
    $user          = get_userdata($user_id);
    $customer_name  = $order->get_billing_first_name() ?: $user->display_name;

    $subject = 'Twoj Panel Aftermarket jest gotowy!';
    $message = "Czesc {$customer_name},\n\n"
        . "Dziekujemy za zakup pakietu " . ucfirst($package) . "!\n\n"
        . "Twój panel sponsora jest już aktywny. Zaloguj się na swoje konto:\n"
        . "{$dashboard_url}\n\n"
        . "Login: {$user->user_email}\n"
        . "Hasło: Użyj hasła, które podałeś podczas składania zamówienia.\n\n"
        . "Zespol Aftermarket";

    wp_mail($user->user_email, $subject, $message);
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
   10. PRZEKIEROWANIE WOO
═══════════════════════════════════════════ */

add_filter('woocommerce_add_to_cart_redirect', function () {
    return wc_get_checkout_url();
});

// Diagnostyka koszyka (wyświetli niewidoczny komentarz w kodzie strony)
add_action('wp_footer', function() {
    if (function_exists('WC') && WC()->cart) {
        $cart_count = WC()->cart->get_cart_contents_count();
        echo "\n<!-- Aftermarket Woo Debug: Cart Count = " . intval($cart_count) . " -->\n";
        if ($cart_count === 0 && is_checkout()) {
            echo "<!-- Aftermarket Woo Debug: Koszyk jest pusty na stronie kasy! Sesja lub ciasteczka mogą nie działać na tym serwerze. -->\n";
        }
    }
});


