<?php
/**
 * Plugin Name: Aftermarket Email Branding
 * Plugin URI: https://aftermarket.ag
 * Description: Premium Custom Dark-Mode Email Branding for WooCommerce with an interactive admin settings panel to customize templates, colors, and content directly from WordPress dashboard.
 * Version: 1.2.0
 * Author: Aftermarket Team
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Nadpisywanie szablonów WooCommerce z poziomu wtyczki (najwyższy priorytet)
add_filter( 'woocommerce_locate_template', 'am_email_brand_locate_template', 999, 3 );
function am_email_brand_locate_template( $template, $template_name, $template_path ) {
    $custom_templates = array(
        'emails/email-header.php',
        'emails/email-footer.php',
        'emails/email-styles.php'
    );
    if ( in_array( $template_name, $custom_templates ) ) {
        $plugin_template = plugin_dir_path( __FILE__ ) . 'templates/' . $template_name;
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }
    return $template;
}

// Domyślne wartości szablonów (kod HTML i CSS)
function am_email_get_default_header() {
    return '<!DOCTYPE html>
<html {language_attributes}>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title>{site_title}</title>
</head>
<body marginwidth="0" topmargin="0" marginheight="0" offset="0">
	<div id="wrapper">
		<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" id="template_container">
						<tr>
							<td align="center" valign="top">
								<!-- Header -->
								<table border="0" cellpadding="0" cellspacing="0" id="template_header">
									<tr>
										<td id="header_wrapper">
											<!-- Logo / Brand Name -->
											<div style="font-size: 26px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.5px; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;">
												{logo_text}<span style="color: {base_color};">.</span>
											</div>
											<h1 style="margin-top: 25px; margin-bottom: 0; color: #ffffff; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; font-size: 24px; font-weight: 700; line-height: 1.3; letter-spacing: -0.5px;">{email_heading}</h1>
										</td>
									</tr>
								</table>
								<!-- End Header -->
							</td>
						</tr>
						<tr>
							<td align="center" valign="top">
								<!-- Body -->
								<table border="0" cellpadding="0" cellspacing="0" id="template_body">
									<tr>
										<td valign="top" id="body_content">
											<!-- Content -->
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td valign="top" id="body_content_inner">';
}

function am_email_get_default_footer() {
    return '															</td>
														</tr>
													</table>
													<!-- End Content -->
												</td>
											</tr>
										</table>
										<!-- End Body -->
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<!-- Footer -->
							<table border="0" cellpadding="10" cellspacing="0" id="template_footer">
								<tr>
									<td valign="top" id="credit_wrapper">
										<table border="0" cellpadding="10" cellspacing="0" width="100%">
											<tr>
												<td colspan="2" valign="middle" id="credit">
													<p style="margin: 0 0 10px 0; font-size: 13px; color: #52525B; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;">
														{footer_copy}
													</p>
													<p style="margin: 0; font-size: 11px; color: #3F3F46; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;">
														Otrzymujesz tę wiadomość, ponieważ złożyłeś zamówienie lub zarejestrowałeś się w serwisie {site_title}.
													</p>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							<!-- End Footer -->
						</td>
					</tr>
				</table>
			</tr>
		</table>
	</div>
</body>
</html>';
}

function am_email_get_default_styles() {
    return 'body {
	background-color: {bg_color};
	padding: 0;
	margin: 0;
	-webkit-text-size-adjust: none !important;
	width: 100% !important;
}

#wrapper {
	background-color: {bg_color};
	margin: 0;
	padding: 70px 0;
	-webkit-text-size-adjust: none !important;
	width: 100% !important;
}

#template_container {
	box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
	background-color: {body_color};
	border: 1px solid {border_color};
	border-radius: 16px !important;
	overflow: hidden;
	max-width: 600px;
}

#template_header {
	background: linear-gradient(135deg, #1E1B4B 0%, #0B0B14 100%);
	border-bottom: 1px solid rgba(255, 255, 255, 0.06);
	color: #FFFFFF;
	border-top-left-radius: 16px !important;
	border-top-right-radius: 16px !important;
}

#header_wrapper {
	padding: 38px 48px;
	display: block;
}

#template_header h1 {
	color: #FFFFFF;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 24px;
	font-weight: 700;
	line-height: 1.3;
	margin: 0;
	text-shadow: 0 2px 10px rgba(0,0,0,0.3);
	-webkit-font-smoothing: antialiased;
}

#template_body {
	background-color: {body_color};
}

#body_content {
	background-color: {body_color};
}

#body_content_inner {
	color: {text_color};
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 15px;
	line-height: 150%;
	text-align: left;
	padding: 48px;
}

#template_footer {
	border-top: 1px solid {border_color};
	background-color: #09090F;
}

#credit {
	padding: 30px 48px;
	text-align: center;
	border: 0;
	color: #A1A1AA;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 12px;
	line-height: 150%;
}

a {
	color: {base_color};
	font-weight: normal;
	text-decoration: underline;
}

h1 {
	color: #FFFFFF;
	display: block;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 24px;
	font-weight: 700;
	line-height: 1.3;
	margin: 0 0 18px;
	text-align: left;
}

h2 {
	color: #FFFFFF;
	display: block;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 18px;
	font-weight: 600;
	line-height: 1.3;
	margin: 0 0 16px;
	text-align: left;
}

h3 {
	color: #FFFFFF;
	display: block;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 16px;
	font-weight: 600;
	line-height: 1.3;
	margin: 16px 0 8px;
	text-align: left;
}

.link-button {
	display: inline-block;
	padding: 13px 28px;
	background: linear-gradient(135deg, {base_color} 0%, #A855F7 100%);
	color: #FFFFFF !important;
	text-decoration: none !important;
	font-weight: 700;
	font-size: 14px;
	border-radius: 9999px;
	box-shadow: 0 8px 16px rgba(244, 63, 94, 0.2);
	border: 1px solid rgba(255, 255, 255, 0.12);
	margin: 20px 0;
}

#addresses {
	margin-top: 20px;
}

.td {
	color: {text_color};
	border: 1px solid {border_color};
	vertical-align: middle;
}

.address {
	padding: 12px;
	color: #A1A1AA;
	border: 1px solid {border_color};
}

.text {
	color: {text_color};
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

table.td-table {
	width: 100%;
	border-collapse: collapse;
	margin-top: 15px;
	margin-bottom: 25px;
}

table.td-table th {
	border-bottom: 2px solid {border_color};
	color: #FFFFFF;
	font-weight: 700;
	padding: 12px 10px;
	text-align: left;
}

table.td-table td {
	border-bottom: 1px solid {border_color};
	padding: 12px 10px;
	color: {text_color};
}

table.td-table tfoot td {
	border-bottom: none;
	font-weight: 700;
	padding: 8px 10px;
}

table.td-table tfoot tr:first-child td {
	border-top: 2px solid {border_color};
	padding-top: 15px;
}';
}

// Tworzenie menu w panelu administratora
add_action('admin_menu', 'am_email_branding_add_menu');
function am_email_branding_add_menu() {
    add_menu_page(
        'E-maile Aftermarket',
        'Branding E-maili',
        'manage_options',
        'am-email-branding',
        'am_email_branding_render_settings',
        'dashicons-email-alt',
        57
    );
}

// Inicjalizacja skryptów i stylów WordPress Color Picker w panelu wtyczki
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'toplevel_page_am-email-branding') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
});

// Renderowanie strony ustawień wtyczki
function am_email_branding_render_settings() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Zapisywanie formularza
    if (isset($_POST['am_email_save_branding']) && check_admin_referer('am_email_branding_action', 'am_email_branding_nonce')) {
        update_option('woocommerce_email_base_color', sanitize_hex_color($_POST['am_email_base_color']));
        update_option('woocommerce_email_background_color', sanitize_hex_color($_POST['am_email_background_color']));
        update_option('woocommerce_email_body_background_color', sanitize_hex_color($_POST['am_email_body_background_color']));
        update_option('woocommerce_email_text_color', sanitize_hex_color($_POST['am_email_text_color']));

        update_option('am_email_logo_text', sanitize_text_field($_POST['am_email_logo_text']));
        update_option('am_email_footer_copy', wp_kses_post($_POST['am_email_footer_copy']));

        update_option('am_email_custom_header_html', wp_kses_post(stripslashes($_POST['am_email_custom_header_html'])));
        update_option('am_email_custom_footer_html', wp_kses_post(stripslashes($_POST['am_email_custom_footer_html'])));
        update_option('am_email_custom_styles_css', wp_strip_all_tags(stripslashes($_POST['am_email_custom_styles_css'])));

        // Czyszczenie cache WooCommerce
        delete_transient('woocommerce_template_directory');
        if (function_exists('wc_delete_product_transients')) {
            wc_delete_product_transients();
        }

        echo '<div class="notice notice-success is-dismissible" style="border-left-color: #F43F5E;"><p><strong>Gotowe! Zmiany zostały zapisane i wdrożone.</strong></p></div>';
    }

    // Pobieramy obecne wartości z bazy
    $base_color = get_option('woocommerce_email_base_color', '#F43F5E');
    $bg_color   = get_option('woocommerce_email_background_color', '#0B0B14');
    $body_color = get_option('woocommerce_email_body_background_color', '#121221');
    $text_color = get_option('woocommerce_email_text_color', '#D4D4D8');

    $logo_text   = get_option('am_email_logo_text', 'Aftermarket');
    $footer_copy = get_option('am_email_footer_copy', '&copy; ' . date('Y') . ' Aftermarket.ag. Wszelkie prawa zastrzeżone.');

    $custom_header = get_option('am_email_custom_header_html', am_email_get_default_header());
    $custom_footer = get_option('am_email_custom_footer_html', am_email_get_default_footer());
    $custom_styles = get_option('am_email_custom_styles_css', am_email_get_default_styles());

    ?>
    <div class="wrap" style="max-width: 800px; font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,sans-serif; background: #fff; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0; margin-top: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <h1 style="font-weight: 800; font-size: 26px; margin: 0 0 10px 0; color: #111;">
            🎨 Kreator Wyglądu E-maili <span style="color: #F43F5E;">Aftermarket</span>
        </h1>
        <p style="color: #64748b; font-size: 15px; margin: 0 0 30px 0; line-height: 1.5;">
            Dostosuj kolory, logo oraz stopkę wiadomości wysyłanych do Twoich klientów. Wszystkie zmiany zapisują się automatycznie.
        </p>

        <form method="post" action="">
            <?php wp_nonce_field('am_email_branding_action', 'am_email_branding_nonce'); ?>

            <!-- KOLORY -->
            <h3 style="font-size: 16px; font-weight: 700; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; margin-top: 0; color: #1e293b;">1. Kolory wiadomości</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px; font-size:14px; color:#334155;">Główny kolor akcentów (przyciski, kropka w logo):</label>
                    <input type="text" name="am_email_base_color" class="color-picker-field" value="<?php echo esc_attr($base_color); ?>" />
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px; font-size:14px; color:#334155;">Kolor tła na zewnątrz (tło maila):</label>
                    <input type="text" name="am_email_background_color" class="color-picker-field" value="<?php echo esc_attr($bg_color); ?>" />
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px; font-size:14px; color:#334155;">Kolor tła wewnątrz (karta wiadomości):</label>
                    <input type="text" name="am_email_body_background_color" class="color-picker-field" value="<?php echo esc_attr($body_color); ?>" />
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px; font-size:14px; color:#334155;">Kolor głównego tekstu:</label>
                    <input type="text" name="am_email_text_color" class="color-picker-field" value="<?php echo esc_attr($text_color); ?>" />
                </div>
            </div>

            <!-- TREŚĆ -->
            <h3 style="font-size: 16px; font-weight: 700; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; margin-top: 30px; color: #1e293b;">2. Treści nagłówka i stopki</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="am_email_logo_text" style="display:block; font-weight:600; margin-bottom:8px; font-size:14px; color:#334155;">Tekst Logo (wyświetla się na samej górze wiadomości):</label>
                <input type="text" name="am_email_logo_text" id="am_email_logo_text" value="<?php echo esc_attr($logo_text); ?>" style="width:100%; max-width:400px; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size:14px;" />
            </div>

            <div style="margin-bottom: 30px;">
                <label for="am_email_footer_copy" style="display:block; font-weight:600; margin-bottom:8px; font-size:14px; color:#334155;">Podpis stopki (prawa autorskie na samym dole):</label>
                <textarea name="am_email_footer_copy" id="am_email_footer_copy" rows="2" style="width:100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size:14px; font-family:sans-serif;"><?php echo esc_textarea($footer_copy); ?></textarea>
            </div>

            <!-- UKRYTE ZAAWANSOWANE OPCJE KODU -->
            <details style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 30px;">
                <summary style="font-weight: 700; color: #475569; cursor: pointer; outline: none; font-size: 14px; user-select: none;">
                    🛠️ Opcje zaawansowane (Dla programistów - edycja kodu HTML/CSS)
                </summary>
                
                <div style="margin-top: 15px;">
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 15px;">
                        ⚠️ Nie edytuj poniższych pól, jeśli nie znasz HTML/CSS. Zostaw je domyślne, aby zachować luksusowy układ.
                    </p>

                    <h4 style="font-weight:600; font-size:13px; margin: 15px 0 5px 0;">Kod Nagłówka (HTML):</h4>
                    <textarea name="am_email_custom_header_html" rows="8" style="width: 100%; font-family: monospace; font-size: 12px; padding: 8px; border:1px solid #cbd5e1; border-radius:4px;"><?php echo esc_textarea($custom_header); ?></textarea>

                    <h4 style="font-weight:600; font-size:13px; margin: 15px 0 5px 0;">Kod Stopki (HTML):</h4>
                    <textarea name="am_email_custom_footer_html" rows="8" style="width: 100%; font-family: monospace; font-size: 12px; padding: 8px; border:1px solid #cbd5e1; border-radius:4px;"><?php echo esc_textarea($custom_footer); ?></textarea>

                    <h4 style="font-weight:600; font-size:13px; margin: 15px 0 5px 0;">Arkusz Stylów CSS:</h4>
                    <textarea name="am_email_custom_styles_css" rows="8" style="width: 100%; font-family: monospace; font-size: 12px; padding: 8px; border:1px solid #cbd5e1; border-radius:4px;"><?php echo esc_textarea($custom_styles); ?></textarea>
                </div>
            </details>

            <!-- ZAPIS -->
            <div style="border-top: 1px solid #f1f5f9; padding-top: 20px; display: flex; align-items: center; gap: 15px;">
                <input type="submit" name="am_email_save_branding" class="button button-primary button-large" style="background: #F43F5E; border-color: #E11D48; font-weight:700; padding: 8px 30px; height: auto; font-size: 15px; border-radius: 6px; box-shadow: 0 4px 6px -1px rgba(244,63,94,0.3);" value="Zapisz ustawienia" />
                <a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=email')); ?>" class="button button-large" style="padding:8px 20px; height:auto; font-size:15px; border-radius:6px;">Podgląd szablonu</a>
            </div>
        </form>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('.color-picker-field').wpColorPicker();
        });
    </script>
    <?php
}
