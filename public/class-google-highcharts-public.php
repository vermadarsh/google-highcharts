<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       multidots
 * @since      1.0.0
 *
 * @package    Google_Highcharts
 * @subpackage Google_Highcharts/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Google_Highcharts
 * @subpackage Google_Highcharts/public
 * @author     Multidots <inquiry@multidots.com>
 */
class Google_Highcharts_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/google-highcharts-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
    	global $post;
	    if ( has_shortcode( $post->post_content, 'highchart') ) {
		    wp_enqueue_script($this->plugin_name, HC_PLUGIN_URL . 'public/js/google-highcharts-public.js', array('jquery'), $this->version, true);
	    }

    }

    /**
     * Create uploads directory for managing the file uploads.
     *
     * @since    1.0.0
     */
    public function hc_create_uploads_directory() {
        // Create uploads directory
        $wp_upload_dir = wp_upload_dir();
        $hc_upload_dir = $wp_upload_dir['basedir'] . '/google-sheets-uploads';
        $hc_upload_dir_url = $wp_upload_dir['baseurl'] . '/google-sheets-uploads';
        if (!file_exists($hc_upload_dir)) {
            mkdir($hc_upload_dir, 0755, true);
        }

        if (!defined('HC_UPLOADS_PATH')) {
            define('HC_UPLOADS_PATH', $hc_upload_dir);
        }

        if (!defined('HC_UPLOADS_URL')) {
            define('HC_UPLOADS_URL', $hc_upload_dir_url);
        }
    }

    /**
     * Single highchart template.
     *
     * @since    1.0.0
     */
    public function hc_highchart_single( $template ) {

        global $post;
        if( 'highchart-shortcode' === get_post_type( $post->ID ) ) {
            $file = HC_PLUGIN_PATH . 'public/partials/hc-single-highchart.php';
            if( file_exists( $file ) ) {
                $template = $file;
            }
        }
        return $template;

    }

}
