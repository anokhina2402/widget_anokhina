<?php
/*
Plugin Name: Widget Anokhina
Plugin URI:
Description: Widget of Temperature and Exchange Rates
Author: Anokhina Olha
Author URI:
Text Domain: widget_anokhina
Domain Path:
Version: 1.0.0
*/

define( 'WIDGET_ANOKHINA_VERSION', '1.0.0' );

define( 'WIDGET_ANOKHINA_REQUIRED_WP_VERSION', '4.4' );

define( 'WIDGET_ANOKHINA_PLUGIN', __FILE__ );

define( 'WIDGET_ANOKHINA_PLUGIN_BASENAME', plugin_basename( WIDGET_ANOKHINA_PLUGIN ) );

define( 'WIDGET_ANOKHINA_PLUGIN_NAME', trim( dirname( WIDGET_ANOKHINA_PLUGIN_BASENAME ), '/' ) );

define( 'WIDGET_ANOKHINA_PLUGIN_DIR', untrailingslashit( dirname( WIDGET_ANOKHINA_PLUGIN ) ) );

define( 'WIDGET_ANOKHINA_TEMPLATES_DIR', WIDGET_ANOKHINA_PLUGIN_DIR . '/templates' );

define( 'WIDGET_ANOKHINA_INCLUDES_DIR', WIDGET_ANOKHINA_PLUGIN_DIR . '/includes' );

define( 'WIDGET_ANOKHINA_URL', get_site_url() . '/wp-content/plugins/widget_anokhina/' );

define( 'WIDGET_ANOKHINA_LAT', '50.433334' );

define( 'WIDGET_ANOKHINA_LON', '30.516666' );

define( 'WIDGET_ANOKHINA_UNIT', 'c' );

define( 'WIDGET_ANOKHINA_TEMP_API_KEY', 'e67b4bcb2aa916f14208f63ed6db1197' );

define( 'WIDGET_ANOKHINA_TEMP_API_URL', 'https://api.darksky.net/forecast/' );

define( 'WIDGET_ANOKHINA_RATE_API_URL', 'https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange' );

define( 'WIDGET_ANOKHINA_CURRENCY', 'USD' );

if ( ! defined( 'WIDGET_ANOKHINA_LOAD_JS' ) ) {
	define( 'WIDGET_ANOKHINA_LOAD_JS', true );
}

if ( ! defined( 'WIDGET_ANOKHINA_LOAD_CSS' ) ) {
	define( 'WIDGET_ANOKHINA_LOAD_CSS', true );
}

define( 'WIDGET_ANOKHINA_PLUGIN_URL', untrailingslashit( plugins_url( '', WIDGET_ANOKHINA_PLUGIN ) ) );

add_action('widgets_init', 'widget_anokhina');

function widget_anokhina() {

    require WIDGET_ANOKHINA_INCLUDES_DIR . '/widget-anokhina.php';
    return register_widget("Widget_Anokhina");
}

add_action( 'wp_enqueue_scripts', 'widget_anokhina_enqueue_scripts' );

function widget_anokhina_enqueue_scripts(){

    wp_enqueue_style(
        'widget_anokhina',
        WIDGET_ANOKHINA_URL . 'includes/css/widget_anokhina.css',
        array(),
        time()
        );
}

add_action( 'admin_enqueue_scripts', 'widget_anokhina_admin_enqueue_scripts' );

function widget_anokhina_admin_enqueue_scripts(){

    wp_enqueue_script(
        'twidget_anokhina_main',
        WIDGET_ANOKHINA_URL . 'includes/js/admin.js'
        ,
        array('jquery'),
        time(),
        false
    );

}

