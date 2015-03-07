<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    ShaplaTools
 * @subpackage ShaplaTools/public
 * @author     Sayful Islam <sayful.islam001@gmail.com>
 */
class ShaplaTools_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $shaplatools    The ID of this plugin.
	 */
	private $shaplatools;

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
	 * @param      string    $shaplatools       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $shaplatools, $version ) {

		$this->shaplatools = $shaplatools;
		$this->version = $version;

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
		 * defined in ShaplaTools_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ShaplaTools_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->shaplatools, plugin_dir_url( __FILE__ ) . 'css/shaplatools.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->shaplatools.'-animate', plugin_dir_url( __FILE__ ) . 'library/animate/animate.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->shaplatools.'-carousel', plugin_dir_url( __FILE__ ) . 'library/owl-carousel/owl.carousel.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->shaplatools.'-carousel-theme', plugin_dir_url( __FILE__ ) . 'library/owl-carousel/owl.theme.green.css', array(), $this->version, 'all' );


		wp_register_style( 'font-awesome', plugin_dir_url( dirname( __FILE__ ) ) . '/assets/css/font-awesome.css' , '', '4.1.0', 'all' );
		wp_register_style( 'shapla-shortcode-styles', plugin_dir_url( dirname( __FILE__ ) ) . '/assets/css/shapla-shortcodes.css' , array( 'font-awesome' ), $this->version, 'all' );

		wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'shapla-shortcode-styles' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ShaplaTools_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ShaplaTools_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->shaplatools, plugin_dir_url( __FILE__ ) . 'js/shaplatools.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script( $this->shaplatools.'-carousel', plugin_dir_url( __FILE__ ) . 'library/owl-carousel/owl.carousel.min.js', array( 'jquery' ), '2.0.0', true );



		wp_register_script( 'shapla-shortcode-scripts', plugin_dir_url( dirname( __FILE__ ) ). '/assets/js/shapla-shortcode-scripts.js', array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-tabs' ), $this->version, true );
		wp_enqueue_script( 'shapla-shortcode-scripts' );

	}

}
