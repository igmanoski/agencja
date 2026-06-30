<?php
/**
 * Template Name: Checkout Premium
 *
 * Wieloetapowa, premium strona zamówienia pakietu Aftermarket.
 * Zastępuje domyślny formularz WooCommerce.
 */
$is_order_received = is_wc_endpoint_url('order-received');
global $wp;
$order_id = isset($wp->query_vars['order-received']) ? intval($wp->query_vars['order-received']) : 0;
$order = $order_id ? wc_get_order($order_id) : null;

if (!$is_order_received) {
    if (!function_exists('WC') || WC()->cart->is_empty()) {
        wp_redirect(home_url('/'));
        exit;
    }
}

$is_logged_in = is_user_logged_in();

// Dane koszyka
$cart      = WC()->cart;
$cart_item = reset($cart->get_cart());
$product   = $cart_item ? wc_get_product($cart_item['product_id']) : null;
$product_name = $product ? $product->get_name() : 'Pakiet';
$total_raw    = (float) $cart->get_total('edit');

$starter_pid = (int) get_option('am_starter_product_id', 0);
$pro_pid     = (int) get_option('am_pro_product_id', 0);
$is_pro      = $cart_item && (int) $cart_item['product_id'] === $pro_pid;

$features = $is_pro ? [
    'Udział w 2 kampaniach (konkursach)',
    'Pełna ekspozycja i promowanie profilu na każdej kampanii',
    'Dostęp do Panelu Sponsora (przyrosty live)',
    'Wyróżnienie na InstaStories — dedykowana wzmianka',
    'Dedykowany opiekun kampanii',
    'Konsultacja i audyt optymalizacji profilu',
    'Priorytetowa rezerwacja kolejnej edycji z rabatem',
    'Faktura VAT + szczegółowy raport po kampanii',
] : [
    'Udział w 1 wybranej kampanii konkursowej',
    'Pełna ekspozycja i promowanie profilu',
    'Dostęp do Panelu Sponsora (przyrosty live)',
    'Dedykowany opiekun kampanii',
    'Faktura VAT + raport po kampanii',
];

// Nonce
$checkout_nonce = wp_create_nonce('woocommerce-process_checkout');
$login_nonce    = wp_create_nonce('aftermarket_login');
$register_nonce = wp_create_nonce('aftermarket_register');

// URL stron prawnych
$reg_page  = get_page_by_path('regulamin');
$pol_page  = get_page_by_path('polityka-prywatnosci');
$reg_url   = $reg_page ? get_permalink($reg_page) : home_url('/regulamin/');
$pol_url   = $pol_page ? get_permalink($pol_page) : home_url('/polityka-prywatnosci/');

$current_user_email = $is_logged_in ? wp_get_current_user()->user_email : '';

get_header();
?>

<style>
/* ══════════════════════════════════════════════
   PREMIUM CHECKOUT — lokalne style
══════════════════════════════════════════════ */
.am-co-wrap {
    min-height: 100vh;
    padding: 110px 20px 100px;
    max-width: 1120px;
    margin: 0 auto;
}

/* ── Stepper ── */
.am-stepper {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 60px;
    gap: 0;
}
.am-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    flex: 1;
    max-width: 180px;
    position: relative;
    z-index: 1;
}
.am-step-circle {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    border: 2px solid rgba(255,255,255,0.09);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 1.05rem;
    color: rgba(255,255,255,0.3);
    transition: all 0.45s cubic-bezier(.16,1,.3,1);
}
.am-step.active .am-step-circle {
    background: linear-gradient(135deg, #F43F5E, #A855F7);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 0 28px rgba(244,63,94,.4);
}
.am-step.done .am-step-circle {
    background: linear-gradient(135deg, #10B981, #34D399);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 0 18px rgba(16,185,129,.3);
}
.am-step-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: rgba(255,255,255,0.28);
    text-transform: uppercase;
    letter-spacing: .09em;
    text-align: center;
    transition: color .3s;
    white-space: nowrap;
}
.am-step.active .am-step-label { color: rgba(255,255,255,.85); }
.am-step.done  .am-step-label  { color: #10B981; }
.am-connector {
    flex: 1;
    max-width: 130px;
    height: 2px;
    background: rgba(255,255,255,0.06);
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.am-connector-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #F43F5E, #A855F7);
    transition: width .6s cubic-bezier(.16,1,.3,1);
}
.am-connector.filled .am-connector-fill { width: 100%; }

/* ── Glass Card ── */
.am-card {
    background: linear-gradient(160deg, rgba(255,255,255,0.018) 0%, rgba(255,255,255,0.004) 100%);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 22px;
    padding: 48px 52px;
    backdrop-filter: blur(24px);
    box-shadow: 0 40px 80px rgba(0,0,0,0.6), inset 0 1px 0 rgba(255,255,255,0.05);
}
@media (max-width: 700px) {
    .am-card { padding: 28px 22px; }
    .am-co-wrap { padding: 100px 16px 80px; }
}

/* ── Panels ── */
.am-panel { display: none; animation: coFadeUp .45s cubic-bezier(.16,1,.3,1) both; }
.am-panel.visible { display: block; }
@keyframes coFadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

/* ── Tabs ── */
.am-tabs {
    display: flex;
    gap: 4px;
    background: rgba(255,255,255,0.04);
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 36px;
}
.am-tab-btn {
    flex: 1;
    background: transparent;
    border: none;
    color: rgba(255,255,255,.45);
    font-family: var(--fb);
    font-size: .88rem;
    font-weight: 700;
    padding: 13px 20px;
    border-radius: 9px;
    cursor: pointer;
    transition: all .3s ease;
    text-transform: uppercase;
    letter-spacing: .05em;
}
.am-tab-btn.active {
    background: linear-gradient(135deg, rgba(244,63,94,.18), rgba(168,85,247,.18));
    color: #fff;
    border: 1px solid rgba(244,63,94,.25);
}
.am-tab-panel { display: none; }
.am-tab-panel.active { display: block; }

/* ── Fields ── */
.am-field { margin-bottom: 20px; }
.am-label {
    display: block;
    font-size: .78rem;
    font-weight: 700;
    color: rgba(255,255,255,.45);
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: 8px;
}
.am-label .req { color: #F43F5E; margin-left: 2px; }
.am-input {
    width: 100%;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 11px;
    padding: 14px 18px;
    color: #fff;
    font-family: var(--fb);
    font-size: .95rem;
    transition: all .3s ease;
    box-sizing: border-box;
    -webkit-appearance: none;
}
.am-input::placeholder { color: rgba(255,255,255,.22); }
.am-input:focus {
    outline: none;
    border-color: rgba(244,63,94,.45);
    background: rgba(255,255,255,.07);
    box-shadow: 0 0 0 4px rgba(244,63,94,.07);
}
.am-input.ig-input { padding-left: 46px; }
.am-input-wrap { position: relative; }
.am-input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,.28);
    pointer-events: none;
    display: flex;
    align-items: center;
}
.am-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 600px) { .am-row2 { grid-template-columns: 1fr; } }

/* ── Buttons ── */
.am-btn-main {
    width: 100%;
    background: linear-gradient(135deg, #F43F5E, #A855F7);
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 11px;
    color: #fff;
    font-family: var(--fb);
    font-weight: 900;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: 17px 32px;
    cursor: pointer;
    transition: all .3s ease;
    box-shadow: 0 8px 26px rgba(244,63,94,.3);
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.am-btn-main:hover { transform: translateY(-2px); box-shadow: 0 14px 34px rgba(244,63,94,.45); }
.am-btn-main:disabled { opacity: .45; cursor: not-allowed; transform: none; }
.am-btn-back {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.09);
    color: rgba(255,255,255,.65);
    border-radius: 11px;
    padding: 15px 26px;
    font-family: var(--fb);
    font-weight: 700;
    font-size: .88rem;
    cursor: pointer;
    transition: all .3s ease;
    text-transform: uppercase;
    letter-spacing: .04em;
    white-space: nowrap;
}
.am-btn-back:hover { background: rgba(255,255,255,.1); color: #fff; }

/* ── Alerts ── */
.am-error {
    background: rgba(244,63,94,.09);
    border: 1px solid rgba(244,63,94,.28);
    border-radius: 9px;
    color: #FCA5A5;
    font-size: .88rem;
    padding: 13px 17px;
    margin-bottom: 22px;
    display: none;
    line-height: 1.5;
}
.am-error.show { display: block; }
.am-success {
    background: rgba(16,185,129,.09);
    border: 1px solid rgba(16,185,129,.28);
    border-radius: 9px;
    color: #6EE7B7;
    font-size: .88rem;
    padding: 13px 17px;
    margin-bottom: 22px;
    display: none;
    align-items: center;
    gap: 8px;
}
.am-success.show { display: flex; }

/* ── Logged-in badge ── */
.am-logged-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(16,185,129,.07);
    border: 1px solid rgba(16,185,129,.18);
    border-radius: 11px;
    padding: 14px 18px;
    color: #6EE7B7;
    font-size: .86rem;
    font-weight: 600;
    margin-bottom: 28px;
}

/* ── Section titles ── */
.am-title {
    font-size: 1.65rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: -.01em;
    background: linear-gradient(90deg, #fff 20%, rgba(255,255,255,.55) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 8px;
}
.am-sub {
    font-size: .88rem;
    color: rgba(255,255,255,.38);
    margin-bottom: 36px;
    line-height: 1.55;
}

/* ── Invoice toggle ── */
.am-invoice-toggle {
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    padding: 17px 20px;
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 12px;
    margin-bottom: 20px;
    transition: all .3s ease;
    user-select: none;
}
.am-invoice-toggle:hover { background: rgba(255,255,255,.05); border-color: rgba(255,255,255,.11); }
.am-chkbox {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    border: 2px solid rgba(255,255,255,.18);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .3s ease;
}
.am-chkbox.checked {
    background: linear-gradient(135deg, #F43F5E, #A855F7);
    border-color: transparent;
}
.am-invoice-fields {
    overflow: hidden;
    max-height: 0;
    transition: max-height .45s cubic-bezier(.16,1,.3,1);
}
.am-invoice-fields.open { max-height: 420px; }

/* ── Payment layout ── */
.am-pay-layout {
    display: grid;
    grid-template-columns: 1fr 390px;
    gap: 28px;
    align-items: start;
}
@media (max-width: 900px) {
    .am-pay-layout { grid-template-columns: 1fr; }
}

/* ── Order summary card ── */
.am-summary {
    background: linear-gradient(160deg, rgba(168,85,247,.06), rgba(244,63,94,.04));
    border: 1px solid rgba(168,85,247,.18);
    border-radius: 22px;
    padding: 38px;
    position: sticky;
    top: 96px;
    box-shadow: 0 20px 50px rgba(0,0,0,.4);
}
.am-summary-badge {
    font-size: .63rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: #F43F5E;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.am-summary-name {
    font-size: 1.45rem;
    font-weight: 900;
    text-transform: uppercase;
    background: linear-gradient(90deg, #fff 30%, #C4B5FD 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 6px;
}
.am-summary-price {
    font-size: 2.6rem;
    font-weight: 900;
    color: #fff;
    line-height: 1;
    margin-bottom: 4px;
}
.am-summary-price sup {
    font-size: 1rem;
    font-weight: 600;
    color: rgba(255,255,255,.45);
    font-family: var(--fb);
    vertical-align: super;
}
.am-summary-tax {
    font-size: .76rem;
    color: rgba(255,255,255,.35);
    margin-bottom: 22px;
}
.am-summary-div {
    height: 1px;
    background: rgba(255,255,255,.06);
    margin: 20px 0;
}
.am-summary-feats {
    list-style: none;
    padding: 0;
    margin: 0 0 22px;
    display: flex;
    flex-direction: column;
    gap: 9px;
}
.am-summary-feats li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: .82rem;
    color: rgba(255,255,255,.72);
    font-weight: 500;
    line-height: 1.4;
}
.am-summary-feats li::before {
    content: '✓';
    color: #10B981;
    font-weight: 900;
    font-size: .85rem;
    flex-shrink: 0;
    margin-top: 1px;
}
.am-summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
}

/* ── Payment method box ── */
.am-pay-method {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 13px;
    padding: 20px;
    margin-bottom: 28px;
}
.am-pay-method-label {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: rgba(255,255,255,.35);
    margin-bottom: 14px;
}
.am-pay-method-row {
    display: flex;
    align-items: center;
    gap: 14px;
    color: #fff;
    font-weight: 600;
}
.am-pay-icon {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* ── Legal checkboxes ── */
.am-legal {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    cursor: pointer;
    margin-bottom: 18px;
    user-select: none;
}
.am-legal-box {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    border: 2px solid rgba(255,255,255,.18);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .3s ease;
    margin-top: 1px;
    cursor: pointer;
}
.am-legal-box.checked {
    background: linear-gradient(135deg, #F43F5E, #A855F7);
    border-color: transparent;
}
.am-legal-txt {
    font-size: .82rem;
    color: rgba(255,255,255,.5);
    line-height: 1.55;
}
.am-legal-txt a { color: #93C5FD; text-decoration: underline; text-underline-offset: 2px; }
.am-legal-txt a:hover { color: #60A5FA; }

/* ── Spinner ── */
.am-spinner {
    width: 18px; height: 18px;
    border: 2px solid rgba(255,255,255,.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: amSpin .65s linear infinite;
    display: none; flex-shrink: 0;
}
.am-spinner.show { display: block; }
@keyframes amSpin { to { transform: rotate(360deg); } }

/* ── Pay button ── */
.am-pay-btn {
    width: 100%;
    background: linear-gradient(135deg, #F43F5E, #A855F7);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 13px;
    color: #fff;
    font-family: var(--fb);
    font-weight: 900;
    font-size: 1.15rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: 20px 32px;
    cursor: pointer;
    transition: all .35s ease;
    box-shadow: 0 10px 30px rgba(244,63,94,.35);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 8px;
}
.am-pay-btn:hover { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(244,63,94,.5); }
.am-pay-btn:disabled { opacity: .45; cursor: not-allowed; transform: none; }
</style>

<?php if ($is_order_received && $order): 
    $order_total = $order->get_total();
    $payment_method = $order->get_payment_method_title();
    $bacs_accounts = get_option('woocommerce_bacs_accounts');
    $dashboard_page = get_page_by_path('dashboard');
    $dashboard_url  = $dashboard_page ? get_permalink($dashboard_page) : home_url('/dashboard/');
    
    $status = $order->get_status();
    $is_paid = in_array($status, array('completed', 'processing'), true);
    $is_pending = ($status === 'pending');
    $is_failed = in_array($status, array('failed', 'cancelled'), true);
    
    // Ustalamy kolory i teksty w zależności od statusu
    if ($is_paid) {
        $status_label = 'AKTYWNE';
        $status_color = '#10B981';
        $title_text = 'Dziękujemy za zamówienie!';
        $sub_text = 'Twoje zamówienie <strong>#' . esc_html($order_id) . '</strong> zostało pomyślnie opłacone.';
        $desc_text = 'Dane do logowania do Panelu Sponsora zostały wysłane na Twój e-mail. Możesz przejść do panelu bezpośrednio, klikając poniższy przycisk.';
        $btn_text = 'Przejdź do Panelu Sponsora';
        $btn_url = $dashboard_url;
        $icon_html = '<div style="width: 72px; height: 72px; background: rgba(16, 185, 129, 0.1); border: 2px solid #10B981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; color: #10B981; box-shadow: 0 0 30px rgba(16,185,129,0.3);"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>';
    } elseif ($is_pending) {
        $status_label = 'OCZEKUJE NA PŁATNOŚĆ';
        $status_color = '#F59E0B';
        $title_text = 'Oczekiwanie na płatność';
        $sub_text = 'Twoje zamówienie <strong>#' . esc_html($order_id) . '</strong> zostało zarejestrowane.';
        $desc_text = 'Oczekujemy na zaksięgowanie wpłaty. Po pomyślnym przetworzeniu płatności przez bramkę, Twoje konto zostanie natychmiast aktywowane, a dane do logowania otrzymasz e-mailem.';
        $btn_text = 'Spróbuj zapłacić ponownie';
        $btn_url = $order->get_checkout_payment_url();
        $icon_html = '<div style="width: 72px; height: 72px; background: rgba(245, 158, 11, 0.1); border: 2px solid #F59E0B; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; color: #F59E0B; box-shadow: 0 0 30px rgba(245,158,11,0.3);"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>';
    } else { // failed / cancelled
        $status_label = 'NIEAKTYWNE (BŁĄD PŁATNOŚCI)';
        $status_color = '#EF4444';
        $title_text = 'Płatność nie powiodła się';
        $sub_text = 'Niestety, płatność za zamówienie <strong>#' . esc_html($order_id) . '</strong> została odrzucona lub anulowana.';
        $desc_text = 'Twoje konto sponsorskie nie zostało jeszcze aktywowane. Możesz spróbować sfinalizować płatność ponownie za pomocą poniższego przycisku.';
        $btn_text = 'Spróbuj ponownie opłacić zamówienie';
        $btn_url = $order->get_checkout_payment_url();
        $icon_html = '<div style="width: 72px; height: 72px; background: rgba(239, 68, 68, 0.1); border: 2px solid #EF4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; color: #EF4444; box-shadow: 0 0 30px rgba(239,68,68,0.3);"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div>';
    }
    ?>
    <div class="am-co-wrap" style="max-width: 600px;">
        <div class="am-card" style="text-align: center;">
            <?php echo $icon_html; ?>
            
            <h2 class="am-title" style="background: linear-gradient(90deg, #FFFFFF, <?php echo $status_color; ?>); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?php echo esc_html($title_text); ?></h2>
            <p class="am-sub" style="font-size: 1rem; color: rgba(255,255,255,0.7); margin-bottom: 24px;">
                <?php echo $sub_text; ?>
            </p>
            
            <div class="am-summary-div"></div>
            
            <div style="text-align: left; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 20px; margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="color: rgba(255,255,255,0.4);">Status konta:</span>
                    <span style="color: <?php echo $status_color; ?>; font-weight: 700;"><?php echo esc_html($status_label); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="color: rgba(255,255,255,0.4);">Kwota brutto:</span>
                    <span style="color: #fff; font-weight: 700;"><?php echo number_format($order_total, 2, ',', ' '); ?> zł</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: rgba(255,255,255,0.4);">Metoda płatności:</span>
                    <span style="color: #fff; font-weight: 700;"><?php echo esc_html($payment_method); ?></span>
                </div>
            </div>

            <?php if ($order->get_payment_method() === 'bacs' && $is_pending): ?>
                <div style="text-align: left; background: rgba(59,130,246,0.05); border: 1px solid rgba(59,130,246,0.2); border-radius: 12px; padding: 20px; margin-bottom: 30px;">
                    <h4 style="color: #fff; margin-top: 0; margin-bottom: 10px; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em; background: linear-gradient(90deg, #fff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Dane do przelewu tradycyjnego:</h4>
                    <?php 
                    if ( ! empty( $bacs_accounts ) ) {
                        foreach ( $bacs_accounts as $account ) {
                            echo '<p style="margin: 5px 0; font-size: 0.88rem; color: rgba(255,255,255,0.7);">';
                            if ( ! empty( $account['account_name'] ) ) {
                                echo '<strong>Nazwa odbiorcy:</strong> ' . esc_html( $account['account_name'] ) . '<br>';
                            }
                            if ( ! empty( $account['bank_name'] ) ) {
                                echo '<strong>Nazwa banku:</strong> ' . esc_html( $account['bank_name'] ) . '<br>';
                            }
                            if ( ! empty( $account['account_number'] ) ) {
                                echo '<strong>Numer konta:</strong> <code style="background: rgba(255,255,255,0.08); padding: 2px 6px; border-radius: 4px; color: #fff; font-size: 0.9rem;">' . esc_html( $account['account_number'] ) . '</code><br>';
                            }
                            if ( ! empty( $account['sort_code'] ) ) {
                                echo '<strong>Kod banku:</strong> ' . esc_html( $account['sort_code'] ) . '<br>';
                            }
                            if ( ! empty( $account['iban'] ) ) {
                                echo '<strong>IBAN:</strong> ' . esc_html( $account['iban'] ) . '<br>';
                            }
                            if ( ! empty( $account['bic'] ) ) {
                                echo '<strong>BIC / SWIFT:</strong> ' . esc_html( $account['bic'] ) . '<br>';
                            }
                            echo '</p>';
                        }
                    } else {
                        echo '<p style="font-size: 0.88rem; color: rgba(255,255,255,0.7); margin: 0;">Skonfiguruj dane konta bankowego w WooCommerce -> Ustawienia -> Płatności -> Przelew tradycyjny.</p>';
                    }
                    ?>
                    <p style="font-size: 0.8rem; color: rgba(255,255,255,0.4); margin: 10px 0 0 0; line-height: 1.4;">
                        W tytule przelewu prosimy wpisać: <strong>Zamówienie #<?php echo esc_html($order_id); ?></strong>. Twoje konto sponsorskie zostanie aktywowane po zaksięgowaniu wpłaty.
                    </p>
                </div>
            <?php endif; ?>

            <p style="font-size: 0.88rem; color: rgba(255,255,255,0.5); line-height: 1.6; margin-bottom: 30px;">
                <?php echo $desc_text; ?>
            </p>

            <a href="<?php echo esc_url($btn_url); ?>" class="am-pay-btn" style="text-decoration: none;">
                <span><?php echo esc_html($btn_text); ?></span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>
        </div>
    </div>
<?php else: ?>
<div class="am-co-wrap">

    <!-- ════ STEPPER ════ -->
    <div class="am-stepper">
        <div class="am-step <?php echo $is_logged_in ? 'done' : 'active'; ?>" id="si-1">
            <div class="am-step-circle">
                <?php if ($is_logged_in): ?>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                <?php else: ?>1<?php endif; ?>
            </div>
            <div class="am-step-label">Konto</div>
        </div>
        <div class="am-connector <?php echo $is_logged_in ? 'filled' : ''; ?>" id="conn-1"><div class="am-connector-fill"></div></div>
        <div class="am-step <?php echo $is_logged_in ? 'active' : ''; ?>" id="si-2">
            <div class="am-step-circle">2</div>
            <div class="am-step-label">Twoje Dane</div>
        </div>
        <div class="am-connector" id="conn-2"><div class="am-connector-fill"></div></div>
        <div class="am-step" id="si-3">
            <div class="am-step-circle">3</div>
            <div class="am-step-label">Płatność</div>
        </div>
    </div>

    <!-- ════ STEP 1: KONTO ════ -->
    <div class="am-panel <?php echo !$is_logged_in ? 'visible' : ''; ?>" id="panel-1">
        <div class="am-card" style="max-width:540px;margin:0 auto;">
            <h2 class="am-title">Twoje Konto</h2>
            <p class="am-sub">Zaloguj się lub utwórz nowe konto, aby zarządzać swoim Panelem Sponsora.</p>

            <div class="am-tabs">
                <button class="am-tab-btn active" onclick="switchTab('login',this)">Mam już konto</button>
                <button class="am-tab-btn" onclick="switchTab('register',this)">Nowe konto</button>
            </div>

            <!-- Login -->
            <div class="am-tab-panel active" id="tab-login">
                <div class="am-error" id="err-login"></div>
                <div class="am-field">
                    <label class="am-label" for="l-email">Adres email <span class="req">*</span></label>
                    <input type="email" id="l-email" class="am-input" placeholder="jan@twojamarka.pl" autocomplete="email">
                </div>
                <div class="am-field">
                    <label class="am-label" for="l-pass">Hasło <span class="req">*</span></label>
                    <input type="password" id="l-pass" class="am-input" placeholder="••••••••" autocomplete="current-password">
                </div>
                <button class="am-btn-main" id="btn-login" onclick="doLogin()">
                    <span>Zaloguj się i przejdź dalej</span>
                    <div class="am-spinner" id="sp-login"></div>
                </button>
            </div>

            <!-- Register -->
            <div class="am-tab-panel" id="tab-register">
                <div class="am-error" id="err-register"></div>
                <div class="am-field">
                    <label class="am-label" for="r-email">Adres email <span class="req">*</span></label>
                    <input type="email" id="r-email" class="am-input" placeholder="jan@twojamarka.pl" autocomplete="email">
                </div>
                <div class="am-row2">
                    <div class="am-field">
                        <label class="am-label" for="r-pass">Hasło <span class="req">*</span></label>
                        <input type="password" id="r-pass" class="am-input" placeholder="Min. 8 znaków" autocomplete="new-password">
                    </div>
                    <div class="am-field">
                        <label class="am-label" for="r-pass2">Powtórz hasło <span class="req">*</span></label>
                        <input type="password" id="r-pass2" class="am-input" placeholder="••••••••" autocomplete="new-password">
                    </div>
                </div>
                <button class="am-btn-main" id="btn-register" onclick="doRegister()">
                    <span>Utwórz konto i przejdź dalej</span>
                    <div class="am-spinner" id="sp-register"></div>
                </button>
            </div>
        </div>
    </div>

    <!-- ════ STEP 2: DANE ════ -->
    <div class="am-panel <?php echo $is_logged_in ? 'visible' : ''; ?>" id="panel-2">
        <div class="am-card" style="max-width:620px;margin:0 auto;">

            <?php if ($is_logged_in): ?>
            <div class="am-logged-badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Zalogowany jako <?php echo esc_html($current_user_email); ?>
            </div>
            <?php endif; ?>

            <h2 class="am-title">Twoje Dane</h2>
            <p class="am-sub">Podaj dane potrzebne do aktywacji Panelu Sponsora i ewentualnej faktury.</p>

            <div class="am-error" id="err-data"></div>

            <div class="am-field">
                <label class="am-label" for="d-ig">Profil Instagram do promocji <span class="req">*</span></label>
                <div class="am-input-wrap">
                    <div class="am-input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </div>
                    <input type="text" id="d-ig" class="am-input ig-input" placeholder="np. @twoja_marka">
                </div>
            </div>

            <div class="am-row2" style="margin-bottom:20px;">
                <div class="am-field" style="margin-bottom:0;">
                    <label class="am-label" for="d-first">Imię <span class="req">*</span></label>
                    <input type="text" id="d-first" class="am-input" placeholder="Jan" autocomplete="given-name">
                </div>
                <div class="am-field" style="margin-bottom:0;">
                    <label class="am-label" for="d-last">Nazwisko <span class="req">*</span></label>
                    <input type="text" id="d-last" class="am-input" placeholder="Kowalski" autocomplete="family-name">
                </div>
            </div>

            <div class="am-field">
                <label class="am-label" for="d-email">Adres email <span class="req">*</span></label>
                <input type="email" id="d-email" class="am-input" placeholder="jan@twojamarka.pl" value="<?php echo esc_attr($current_user_email); ?>" autocomplete="email">
            </div>



            <!-- Invoice toggle -->
            <div class="am-invoice-toggle" onclick="toggleInvoice()">
                <div class="am-chkbox" id="inv-box"></div>
                <div>
                    <div style="font-weight:700;font-size:.92rem;color:#fff;">Chcę otrzymać fakturę VAT</div>
                    <div style="font-size:.78rem;color:rgba(255,255,255,.38);margin-top:3px;">Podaj dane firmy do faktury</div>
                </div>
            </div>

            <div class="am-invoice-fields" id="inv-fields">
                <div class="am-field">
                    <label class="am-label" for="d-company">Nazwa firmy <span class="req">*</span></label>
                    <input type="text" id="d-company" class="am-input" placeholder="Twoja Firma sp. z o.o.">
                </div>
                <div class="am-row2">
                    <div class="am-field">
                        <label class="am-label" for="d-nip">NIP <span class="req">*</span></label>
                        <input type="text" id="d-nip" class="am-input" placeholder="0000000000">
                    </div>
                    <div class="am-field">
                        <label class="am-label" for="d-post">Kod pocztowy <span class="req">*</span></label>
                        <input type="text" id="d-post" class="am-input" placeholder="00-000">
                    </div>
                </div>
                <div class="am-field">
                    <label class="am-label" for="d-addr">Ulica i numer <span class="req">*</span></label>
                    <input type="text" id="d-addr" class="am-input" placeholder="ul. Przykładowa 1/2">
                </div>
                <div class="am-field">
                    <label class="am-label" for="d-city">Miasto <span class="req">*</span></label>
                    <input type="text" id="d-city" class="am-input" placeholder="Warszawa">
                </div>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px;">
                <?php if (!$is_logged_in): ?>
                <button class="am-btn-back" onclick="goStep(1)">← Wróć</button>
                <?php endif; ?>
                <button class="am-btn-main" onclick="goStep(3)" style="flex:1;">
                    <span>Dalej — Płatność</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ════ STEP 3: PŁATNOŚĆ ════ -->
    <div class="am-panel" id="panel-3">
        <div class="am-pay-layout">

            <!-- Lewa: zgody + przycisk -->
            <div class="am-card">
                <h2 class="am-title">Finalizacja</h2>
                <p class="am-sub">Sprawdź podsumowanie i sfinalizuj zakup swojego miejsca sponsorskiego.</p>

                <div class="am-error" id="err-pay"></div>

                <!-- Metoda płatności -->
                <?php
                $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
                $default_gateway = 'bacs';
                if (!empty($available_gateways)) {
                    $keys = array_keys($available_gateways);
                    $default_gateway = $keys[0];
                }
                ?>

                <?php if (!empty($available_gateways)) : ?>
                    <div class="am-pay-method">
                        <div class="am-pay-method-label">Metoda płatności</div>
                        <?php 
                        $is_first = true;
                        foreach ($available_gateways as $gateway_id => $gateway) : 
                            $checked = $is_first ? 'checked' : '';
                            $icon_html = $gateway->get_icon();
                        ?>
                            <label class="am-pay-method-row" style="display: flex; align-items: center; gap: 12px; cursor: pointer; margin-bottom: 12px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); padding: 15px; border-radius: 8px; transition: all 0.2s ease;">
                                <input type="radio" name="am-choose-payment" value="<?php echo esc_attr($gateway_id); ?>" <?php echo $checked; ?> onchange="document.getElementById('wc-payment-method').value = this.value;" style="accent-color: #F43F5E; width: 16px; height: 16px; margin: 0; cursor: pointer;">
                                <div class="am-pay-icon" style="margin-left: 5px; display: flex; align-items: center; justify-content: center; min-width: 40px;">
                                    <?php if ($icon_html) : ?>
                                        <div class="custom-gateway-logo-wrap">
                                            <?php echo $icon_html; ?>
                                        </div>
                                    <?php else : ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: rgba(255,255,255,0.6);"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                    <?php endif; ?>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-size:.92rem;font-weight:700; color: #fff;"><?php echo esc_html($gateway->get_title()); ?></div>
                                    <?php if ($gateway->get_description()) : ?>
                                        <div style="font-size:.75rem;color:rgba(255,255,255,.4);margin-top:2px;"><?php echo wp_kses_post($gateway->get_description()); ?></div>
                                    <?php endif; ?>
                                </div>
                            </label>
                        <?php 
                            $is_first = false;
                        endforeach; 
                        ?>
                        <style>
                            /* Stylizacja logotypów bramek płatności (Hotpay, Blik, itp.) */
                            .custom-gateway-logo-wrap img {
                                max-height: 22px !important;
                                width: auto !important;
                                height: auto !important;
                                display: block !important;
                                filter: brightness(1) contrast(1.05);
                                border-radius: 4px;
                            }
                            .custom-gateway-logo-wrap a {
                                pointer-events: none !important; /* blokujemy klikalność linków w logach */
                            }
                        </style>
                    </div>
                <?php else : ?>
                    <div class="am-pay-method">
                        <div class="am-pay-method-label" style="color: #F43F5E;">Brak aktywnych metod płatności w sklepie</div>
                    </div>
                <?php endif; ?>

                <!-- Zgody -->
                <div style="margin-bottom:28px;">
                    <label class="am-legal" onclick="toggleCheck('c1')">
                        <div class="am-legal-box" id="c1-box"></div>
                        <span class="am-legal-txt">
                            Zapoznałem/-am się z <a href="<?php echo esc_url($reg_url); ?>" target="_blank" onclick="event.stopPropagation()">Regulaminem</a>
                            oraz <a href="<?php echo esc_url($pol_url); ?>" target="_blank" onclick="event.stopPropagation()">Polityką prywatności</a>
                            i akceptuję ich treść. <span style="color:#F43F5E;">*</span>
                        </span>
                    </label>

                    <label class="am-legal" onclick="toggleCheck('c2')">
                        <div class="am-legal-box" id="c2-box"></div>
                        <span class="am-legal-txt">
                            Wyrażam zgodę na natychmiastowe wykonanie usługi i przyjmuję do wiadomości, że po uruchomieniu kampanii
                            <strong style="color:rgba(255,255,255,.8);">tracę prawo do odstąpienia od umowy</strong>
                            zgodnie z art.&nbsp;38 ust.&nbsp;1 pkt&nbsp;1 Ustawy o prawach konsumenta. <span style="color:#F43F5E;">*</span>
                        </span>
                    </label>
                </div>

                <div style="display:flex;gap:12px;">
                    <button class="am-btn-back" onclick="goStep(2)">← Wróć</button>
                    <button class="am-pay-btn" id="btn-pay" onclick="placeOrder()" style="flex:1;">
                        <span>Kupuję i Płacę</span>
                        <div class="am-spinner" id="sp-pay"></div>
                    </button>
                </div>

                <p style="text-align:center;font-size:.72rem;color:rgba(255,255,255,.2);margin-top:18px;">
                    🔒 Bezpieczna transmisja SSL · Twoje dane są chronione
                </p>
            </div>

            <!-- Prawa: podsumowanie -->
            <div class="am-summary">
                <div class="am-summary-badge">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                    Wybrany pakiet
                </div>
                <div class="am-summary-name"><?php echo esc_html($product_name); ?></div>
                <div class="am-summary-price">
                    <?php echo number_format($total_raw, 0, ',', '&nbsp;'); ?><sup>zł</sup>
                </div>
                <div class="am-summary-tax">brutto / jednorazowo</div>

                <div class="am-summary-div"></div>

                <ul class="am-summary-feats">
                    <?php foreach ($features as $f): ?>
                    <li><?php echo esc_html($f); ?></li>
                    <?php endforeach; ?>
                </ul>

                <div class="am-summary-div"></div>

                <div class="am-summary-total">
                    <span style="font-size:.84rem;color:rgba(255,255,255,.45);">Razem do zapłaty (brutto)</span>
                    <span style="font-size:1.35rem;font-weight:900;color:#fff;">
                        <?php echo number_format($total_raw, 0, ',', '&nbsp;'); ?> zł
                    </span>
                </div>
            </div>
        </div>
    </div>

</div><!-- /.am-co-wrap -->
<?php endif; ?>

<!-- Ukryty formularz WooCommerce — przetwarza zamówienie w tle -->
<form id="am-wc-form" style="display:none;" method="post">
    <input type="hidden" name="billing_first_name"  id="wc-first">
    <input type="hidden" name="billing_last_name"   id="wc-last">
    <input type="hidden" name="billing_email"        id="wc-email">
    <input type="hidden" name="billing_phone"        id="wc-phone">
    <input type="hidden" name="billing_address_1"   id="wc-addr">
    <input type="hidden" name="billing_city"         id="wc-city">
    <input type="hidden" name="billing_postcode"     id="wc-post">
    <input type="hidden" name="billing_country"      value="PL">
    <input type="hidden" name="billing_ig_username"  id="wc-ig">
    <input type="hidden" name="billing_company"      id="wc-company">
    <input type="hidden" name="billing_nip"          id="wc-nip">
    <input type="hidden" name="payment_method"       id="wc-payment-method" value="<?php echo esc_attr($default_gateway); ?>">
    <input type="hidden" name="woocommerce-process-checkout-nonce" value="<?php echo esc_attr($checkout_nonce); ?>">
    <input type="hidden" name="_wp_http_referer"     value="<?php echo esc_url($_SERVER['REQUEST_URI'] ?? '/zamowienie/'); ?>">
    <input type="hidden" name="createaccount"        value="0">
    <input type="hidden" name="terms"                value="on">
    <input type="hidden" name="terms-field"          value="1">
</form>

<script>
(function () {
    /* ── config ── */
    const AJAX       = '<?php echo esc_js(admin_url("admin-ajax.php")); ?>';
    const NONCE_L    = '<?php echo esc_js($login_nonce); ?>';
    const NONCE_R    = '<?php echo esc_js($register_nonce); ?>';
    const LOGGED_IN  = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

    let step       = LOGGED_IN ? 2 : 1;
    let invOpen    = false;
    let checks     = { c1: false, c2: false };

    /* ── stepper ── */
    function stepperUpdate(s) {
        [1, 2, 3].forEach(i => {
            const ind = document.getElementById('si-' + i);
            if (!ind) return;
            ind.classList.remove('active', 'done');
            const circ = ind.querySelector('.am-step-circle');
            if (i < s) {
                ind.classList.add('done');
                if (circ) circ.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
            } else if (i === s) {
                ind.classList.add('active');
                if (circ && circ.innerHTML.includes('<svg') && i !== (LOGGED_IN ? 1 : 0)) {
                    circ.innerHTML = i; // restore number on revisit
                }
            }
        });
        [1, 2].forEach(i => {
            const c = document.getElementById('conn-' + i);
            if (!c) return;
            if (i < s) c.classList.add('filled');
            else c.classList.remove('filled');
        });
    }

    /* ── go step ── */
    window.goStep = function (target) {
        if (target === 3) {
            const ig = v('d-ig'), fn = v('d-first'), ln = v('d-last'), em = v('d-email');
            if (!ig || !fn || !ln || !em) {
                showErr('err-data', 'Wypełnij wszystkie wymagane pola: profil Instagram, imię, nazwisko i email.');
                return;
            }
            hideErr('err-data');
        }
        document.getElementById('panel-' + step).classList.remove('visible');
        step = target;
        document.getElementById('panel-' + target).classList.add('visible');
        stepperUpdate(target);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    /* ── tabs ── */
    window.switchTab = function (tab, btn) {
        document.querySelectorAll('.am-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.am-tab-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');
    };

    /* ── login ── */
    window.doLogin = function () {
        const email = v('l-email'), pass = v('l-pass');
        if (!email || !pass) { showErr('err-login', 'Podaj adres email i hasło.'); return; }
        load('btn-login', 'sp-login', true);
        const fd = fd2({ action: 'aftermarket_login', email, password: pass, nonce: NONCE_L });
        post(fd).then(d => {
            load('btn-login', 'sp-login', false);
            if (d.success) {
                document.getElementById('d-email').value = email;
                goStep(2);
            } else {
                showErr('err-login', d.data?.message || 'Niepoprawny email lub hasło.');
            }
        }).catch(() => { load('btn-login', 'sp-login', false); showErr('err-login', 'Błąd połączenia.'); });
    };

    /* ── register ── */
    window.doRegister = function () {
        const email = v('r-email'), p1 = v('r-pass'), p2 = v('r-pass2');
        if (!email || !p1) { showErr('err-register', 'Wypełnij wszystkie pola.'); return; }
        if (p1 !== p2) { showErr('err-register', 'Hasła nie są takie same.'); return; }
        if (p1.length < 8) { showErr('err-register', 'Hasło musi mieć minimum 8 znaków.'); return; }
        load('btn-register', 'sp-register', true);
        const fd = fd2({ action: 'aftermarket_register', email, password: p1, nonce: NONCE_R });
        post(fd).then(d => {
            load('btn-register', 'sp-register', false);
            if (d.success) {
                document.getElementById('d-email').value = email;
                goStep(2);
            } else {
                showErr('err-register', d.data?.message || 'Nie udało się utworzyć konta.');
            }
        }).catch(() => { load('btn-register', 'sp-register', false); showErr('err-register', 'Błąd połączenia.'); });
    };

    /* ── Enter keys ── */
    document.addEventListener('keydown', e => {
        if (e.key !== 'Enter' || step !== 1) return;
        if (document.getElementById('tab-login').classList.contains('active')) doLogin();
        else doRegister();
    });

    /* ── invoice toggle ── */
    window.toggleInvoice = function () {
        invOpen = !invOpen;
        const box = document.getElementById('inv-box');
        const fields = document.getElementById('inv-fields');
        box.classList.toggle('checked', invOpen);
        box.innerHTML = invOpen ? checkMark() : '';
        fields.classList.toggle('open', invOpen);
    };

    /* ── legal checks ── */
    window.toggleCheck = function (id) {
        checks[id] = !checks[id];
        const box = document.getElementById(id + '-box');
        box.classList.toggle('checked', checks[id]);
        box.innerHTML = checks[id] ? checkMark() : '';
    };

    /* ── place order ── */
    window.placeOrder = function () {
        if (!checks.c1 || !checks.c2) {
            showErr('err-pay', 'Zaznacz obie wymagane zgody, aby kontynuować.');
            return;
        }
        hideErr('err-pay');

        set('wc-first',   v('d-first'));
        set('wc-last',    v('d-last'));
        set('wc-email',   v('d-email'));
        set('wc-phone',   '');
        set('wc-addr',    v('d-addr')  || 'brak');
        set('wc-city',    v('d-city')  || 'Polska');
        set('wc-post',    v('d-post')  || '00-000');
        set('wc-ig',      v('d-ig'));
        set('wc-company', v('d-company') || '');
        set('wc-nip',     v('d-nip')     || '');

        load('btn-pay', 'sp-pay', true);

        fetch('/?wc-ajax=checkout', {
            method: 'POST',
            credentials: 'same-origin',
            body: new FormData(document.getElementById('am-wc-form')),
        })
        .then(r => r.json())
        .then(d => {
            load('btn-pay', 'sp-pay', false);
            if (d.result === 'success' && d.redirect) {
                window.location.href = d.redirect;
            } else {
                const msg = d.messages ? d.messages.replace(/<[^>]+>/g, '').trim() : 'Nie udało się złożyć zamówienia. Sprawdź dane i spróbuj ponownie.';
                showErr('err-pay', msg);
            }
        })
        .catch(() => { load('btn-pay', 'sp-pay', false); showErr('err-pay', 'Błąd połączenia z serwerem. Odśwież stronę i spróbuj ponownie.'); });
    };

    /* ── utils ── */
    function v(id) { return (document.getElementById(id)?.value || '').trim(); }
    function set(id, val) { const el = document.getElementById(id); if (el) el.value = val; }
    function checkMark() { return '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>'; }
    function fd2(obj) { const f = new FormData(); Object.entries(obj).forEach(([k, val]) => f.append(k, val)); return f; }
    function post(fd) { return fetch(AJAX, { method: 'POST', credentials: 'same-origin', body: fd }).then(r => r.json()); }
    function showErr(id, msg) { const el = document.getElementById(id); if (el) { el.textContent = msg; el.classList.add('show'); } }
    function hideErr(id) { const el = document.getElementById(id); if (el) el.classList.remove('show'); }
    function load(btnId, spId, on) {
        const btn = document.getElementById(btnId);
        const sp  = document.getElementById(spId);
        const sp2 = btn?.querySelector('span');
        if (btn) btn.disabled = on;
        if (sp)  sp.classList.toggle('show', on);
        if (sp2) sp2.style.opacity = on ? '0.45' : '1';
    }

    /* ── init ── */
    stepperUpdate(step);
}());
</script>

<?php get_footer(); ?>
