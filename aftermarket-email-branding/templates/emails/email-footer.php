<?php
/**
 * Dynamic Email Footer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Pobieramy wartości z bazy
$footer_copy = get_option('am_email_footer_copy', '&copy; ' . date('Y') . ' Aftermarket.ag. Wszelkie prawa zastrzeżone.');

// Pobieramy szablon z wtyczki
$footer_template = get_option('am_email_custom_footer_html', am_email_get_default_footer());

// Podmieniamy znaczniki
$footer_template = str_replace('{footer_copy}', wp_kses_post($footer_copy), $footer_template);
$footer_template = str_replace('{site_title}', get_bloginfo('name', 'display'), $footer_template);

echo $footer_template;
