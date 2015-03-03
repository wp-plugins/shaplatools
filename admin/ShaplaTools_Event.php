<?php

/** 
 * The Class.
 */
class ShaplaTools_Event {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'event_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'event_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'manage_event_posts_custom_column', array( $this, 'event_columns_content' ) );
		add_filter( 'manage_edit-event_columns', array( $this, 'event_columns_head' ) );
	}
	public function event_post_type() {

		$labels = array(
			'name'                => _x( 'Events', 'Post Type General Name', 'shaplatools' ),
			'singular_name'       => _x( 'Event', 'Post Type Singular Name', 'shaplatools' ),
			'menu_name'           => __( 'Events', 'shaplatools' ),
			'parent_item_colon'   => __( 'Parent Event:', 'shaplatools' ),
			'all_items'           => __( 'All Events', 'shaplatools' ),
			'view_item'           => __( 'View Event', 'shaplatools' ),
			'add_new_item'        => __( 'Add New Event', 'shaplatools' ),
			'add_new'             => __( 'Add New', 'shaplatools' ),
			'edit_item'           => __( 'Edit Event', 'shaplatools' ),
			'update_item'         => __( 'Update Event', 'shaplatools' ),
			'search_items'        => __( 'Search Event', 'shaplatools' ),
			'not_found'           => __( 'Not found', 'shaplatools' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'shaplatools' ),
		);
		$rewrite = array(
			'slug'                => 'event',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __( 'event', 'shaplatools' ),
			'description'         => __( 'A list of upcoming events.', 'shaplatools' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-calendar-alt',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
		);
		register_post_type( 'event', $args );

	}

	public function event_meta_box() {
	    add_meta_box(
	    	'shaplatools_event_section',
	    	__( 'Event Information','shaplatools' ),
	    	array( $this, 'meta_box_callback' ), 
	    	'event',
	    	'side',
			'core'
	    );
	}
	public function save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['shaplatools_event_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['shaplatools_event_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'shaplatools_event_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		$start_date = sanitize_text_field( $_POST['shaplatools_event_start_date'] );
		$end_date = sanitize_text_field( $_POST['shaplatools_event_end_date'] );
		$venue = sanitize_text_field( $_POST['shaplatools_event_venue'] );

		// Update the meta field.
		update_post_meta( $post_id, '_shaplatools_event_start_date', strtotime($start_date) );
		update_post_meta( $post_id, '_shaplatools_event_end_date', strtotime($end_date) );
		update_post_meta( $post_id, '_shaplatools_event_venue', $venue );
	}

	public function meta_box_callback($post){

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'shaplatools_event_custom_box', 'shaplatools_event_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$start_date = get_post_meta( $post->ID, '_shaplatools_event_start_date', true );
		$end_date = get_post_meta( $post->ID, '_shaplatools_event_end_date', true );
		$venue = get_post_meta( $post->ID, '_shaplatools_event_venue', true );

		//if there is previously saved value then retrieve it, else set it to the current time
		$start_date = ! empty( $start_date ) ? $start_date : time();

		//we assume that if the end date is not present, event ends on the same day
		$end_date = ! empty( $end_date ) ? $end_date : $start_date;

		?>
		<p> 
			<label for="shaplatools_event_start_date"><?php _e( 'Event Start Date:', 'shaplatools' ); ?></label>
			<input type="text" id="shaplatools_event_start_date" name="shaplatools_event_start_date" class="widefat sis-event-date-input" value="<?php echo date( 'F d, Y', $start_date ); ?>" placeholder="Format: February 18, 2015">
		</p>
		<p>
			<label for="shaplatools_event_end_date"><?php _e( 'Event End Date:', 'shaplatools' ); ?></label>
			<input type="text" id="shaplatools_event_end_date" name="shaplatools_event_end_date" class="widefat sis-event-date-input" value="<?php echo date( 'F d, Y', $end_date ); ?>" placeholder="Format: February 18, 2015">
		</p>
		<p>
			<label for="shaplatools_event_venue"><?php _e( 'Event Venue:', 'shaplatools' ); ?></label>
			<input type="text" id="shaplatools_event_venue" name="shaplatools_event_venue" class="widefat" value="<?php echo $venue; ?>" placeholder="eg. Times Square">
		</p>
		<?php
	}
	/**
	 * Custom columns head
	 * @param  array $defaults The default columns in the post admin
	 */
	public function event_columns_head( $defaults ) {
		unset( $defaults['date'] );

		$defaults['start_date'] = __( 'Start Date', 'shaplatools' );
		$defaults['end_date'] = __( 'End Date', 'shaplatools' );
		$defaults['venue'] = __( 'Venue', 'shaplatools' );

		return $defaults;
	}
	/**
	 * Custom columns content
	 * @param  string 	$column_name The name of the current column
	 * @param  int 		$post_id     The id of the current post
	 */
	public function event_columns_content( $column_name ) {

		global $post;

		if ( 'start_date' == $column_name ) {
			$start_date = get_post_meta( $post->ID, '_shaplatools_event_start_date', true );
			echo date( 'F d, Y', $start_date );
		}

		if ( 'end_date' == $column_name ) {
			$end_date = get_post_meta( $post->ID, '_shaplatools_event_end_date', true );
			echo date( 'F d, Y', $end_date );
		}

		if ( 'venue' == $column_name ) {
			$venue = get_post_meta( $post->ID, '_shaplatools_event_venue', true );
			echo $venue;
		}
	}
}

new ShaplaTools_Event();
