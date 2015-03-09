<?php

/** 
 * The Class.
 */
class ShaplaTools_Testimonial {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'testimonial_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'testimonial_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'manage_testimonial_posts_custom_column', array( $this, 'testimonial_columns_content' ) );
		add_filter( 'manage_edit-testimonial_columns', array( $this, 'testimonial_columns_head' ) );
	}
	public static function testimonial_post_type() {

		$labels = array(
			'name'                => _x( 'Testimonials', 'Post Type General Name', 'shaplatools' ),
			'singular_name'       => _x( 'Testimonial', 'Post Type Singular Name', 'shaplatools' ),
			'menu_name'           => __( 'Testimonials', 'shaplatools' ),
			'parent_item_colon'   => __( 'Parent Testimonial:', 'shaplatools' ),
			'all_items'           => __( 'All Testimonials', 'shaplatools' ),
			'view_item'           => __( 'View Testimonial', 'shaplatools' ),
			'add_new_item'        => __( 'Add New Testimonial', 'shaplatools' ),
			'add_new'             => __( 'Add New', 'shaplatools' ),
			'edit_item'           => __( 'Edit Testimonial', 'shaplatools' ),
			'update_item'         => __( 'Update Testimonial', 'shaplatools' ),
			'search_items'        => __( 'Search Testimonial', 'shaplatools' ),
			'not_found'           => __( 'Not found', 'shaplatools' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'shaplatools' ),
		);
		$rewrite = array(
			'slug'                => 'testimonial',
			'with_front'          => false,
		);
		$args = array(
			'label'               => __( 'testimonial', 'shaplatools' ),
			'description'         => __( 'A list of upcoming testimonials.', 'shaplatools' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-testimonial',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
		);
		register_post_type( 'testimonial', $args );

	}

	public function testimonial_meta_box() {
	    add_meta_box(
	    	'shaplatools_testimonial_section',
	    	__( 'Testimonial Details','shaplatools' ),
	    	array( $this, 'meta_box_callback' ), 
	    	'testimonial',
	    	'advanced',
			'core'
	    );
	}
	public function save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['shaplatools_testimonial_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['shaplatools_testimonial_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'shaplatools_testimonial_custom_box' ) )
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

		if ( ! empty( $_POST['shaplatools_testimonial'] ) ) {

			$testimonial['client_name'] = sanitize_text_field( $_POST['shaplatools_testimonial']['client_name'] );
			$testimonial['client_source'] = sanitize_text_field( $_POST['shaplatools_testimonial']['client_source'] );
			$testimonial['client_link'] = sanitize_text_field( $_POST['shaplatools_testimonial']['client_link'] );

			update_post_meta( $post_id, '_shaplatools_testimonial', $testimonial );
		} else {
			delete_post_meta( $post_id, '_shaplatools_testimonial' );
		}

	}

	public function meta_box_callback($post){

		$testimonial = get_post_meta( $post->ID, '_shaplatools_testimonial', true );

		$client_name = ( empty( $testimonial['client_name'] ) ) ? '' : $testimonial['client_name'];
		$client_source = ( empty( $testimonial['client_source'] ) ) ? '' : $testimonial['client_source'];
		$client_link = ( empty( $testimonial['client_link'] ) ) ? '' : $testimonial['client_link'];

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'shaplatools_testimonial_custom_box', 'shaplatools_testimonial_custom_box_nonce' );

		?>
		<table class="form-table">
			<tr valign="top">
                <th scope="row">
                    <label for="client_name">
                        <?php _e('Client\'s Name (optional)','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="regular-text" id="client_name" name="shaplatools_testimonial[client_name]" value="<?php echo esc_attr( $client_name ); ?>" style="width:100% !important">
                    <p><?php _e('','shaplatools'); ?></p>
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="client_source">
                        <?php _e('Business/Site Name (optional)','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="regular-text" id="client_source" name="shaplatools_testimonial[client_source]" value="<?php echo esc_attr( $client_source ); ?>" style="width:100% !important">
                    <p><?php _e('','shaplatools'); ?></p>
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="client_link">
                        <?php _e('Link (optional)','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="regular-text" id="client_link" name="shaplatools_testimonial[client_link]" value="<?php echo esc_attr( $client_link ); ?>" style="width:100% !important">
                    <p><?php _e('','shaplatools'); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}
	/**
	 * Custom columns head
	 * @param  array $defaults The default columns in the post admin
	 */
	public function testimonial_columns_head( $defaults ) {

		$defaults = array(
			'cb' 			=> '<input type="checkbox">',
			'title' 		=> 'Title',
			'post_id' 		=> 'Testimonial ID',
			'testimonial' 	=> 'Testimonial',
			'client_name' 	=> 'Client\'s Name',
			'client_source'	=> 'Business/Site',
			'client_link'	=> 'Link'
		);

		return $defaults;
	}
	/**
	 * Custom columns content
	 * @param  string 	$column_name The name of the current column
	 * @param  int 		$post_id     The id of the current post
	 */
	public function testimonial_columns_content( $column_name ) {

		global $post;

		$testimonial = get_post_meta( $post->ID, '_shaplatools_testimonial', true );

		if ( 'post_id' == $column_name ) {
			echo $post->ID;
		}

		if ( 'testimonial' == $column_name ) {
			the_excerpt();
		}

		if ( 'client_name' == $column_name ) {
			if ( ! empty( $testimonial['client_name'] ) )
			echo $testimonial['client_name'];
		}

		if ( 'client_source' == $column_name ) {
			if ( ! empty( $testimonial['client_source'] ) )
			echo $testimonial['client_source'];
		}

		if ( 'client_link' == $column_name ) {
			if ( ! empty( $testimonial['client_link'] ) )
			echo $testimonial['client_link'];
		}
	}
}