<?php

$labels = array(
	'name'               => __( 'Testimonials', 'shapla' ),
	'singular_name'      => __( 'Testimonial', 'shapla' ),
	'add_new'            => __( 'Add New', 'shapla' ),
	'add_new_item'       => __( 'Add New Testimonial', 'shapla' ),
	'edit_item'          => __( 'Edit Testimonial', 'shapla' ),
	'new_item'           => __( 'New Testimonial', 'shapla' ),
	'view_item'          => __( 'View Testimonial', 'shapla' ),
	'search_items'       => __( 'Search Testimonials', 'shapla' ),
	'not_found'          => __( 'No Testimonials found', 'shapla' ),
	'not_found_in_trash' => __( 'No Testimonials in trash', 'shapla' ),
	'parent_item_colon'  => ''
);

$args = array(
	'labels'              => $labels,
	'public'              => false,
	'exclude_from_search' => true,
	'publicly_queryable'  => false,
	'rewrite'             => array( 'slug' => 'testimonials' ),
	'show_ui'             => true,
	'query_var'           => true,
	'capability_type'     => 'post',
	'hierarchical'        => false,
	'menu_position'       => 35,
	'menu_icon'           => 'dashicons-format-chat',
	'has_archive'         => false,
	'supports'            => array( 'editor', 'thumbnail' )
);

register_post_type( 'testimonials', $args );

/**
 * Adding the necessary metabox
 *
 * This functions is attached to the 'testimonials_post_type()' meta box callback.
 */
function testimonials_meta_boxes() {
	add_meta_box( 'testimonials_form', 'Testimonial Details', 'testimonials_form', 'testimonials', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'testimonials_meta_boxes');

/**
 * Adding the necessary metabox
 *
 * This functions is attached to the 'add_meta_box()' callback.
 */
function testimonials_form() {
	$testimonial_data = get_post_meta( get_the_ID(), '_testimonial', true );
	$client_name = ( empty( $testimonial_data['client_name'] ) ) ? '' : $testimonial_data['client_name'];
	$source = ( empty( $testimonial_data['source'] ) ) ? '' : $testimonial_data['source'];
	$link = ( empty( $testimonial_data['link'] ) ) ? '' : $testimonial_data['link'];

	wp_nonce_field( 'testimonials', 'testimonials' );
	?>
	<table class="form-table">
		<tr valign="top">
            <th scope="row">
                <label for="client_name">
                    <?php _e('Client\'s Name (optional)','shaplatools') ?>
                </label>
            </th>
			<td>
				<input type="text" class="widefat" id="client_name" name="testimonial[client_name]" value="<?php echo esc_attr( $client_name ); ?>">
			</td>
		</tr>
		<tr valign="top">
            <th scope="row">
                <label for="source">
                    <?php _e('Business/Site Name (optional)','shaplatools') ?>
                </label>
            </th>
			<td>
				<input type="text" class="widefat" id="source" name="testimonial[source]" value="<?php echo esc_attr( $source ); ?>">
			</td>
		</tr>
		<tr valign="top">
            <th scope="row">
                <label for="link">
                    <?php _e('Link (optional)','shaplatools') ?>
                </label>
            </th>
			<td>
				<input type="text" class="widefat" id="link" name="testimonial[link]" value="<?php echo esc_attr( $link ); ?>">
			</td>
		</tr>
	</table>
	<?php
}

add_action( 'save_post', 'testimonials_save_post' );
/**
 * Data validation and saving
 *
 * This functions is attached to the 'save_post' action hook.
 */
function testimonials_save_post( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( ! empty( $_POST['testimonials'] ) && ! wp_verify_nonce( $_POST['testimonials'], 'testimonials' ) )
		return;

	if ( ! empty( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;
	}

	if ( ! wp_is_post_revision( $post_id ) && 'testimonials' == get_post_type( $post_id ) ) {
		remove_action( 'save_post', 'testimonials_save_post' );

		wp_update_post( array(
			'ID' => $post_id,
			'post_title' => 'Testimonial - ' . $post_id
		) );

		add_action( 'save_post', 'testimonials_save_post' );
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

add_filter( 'manage_edit-testimonials_columns', 'testimonials_edit_columns' );
/**
 * Modifying the list view columns
 *
 * This functions is attached to the 'manage_edit-testimonials_columns' filter hook.
 */
function testimonials_edit_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox">',
		'title' => 'Title',
		'testimonial' => 'Testimonial',
		'testimonial-client-name' => 'Client\'s Name',
		'testimonial-source' => 'Business/Site',
		'testimonial-link' => 'Link',
		'date' => 'Date'
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
	}
}