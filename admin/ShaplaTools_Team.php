<?php

/** 
 * The Class.
 */
class ShaplaTools_Team {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'team_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'team_meta_box' ) );
		add_action( 'add_meta_boxes', array( $this, 'post_thumbnail_image_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'manage_team_posts_custom_column', array( $this, 'team_columns_content' ) );
		add_filter( 'manage_edit-team_columns', array( $this, 'team_columns_head' ) );
	}
	public static function team_post_type() {

		$labels = array(
			'name'                => _x( 'Team Members', 'Post Type General Name', 'shaplatools' ),
			'singular_name'       => _x( 'Team Member', 'Post Type Singular Name', 'shaplatools' ),
			'menu_name'           => __( 'Team Members', 'shaplatools' ),
			'all_items'           => __( 'All Team Members', 'shaplatools' ),
			'view_item'           => __( 'View Team Member', 'shaplatools' ),
			'add_new_item'        => __( 'Add New Team Member', 'shaplatools' ),
			'add_new'             => __( 'Add New', 'shaplatools' ),
			'edit_item'           => __( 'Edit Team Member', 'shaplatools' ),
			'update_item'         => __( 'Update Team Member', 'shaplatools' ),
			'search_items'        => __( 'Search Team Member', 'shaplatools' ),
			'not_found'           => __( 'Not found', 'shaplatools' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'shaplatools' ),
			'parent_item_colon'   => '',
		);
		$rewrite = array(
			'slug'                => 'team',
			'with_front'          => false,
		);
		$args = array(
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-groups',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
		);
		register_post_type( 'team', $args );

	}

	public function team_meta_box() {
	    add_meta_box(
	    	'shaplatools_team_section',
	    	__( 'Team Member Details','shaplatools' ),
	    	array( $this, 'meta_box_callback' ), 
	    	'team',
	    	'advanced',
			'core'
	    );
	}

	public function post_thumbnail_image_box() {
	    remove_meta_box( 'postimagediv', 'team', 'side' );
	    add_meta_box(
	    	'postimagediv', 
	    	__('Team Member Image', 'shaplatools'), 
	    	'post_thumbnail_meta_box', 
	    	'team', 
	    	'side', 
	    	'low'
	    );
	}

	public function save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['shaplatools_team_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['shaplatools_team_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'shaplatools_team_custom_box' ) )
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
		$member_name = sanitize_text_field( $_POST['shaplatools_team_member_name'] );
		$member_designation = sanitize_text_field( $_POST['shaplatools_team_member_designation'] );
		$member_description = sanitize_text_field( $_POST['shaplatools_team_member_description'] );

		// Update the meta field.
		update_post_meta( $post_id, '_shaplatools_team_member_name', $member_name );
		update_post_meta( $post_id, '_shaplatools_team_member_designation', $member_designation );
		update_post_meta( $post_id, '_shaplatools_team_member_description', $member_description );
	}

	public function meta_box_callback($post){

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'shaplatools_team_custom_box', 'shaplatools_team_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$member_name = get_post_meta( $post->ID, '_shaplatools_team_member_name', true );
		$member_designation = get_post_meta( $post->ID, '_shaplatools_team_member_designation', true );
		$member_description = get_post_meta( $post->ID, '_shaplatools_team_member_description', true );

		?>
		<table class="form-table">
			<tr valign="top">
                <th scope="row">
                    <label for="shaplatools_team_member_name">
                        <?php _e('Team Member Name','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="regular-text" id="shaplatools_team_member_name" name="shaplatools_team_member_name" value="<?php echo esc_attr( $member_name ); ?>" style="width:100% !important">
                    <p><?php _e('','shaplatools'); ?></p>
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="shaplatools_team_member_designation">
                        <?php _e('Team Member Designation','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="widefat" id="shaplatools_team_member_designation" name="shaplatools_team_member_designation" value="<?php echo esc_attr( $member_designation ); ?>">
                    <p><?php _e('','shaplatools'); ?></p>
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="shaplatools_team_member_description">
                        <?php _e('Member Short Description','shaplatools') ?>
                    </label>
                </th>
				<td>
                    <?php
						$content = get_post_meta( $post->ID, '_shaplatools_team_member_description', true );
						
						$editor_id = 'shaplatools_team_member_description';

						wp_editor( 
							$content, 
							$editor_id,
							array(
								'textarea_rows' => 7,
								'media_buttons' => false
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
	public function team_columns_head( $defaults ) {

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
	/**
	 * Custom columns content
	 * @param  string 	$column_name The name of the current column
	 * @param  int 		$post_id     The id of the current post
	 */
	public function team_columns_content( $column_name ) {

		global $post;

		if ( 'member_name' == $column_name ) {
			$member_name = get_post_meta( $post->ID, '_shaplatools_team_member_name', true );
			echo $member_name;
		}

		if ( 'member_designation' == $column_name ) {
			$member_designation = get_post_meta( $post->ID, '_shaplatools_team_member_designation', true );
			echo $member_designation;
		}

		if ( 'member_description' == $column_name ) {
			$member_description = get_post_meta( $post->ID, '_shaplatools_team_member_description', true );
			echo $member_description;
		}

		if ( 'member_image' == $column_name ) {
			echo get_the_post_thumbnail( $post->ID, array(50,50));
		}
	}
}