<?php

$labels = array(
	'name'               => __( 'Team', 'shapla' ),
	'singular_name'      => __( 'Team', 'shapla' ),
	'add_new'            => __( 'Add New', 'shapla' ),
	'add_new_item'       => __( 'Add New Team Member', 'shapla' ),
	'edit_item'          => __( 'Edit Team Member', 'shapla' ),
	'new_item'           => __( 'New Team Member', 'shapla' ),
	'view_item'          => __( 'View Team Member', 'shapla' ),
	'search_items'       => __( 'Search Team Member', 'shapla' ),
	'not_found'          => __( 'No Team Member found', 'shapla' ),
	'not_found_in_trash' => __( 'No Team Member in trash', 'shapla' ),
	'parent_item_colon'  => ''
);

$args = array(
	'labels'              => $labels,
	'public'              => false,
	'exclude_from_search' => true,
	'publicly_queryable'  => false,
	'rewrite'             => array('slug' => 'team'),
	'show_ui'             => true,
	'query_var'           => true,
	'capability_type'     => 'post',
	'hierarchical'        => false,
	'menu_position'       => 34,
	'menu_icon'           => 'dashicons-groups',
	'has_archive'         => false,
	'supports'            => array( 'thumbnail' )
);

register_post_type( 'team', $args );

function shapla_team_meta_box() {
	add_meta_box(
	    'shaplatools_team_section',
	    __( 'Team Member Details','shapla' ),
	    'shapla_team_meta_box_callback', 
	    'team',
	    'advanced',
		'core'
	);
}
add_action( 'add_meta_boxes', 'shapla_team_meta_box');


function shapla_team_post_thumbnail_image_box() {
	remove_meta_box( 'postimagediv', 'team', 'side' );
	add_meta_box(
	    'postimagediv', 
	    __('Team Member Image', 'shapla'), 
	    'post_thumbnail_meta_box', 
	    'team', 
	    'side', 
	    'low'
	);
}
add_action( 'add_meta_boxes', 'shapla_team_post_thumbnail_image_box');
function shapla_team_save($post_id){

	// Check if our nonce is set.
	if ( ! isset( $_POST['shaplatools_team_custom_box_nonce'] ) )
		return $post_id;

	$nonce = $_POST['shaplatools_team_custom_box_nonce'];

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'shaplatools_team_custom_box' ) )
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

	if ( ! wp_is_post_revision( $post_id ) && 'team' == get_post_type( $post_id ) ) {
		remove_action( 'save_post', 'shapla_team_save' );

		wp_update_post( array(
			'ID' => $post_id,
			'post_title' => 'Team - ' . $post_id
		) );

		add_action( 'save_post', 'shapla_team_save' );
	}

	/* OK, its safe for us to save the data now. */
	if ( ! empty( $_POST['shaplatools_team'] ) ) {

		$team['member_name'] = sanitize_text_field( $_POST['shaplatools_team']['member_name'] );
		$team['member_designation'] = sanitize_text_field( $_POST['shaplatools_team']['member_designation'] );
		$team['member_description'] = esc_textarea( $_POST['shaplatools_team']['member_description'] );

		update_post_meta( $post_id, '_shaplatools_team', $team );
	} else {
		delete_post_meta( $post_id, '_shaplatools_team' );
	}
}
add_action( 'save_post', 'shapla_team_save');

function shapla_team_meta_box_callback(){

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'shaplatools_team_custom_box', 'shaplatools_team_custom_box_nonce' );

	$team = get_post_meta( get_the_ID(), '_shaplatools_team', true );

	$member_name = ( empty( $team['member_name'] ) ) ? '' : $team['member_name'];
	$member_designation = ( empty( $team['member_designation'] ) ) ? '' : $team['member_designation'];
	$member_description = ( empty( $team['member_description'] ) ) ? '' : $team['member_description'];

	?>
		<table class="form-table">
			<tr valign="top">
                <th scope="row">
                    <label for="member_name">
                        <?php _e('Team Member Name','shapla') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="regular-text" id="member_name" name="shaplatools_team[member_name]" value="<?php echo esc_attr( $member_name ); ?>" style="width:100% !important">
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="member_designation">
                        <?php _e('Team Member Designation','shapla') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="widefat" id="member_designation" name="shaplatools_team[member_designation]" value="<?php echo esc_attr( $member_designation ); ?>">
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="shaplatools_team_member_description">
                        <?php _e('Member Short Description','shapla') ?>
                    </label>
                </th>
				<td>
                    <?php
						$content = $member_description;
						
						$editor_id = 'shaplatools_team_member_description';

						wp_editor( 
							$content, 
							$editor_id,
							array(
								'textarea_rows' => 10,
								'media_buttons' => false,
								'textarea_name' => 'shaplatools_team[member_description]'
							)
						);

					?>
				</td>
			</tr>
		</table>
	<?php
}

/**
 * Custom columns head
 * @param  array $defaults The default columns in the post admin
 */
function shapla_team_columns_head( $defaults ) {

	$defaults = array(
		'cb' 					=> '<input type="checkbox">',
		'title' 				=> 'Title',
		'member_name' 			=> 'Team Member Name',
		'member_designation' 	=> 'Designation',
		'member_description'	=> 'Short Description',
		'member_image'			=> 'Member Image',
	);

	return $defaults;
}
add_filter('manage_edit-team_columns', 'shapla_team_columns_head');
/**
 * Custom columns content
 * @param  string 	$column_name The name of the current column
 * @param  int 		$post_id     The id of the current post
 */
function shapla_team_columns_content( $column_name ) {

	$team = get_post_meta( get_the_ID(), '_shaplatools_team', true );

	if ( 'member_name' == $column_name ) {
		if ( ! empty( $team['member_name'] ) )
		echo $team['member_name'];
	}

	if ( 'member_designation' == $column_name ) {
		if ( ! empty( $team['member_designation'] ) )
		echo $team['member_designation'];
	}

	if ( 'member_description' == $column_name ) {
		if ( ! empty( $team['member_description'] ) )
		echo $team['member_description'];
	}

	if ( 'member_image' == $column_name ) {
		echo get_the_post_thumbnail( get_the_ID(), array(50,50));
	}
}
add_action('manage_posts_custom_column',  'shapla_team_columns_content');