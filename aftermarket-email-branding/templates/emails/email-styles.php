<?php
/**
 * Dynamic Email Styles override in plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Pobieramy kolory z bazy
$base_color = get_option('woocommerce_email_base_color', '#F43F5E');
$bg_color   = get_option('woocommerce_email_background_color', '#0B0B14');
$body_color = get_option('woocommerce_email_body_background_color', '#121221');
$text_color = get_option('woocommerce_email_text_color', '#D4D4D8');
$border     = 'rgba(255,255,255,0.08)';

// Pobieramy kod CSS z wtyczki
$css_template = get_option('am_email_custom_styles_css', am_email_get_default_styles());

// Podmieniamy znaczniki w kodzie CSS
$css_template = str_replace('{base_color}', esc_attr($base_color), $css_template);
$css_template = str_replace('{bg_color}', esc_attr($bg_color), $css_template);
$css_template = str_replace('{body_color}', esc_attr($body_color), $css_template);
$css_template = str_replace('{text_color}', esc_attr($text_color), $css_template);
$css_template = str_replace('{border_color}', esc_attr($border), $css_template);

echo $css_template;
