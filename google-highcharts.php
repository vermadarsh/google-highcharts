<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              multidots
 * @since             1.0.0
 * @package           Google_Highcharts
 *
 * @wordpress-plugin
 * Plugin Name:       Google Highcharts
 * Plugin URI:        google-highcharts
 * Description:       This plugin creates highchart shortcodes based on xlsx input.
 * Version:           1.0.0
 * Author:            Multidots
 * Author URI:        multidots
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       google-highcharts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'HC_PLUGIN_VERSION', '1.0.0' );

/**
 * Define plugin directory URL
 */
if( ! defined( 'HC_PLUGIN_URL' ) ) {
	define( 'HC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Define plugin directory path
 */
if( ! defined( 'HC_PLUGIN_PATH' ) ) {
	define( 'HC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-google-highcharts-activator.php
 */
function activate_google_highcharts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-highcharts-activator.php';
	Google_Highcharts_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-google-highcharts-deactivator.php
 */
function deactivate_google_highcharts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-highcharts-deactivator.php';
	Google_Highcharts_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_google_highcharts' );
register_deactivation_hook( __FILE__, 'deactivate_google_highcharts' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_google_highcharts() {

    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-google-highcharts.php';
	$plugin = new Google_Highcharts();
	$plugin->run();

}

/**
 * Initialize the plugin.
 */
add_action('plugins_loaded', 'hc_plugin_init');
function hc_plugin_init() {
    run_google_highcharts();
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'hc_plugin_links' );
}

/**
 * Settings link on plugin listing page
 */
function hc_plugin_links( $links ) {

    $hc_links = array(
        '<a href="'.admin_url('edit.php?post_type=highchart-shortcode').'">'.__( 'Settings', 'google-highcharts' ).'</a>'
    );
    return array_merge( $links, $hc_links );

}

if( !  function_exists( 'debug' ) ) {
    function debug( $params ) {
        echo '<pre>';
        print_r( $params );
        echo '</pre>';
    }
}