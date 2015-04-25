<?php

	$labels = array(
		'name'                => _x( 'Features', 'Post Type General Name', 'shapla' ),
		'singular_name'       => _x( 'Feature', 'Post Type Singular Name', 'shapla' ),
		'menu_name'           => __( 'Features', 'shapla' ),
		'parent_item_colon'   => __( 'Parent Feature:', 'shapla' ),
		'all_items'           => __( 'All Features', 'shapla' ),
		'view_item'           => __( 'View Feature', 'shapla' ),
		'add_new_item'        => __( 'Add New Feature', 'shapla' ),
		'add_new'             => __( 'Add New', 'shapla' ),
		'edit_item'           => __( 'Edit Feature', 'shapla' ),
		'update_item'         => __( 'Update Feature', 'shapla' ),
		'search_items'        => __( 'Search Feature', 'shapla' ),
		'not_found'           => __( 'Not found', 'shapla' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'shapla' ),
	);
	$args = array(
		'label'               => __( 'feature', 'shapla' ),
		'description'         => __( 'Add features', 'shapla' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 35,
		'menu_icon'           => 'dashicons-pressthis',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'features', $args );

//Adding metabox for feature information

function shapla_add_feature_info_metabox() {
	add_meta_box( 'shaplatools_feature_section', __( 'Extra Settings', 'shapla' ), 'shapla_render_feature_info_metabox', 'features','side', 'core' );
}
add_action( 'add_meta_boxes', 'shapla_add_feature_info_metabox' );


/**
 * Rendering the metabox for feature information
 * @param  object $post The post object
 */
function shapla_render_feature_info_metabox( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'shaplatools_feature_custom_box', 'shaplatools_feature_custom_box_nonce' );

	// Use get_post_meta to retrieve an existing value from the database.
		
	$feature = get_post_meta( get_the_ID(), '_shaplatools_feature', true );

	$fa_icon = ( empty( $feature['fa_icon'] ) ) ? '' : $feature['fa_icon'];

	?>
		<p>
			<label for="fa_icon"><?php _e( 'Font Awesome Icon Class:', 'shapla' ); ?></label>
			<input type="text" id="fa_icon" name="shaplatools_feature[fa_icon]" class="widefat" value="<?php echo $fa_icon; ?>" placeholder="fa fa-wordpress">
			<span>Add <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome</a> Icon Class</span>
		</p>
	<?php
}


/**
 * Saving the feature along with its meta values
 * @param  int $post_id The id of the current post
 */
function shapla_save_feature_info( $post_id ) {

	// Check if our nonce is set.
	if ( ! isset( $_POST['shaplatools_feature_custom_box_nonce'] ) )
		return $post_id;

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['shaplatools_feature_custom_box_nonce'], 'shaplatools_feature_custom_box' ) )
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
	if ( ! empty( $_POST['shaplatools_feature'] ) ) {

		$feature['fa_icon'] = sanitize_text_field( $_POST['shaplatools_feature']['fa_icon'] );

		update_post_meta( $post_id, '_shaplatools_feature', $feature );
	} else {
		delete_post_meta( $post_id, '_shaplatools_feature' );
	}
}
add_action( 'save_post', 'shapla_save_feature_info' );