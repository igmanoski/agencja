<?php
/**
 * Plugin Name: Aftermarket Email Branding
 * Plugin URI: https://aftermarket.ag
 * Description: Premium Hardcoded Dark-Mode Email Branding for WooCommerce. Implements the gorgeous dark-cyber aesthetic across all transactional emails.
 * Version: 2.0.0
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

// Automatyczne ustawianie odpowiednich opcji w bazie przy aktywacji wtyczki (dla kompatybilności z WooCommerce customizerem)
register_activation_hook( __FILE__, 'am_email_branding_activate_plugin' );
function am_email_branding_activate_plugin() {
    update_option('woocommerce_email_base_color', '#F43F5E');
    update_option('woocommerce_email_background_color', '#0B0B14');
    update_option('woocommerce_email_body_background_color', '#121221');
    update_option('woocommerce_email_text_color', '#D4D4D8');
    
    // Czyszczenie cache szablonów
    delete_transient('woocommerce_template_directory');
    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients();
    }
}
