<?php

if( !class_exists('ShaplaTools_Slide') ):

class ShaplaTools_Slide {

	public function __construct(){
		add_action( 'init', array ($this, 'post_type') );
		add_action( 'init', array ($this, 'taxonomy') );
	}

	/**
	 * Register a slide post type.
	 * @package ShaplaTools
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public static function post_type(){

		$labels = array(
			'name'               => __( 'Slides', 'shapla' ),
			'singular_name'      => __( 'Slide', 'shapla' ),
			'add_new'            => __( 'Add New', 'shapla' ),
			'add_new_item'       => __( 'Add New Slide', 'shapla' ),
			'edit_item'          => __( 'Edit Slide', 'shapla' ),
			'new_item'           => __( 'New Slide', 'shapla' ),
			'view_item'          => __( 'View Slide', 'shapla' ),
			'search_items'       => __( 'Search Slide', 'shapla' ),
			'not_found'          => __( 'No Slides found', 'shapla' ),
			'not_found_in_trash' => __( 'No Slides found in trash', 'shapla' ),
			'parent_item_colon'  => ''
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => array('slug' => 'slide'),
			'show_ui'             => true,
			'query_var'           => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_position'       => 35,
			'menu_icon'           => 'dashicons-images-alt2',
			'has_archive'         => false,
			'supports'            => array( 'title','editor', 'thumbnail' )
		);

		register_post_type( 'slide', $args );
	}

	/**
	 * Register a skill taxonomy for slide post type.
	 * @package ShaplaTools
	 * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	public static function taxonomy(){
		register_taxonomy( 'slide', 'slide', array(
			'label'             => __( 'Slide Categories', 'shapla' ),
			'singular_label'    => __( 'Slide Category', 'shapla' ),
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'args'              => array( 'orderby' => 'term_order' ),
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'slide', 'hierarchical' => true)
		) );
	}
}

function run_shaplatools_slide(){
	$shaplatools_slide = new ShaplaTools_Slide();
	return $shaplatools_slide;
}
//run_shaplatools_slide();
endif;