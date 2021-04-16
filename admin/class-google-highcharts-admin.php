<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       multidots
 * @since      1.0.0
 *
 * @package    Google_Highcharts
 * @subpackage Google_Highcharts/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Google_Highcharts
 * @subpackage Google_Highcharts/admin
 * @author     Multidots <inquiry@multidots.com>
 */
class Google_Highcharts_Admin {

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
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

        wp_enqueue_style( $this->plugin_name, HC_PLUGIN_URL . 'admin/css/google-highcharts-admin.css' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, HC_PLUGIN_URL . 'admin/js/google-highcharts-admin.js', array( 'jquery' ) );

	}

	public function highchart_shortcodes_custom_post_type() {
        $labels = array(
            'name'					=>	__( 'Highchart Shortcode', 'google-highcharts' ),
            'singular_name'			=>	__( 'Highchart Shortcode', 'google-highcharts' ),
            'menu_name'				=>	__( 'Highchart Shortcodes', 'google-highcharts' ),
            'name_admin_bar'		=>	__( 'Highchart Shortcode', 'google-highcharts' ),
            'add_new'				=>	__( 'Add New', 'google-highcharts' ),
            'add_new_item'			=>	__( 'Add New Highchart Shortcode', 'google-highcharts' ),
            'new_item'				=>	__( 'New Highchart Shortcode', 'google-highcharts' ),
            'edit_item'				=>	__( 'Edit Highchart Shortcode', 'google-highcharts' ),
            'view_item'				=>	__( 'View Highchart Shortcode', 'google-highcharts' ),
            'all_items'				=>	__( 'Highchart Shortcodes', 'google-highcharts' ),
            'search_items'			=>	__( 'Search Highchart Shortcodes', 'google-highcharts' ),
            'parent_item_colon'		=>	__( 'Parent Highchart Shortcodes:', 'google-highcharts' ),
            'not_found'				=>	__( 'No Highchart Shortcodes Found.', 'google-highcharts' ),
            'not_found_in_trash'	=>	__( 'No Highchart Shortcodes Found In Trash.', 'google-highcharts' )
        );

        $args = array(
            'labels'				=>	$labels,
            'public'				=>	true,
            'menu_icon'				=>	'dashicons-chart-area',
            'publicly_queryable'	=>	true,
            'show_ui'				=>	true,
            'show_in_menu'			=>	true,
            'query_var'				=>	true,
            'rewrite'				=>	array( 'slug' => 'highcharts' ),
            'capability_type'		=>	'post',
            'capabilities'          =>  array(
                'create_posts'      =>  'do_not_allow',
            ),
            'map_meta_cap'          =>  true,
            'has_archive'			=>	true,
            'hierarchical'			=>	false,
            'menu_position'			=>	null,
            'supports'				=>	array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
        );

		register_post_type( 'highchart-shortcode', $args );
		$set = get_option( 'cpt_hcs_flushed_rewrite_rules' );
		if ( 'yes' !== $set ) {
			flush_rewrite_rules( false );
			update_option( 'cpt_hcs_flushed_rewrite_rules', 'yes' );
		}
	}


	public function hc_plugin_settings() {
		add_submenu_page(
			'edit.php?post_type=highchart-shortcode',
			__( 'Add New Shortcode', 'google-highcharts' ),
            __( 'New Shortcode', 'google-highcharts' ),
			'manage_options',
			'add-new-highchart',
			array( $this, 'hc_add_highchart_page' )
		);

		add_submenu_page(
			null,
			__( 'Edit Shortcode', 'google-highcharts' ),
            __( 'Edit Shortcode', 'google-highcharts' ),
			'manage_options',
			'edit-highchart',
			array( $this, 'hc_edit_highchart_page' )
		);

	}

	public function hc_add_highchart_page() {

        $file = HC_PLUGIN_PATH . 'admin/partials/highchart-shortcode-form.php';
        if( file_exists( $file ) ) {
            include $file;
        }

	}

	public function hc_edit_highchart_page() {

        $file = HC_PLUGIN_PATH . 'admin/partials/highchart-edit-shortcode-form.php';
        if( file_exists( $file ) ) {
            include $file;
        }

	}

	/**
	 * Unset the quick edit for products
	 *
	 * @author     Multidots <info@multidots.com>
	 * @since      1.0.0
	 */
	public function hc_manage_highcharts_post_row_actions( $actions, $post ) {

		// Post Row Actions - Highcharts
		if ( 'highchart-shortcode' === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['edit'] );
			$actions['id'] = 'ID: ' . $post->ID;

			$actions['edit'] = '<a title="' . $post->post_title . '" href="' . admin_url( "/edit.php?post_type=highchart-shortcode&page=edit-highchart&action=edit&cid={$post->ID}" ) . '">' . __( 'Edit Highchart', 'google-highcharts' ) . '</a>';

		}

		return $actions;

	}

	/**
	 * Add new columns to the wc_user_membership listing on admin end.
	 *
	 * @since    1.0.0
	 * @author   Multidots <info@multidots.com>
	 */
	public function hc_highchart_shortcodes_new_column_heading( $defaults ) {

		$defaults['shortcode'] = __( 'Shortcode', 'google-highcharts' );
        $defaults['hc_preview'] = __( 'Preview', 'google-highcharts' );
		unset( $defaults['comments'] );
		unset( $defaults['author'] );
		unset( $defaults['date'] );

		return $defaults;

	}

	/**
	 * Add content to the new added columns.
	 *
	 * @since    1.0.0
	 * @author   Multidots <info@multidots.com>
	 */
	public function hc_highchart_shortcodes_new_column_content( $column_name, $postid ) {

	    $title = get_post_meta( $postid, 'chart_title', true );
	    $currency = get_post_meta( $postid, 'default_currency', true );
	    // Show the shortcode of the highchart
		if ( $column_name == 'shortcode' ) {
			$val = '[highchart chartid="' . $postid . '" title="' . $title . '"]';
			?>
            <textarea readonly id="hc-highchart-shortcode-text-<?php echo $postid;?>" rows="3" cols="55"><?php echo $val;?></textarea>
            <div class="row-actions">
                <span class="view">
                    <a class="hc-copy-shortcode" data-chartid="<?php echo $postid;?>" href="javascript:void(0);" rel="bookmark"><?php esc_html_e( 'Copy Shortcode', 'google-highcharts' );?></a>
                </span>
            </div>
			<?php
		}

        // Show the preview of the highchart
        if ( $column_name == 'hc_preview' ) {
	        echo do_shortcode( '[highchart chartid="' . $postid . '" title="' . $title . '"]' );
        }

	}

    /**
     * Create template for highcharts.
     *
     * @since    1.0.0
     */
    public function hc_create_shortcode_template( $atts ) {

        $file = HC_PLUGIN_PATH . 'admin/partials/hc-highchart-shortcode-template.php';
        if( file_exists( $file ) ) {
        	ob_start();
            include $file;
            return ob_get_clean();
        }

    }

    /**
     * Edit admin edit chart link.
     *
     * @since    1.0.0
     */
    public function hc_edit_chart_link( $link, $post_id ) {

        if( 'highchart-shortcode' === get_post_type( $post_id ) ) {
        	$link = admin_url( "/edit.php?post_type=highchart-shortcode&page=edit-highchart&action=edit&cid={$post_id}" );
        }

        return $link;

    }


}

