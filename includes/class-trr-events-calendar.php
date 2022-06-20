<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://mortensassi.com
 * @since      1.0.0
 *
 * @package    Trr_Events_Calendar
 * @subpackage Trr_Events_Calendar/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Trr_Events_Calendar
 * @subpackage Trr_Events_Calendar/includes
 * @author     Morten Sassi <dev@mortensassi.com>
 */
class Trr_Events_Calendar {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Trr_Events_Calendar_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TRR_EVENTS_CALENDAR_VERSION' ) ) {
			$this->version = TRR_EVENTS_CALENDAR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'trr-events-calendar';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
        $this->register_events_cpt();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Trr_Events_Calendar_Loader. Orchestrates the hooks of the plugin.
	 * - Trr_Events_Calendar_i18n. Defines internationalization functionality.
	 * - Trr_Events_Calendar_Admin. Defines all hooks for the admin area.
	 * - Trr_Events_Calendar_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-trr-events-calendar-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-trr-events-calendar-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-trr-events-calendar-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-trr-events-calendar-public.php';

		$this->loader = new Trr_Events_Calendar_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Trr_Events_Calendar_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Trr_Events_Calendar_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Trr_Events_Calendar_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Trr_Events_Calendar_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

    private function register_events_cpt() {
        add_action( 'init', function () {


            $eventLabels = array(
                'name'               => __( 'Events', 'trr' ),
                'singular_name'      => __( 'Event', 'trr' ),
                'menu_name'          => __( 'Events', 'trr' ),
                'name_admin_bar'     => __( 'Events', 'trr' ),
                'add_new'            => __( 'Add Event', 'trr' ),
                'add_new_item'       => __( 'Add new Event', 'trr' ),
                'new_item'           => __( 'Add Event', 'trr' ),
                'edit_item'          => __( 'Edit Event', 'trr' ),
                'view_item'          => __( 'View Event', 'trr' ),
                'all_items'          => __( 'All Events', 'trr' ),
                'search_items'       => __( 'Search Events', 'trr' ),
                'parent_item_colon'  => __( 'Parentinstitution:', 'trr' ),
                'not_found'          => __( 'Event not found', 'trr' ),
                'not_found_in_trash' => __( 'Event not found in Trash.', 'trr' )
            );

            $eventArgs = array(
                'labels'             => $eventLabels,
                'menu_icon'          => 'data:image/svg+xml;base64,' . base64_encode( '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 374 314"><path d="M358.91 28.19h-35.992V.249h-28.414V28.19h-215V.249H51.09V28.19H15.094C7.25 28.19.887 34.55.887 42.397v257.16c0 7.844 6.363 14.207 14.207 14.207h343.82c7.848 0 14.207-6.363 14.207-14.207V42.397c0-7.848-6.36-14.207-14.207-14.207h-.004ZM110.28 247.46H65.292v-28.414h44.988v28.414Zm0-55.41H65.292v-28.414h44.988v28.414Zm0-55.41H65.292v-28.414h44.988v28.414Zm66.301 110.34h-44.988v-28.414h44.988v28.414Zm0-55.41-44.988.004V163.16h44.988v28.41Zm0-55.41-44.988.004V107.75h44.988v28.41Zm66.301 110.34-45.461.004V218.09h44.988l.473 28.41Zm0-55.41-45.461.004V162.68h44.988l.473 28.41Zm0-55.41-45.461.008v-28.414h44.988l.473 28.406Zm66.301 110.34-45.461.008v-28.414h44.988l.473 28.406Zm0-55.41-45.461.008v-28.414h44.988l.473 28.406Zm0-55.41-45.461.008v-28.414h44.988l.473 28.406Z" fill="currentColor"/></svg>' ),
                'description'        => __( 'Event Description.', 'trr' ),
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => 'events', 'with_front' => false ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => 35,
                'supports'           => array( 'title', 'editor' ),
                'acfe_admin_archive' => true,
            );

            register_post_type( 'trr_event', $eventArgs );

            register_taxonomy(
                'event-location',
                'trr_event',
                array(
                    'hierarchical'       => false,
                    'label'              => __( 'Event Location', 'trr' ),
                    'singular_name'      => __( 'Location', 'trr' ),
                    'rewrite'            => true,
                    'query_var'          => true,
                    'show_ui'            => true,
                    'show_in_quick_edit' => false,
                    'meta_box_cb'        => false,
                ) );

            add_shortcode('trr_calendar', function () {
                $app_id = $this->get_plugin_name() . '-app';

                return '<div id="' . $app_id . '"></div>';
            });

        } );
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Trr_Events_Calendar_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
