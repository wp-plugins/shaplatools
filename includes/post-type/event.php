<?php

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

// Move featured image box under title
function shapla_events_img_box()
{
    remove_meta_box( 'postimagediv', 'event', 'side' );
    add_meta_box('postimagediv', __('Upload Event Image', 'shapla'), 'post_thumbnail_meta_box', 'event', 'side', 'low');
}
add_action('do_meta_boxes', 'shapla_events_img_box');

//Adding metabox for event information

function shapla_add_event_info_metabox() {
	add_meta_box( 'shaplatools_event_section', __( 'Event Info', 'shapla' ), 'shapla_render_event_info_metabox', 'event','side', 'core' );
}
add_action( 'add_meta_boxes', 'shapla_add_event_info_metabox' );


/**
 * Rendering the metabox for event information
 * @param  object $post The post object
 */
function shapla_render_event_info_metabox( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'shaplatools_event_custom_box', 'shaplatools_event_custom_box_nonce' );

	// Use get_post_meta to retrieve an existing value from the database.
		
	$event = get_post_meta( get_the_ID(), '_shaplatools_event', true );

	$start_date = ( empty( $event['start_date'] ) ) ? '' : $event['start_date'];
	$end_date = ( empty( $event['end_date'] ) ) ? '' : $event['end_date'];
	$venue = ( empty( $event['venue'] ) ) ? '' : $event['venue'];

	//if there is previously saved value then retrieve it, else set it to the current time
	$start_date = ! empty( $start_date ) ? $start_date : time();

	//we assume that if the end date is not present, event ends on the same day
	$end_date = ! empty( $end_date ) ? $end_date : $start_date;

	?>
		<p> 
			<label for="start_date"><?php _e( 'Event Start Date:', 'shapla' ); ?></label>
			<input type="text" id="start_date" name="shaplatools_event[start_date]" class="widefat shapla-event-date-input" value="<?php echo date_i18n( get_option( 'date_format' ), $start_date ); ?>" placeholder="Format: February 18, 2015">
		</p>
		<p>
			<label for="end_date"><?php _e( 'Event End Date:', 'shapla' ); ?></label>
			<input type="text" id="end_date" name="shaplatools_event[end_date]" class="widefat shapla-event-date-input" value="<?php echo date_i18n( get_option( 'date_format' ), $end_date ); ?>" placeholder="Format: February 18, 2015">
		</p>
		<p>
			<label for="venue"><?php _e( 'Event Venue:', 'shapla' ); ?></label>
			<input type="text" id="venue" name="shaplatools_event[venue]" class="widefat" value="<?php echo $venue; ?>" placeholder="eg. Times Square">
		</p>
	<?php
}


/**
 * Saving the event along with its meta values
 * @param  int $post_id The id of the current post
 */
function shapla_save_event_info( $post_id ) {

	// Check if our nonce is set.
	if ( ! isset( $_POST['shaplatools_event_custom_box_nonce'] ) )
		return $post_id;

	$nonce = $_POST['shaplatools_event_custom_box_nonce'];

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'shaplatools_event_custom_box' ) )
		return $post_id;

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
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
	if ( ! empty( $_POST['shaplatools_event'] ) ) {

		$event['start_date'] = strtotime(sanitize_text_field( $_POST['shaplatools_event']['start_date'] ));
		$event['end_date'] = strtotime(sanitize_text_field( $_POST['shaplatools_event']['end_date'] ));
		$event['venue'] = sanitize_text_field( $_POST['shaplatools_event']['venue'] );

		update_post_meta( $post_id, '_shaplatools_event', $event );
	} else {
		delete_post_meta( $post_id, '_shaplatools_event' );
	}
}
add_action( 'save_post', 'shapla_save_event_info' );

function shapla_custom_columns_head( $defaults ) {
	unset( $defaults['date'] );

	$defaults['start_date'] = __( 'Start Date', 'shapla' );
	$defaults['end_date'] = __( 'End Date', 'shapla' );
	$defaults['venue'] = __( 'Venue', 'shapla' );

	return $defaults;
}
add_filter( 'manage_edit-event_columns', 'shapla_custom_columns_head', 10 );

function shapla_custom_columns_content( $column_name, $post_id ) {

	$event = get_post_meta( get_the_ID(), '_shaplatools_event', true );

	if ( 'start_date' == $column_name ) {

		if ( ! empty( $event['start_date'] ) )
		echo date_i18n( get_option( 'date_format' ), $event['start_date']);
	}

	if ( 'end_date' == $column_name ) {

		if ( ! empty( $event['end_date'] ) )
		echo date_i18n( get_option( 'date_format' ), $event['end_date']);
	}

	if ( 'venue' == $column_name ) {

		if ( ! empty( $event['venue'] ) )
		echo $event['venue'];
	}
}
add_action( 'manage_event_posts_custom_column', 'shapla_custom_columns_content', 10, 2 );