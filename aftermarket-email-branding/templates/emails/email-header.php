<?php
/**
 * Dynamic Email Header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Pobieramy wartości z bazy
$base_color = get_option('woocommerce_email_base_color', '#F43F5E');
$logo_text  = get_option('am_email_logo_text', 'Aftermarket');

// Pobieramy kod szablonu z ustawień wtyczki
$header_template = get_option('am_email_custom_header_html', am_email_get_default_header());

// Podmieniamy znaczniki (placeholders) w locie
$header_template = str_replace('{language_attributes}', is_rtl() ? 'rightmargin="0" direction="rtl"' : 'leftmargin="0"', $header_template);
$header_template = str_replace('{site_title}', get_bloginfo('name', 'display'), $header_template);
$header_template = str_replace('{logo_text}', esc_html($logo_text), $header_template);
$header_template = str_replace('{base_color}', esc_attr($base_color), $header_template);
$header_template = str_replace('{email_heading}', esc_html($email_heading), $header_template);

echo $header_template;
