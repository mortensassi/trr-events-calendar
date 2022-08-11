<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://mortensassi.com
 * @since      1.0.0
 *
 * @package    Trr_Events_Calendar
 * @subpackage Trr_Events_Calendar/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Trr_Events_Calendar
 * @subpackage Trr_Events_Calendar/public
 * @author     Morten Sassi <dev@mortensassi.com>
 */
class Trr_Events_Calendar_Public {

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

    private $manifest;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

        $manifest_path = plugin_dir_url( __FILE__ ) . 'app/dist/manifest.json';
        $manifest = file_get_contents($manifest_path);

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->manifest = json_decode($manifest, true);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Trr_Events_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Trr_Events_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/trr-events-calendar-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Trr_Events_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Trr_Events_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        if (is_post_type_archive('trr_event')) {
	        if (defined('WP_ENV') && WP_ENV == 'development') { ?>
                <script type="module" src="https://localhost:3002/@vite/client"></script>
                <script type="module" src="https://localhost:3002/src/main.js"></script>
		        <?php
	        } else {
		        wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'app/dist/' . $this->manifest['src/main.js']['file'], [], $this->version, true );

		        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'app/dist/style.css', $this->version, true );
		        wp_enqueue_script($this->plugin_name);
	        };
        }
	}

}
