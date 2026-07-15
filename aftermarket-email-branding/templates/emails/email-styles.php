<?php
/**
 * Email Styles override in plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load colors
$bg              = '#0B0B14';
$body            = '#121221';
$base            = '#F43F5E';
$base_text       = '#FFFFFF';
$text            = '#D4D4D8';
$text_muted      = '#A1A1AA';
$border_color    = 'rgba(255,255,255,0.08)';

?>
body {
	background-color: <?php echo esc_attr( $bg ); ?>;
	padding: 0;
	margin: 0;
	-webkit-text-size-adjust: none !important;
	width: 100% !important;
}

#wrapper {
	background-color: <?php echo esc_attr( $bg ); ?>;
	margin: 0;
	padding: 70px 0;
	-webkit-text-size-adjust: none !important;
	width: 100% !important;
}

#template_container {
	box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
	background-color: <?php echo esc_attr( $body ); ?>;
	border: 1px solid <?php echo esc_attr( $border_color ); ?>;
	border-radius: 16px !important;
	overflow: hidden;
	max-width: 600px;
}

#template_header {
	background: linear-gradient(135deg, #1E1B4B 0%, #0B0B14 100%);
	border-bottom: 1px solid rgba(255, 255, 255, 0.06);
	color: <?php echo esc_attr( $base_text ); ?>;
	border-top-left-radius: 16px !important;
	border-top-right-radius: 16px !important;
}

#header_wrapper {
	padding: 38px 48px;
	display: block;
}

#template_header h1 {
	color: <?php echo esc_attr( $base_text ); ?>;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 24px;
	font-weight: 700;
	line-height: 1.3;
	margin: 0;
	text-shadow: 0 2px 10px rgba(0,0,0,0.3);
	-webkit-font-smoothing: antialiased;
}

#template_body {
	background-color: <?php echo esc_attr( $body ); ?>;
}

#body_content {
	background-color: <?php echo esc_attr( $body ); ?>;
}

#body_content_inner {
	color: <?php echo esc_attr( $text ); ?>;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 15px;
	line-height: 150%;
	text-align: left;
	padding: 48px;
}

#template_footer {
	border-top: 1px solid <?php echo esc_attr( $border_color ); ?>;
	background-color: #09090F;
}

#credit {
	padding: 30px 48px;
	text-align: center;
	border: 0;
	color: <?php echo esc_attr( $text_muted ); ?>;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 12px;
	line-height: 150%;
}

a {
	color: <?php echo esc_attr( $base ); ?>;
	font-weight: normal;
	text-decoration: underline;
}

h1 {
	color: <?php echo esc_attr( $base_text ); ?>;
	display: block;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 24px;
	font-weight: 700;
	line-height: 1.3;
	margin: 0 0 18px;
	text-align: left;
}

h2 {
	color: <?php echo esc_attr( $base_text ); ?>;
	display: block;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 18px;
	font-weight: 600;
	line-height: 1.3;
	margin: 0 0 16px;
	text-align: left;
}

h3 {
	color: <?php echo esc_attr( $base_text ); ?>;
	display: block;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	font-size: 16px;
	font-weight: 600;
	line-height: 1.3;
	margin: 16px 0 8px;
	text-align: left;
}

a.link {
	color: <?php echo esc_attr( $base ); ?>;
}

.link-button {
	display: inline-block;
	padding: 13px 28px;
	background: linear-gradient(135deg, #F43F5E 0%, #A855F7 100%);
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
	color: <?php echo esc_attr( $text ); ?>;
	border: 1px solid <?php echo esc_attr( $border_color ); ?>;
	vertical-align: middle;
}

.address {
	padding: 12px;
	color: <?php echo esc_attr( $text_muted ); ?>;
	border: 1px solid <?php echo esc_attr( $border_color ); ?>;
}

.text {
	color: <?php echo esc_attr( $text ); ?>;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

table.td-table {
	width: 100%;
	border-collapse: collapse;
	margin-top: 15px;
	margin-bottom: 25px;
}

table.td-table th {
	border-bottom: 2px solid <?php echo esc_attr( $border_color ); ?>;
	color: <?php echo esc_attr( $base_text ); ?>;
	font-weight: 700;
	padding: 12px 10px;
	text-align: left;
}

table.td-table td {
	border-bottom: 1px solid <?php echo esc_attr( $border_color ); ?>;
	padding: 12px 10px;
	color: <?php echo esc_attr( $text ); ?>;
}

table.td-table tfoot td {
	border-bottom: none;
	font-weight: 700;
	padding: 8px 10px;
}

table.td-table tfoot tr:first-child td {
	border-top: 2px solid <?php echo esc_attr( $border_color ); ?>;
	padding-top: 15px;
}
