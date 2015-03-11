<?php

$labels = array(
	'name'               => __( 'Slides', 'shapla' ),
	'singular_name'      => __( 'Slide', 'shapla' ),
	'add_new'            => __( 'Add New', 'shapla' ),
	'add_new_item'       => __( 'Add New Slide', 'shapla' ),
	'edit_item'          => __( 'Edit Slide', 'shapla' ),
	'new_item'           => __( 'New Slide', 'shapla' ),
	'view_item'          => __( 'View Slide', 'shapla' ),
	'search_items'       => __( 'Search Slide', 'shapla' ),
	'not_found'          => __( 'No Slides found', 'shapla' ),
	'not_found_in_trash' => __( 'No Slides found in trash', 'shapla' ),
	'parent_item_colon'  => ''
);

$args = array(
	'labels'              => $labels,
	'public'              => false,
	'exclude_from_search' => true,
	'publicly_queryable'  => true,
	'rewrite'             => array('slug' => 'slides'),
	'show_ui'             => true,
	'query_var'           => true,
	'capability_type'     => 'post',
	'hierarchical'        => false,
	'menu_position'       => 33,
	'menu_icon'           => 'dashicons-images-alt2',
	'has_archive'         => false,
	'supports'            => array( 'title','editor', 'thumbnail' )
);

register_post_type( 'slides', $args );

register_taxonomy( 'slide-category', 'slides', array(
	'label'             => __( 'Slide Categories', 'shapla' ),
	'singular_label'    => __( 'Slide Category', 'shapla' ),
	'public'            => true,
	'hierarchical'      => true,
	'show_ui'           => true,
	'show_in_nav_menus' => true,
	'args'              => array( 'orderby' => 'term_order' ),
	'query_var'         => true,
	'rewrite'           => array( 'slug' => 'slide-category', 'hierarchical' => true)
) );

function shapla_slide_meta_box(){
	add_meta_box(
	    'shaplatools_slider_section',
	    __( 'Slide Settings','shaplatools' ),
	    'shapla_slide_meta_box_callback', 
	    'slides',
	    'advanced',
		'high'
	);
}
add_action( 'add_meta_boxes', 'shapla_slide_meta_box');

function shapla_slide_save($post_id){

	// Check if our nonce is set.
	if ( ! isset( $_POST['shaplatools_slider_meta_box_nonce'] ) )
		return $post_id;

	$nonce = $_POST['shaplatools_slider_meta_box_nonce'];

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'shaplatools_slider_inner_custom_box' ) )
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
	if ( ! empty( $_POST['shaplatools_slide'] ) ) {

		$slide['caption'] = sanitize_text_field( $_POST['shaplatools_slide']['caption'] );
		$slide['transition'] = sanitize_text_field( $_POST['shaplatools_slide']['transition'] );
		$slide['link_target'] = sanitize_text_field( $_POST['shaplatools_slide']['link_target'] );

		if ((trim($_POST['shaplatools_slide']['slide_link'])) != '') {

			$slide['slide_link'] = sanitize_text_field( $_POST['shaplatools_slide']['slide_link'] );

		} else {
			$slide['slide_link'] = esc_url(get_permalink());
		}

		update_post_meta( $post_id, '_shaplatools_slide', $slide );
	} else {
		delete_post_meta( $post_id, '_shaplatools_slide' );
	}
}
add_action( 'save_post', 'shapla_slide_save');

function shapla_slide_meta_box_callback(){
	
	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'shaplatools_slider_inner_custom_box', 'shaplatools_slider_meta_box_nonce' );

	// Use get_post_meta to retrieve an existing value from the database.
	$slide = get_post_meta( get_the_ID(), '_shaplatools_slide', true );

	$caption = ( empty( $slide['caption'] ) ) ? '' : $slide['caption'];
	$transition = ( empty( $slide['transition'] ) ) ? '' : $slide['transition'];
	$slide_link = ( empty( $slide['slide_link'] ) ) ? '' : $slide['slide_link'];
	$link_target = ( empty( $slide['link_target'] ) ) ? '' : $slide['link_target'];

    ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="caption">
                        <?php _e('Slide Caption','shapla') ?>
                    </label>
                </th>
                <td>
                    <input type="text" class="widefat" id="caption" name="shaplatools_slide[caption]" value="<?php echo esc_attr( $caption ); ?>">
                    <p><?php _e('Write slide caption.','shapla'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="slide_link">
                        <?php _e('Slide Link','shapla') ?>
                    </label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="slide_link" name="shaplatools_slide[slide_link]" value="<?php echo esc_attr( $slide_link ); ?>" style="width:100% !important">
                    <p><?php _e('Write slide link URL. If you want to use current slide link, just leave it blank. If you do not want any link write (#) without bracket or write desired link..','shapla'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="link_target">
                        <?php _e('Slide Link Target','shapla') ?>
                    </label>
                </th>
                <td>
                    <select name="shaplatools_slide[link_target]">
                    	<option value="_self" <?php selected( $link_target, '_self' ); ?>>Self</option>
                    	<option value="_blank" <?php selected( $link_target, '_blank' ); ?>>Blank</option>
                    </select>
                    <p><?php _e('Select Self to open the slide in the same frame as it was clicked (this is default) or select Blank open the slide in a new window or tab.','shapla'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="transition">
                        <?php _e('Slide Transition','shapla') ?>
                    </label>
                </th>
                <td>
                    <select name="shaplatools_slide[transition]">
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
                    <p><?php _e('Select transition for this slide.','shapla'); ?></p>
                </td>
            </tr>
        </table>
    <?php
}

function shapla_slide_columns_head($defaults){
	unset( $defaults['date'] );

	$defaults['slide_caption'] = __( 'Slide Caption', 'shaplatools' );
	$defaults['slide_link'] = __( 'Slide Link', 'shaplatools' );
	$defaults['link_target'] = __( 'Link Target', 'shaplatools' );
	$defaults['slide_transition'] = __( 'Slide Transition', 'shaplatools' );

	return $defaults;
}
add_filter( 'manage_edit-slides_columns', 'shapla_slide_columns_head');


function shapla_slide_columns_content($column_name){

	$slide = get_post_meta( get_the_ID(), '_shaplatools_slide', true );

	if ( 'slide_caption' == $column_name ) {
		if ( ! empty( $slide['caption'] ) )
		echo $slide['caption'];
	}

	if ( 'slide_link' == $column_name ) {
		if ( ! empty( $slide['slide_link'] ) )
		echo $slide['slide_link'];
	}

	if ( 'link_target' == $column_name ) {
		if ( ! empty( $slide['link_target'] ) )
		echo $slide['link_target'];
	}

	if ( 'slide_transition' == $column_name ) {

		if ( ! empty( $slide['transition'] ) ){
			echo $slide['transition'];
		} else {
			echo 'Random';
		}
	}
}
add_action( 'manage_slides_posts_custom_column', 'shapla_slide_columns_content');