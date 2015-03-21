<?php

class ShaplaTools_Typeahead {
	public $plugin_url;

	public function __construct() {
		$this->plugin_url = plugin_dir_url(dirname(dirname(__FILE__)));

		add_action( 'wp_enqueue_scripts', array( $this, 'typeahead_enqueue_scripts' ) );

		add_action( 'wp_ajax_nopriv_ajax_search', array( $this, 'shapla_woo_ajax_search' ) );
		add_action( 'wp_ajax_ajax_search', array( $this, 'shapla_woo_ajax_search' ) );
	}

	/**
	 * Enqueue Typeahead.js and the stylesheet
	 *
	 * @since 1.0.0
	 */
	public function typeahead_enqueue_scripts() {
		wp_enqueue_script( 'wp_typeahead_js', $this->plugin_url . 'assets/js/typeahead.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'wp_hogan_js' , $this->plugin_url . 'assets/js/hogan.min.js', array( 'wp_typeahead_js' ), '', true );
		wp_enqueue_script( 'typeahead_wp_plugin' , $this->plugin_url . 'assets/js/wp-typeahead.js', array( 'wp_typeahead_js', 'wp_hogan_js' ), '', true );

		$wp_typeahead_vars = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'typeahead_wp_plugin', 'wp_typeahead', $wp_typeahead_vars );

		wp_enqueue_style( 'wp_typeahead_css', $this->plugin_url . 'assets/css/typeahead.css' );
	}

	/**
	 * Ajax query for the search
	 *
	 * @since 1.0.0
	 */
	public function shapla_woo_ajax_search() {
		if ( isset( $_REQUEST['fn'] ) && 'get_ajax_search' == $_REQUEST['fn'] ) {
			$search_query = new WP_Query( array(
				's' => $_REQUEST['terms'],
				'post_type' => 'product',
				'posts_per_page' => 10,
				'no_found_rows' => true,
			) );

			$results = array( );
			if ( $search_query->get_posts() ) {
				foreach ( $search_query->get_posts() as $the_post ) {
					$title = get_the_title( $the_post->ID );
					$results[] = array(
						'value' => $title,
						'url' => get_permalink( $the_post->ID ),
						'tokens' => explode( ' ', $title ),
					);
				}
			} else {
				$results[] = __( 'Sorry. No results match your search.', 'wp-typeahead' );
			}

			wp_reset_postdata();
			echo json_encode( $results );
		}
		die();
	}
}
$shaplatools_typeahead = new ShaplaTools_Typeahead;
