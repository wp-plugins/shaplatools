<?php

if( !class_exists('ShaplaTools_Event') ):

class ShaplaTools_Event {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	public function __construct(){
		add_action( 'init', array ($this, 'post_type') );
		add_action( 'widgets_init', 'shaplatools_register_widget_event' );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register a event post type.
	 * @package ShaplaTools
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public static function post_type(){

		$labels = array(
			'name'                => _x( 'Events', 'Post Type General Name', 'shapla' ),
			'singular_name'       => _x( 'Event', 'Post Type Singular Name', 'shapla' ),
			'menu_name'           => __( 'Event', 'shapla' ),
			'parent_item_colon'   => __( 'Parent Event:', 'shapla' ),
			'all_items'           => __( 'All Events', 'shapla' ),
			'view_item'           => __( 'View Event', 'shapla' ),
			'add_new_item'        => __( 'Add New Event', 'shapla' ),
			'add_new'             => __( 'Add New', 'shapla' ),
			'edit_item'           => __( 'Edit Event', 'shapla' ),
			'update_item'         => __( 'Update Event', 'shapla' ),
			'search_items'        => __( 'Search Event', 'shapla' ),
			'not_found'           => __( 'Not found', 'shapla' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'shapla' ),
		);
		$args = array(
			'label'               => __( 'event', 'shapla' ),
			'description'         => __( 'A list of upcoming events', 'shapla' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 35,
			'menu_icon'           => 'dashicons-calendar-alt',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);
		register_post_type( 'event', $args );
	}
}

function run_shaplatools_event(){
	ShaplaTools_Event::get_instance();
}
//run_shaplatools_event();
endif;