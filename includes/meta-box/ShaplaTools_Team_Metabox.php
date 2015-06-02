<?php

if( !class_exists('ShaplaTools_Team_Metabox') ):

class ShaplaTools_Team_Metabox {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'add_meta_boxes', array( $this, 'team_member_image' ) );

		add_filter( 'manage_edit-team_columns', array ($this, 'columns_head') );
		add_action( 'manage_team_posts_custom_column', array ($this, 'columns_content') );
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

	public function team_member_image(){
		remove_meta_box( 'postimagediv', 'team', 'side' );
		add_meta_box('postimagediv', __('Team Member Image', 'shapla'), 'post_thumbnail_meta_box', 'team', 'side', 'low'		);
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box() {
		$meta_box = array(
		    'id' => 'shapla-metabox-team',
		    'title' => __('Team Member Details', 'shapla'),
		    'description' => __('Here you can customize your team members details.', 'shapla'),
		    'page' => 'team',
		    'context' => 'advanced',
		    'priority' => 'core',
		    'fields' => array(
		        array(
		            'name' => __('Team Member Name', 'shapla'),
		            'desc' => __('Enter team member name.', 'shapla'),
		            'id' => '_shapla_team_name',
		            'type' => 'text',
		            'std' => ''
		        ),
		        array(
		            'name' => __('Team Member Designation', 'shapla'),
		            'desc' => __('Enter team member designation.', 'shapla'),
		            'id' => '_shapla_team_designation',
		            'type' => 'text',
		            'std' => ''
		        ),
		        array(
		            'name' => __('Member Short Description', 'shapla'),
		            'desc' => __('Enter team member short description.', 'shapla'),
		            'id' => '_shapla_team_description',
		            'type' => 'textarea',
		            'std' => '',
		        ),
		    )
		);
		$ShaplaTools_Metaboxs = new ShaplaTools_Metaboxs();
		$ShaplaTools_Metaboxs->shapla_add_meta_box($meta_box);
	}

	public function columns_head( $defaults ) {
		$defaults = array(
			'cb' 					=> '<input type="checkbox">',
			'title' 				=> 'Title',
			'member_name' 			=> 'Team Member Name',
			'member_designation' 	=> 'Designation',
			'member_designation'	=> 'Short Description',
			'member_image'			=> 'Member Image',
		);

		return $defaults;
	}

	public function columns_content( $column_name ) {

		$name 			= get_post_meta( get_the_ID(), '_shapla_team_name', true );
		$designation 	= get_post_meta( get_the_ID(), '_shapla_team_designation', true );
		$description 	= get_post_meta( get_the_ID(), '_shapla_team_description', true );

		if ( 'member_name' == $column_name ) {

			if ( ! empty( $name ) )
			echo $name;
		}

		if ( 'member_designation' == $column_name ) {

			if ( ! empty( $designation ) )
			echo $designation;
		}

		if ( 'member_designation' == $column_name ) {

			if ( ! empty( $description ) )
			echo $description;
		}

		if ( 'member_image' == $column_name ) {

			echo get_the_post_thumbnail( get_the_ID(), array(50,50));
		}
	}
}

function run_shaplatools_team_meta(){
	if (is_admin())
		ShaplaTools_Team_Metabox::get_instance();
}
run_shaplatools_team_meta();
endif;