<?php

class ShaplaTools_Typeahead {
	public $plugin_url;

	public function __construct() {
		add_action( 'wp_ajax_nopriv_ajax_search', array( $this, 'shapla_woo_ajax_search' ) );
		add_action( 'wp_ajax_ajax_search', array( $this, 'shapla_woo_ajax_search' ) );
	}

	/**
	 * Ajax query for the search
	 *
	 * @since 1.0.0
	 */
	public function shapla_woo_ajax_search() {
		if ( isset( $_REQUEST['fn'] ) && 'get_ajax_search' == $_REQUEST['fn'] ) {

			$args = array(
				's' => $_REQUEST['terms'],
				//'post_type' => 'product',
				'posts_per_page' => 10,
				'no_found_rows' => true,
			);
			$search_query = new WP_Query( $args );

			$results = array( );

			if ( $search_query->get_posts() ) {
				foreach ( $search_query->get_posts() as $the_post ) {
					$title = get_the_title( $the_post->ID );
					$img_url = wp_get_attachment_thumb_url( get_post_thumbnail_id($the_post->ID), 'thumbnail' );
					$results[] = array(
						'value' => $title,
						'img_url' => $img_url,
						'url' => get_permalink( $the_post->ID ),
						'tokens' => explode( ' ', $title ),
					);
				}
			} else {
				$results[] = __( 'Sorry. No results match your search.', 'shapla' );
			}

			wp_reset_postdata();
			echo json_encode( $results );
		}
		die();
	}
}
$shaplatools_typeahead = new ShaplaTools_Typeahead;
