<?php

/** 
 * The Class.
 */
class ShaplaTools_Portfolio {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'portfolio_post_type' ) );
		add_action( 'init', array( $this, 'portfolio_taxonomy' ) );
		add_action( 'add_meta_boxes', array( $this, 'portfolio_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'manage_portfolio_posts_custom_column', array( $this, 'portfolio_columns_content' ) );
		add_filter( 'manage_edit-portfolio_columns', array( $this, 'portfolio_columns_head' ) );
	}
	public static function portfolio_post_type() {

		$labels = array(
			'name'                => _x( 'Portfolios', 'Post Type General Name', 'shaplatools' ),
			'singular_name'       => _x( 'Portfolio', 'Post Type Singular Name', 'shaplatools' ),
			'menu_name'           => __( 'Portfolios', 'shaplatools' ),
			'parent_item_colon'   => __( 'Parent Portfolio:', 'shaplatools' ),
			'all_items'           => __( 'All Portfolios', 'shaplatools' ),
			'view_item'           => __( 'View Portfolio', 'shaplatools' ),
			'add_new_item'        => __( 'Add New Portfolio', 'shaplatools' ),
			'add_new'             => __( 'Add New', 'shaplatools' ),
			'edit_item'           => __( 'Edit Portfolio', 'shaplatools' ),
			'update_item'         => __( 'Update Portfolio', 'shaplatools' ),
			'search_items'        => __( 'Search Portfolio', 'shaplatools' ),
			'not_found'           => __( 'Not found', 'shaplatools' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'shaplatools' ),
		);
		$rewrite = array(
			'slug'                => 'portfolio',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __( 'portfolio', 'shaplatools' ),
			'description'         => __( 'Add portfolio item into your theme.', 'shaplatools' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'taxonomies'          => array( 'portfolio_category' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-portfolio',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
		);
		register_post_type( 'portfolio', $args );

	}

	public function portfolio_taxonomy() {

	    $labels = array(
	        'name'                       => _x( 'Portfolio Categories', 'Taxonomy General Name', 'shaplatools' ),
	        'singular_name'              => _x( 'Portfolio Category', 'Taxonomy Singular Name', 'shaplatools' ),
	        'menu_name'                  => __( 'Portfolio Categories', 'shaplatools' ),
	        'all_items'                  => __( 'All Portfolio Categories', 'shaplatools' ),
	        'parent_item'                => __( 'Parent Portfolio Category', 'shaplatools' ),
	        'parent_item_colon'          => __( 'Parent Portfolio Category:', 'shaplatools' ),
	        'new_item_name'              => __( 'New Portfolio Category Name', 'shaplatools' ),
	        'add_new_item'               => __( 'Add New Portfolio Category', 'shaplatools' ),
	        'edit_item'                  => __( 'Edit Portfolio Category', 'shaplatools' ),
	        'update_item'                => __( 'Update Portfolio Category', 'shaplatools' ),
	        'separate_items_with_commas' => __( 'Separate Portfolio Categories with commas', 'shaplatools' ),
	        'search_items'               => __( 'Search Portfolio Categories', 'shaplatools' ),
	        'add_or_remove_items'        => __( 'Add or remove Portfolio Categories', 'shaplatools' ),
	        'choose_from_most_used'      => __( 'Choose from the most used Portfolio Categories', 'shaplatools' ),
	        'not_found'                  => __( 'Not Found', 'shaplatools' ),
	    );
	    $args = array(
	        'labels'                     => $labels,
	        'hierarchical'               => false,
	        'public'                     => true,
	        'show_ui'                    => true,
	        'show_admin_column'          => true,
	        'show_in_nav_menus'          => true,
	        'show_tagcloud'              => true,
	        'rewrite'                    => array( 'slug' => 'portfolio-category', ),
	    );
	    register_taxonomy( 'portfolio_category', array( 'portfolio' ), $args );

	}

	public function portfolio_meta_box() {
	    add_meta_box(
	    	'shaplatools_portfolio_section',
	    	__( 'Portfolio Settings','shaplatools' ),
	    	array( $this, 'meta_box_callback' ), 
	    	'portfolio',
	    	'advanced',
			'high'
	    );
	}
	public function save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['shaplatools_portfolio_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['shaplatools_portfolio_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'shaplatools_portfolio_custom_box' ) )
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
		$subtitle = sanitize_text_field( $_POST['shaplatools_portfolio_subtitle'] );
		$client = sanitize_text_field( $_POST['shaplatools_portfolio_client'] );
		$date = sanitize_text_field( $_POST['shaplatools_portfolio_date'] );
		$url = sanitize_text_field( $_POST['shaplatools_portfolio_url'] );

		// Update the meta field.
		update_post_meta( $post_id, '_shaplatools_portfolio_subtitle', $subtitle );
		update_post_meta( $post_id, '_shaplatools_portfolio_client', $client );
		update_post_meta( $post_id, '_shaplatools_portfolio_date', strtotime($date) );
		update_post_meta( $post_id, '_shaplatools_portfolio_url', $url );
	}

	public function meta_box_callback($post){

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'shaplatools_portfolio_custom_box', 'shaplatools_portfolio_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$subtitle = get_post_meta( $post->ID, '_shaplatools_portfolio_subtitle', true );
		$client = get_post_meta( $post->ID, '_shaplatools_portfolio_client', true );
		$date = get_post_meta( $post->ID, '_shaplatools_portfolio_date', true );
		$url = get_post_meta( $post->ID, '_shaplatools_portfolio_url', true );
		
		?>
		<table class="form-table">
			<tr valign="top">
                <th scope="row">
                    <label for="shaplatools_portfolio_subtitle">
                        <?php _e('Subtitle','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="regular-text" id="shaplatools_portfolio_subtitle" name="shaplatools_portfolio_subtitle" value="<?php echo esc_attr( $subtitle ); ?>" style="width:100% !important">
                    <p><?php _e('Enter the subtitle for this portfolio item.','shaplatools'); ?></p>
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="shaplatools_portfolio_client">
                        <?php _e('Client Name','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="regular-text" id="shaplatools_portfolio_client" name="shaplatools_portfolio_client" value="<?php echo esc_attr( $client ); ?>">
                    <p><?php _e('Enter the client name of the project.','shaplatools'); ?></p>
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="shaplatools_portfolio_url">
                        <?php _e('Project URL','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="url" class="regular-text" id="shaplatools_portfolio_url" name="shaplatools_portfolio_url" value="<?php echo esc_attr( $url ); ?>">
                    <p><?php _e('Enter the project URL','shaplatools'); ?></p>
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="shaplatools_portfolio_date">
                        <?php _e('Project Date','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="" id="shaplatools_portfolio_date" name="shaplatools_portfolio_date" value="<?php echo date( 'F d, Y', $date ); ?>">
                    <p><?php _e('Choose the project date.','shaplatools'); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}
	/**
	 * Custom columns head
	 * @param  array $defaults The default columns in the post admin
	 */
	public function portfolio_columns_head( $defaults ) {
		unset( $defaults['date'] );

		$defaults['project_date'] = __( 'Project Date', 'shaplatools' );
		$defaults['project_client'] = __( 'Client', 'shaplatools' );
		$defaults['project_url'] = __( 'Project URL', 'shaplatools' );

		return $defaults;
	}
	/**
	 * Custom columns content
	 * @param  string 	$column_name The name of the current column
	 * @param  int 		$post_id     The id of the current post
	 */
	public function portfolio_columns_content( $column_name ) {

		global $post;

		if ( 'project_date' == $column_name ) {
			$portfolio_date = get_post_meta( $post->ID, '_shaplatools_portfolio_date', true );
			echo date( 'F d, Y', $portfolio_date );
		}

		if ( 'project_client' == $column_name ) {
			$portfolio_client = get_post_meta( $post->ID, '_shaplatools_portfolio_client', true );
			echo $portfolio_client;
		}

		if ( 'project_url' == $column_name ) {
			$project_url = get_post_meta( $post->ID, '_shaplatools_portfolio_url', true );
			echo $project_url;
		}
	}
}

new ShaplaTools_Portfolio();
