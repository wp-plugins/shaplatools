<?php

if( !class_exists('ShaplaTools_Testimonial') ):

class ShaplaTools_Testimonial {

	public function __construct(){
		add_action( 'init', array ($this, 'post_type') );
	}

	/**
	 * Register a testimonial post type.
	 * @package ShaplaTools
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public static function post_type(){

		$labels = array(
			'name'                => _x( 'Testimonials', 'Post Type General Name', 'shapla' ),
			'singular_name'       => _x( 'Testimonial', 'Post Type Singular Name', 'shapla' ),
			'menu_name'           => __( 'Testimonials', 'shapla' ),
			'parent_item_colon'   => __( 'Parent Testimonial:', 'shapla' ),
			'all_items'           => __( 'All Testimonials', 'shapla' ),
			'view_item'           => __( 'View Testimonial', 'shapla' ),
			'add_new_item'        => __( 'Add New Testimonial', 'shapla' ),
			'add_new'             => __( 'Add New', 'shapla' ),
			'edit_item'           => __( 'Edit Testimonial', 'shapla' ),
			'update_item'         => __( 'Update Testimonial', 'shapla' ),
			'search_items'        => __( 'Search Testimonial', 'shapla' ),
			'not_found'           => __( 'Not found', 'shapla' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'shapla' ),
		);
		$args = array(
			'label'               => __( 'testimonials', 'shapla' ),
			'description'         => __( 'Post Type Description', 'shapla' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 30,
			'menu_icon'           => 'dashicons-format-chat',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => array('slug' => 'testimonials',),
			'capability_type'     => 'post',
		);
		register_post_type( 'testimonial', $args );
	}
}

function run_shaplatools_testimonial(){
	$shaplatools_testimonial_activated = true;
	$shaplatools_testimonial = new ShaplaTools_Testimonial();
	return $shaplatools_testimonial;
}
//run_shaplatools_testimonial();
endif;