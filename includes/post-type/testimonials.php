<?php

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
		'supports'            => array( 'editor', 'thumbnail', ),
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

/**
 * Adding the necessary metabox
 *
 * This functions is attached to the 'testimonials_post_type()' meta box callback.
 */
function shapla_testimonials_meta_box() {
	add_meta_box( 
		'testimonials_form_id',
		__('Testimonial Details','shapla'),
		'shapla_testimonials_form', 
		'testimonial', 
		'normal', 
		'high'
	);
}
add_action( 'add_meta_boxes', 'shapla_testimonials_meta_box');

/**
 * Adding the necessary metabox
 *
 * This functions is attached to the 'add_meta_box()' callback.
 */
function shapla_testimonials_form() {
	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'shapla_testimonials_meta_box', 'shapla_testimonials_meta_box_nonce' );

	$testimonial = get_post_meta( get_the_ID(), '_testimonial', true );
	$client_name = ( empty( $testimonial['client_name'] ) ) ? '' : $testimonial['client_name'];
	$source = ( empty( $testimonial['source'] ) ) ? '' : $testimonial['source'];
	$link = ( empty( $testimonial['link'] ) ) ? '' : $testimonial['link'];

	?>
	<table class="form-table">
		<tr valign="top">
            <th scope="row">
                <label for="client_name">
                    <?php _e('Client\'s Name (optional)','shapla') ?>
                </label>
            </th>
			<td>
				<input type="text" class="widefat" id="client_name" name="testimonial[client_name]" value="<?php echo esc_attr( $client_name ); ?>">
			</td>
		</tr>
		<tr valign="top">
            <th scope="row">
                <label for="source">
                    <?php _e('Business/Site Name (optional)','shapla') ?>
                </label>
            </th>
			<td>
				<input type="text" class="widefat" id="source" name="testimonial[source]" value="<?php echo esc_attr( $source ); ?>">
			</td>
		</tr>
		<tr valign="top">
            <th scope="row">
                <label for="link">
                    <?php _e('Business/Site Link (optional)','shapla') ?>
                </label>
            </th>
			<td>
				<input type="text" class="widefat" id="link" name="testimonial[link]" value="<?php echo esc_attr( $link ); ?>">
			</td>
		</tr>
	</table>
	<?php
}

add_action( 'save_post', 'shapla_testimonials_save_post' );
/**
 * Data validation and saving
 *
 * This functions is attached to the 'save_post' action hook.
 */
function shapla_testimonials_save_post( $post_id ) {
	
	// Check if our nonce is set.
	if ( ! isset( $_POST['shapla_testimonials_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['shapla_testimonials_meta_box_nonce'], 'shapla_testimonials_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */

	if ( ! wp_is_post_revision( $post_id ) && 'testimonial' == get_post_type( $post_id ) ) {
		remove_action( 'save_post', 'shapla_testimonials_save_post' );

		wp_update_post( array(
			'ID' => $post_id,
			'post_title' => 'Testimonial - ' . $post_id
		) );

		add_action( 'save_post', 'shapla_testimonials_save_post' );
	}

	if ( ! empty( $_POST['testimonial'] ) ) {
		$testimonial_data['client_name'] = ( empty( $_POST['testimonial']['client_name'] ) ) ? '' : sanitize_text_field( $_POST['testimonial']['client_name'] );
		$testimonial_data['source'] = ( empty( $_POST['testimonial']['source'] ) ) ? '' : sanitize_text_field( $_POST['testimonial']['source'] );
		$testimonial_data['link'] = ( empty( $_POST['testimonial']['link'] ) ) ? '' : esc_url( $_POST['testimonial']['link'] );

		update_post_meta( $post_id, '_testimonial', $testimonial_data );
	} else {
		delete_post_meta( $post_id, '_testimonial' );
	}
}

add_filter( 'manage_edit-testimonial_columns', 'testimonials_edit_columns' );
/**
 * Modifying the list view columns
 *
 * This functions is attached to the 'manage_edit-testimonial_columns' filter hook.
 */
function testimonials_edit_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox">',
		'title' => 'Title',
		'testimonial' => 'Testimonial',
		'testimonial-client-name' => 'Client\'s Name',
		'testimonial-source' => 'Business/Site',
		'testimonial-link' => 'Link',
		'testimonial-avatar' => 'Client\'s Avatar'
	);

	return $columns;
}

add_action( 'manage_posts_custom_column', 'testimonials_columns', 10, 2 );
/**
 * Customizing the list view columns
 *
 * This functions is attached to the 'manage_posts_custom_column' action hook.
 */
function testimonials_columns( $column, $post_id ) {
	$testimonial_data = get_post_meta( $post_id, '_testimonial', true );
	switch ( $column ) {
		case 'testimonial':
			the_excerpt();
			break;
		case 'testimonial-client-name':
			if ( ! empty( $testimonial_data['client_name'] ) )
				echo $testimonial_data['client_name'];
			break;
		case 'testimonial-source':
			if ( ! empty( $testimonial_data['source'] ) )
				echo $testimonial_data['source'];
			break;
		case 'testimonial-link':
			if ( ! empty( $testimonial_data['link'] ) )
				echo $testimonial_data['link'];
			break;
		case 'testimonial-avatar':
			echo get_the_post_thumbnail( get_the_ID(), array(64,64));
			break;
	}
}