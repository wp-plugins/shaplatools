<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    ShaplaTools
 * @subpackage ShaplaTools/admin
 * @author     Sayful Islam <sayful.islam001@gmail.com>
 */
class ShaplaTools_Admin {

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
	 * @param      string    $shaplatools       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $shaplatools, $version ) {

		$this->shaplatools = $shaplatools;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
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

		wp_enqueue_style( $this->shaplatools, plugin_dir_url( __FILE__ ) . 'css/shaplatools-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
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

		//wp_enqueue_script( $this->shaplatools, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

	function hook_scripts( $hook ) {

		/**
		 * Enqueueing scripts and styles in the admin
		 * @param  int $hook Current page hook
		 */

		global $post_type;

		if ( ( 'post.php' == $hook || 'post-new.php' == $hook ) && (( 'event' == $post_type ) || ( 'portfolio' == $post_type ))) {

			wp_enqueue_script( $this->shaplatools.'-datepicker', plugin_dir_url( __FILE__ ) . 'js/datepicker.js', array(  'jquery', 'jquery-ui-datepicker' ), $this->version, true );

			wp_enqueue_style( $this->shaplatools.'-jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css', array(), '1.11.3', 'all' );
			wp_enqueue_style( $this->shaplatools.'-jquery-ui-structure', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.structure.min.css', array(), '1.11.3', 'all' );
			wp_enqueue_style( $this->shaplatools.'-jquery-ui-theme', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.theme.css', array(), '1.11.3', 'all' );
		}
	}

	/**
	 * Widget styles.
	 *
	 * @return void
	 */
	public function widget_styles() {
		global $pagenow;
		if( $pagenow != 'widgets.php' ) return;
		?>
		<style type="text/css">
		div[id*="_shapla"] .widget-top{
		  background: #C8E5F3 !important;
		  border-color: #B4D0DD !important;
		  box-shadow: inset 0 1px 0 white !important;
		  -webkit-box-shadow: inset 0 1px 0 white !important;
		  -moz-box-shadow: inset 0 1px 0 white !important;
		  -ms-box-shadow: inset 0 1px 0 white !important;
		  -o-box-shadow: inset 0 1px 0 white !important;
		  background: -moz-linear-gradient(top,  #EAF8FF 0%, #C8E5F3 100%) !important;
		  background: -webkit-linear-gradient(top, #EAF8FF 0%,#C8E5F3 100%) !important;
		  background: linear-gradient(to bottom, #EAF8FF 0%,#C8E5F3 100%) !important;
		  border-bottom: 1px solid #98B3C0 !important;
		  margin-top: 0px;
		}
		</style>
		<?php
	}

	public function shapla_register_widget(){

		$this->options = get_option( 'shaplatools_options' );

		if ( isset($this->options['show_event']) && '1' == $this->options['show_event'] ) {

			register_widget( 'Shapla_Event' );

		}

		if ( isset($this->options['show_testimonial']) && '1' == $this->options['show_testimonial'] ) {

			register_widget( 'Shapla_Testimonial' );
			
		}
	}

}