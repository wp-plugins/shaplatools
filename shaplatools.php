<?php
/**
 * Plugin Name:       ShaplaTools
 * Plugin URI:        https://wordpress.org/plugins/shaplatools/
 * Description:       ShaplaTools is a powerful plugin to extend functionality to your WordPress themes. 
 * Version:           1.0.0
 * Author:            Sayful Islam
 * Author URI:        http://sayful.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shapla
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'ShaplaTools' ) ) {

/**
 * Main ShaplaTools Class
 *
 * @package ShaplaTools
 * @version 1.0.0
 * @author Sayful Islam
 * @link http://sayful.net
 */

class ShaplaTools {

	/**
	* @var string
	*/
	public $version = '1.0.0';

	/**
	* @var string
	*/
	public $plugin_url;

	/**
	* @var string
	*/
	public $plugin_path;

	/**
	* @var string
	*/
	public $template_url;

	/**
	 * ShaplaTools Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// Define version constant
		define( 'SHAPLATOOLS_VERSION', $this->version );

		// Hooks
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_menu', array( &$this, 'shapla_add_options_page' ) );
		add_action( 'admin_head', array( &$this, 'widget_styles' ) );
		add_action( 'customize_controls_print_scripts', array( &$this, 'customize_styles' ) );
		add_action( 'wp_footer', array( &$this, 'inline_scripts' ) );

		// Include required files
		$this->includes();
	}

	/**
	 * Add custom links on plugins page.
	 *
	 * @access public
	 * @param mixed $links
	 * @return void
	 */
	public function action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'options-general.php?page=shaplatools' ) . '">' . __( 'Settings', 'shapla' ) . '</a>'
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Initiate all the stuff.
	 *
	 * @return void
	 */
	function init() {
		$this->shapla_load_textdomain();

		add_action( 'wp_enqueue_scripts', array( &$this, 'frontend_style' ), 0 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_style' ), 0 );
		add_filter( 'body_class', array( &$this, 'body_class' ) );

		add_filter( 'contextual_help', array( &$this, 'contextual_help' ), 10, 3 );

		/**
		 * @deprecated 1.2
		 */
		if( current_theme_supports( 'shapla-portfolio' ) ) include_once( 'includes/post-type/portfolio.php' );
		if( current_theme_supports( 'shapla-slides' ) ) include_once( 'includes/post-type/slides.php' );
		if( current_theme_supports( 'shapla-team' ) ) include_once( 'includes/post-type/team.php' );
		if( current_theme_supports( 'shapla-testimonials' ) ) include_once( 'includes/post-type/testimonials.php' );
		if( current_theme_supports( 'shapla-event' ) ) include_once( 'includes/post-type/event.php' );
		if( current_theme_supports( 'shapla-features' ) ) include_once( 'includes/post-type/features.php' );

		/**
		 * Include custom post type files, depending on which are supported.
		 *
		 * @since 1.2
		 */
		if ( current_theme_supports( 'post-type' ) ) {
			$theme_supports = get_theme_support( 'post-type' );

			foreach ( $theme_supports[0] as $support ) {
				include_once( "includes/post-type/{$support}.php" );
			}
		}
	}

	/**
	 * Add ShaplaTools admin options.
	 *
	 * @global string $shaplatools_options One true options page
	 * @return void
	 */
	function shapla_add_options_page() {
		add_options_page( __( 'ShaplaTools Options', 'shapla' ), __( 'ShaplaTools', 'shapla' ), 'manage_options', 'shaplatools', 'shaplatools_options_page' );
	}

	/**
	 * Setup localisation.
	 *
	 * @return void
	 */
	function shapla_load_textdomain() {
		// Set filter for plugin's languages directory
		$shaplatools_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$shaplatools_lang_dir = apply_filters( 'shaplatools_languages_directory', $shaplatools_lang_dir );

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), 'shapla' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'shapla', $locale );

		// Setup paths to current locale file
		$mofile_local  = $shaplatools_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/shaplatools/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/shaplatools folder
			load_textdomain( 'shapla', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/shaplatools/languages/ folder
			load_textdomain( 'shapla', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'shapla', false, $shaplatools_lang_dir );
		}
	}

	/**
	 * Include admin and frontend files.
	 *
	 * @uses ShaplaTools::admin_includes() Includes admin files
	 * @uses ShaplaTools::frontend_includes() Includes frontend files
	 * @return void
	 */
	public function includes() {
		global $shaplatools_options;
		require_once('includes/settings/settings.php');
		require_once('includes/classes/ShaplaTools_Typeahead.php');
		$shaplatools_options = shaplatools_get_settings();

		if ( is_admin() ){
			$this->admin_includes();
		}
		if( !is_admin() ){
			$this->frontend_includes();
		}

		// Widgets
		include_once( 'includes/widgets/widget-dribbble.php' );
		include_once( 'includes/widgets/widget-flickr.php' );
		include_once( 'includes/widgets/widget-instagram.php' );
		include_once( 'includes/widgets/widget-twitter.php' );
		include_once( 'includes/widgets/widget-fb_like_box.php' );
		include_once( 'includes/widgets/widget-contact.php' );

		if (isset($shaplatools_event_activated) && $shaplatools_event_activated == true) {
			include_once( 'includes/widgets/widget-event.php' );
		}
		if (isset($shaplatools_testimonial_activated) && $shaplatools_testimonial_activated == true) {
			include_once( 'includes/widgets/widget-testimonials.php' );
		}

		// Post Types
		include_once( 'includes/post-type/ShaplaTools_Team.php' );
		include_once( 'includes/post-type/ShaplaTools_Event.php' );
		include_once( 'includes/post-type/ShaplaTools_Slide.php' );
		include_once( 'includes/post-type/ShaplaTools_Feature.php' );
		include_once( 'includes/post-type/ShaplaTools_Portfolio.php' );
		include_once( 'includes/post-type/ShaplaTools_Testimonial.php' );
	}

	/**
	* Include admin files.
	*
	* @return void
	*/
	public function admin_includes(){
		include_once( 'shortcodes/shapla-shortcodes.php' );
		include_once( 'includes/settings/settings.php' );
		include_once( 'includes/classes/ShaplaTools_Gallery.php' );
		include_once( 'includes/classes/Shapla_Retina_2x.php' );
		include_once( 'includes/classes/ShaplaTools_Metaboxs.php' );

		// Meta Box
		include_once( 'includes/meta-box/ShaplaTools_Portfolio_Metabox.php' );
		include_once( 'includes/meta-box/ShaplaTools_Feature_Metabox.php' );
		include_once( 'includes/meta-box/ShaplaTools_Event_Metabox.php' );
		include_once( 'includes/meta-box/ShaplaTools_NivoSlide_Metabox.php' );
		include_once( 'includes/meta-box/ShaplaTools_Testimonial_Metabox.php' );
	}

	/**
	 * Include frontend files.
	 *
	 * @return void
	 */
	public function frontend_includes(){
		include_once( plugin_dir_path( __FILE__ ) .'shortcodes/shortcodes.php' );
	}

	/**
	 * Add frontend scripts and styles.
	 *
	 * @return void
	 */
	public function frontend_style() {
		wp_register_style( 'font-awesome', $this->plugin_url() . '/assets/css/font-awesome.css' , '', '4.1.0', 'all' );
		wp_register_style( 'shapla-shortcode-styles', $this->plugin_url() . '/assets/css/shapla-shortcodes.css' , array( 'font-awesome' ), $this->version, 'all' );
		wp_register_script( 'shapla-shortcode-scripts', $this->plugin_url(). '/assets/js/shapla-shortcode-scripts.js', array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-tabs' ), $this->version, true );

		/**!
		 * Register typeahead.js
		 * Register hogan.js
		 */
		wp_register_style( 'shapla-typeahead-search', $this->plugin_url(). '/assets/library/typeahead/typeahead.css' , array(), $this->version, 'all' );

		wp_register_script( 'typeahead_js', $this->plugin_url(). '/assets/library/typeahead/typeahead.min.js', array('jquery'), '0.9.0', true );
		wp_register_script( 'hogan_js', $this->plugin_url(). '/assets/library/typeahead/hogan.min.js', array('typeahead_js'), '', true );

		/**!
		 * Enqueue ShaplaTools custom style
		 * Enqueue ShaplaTools custom script
		 */
		wp_enqueue_style( 'shaplatools', $this->plugin_url() . '/assets/css/shaplatools.css', array(), $this->version, 'all' );
		wp_enqueue_script( 'shaplatools', $this->plugin_url(). '/assets/js/shaplatools.js', array( 'jquery' ), $this->version, true );

		/**!
		 * Register Modernizr
		 * Register Shuffle.js
		 */
		wp_register_script( 'modernizr', $this->plugin_url(). '/assets/library/shuffle/jquery.shuffle.modernizr.min.js', array(), '3.2', true );
		wp_register_script( 'shuffle', $this->plugin_url(). '/assets/library/shuffle/jquery.shuffle.min.js', array( 'jquery', 'modernizr' ), '3.2', true );
		wp_register_script( 'shuffle-custom', $this->plugin_url(). '/assets/library/shuffle/shuffle-custom.js', array( 'jquery', 'shuffle' ), '3.2', true );

		/**!
		 * Register Nivo Slider jQuery plugin
		 * Register Nivo Slider Style
		 */
		wp_register_style( 'nivo-slider', $this->plugin_url(). '/assets/library/nivo-slider/nivo-slider.min.css' , array(), '3.2', 'all' );
		wp_register_script( 'nivo-slider', $this->plugin_url(). '/assets/library/nivo-slider/jquery.nivo.slider.js', array( 'jquery' ), '3.2', true );

		wp_register_style( 'animate-css', $this->plugin_url(). '/assets/library/animate-css/animate.min.css', array(), $this->version, 'all' );

		/**!
		 * Enqueue Owl Carousel plugin
		 * Enqueue Owl Carousel Style
		 */
		wp_enqueue_style( 'owl-carousel', $this->plugin_url(). '/assets/library/owl-carousel/owl.carousel.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'owl-carousel-theme', $this->plugin_url(). '/assets/library/owl-carousel/owl.theme.green.css', array(), $this->version, 'all' );
		wp_enqueue_script( 'owl-carousel', $this->plugin_url(). '/assets/library/owl-carousel/owl.carousel.min.js', array( 'jquery' ), '2.0.0', true );



		global $post;
		$this->options = get_option('shaplatools_options');
     
	    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'shapla_slide') ) {
			wp_enqueue_script( 'nivo-slider' );
			wp_enqueue_style( 'nivo-slider' );
	    }
     
	    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'shapla_portfolio') ) {
			wp_enqueue_script( 'shuffle' );
			wp_enqueue_script( 'modernizr' );
			wp_enqueue_script( 'shuffle-custom' );
	    }


	    if ( isset($this->options['typeahead_search']) && $this->options['typeahead_search'] != 'no_search' ) {
			wp_enqueue_style( 'shapla-typeahead-search' );
			wp_enqueue_script( 'typeahead_js' );
			wp_enqueue_script( 'hogan_js' );

			if (isset($this->options['typeahead_search']) && $this->options['typeahead_search'] == 'product_search') {
				wp_enqueue_script( 'typeahead_woo_search', $this->plugin_url(). '/assets/library/typeahead/woo-typeahead.js', array('typeahead_js', 'hogan_js'), '', true );
				wp_localize_script( 'typeahead_woo_search', 'wp_typeahead', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			} else {
				wp_enqueue_script( 'typeahead_general_search', $this->plugin_url(). '/assets/library/typeahead/wp-typeahead.js', array('typeahead_js', 'hogan_js'), '', true );
				wp_localize_script( 'typeahead_general_search', 'wp_typeahead', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			}
	    }

	    if ( isset($this->options['retina_graphics']) && $this->options['retina_graphics'] == 'retina_yes' ) {
	    	wp_enqueue_script( 'retina-js', $this->plugin_url(). '/assets/js/retina.min.js', array( 'jquery' ), '1.3.0', true );
	    }

		//wp_enqueue_style( 'animate-css' );
		wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'shapla-shortcode-styles' );
		wp_enqueue_script( 'shapla-shortcode-scripts' );
	}

	/**
	 * Add frontend scripts and styles.
	 *
	 * @return void
	 */
	public function admin_style( $hook ) {

		global $post_type;

		if ( ( 'post.php' == $hook || 'post-new.php' == $hook ) && (( 'event' == $post_type ) || ( 'portfolio' == $post_type ))) {

			wp_enqueue_script( 'shapla-admin-datepicker', $this->plugin_url(). '/assets/admin/js/datepicker.js', array(  'jquery', 'jquery-ui-datepicker' ), $this->version, true );

			wp_enqueue_style( 'shapla-admin-jquery-ui', $this->plugin_url(). '/assets/admin/css/jquery-ui.min.css', array(), '1.11.3', 'all' );
			wp_enqueue_style( 'shapla-admin-jquery-ui-structure', $this->plugin_url().'/assets/admin/css/jquery-ui.structure.min.css', array(), '1.11.3', 'all' );
			wp_enqueue_style( 'shapla-admin-jquery-ui-theme', $this->plugin_url(). '/assets/admin/css/jquery-ui.theme.css', array(), '1.11.3', 'all' );
		}
		if( $hook == 'post.php' || $hook == 'post-new.php' ){
			wp_enqueue_style( 'shapla-admin', $this->plugin_url().'/assets/admin/css/shaplatools-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Plugin path.
	 *
	 * @return string Plugin path
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;

		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Plugin url.
	 *
	 * @return string Plugin url
	 */
	public function plugin_url() {
		if ( $this->plugin_url ) return $this->plugin_url;
		return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Add shaplatools to body class for use on frontend.
	 *
	 * @since 1.0.0
	 * @return array $classes List of classes
	 */
	public function body_class( $classes ) {
		$classes[] = 'shaplatools';
		return $classes;
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

	/**
	 * customize styles.
	 *
	 * @return void
	 */
	public function customize_styles() {
		global $pagenow;
		if( $pagenow != 'customize.php' ) return;
		?>
		<style type="text/css">

		/* OVERRIDE WP CUSTOMIZER */
		#customize-controls,
		.wp-full-overlay-sidebar-content,
		#customize-info .accordion-section-title {
			background: #363B3F;
			color: #eee;
		}
		#customize-controls .customize-controls-close {
			background: #F32A40;
			color: #eee;
		}
		#customize-controls .control-panel-back {
			color: #000;
		}
		#customize-theme-controls .accordion-section-title {
		  background-color: #00694D;
		  border-bottom: 1px solid #eee;
		  color: #eee;
		}
		.control-section.control-panel > .accordion-section-title::after {
		  background: none repeat scroll 0 0 #F32A40;
		  border-left: 1px solid #eee;
		  color: #eee;
		}
		#customize-info .accordion-section-title:focus, #customize-info .accordion-section-title:hover, #customize-info.open .accordion-section-title, #customize-theme-controls .control-section .accordion-section-title:focus, #customize-theme-controls .control-section .accordion-section-title:hover, #customize-theme-controls .control-section.open .accordion-section-title, #customize-theme-controls .control-section:hover > .accordion-section-title {
		  background: none repeat scroll 0 0 #F32A40;
		  color: #fff;
		}
		#customize-theme-controls .control-section .accordion-section-title:focus::after, #customize-theme-controls .control-section .accordion-section-title:hover::after, #customize-theme-controls .control-section.open .accordion-section-title::after, #customize-theme-controls > .control-section:hover .accordion-section-title::after {
		  color: #fff;
		}

		</style>
		<?php
	}

	/**
	 * Add help screen for ShaplaTools settings page.
	 *
	 * @param  string $contextual_help
	 * @param  string $screen_id       String of the settings page
	 * @param  object $screen          Current screen object containing all details
	 * @since  1.1
	 * @return object Help object
	 */
	function contextual_help( $contextual_help, $screen_id, $screen ) {
		if ( "settings_page_shaplatools" != $screen_id )
			return;

		$screen->set_help_sidebar(
			'<p><strong>' . sprintf( __( 'For more information:', 'shapla' ) . '</strong></p>' .
			'<p>' . sprintf( __( 'Visit the <a href="%s" target="_blank">documentation</a> on the WordPress Directory.', 'shapla' ), esc_url( 'https://wordpress.org/plugins/shaplatools/' ) ) ) . '</p>' .
			'<p>' . sprintf(
						__( '<a href="%s" target="_blank">Post an issue</a> on <a href="%s" target="_blank">Support Forum</a>.', 'shapla' ),
						esc_url( 'https://wordpress.org/support/plugin/shaplatools' ),
						esc_url( 'https://wordpress.org/support/plugin/shaplatools' )
					) . '</p>'
		);

		$screen->add_help_tab( array(
			'id'	    => 'shaplatools-help-google_analytics',
			'title'	    => __( 'ShaplaTools General Settings', 'shapla' ),
			'content'	=>  '<p>' . __( 'Here you can find how to configure general options of this plugin.', 'shapla' ) . '</p>'.
							'<h5>' . __( 'Google Analytics ID', 'shapla' ) . '</h5>'.
							'<p>' . sprintf( __( 'In order to use Google Analytics service, go to <a href="%s" target="_blank">Google Analytics</a> and click Access Google Analytics and register for a service for your site. You will get a Google Analytics ID like this formate (UA-XXXXX-X), paste this ID in Google Analytics ID field and click save.', 'shapla' ), esc_url( 'https://www.google.com/analytics/' ) ) . '</p>'
		) );

		//if ( current_theme_supports( 'post-type',  array( 'portfolio' ) ) ) :
		$screen->add_help_tab( array(
			'id'	    => 'shaplatools-help-portfolio',
			'title'	    => __( 'Portfolio Settings', 'shapla' ),
			'content'	=>  '<p>'. __( 'You can use the following settigns to control the slug/taxonomies for custom post type portfolio and skills.', 'shapla' ) .'</p>'.
							'<p>'. __( '<strong>Portfolio Slug</strong> - This settings is used to set the slug of custom post type &lsquo;portfolio&rsquo;.', 'shapla' ) .'</p>'.
							'<p>'. __( '<strong>Skills Slug</strong> - This settings is used to set the slug of custom post taxonomy &lsquo;skill&rsquo;.', 'shapla' ) .'</p>'
		) );
		//endif;

		$screen->add_help_tab( array(
			'id'	    => 'shaplatools-help-social',
			'title'	    => __( 'Using Social Icons', 'shapla' ),
			'content'	=>  '<h5>'. __( 'Using Social Icons Shortcode' ) .'</h5>'.
							'<p>' . __( 'To use the social icon use the following shortcode:', 'shapla' ) . '</p>'.
							'<pre>[shapla_social] // '. __( 'It would display all social icons with non-empty profile URLs.', 'shapla' ) .'</pre>'.
							'<pre>[shapla_social id="facebook,twitter,google-plus"] // '. __( 'or you can pass specific ids.', 'shapla' ) .'</pre>'.
							'<h5>'. __( 'Using Different Styled Icons' ) .'</h5>'.
							'<p>'. __( 'You can use the social icons in two different styles: normal and square. Just pass the <code>style</code> argument in sidebar.<br>E.g.: <code>[shapla_social id="twitter,facebook" style="square"]</code>.' ) .'</p>'
		) );

		return $contextual_help;
	}

	public function inline_scripts(){
		$this->options = get_option('shaplatools_options');
		
		if( !empty($this->options['google_analytics']) ):
		?>
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','<?php echo esc_attr($this->options['google_analytics']); ?>','auto');ga('send','pageview');
        </script>
		<?php
		endif;
	}

}

$GLOBALS['shaplatools'] = new ShaplaTools();

}


/**
 * Flush the rewrite rules on activation
 * Flush the rewrite rules on deactivation
 */
function shaplatools_activation_deactivation() {

	ShaplaTools_Slide::post_type();
	ShaplaTools_Portfolio::post_type();
	ShaplaTools_Portfolio::taxonomy();
	ShaplaTools_Team::post_type();
	ShaplaTools_Event::post_type();
	ShaplaTools_Feature::post_type();
	ShaplaTools_Testimonial::post_type();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'shaplatools_activation_deactivation' );
register_deactivation_hook( __FILE__, 'shaplatools_activation_deactivation' );


/**
 * Get all available image size
 */

if ( !function_exists('shaplatools_available_img_size') ):

function shaplatools_available_img_size(){

	$shaplatools_img_size = get_intermediate_image_sizes();
	array_push($shaplatools_img_size, 'full');

	$singleArray = array();

	foreach ($shaplatools_img_size as $key => $value){

	    $singleArray[$value] = $value;
	}

	return $singleArray;
}

endif;