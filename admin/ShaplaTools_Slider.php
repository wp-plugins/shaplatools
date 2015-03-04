<?php

/** 
 * The Class.
 */
class ShaplaTools_Slider {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'slider_post_type' ) );
		add_action( 'init', array( $this, 'slider_taxonomy' ) );
		add_action( 'add_meta_boxes', array( $this, 'slider_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'manage_slider_posts_custom_column', array( $this, 'slider_columns_content' ) );
		add_filter( 'manage_edit-slider_columns', array( $this, 'slider_columns_head' ) );
	}
	public static function slider_post_type() {

		$labels = array(
			'name'                => _x( 'Slides', 'Post Type General Name', 'shaplatools' ),
			'singular_name'       => _x( 'Slide', 'Post Type Singular Name', 'shaplatools' ),
			'menu_name'           => __( 'Slider', 'shaplatools' ),
			'parent_item_colon'   => __( 'Parent Slide:', 'shaplatools' ),
			'all_items'           => __( 'All Slides', 'shaplatools' ),
			'view_item'           => __( 'View Slide', 'shaplatools' ),
			'add_new_item'        => __( 'Add New Slide', 'shaplatools' ),
			'add_new'             => __( 'Add New', 'shaplatools' ),
			'edit_item'           => __( 'Edit Slide', 'shaplatools' ),
			'update_item'         => __( 'Update Slide', 'shaplatools' ),
			'search_items'        => __( 'Search Slide', 'shaplatools' ),
			'not_found'           => __( 'Not found', 'shaplatools' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'shaplatools' ),
		);
		$args = array(
			'label'               => __( 'slider', 'shaplatools' ),
			'description'         => __( 'Custom post for Nivo Image Slider', 'shaplatools' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-slides',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
		);
		register_post_type( 'slider', $args );

	}

	public function slider_taxonomy() {

	    $labels = array(
	        'name'                       => _x( 'Slide Categories', 'Taxonomy General Name', 'shaplatools' ),
	        'singular_name'              => _x( 'Slide Category', 'Taxonomy Singular Name', 'shaplatools' ),
	        'menu_name'                  => __( 'Slide Categories', 'shaplatools' ),
	        'all_items'                  => __( 'All Slide Categories', 'shaplatools' ),
	        'parent_item'                => __( 'Parent Slide Category', 'shaplatools' ),
	        'parent_item_colon'          => __( 'Parent Slide Category:', 'shaplatools' ),
	        'new_item_name'              => __( 'New Slide Category Name', 'shaplatools' ),
	        'add_new_item'               => __( 'Add New Slide Category', 'shaplatools' ),
	        'edit_item'                  => __( 'Edit Slide Category', 'shaplatools' ),
	        'update_item'                => __( 'Update Slide Category', 'shaplatools' ),
	        'separate_items_with_commas' => __( 'Separate Slide Categories with commas', 'shaplatools' ),
	        'search_items'               => __( 'Search Slide Categories', 'shaplatools' ),
	        'add_or_remove_items'        => __( 'Add or remove Slide Categories', 'shaplatools' ),
	        'choose_from_most_used'      => __( 'Choose from the most used Slide Categories', 'shaplatools' ),
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
	        'rewrite'                    => array( 'slug' => 'slide-category', ),
	    );
	    register_taxonomy( 'slide_category', array( 'slider' ), $args );

	}

	public function slider_meta_box() {
	    add_meta_box(
	    	'shaplatools_slider_section',
	    	__( 'Slide Settings','shaplatools' ),
	    	array( $this, 'meta_box_callback' ), 
	    	'slider',
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
		if ( ! isset( $_POST['shaplatools_slider_meta_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['shaplatools_slider_meta_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'shaplatools_slider_inner_custom_box' ) )
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
		$caption = sanitize_text_field( $_POST['shaplatools_slider_caption'] );
		$transition = sanitize_text_field( $_POST['shaplatools_slider_transition'] );
		$link_target = sanitize_text_field( $_POST['shaplatools_slider_slide_link_target'] );

		if ((trim($_POST['shaplatools_slider_slide_link'])) != '') {

			$slide_link = esc_url( $_POST['shaplatools_slider_slide_link'] );

		} else {
			$slide_link = esc_url(get_permalink());
		}
		
		

		// Update the meta field.
		update_post_meta( $post_id, '_shaplatools_slider_caption_value', $caption );
		update_post_meta( $post_id, '_shaplatools_slider_transition_value', $transition );
		update_post_meta( $post_id, '_shaplatools_slider_slide_link_value', $slide_link );
		update_post_meta( $post_id, '_shaplatools_slider_slide_link_target_value', $link_target );
	}

	public function meta_box_callback($post){
	
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'shaplatools_slider_inner_custom_box', 'shaplatools_slider_meta_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$caption = get_post_meta( $post->ID, '_shaplatools_slider_caption_value', true );
		$transition = get_post_meta( $post->ID, '_shaplatools_slider_transition_value', true );
		$slide_link = get_post_meta( $post->ID, '_shaplatools_slider_slide_link_value', true );
		$link_target = get_post_meta( $post->ID, '_shaplatools_slider_slide_link_target_value', true );

        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="shaplatools_slider_caption">
                        <?php _e('Slide Caption','nivo-image-slider') ?>
                    </label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="shaplatools_slider_caption" name="shaplatools_slider_caption" value="<?php echo esc_attr( $caption ); ?>" style="width:100% !important">
                    <p><?php _e('Write slide caption.','nivo-image-slider'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="shaplatools_slider_slide_link">
                        <?php _e('Slide Link','nivo-image-slider') ?>
                    </label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="shaplatools_slider_slide_link" name="shaplatools_slider_slide_link" value="<?php echo esc_attr( $slide_link ); ?>" style="width:100% !important">
                    <p><?php _e('Write slide link URL. If you want to use current slide link, just leave it blank. If you do not want any link write (#) without bracket or write desired link..','nivo-image-slider'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="shaplatools_slider_slide_link_target">
                        <?php _e('Slide Link Target','nivo-image-slider') ?>
                    </label>
                </th>
                <td>
                    <select name="shaplatools_slider_slide_link_target">
                    	<option value="_self" <?php selected( $link_target, '_self' ); ?>>Self</option>
                    	<option value="_blank" <?php selected( $link_target, '_blank' ); ?>>Blank</option>
                    </select>
                    <p><?php _e('Select Self to open the slide in the same frame as it was clicked (this is default) or select Blank open the slide in a new window or tab.','nivo-image-slider'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="shaplatools_slider_transition">
                        <?php _e('Slide Transition','nivo-image-slider') ?>
                    </label>
                </th>
                <td>
                    <select name="shaplatools_slider_transition">
                    	<option value="" <?php selected( $transition, '' ); ?>>--- Select ---</option>
                    	<option value="sliceDown" <?php selected( $transition, 'sliceDown' ); ?>>sliceDown</option>
                        <option value="sliceDownLeft" <?php selected( $transition, 'sliceDownLeft' ); ?>>sliceDownLeft</option>
                        <option value="sliceUp" <?php selected( $transition, 'sliceUp' ); ?>>sliceUp</option>
                        <option value="sliceUpLeft" <?php selected( $transition, 'sliceUpLeft' ); ?>>sliceUpLeft</option>
                        <option value="sliceUpDown" <?php selected( $transition, 'sliceUpDown' ); ?>>sliceUpDown</option>
                        <option value="sliceUpDownLeft" <?php selected( $transition, 'sliceUpDownLeft' ); ?>>sliceUpDownLeft</option>
                        <option value="fold" <?php selected( $transition, 'fold' ); ?>>fold</option>
                        <option value="fade" <?php selected( $transition, 'fade' ); ?>>fade</option>
                        <option value="random" <?php selected( $transition, 'random' ); ?>>random</option>
                        <option value="slideInRight" <?php selected( $transition, 'slideInRight' ); ?>>slideInRight</option>
                        <option value="slideInLeft" <?php selected( $transition, 'slideInLeft' ); ?>>slideInLeft</option>
                        <option value="boxRandom" <?php selected( $transition, 'boxRandom' ); ?>>boxRandom</option>
                        <option value="boxRain" <?php selected( $transition, 'boxRain' ); ?>>boxRain</option>
                        <option value="boxRainReverse" <?php selected( $transition, 'boxRainReverse' ); ?>>boxRainReverse</option>
                        <option value="boxRainGrow" <?php selected( $transition, 'boxRainGrow' ); ?>>boxRainGrow</option>
                        <option value="boxRainGrowReverse" <?php selected( $transition, 'boxRainGrowReverse' ); ?>>boxRainGrowReverse</option>
                    </select>
                    <p><?php _e('Select transition for this slide.','nivo-image-slider'); ?></p>
                </td>
            </tr>
        </table>
        <?php
	}
	/**
	 * Custom columns head
	 * @param  array $defaults The default columns in the post admin
	 */
	public function slider_columns_head( $defaults ) {
		unset( $defaults['date'] );

		$defaults['slide_caption'] = __( 'Slide Caption', 'shaplatools' );
		$defaults['slide_link'] = __( 'Slide Link', 'shaplatools' );
		$defaults['link_target'] = __( 'Link Target', 'shaplatools' );
		$defaults['slide_transition'] = __( 'Slide Transition', 'shaplatools' );

		return $defaults;
	}
	/**
	 * Custom columns content
	 * @param  string 	$column_name The name of the current column
	 * @param  int 		$post_id     The id of the current post
	 */
	public function slider_columns_content( $column_name ) {

		global $post;

		if ( 'slide_caption' == $column_name ) {
			$slider_caption = get_post_meta( $post->ID, '_shaplatools_slider_caption_value', true );
			echo $slider_caption;
		}

		if ( 'slide_link' == $column_name ) {
			$slide_link = get_post_meta( $post->ID, '_shaplatools_slider_slide_link_value', true );
			echo $slide_link;
		}

		if ( 'link_target' == $column_name ) {
			$link_target = get_post_meta( $post->ID, '_shaplatools_slider_slide_link_target_value', true );
			echo $link_target;
		}

		if ( 'slide_transition' == $column_name ) {
			$slide_transition = get_post_meta( $post->ID, '_shaplatools_slider_transition_value', true );
			$slide_transition = ($slide_transition != '') ? $slide_transition : 'Random';
			echo $slide_transition;
		}
	}
}