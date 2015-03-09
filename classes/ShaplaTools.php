<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    ShaplaTools
 * @subpackage ShaplaTools/classes
 * @author     Sayful Islam <sayful.islam001@gmail.com>
 */
class ShaplaTools {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ShaplaTools_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $shaplatools    The string used to uniquely identify this plugin.
	 */
	protected $shaplatools;

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
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->shaplatools = 'shaplatools';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->show_custom_post();
		$this->includes();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ShaplaTools_Loader. Orchestrates the hooks of the plugin.
	 * - ShaplaTools_i18n. Defines internationalization functionality.
	 * - ShaplaTools_Admin. Defines all hooks for the dashboard.
	 * - ShaplaTools_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/ShaplaTools_Loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/ShaplaTools_i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/ShaplaTools_Settings.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/ShaplaTools_Admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/ShaplaTools_Gallery.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/ShaplaTools_Portfolio.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/ShaplaTools_Event.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/ShaplaTools_Testimonial.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/ShaplaTools_Slider.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/ShaplaTools_Team.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/ShaplaTools_Team.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shaplatools-gallery-slider.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/ShaplaTools_Public.php';

		$this->loader = new ShaplaTools_Loader();

	}

	private function show_custom_post(){
		$this->options = get_option( 'shaplatools_options' );

		if ( isset($this->options['show_portfolio']) && '1' == $this->options['show_portfolio'] ) {
			new ShaplaTools_Portfolio();
		}

		if ( isset($this->options['show_event']) && '1' == $this->options['show_event'] ) {
			new ShaplaTools_Event();
		}

		if ( isset($this->options['show_testimonial']) && '1' == $this->options['show_testimonial'] ) {
			new ShaplaTools_Testimonial();
		}

		if ( isset($this->options['show_slider']) && '1' == $this->options['show_slider'] ) {
			new ShaplaTools_Slider();
		}

		if ( isset($this->options['show_team']) && '1' == $this->options['show_team'] ) {
			new ShaplaTools_Team();
		}
	}

	public function includes(){

		if ( is_admin() ){
			$this->admin_includes();
		}
		if( !is_admin() ){
			$this->frontend_includes();
		}


		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/widget-testimonials.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/widget-fb_like_box.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/widget-twitter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/widget-event.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/widget-flickr.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/widget-dribbble.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/widget-instagram.php';
	}

	/**
	* Include admin files.
	*
	* @return void
	*/
	public function admin_includes(){
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'shortcodes/shapla-shortcodes.php';
	}

	/**
	 * Include frontend files.
	 *
	 * @return void
	 */
	public function frontend_includes(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'shortcodes/shortcodes.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ShaplaTools_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new ShaplaTools_i18n();
		$plugin_i18n->set_domain( $this->get_shaplatools() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new ShaplaTools_Admin( $this->get_shaplatools(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'hook_scripts' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'widget_styles' );
		$this->loader->add_action( 'widgets_init', $plugin_admin, 'shapla_register_widget' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new ShaplaTools_Public( $this->get_shaplatools(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

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
	public function get_shaplatools() {
		return $this->shaplatools;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    ShaplaTools_Loader    Orchestrates the hooks of the plugin.
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
