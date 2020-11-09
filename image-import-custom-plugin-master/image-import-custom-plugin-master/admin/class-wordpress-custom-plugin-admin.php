<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Wordpress_Custom_Plugin
 * @subpackage Wordpress_Custom_Plugin/admin
 */

 // Lots of help, borrowed code from: https://github.com/rayman813/smashing-custom-fields/blob/master/smashing-fields-approach-1/smashing-fields.php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordpress_Custom_Plugin
 * @subpackage Wordpress_Custom_Plugin/admin
 */
class Wordpress_Custom_Plugin_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Lets add an action to setup the admin menu in the left nav
		add_action( 'admin_menu', array($this, 'add_admin_menu') );
		// Add some actions to setup the settings we want on the wp admin page
		add_action('admin_init', array($this, 'setup_sections'));
		add_action('admin_init', array($this, 'setup_fields'));
		
	}

	/**
	 * Add the menu items to the admin menu
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {}

	/**
	 * Callback function for displaying the admin settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_custom_plugin_admin_page(){}

	/**
	 * Callback function for displaying the second sub menu item page.
	 *
	 * @since    1.0.0
	 */
	public function display_custom_plugin_admin_page_two(){}

	/**
	 * Setup sections in the settings
	 *
	 * @since    1.0.0
	 */
	public function setup_sections() {}

	/**
	 * Callback for each section
	 *
	 * @since    1.0.0
	 */
	public function section_callback( $arguments ) {}

	/**
	 * Field Configuration, each item in this array is one field/setting we want to capture
	 *
	 * @since    1.0.0
	 */
	public function setup_fields() {}

	/**
	 * This handles all types of fields for the settings
	 *
	 * @since    1.0.0
	 */
	public function field_callback($arguments) {}

	/**
	 * Admin Notice
	 * 
	 * This displays the notice in the admin page for the user
	 *
	 * @since    1.0.0
	 */
	public function admin_notice($message) { ?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo($message); ?></p>
			</div><?php
		}

	/**
	 * This handles setting up the rewrite rules for Past Sales
	 *
	 * @since    1.0.0
	 */
	public function setup_rewrites() {
		//
		$url_slug = 'custom-plugin';
		// Lets setup our rewrite rules
		add_rewrite_rule( $url_slug . '/?$', 'index.php?custom_plugin=index', 'top' );
		add_rewrite_rule( $url_slug . '/page/([0-9]{1,})/?$', 'index.php?custom_plugin=items&custom_plugin_paged=$matches[1]', 'top' );
		add_rewrite_rule( $url_slug . '/([a-zA-Z0-9\-]{1,})/?$', 'index.php?custom_plugin=detail&custom_plugin_vehicle=$matches[1]', 'top' );


		// Lets flush rewrite rules on activation
		flush_rewrite_rules();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Custom_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Custom_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Custom_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Custom_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

	}

}
