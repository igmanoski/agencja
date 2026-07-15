<?php
/**
 * Dynamic Email Footer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Pobieramy wartości z bazy
$bg_color    = get_option('woocommerce_email_background_color', '#0B0B14');
$body_color  = get_option('woocommerce_email_body_background_color', '#121221');
$footer_copy = get_option('am_email_footer_copy', '&copy; ' . date('Y') . ' Aftermarket.ag. Wszelkie prawa zastrzeżone.');

// Pobieramy szablon z wtyczki
$footer_template = get_option('am_email_custom_footer_html', am_email_get_default_footer());

// Podmieniamy znaczniki
$footer_template = str_replace('{footer_copy}', wp_kses_post($footer_copy), $footer_template);
$footer_template = str_replace('{site_title}', get_bloginfo('name', 'display'), $footer_template);
$footer_template = str_replace('{bg_color}', esc_attr($bg_color), $footer_template);
$footer_template = str_replace('{body_color}', esc_attr($body_color), $footer_template);
$footer_template = str_replace('{border_color}', 'rgba(255,255,255,0.08)', $footer_template);

echo $footer_template;
