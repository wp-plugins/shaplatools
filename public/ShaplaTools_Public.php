<?php

class ShaplaTools_Public {

	private $shaplatools;

	private $version;

	public function __construct( $shaplatools, $version ) {

		$this->shaplatools = $shaplatools;
		$this->version = $version;

	}

	public function enqueue_styles() {


		wp_enqueue_style( $this->shaplatools, plugin_dir_url( __FILE__ ) . 'css/shaplatools.css', array(), $this->version, 'all' );

		wp_enqueue_style( 'animate-css', plugin_dir_url( __FILE__ ) . 'library/animate/animate.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'library/owl-carousel/owl.carousel.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'owl-carousel-theme', plugin_dir_url( __FILE__ ) . 'library/owl-carousel/owl.theme.green.css', array(), $this->version, 'all' );

		wp_register_style( 'nivo-slider', plugin_dir_url(  __FILE__ ) . 'library/nivo-slider/nivo-slider.css' , array(), '3.2', 'all' );
		wp_register_style( 'nivo-slider-theme', plugin_dir_url(  __FILE__ ) . 'library/nivo-slider/themes/theme.css' , array('nivo-slider'), '3.2', 'all' );

		wp_register_style( 'font-awesome', plugin_dir_url( dirname( __FILE__ ) ) . '/assets/css/font-awesome.css' , '', '4.1.0', 'all' );
		wp_register_style( 'shapla-shortcode-styles', plugin_dir_url( dirname( __FILE__ ) ) . '/assets/css/shapla-shortcodes.css' , array( 'font-awesome' ), $this->version, 'all' );


		wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'shapla-shortcode-styles' );

		wp_enqueue_style( 'nivo-slider' );
		wp_enqueue_style( 'nivo-slider-theme' );

	}

	public function enqueue_scripts() {


		wp_enqueue_script( $this->shaplatools, plugin_dir_url( __FILE__ ) . 'js/shaplatools.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'library/owl-carousel/owl.carousel.min.js', array( 'jquery' ), '2.0.0', true );



		wp_register_script( 'nivo-slider', plugin_dir_url( __FILE__ ). 'library/nivo-slider/jquery.nivo.slider.js', array( 'jquery' ), '3.2', true );

		wp_register_script( 'shapla-shortcode-scripts', plugin_dir_url( dirname( __FILE__ ) ). '/assets/js/shapla-shortcode-scripts.js', array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-tabs' ), $this->version, true );


		wp_enqueue_script( 'shapla-shortcode-scripts' );
		wp_enqueue_script( 'nivo-slider' );

	}

}
