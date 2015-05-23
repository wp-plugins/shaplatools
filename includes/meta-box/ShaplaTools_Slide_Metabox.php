<?php

if( !class_exists('ShaplaTools_Slide_Metabox') ):

class ShaplaTools_Slide_Metabox {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		add_filter( 'manage_edit-slide_columns', array ($this, 'columns_head') );
		add_action( 'manage_slide_posts_custom_column', array ($this, 'columns_content') );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box() {
		$meta_box = array(
		    'id' => 'shapla-metabox-slide',
		    'title' => __('Slide Settings', 'shapla'),
		    'description' => __('Here you can customize your slide details.', 'shapla'),
		    'page' => 'slide',
		    'context' => 'normal',
		    'priority' => 'high',
		    'fields' => array(
		        array(
		            'name' => __('Slide Caption:', 'shapla'),
		            'desc' => __('Enter slide caption. Leave blank if you don\'t want any caption.', 'shapla'),
		            'id' => '_shapla_slide_caption',
		            'type' => 'text',
		            'std' => ''
		        ),
		        array(
		            'name' => __('Slide Link:', 'shapla'),
		            'desc' => __('Enter slide link. Default value is current slide link.', 'shapla'),
		            'id' => '_shapla_slide_link',
		            'type' => 'text',
		            'std' => esc_url(get_permalink())
		        ),
		        array(
		            'name' => __('Slide Link Target:', 'shapla'),
		            'desc' => __('Select Self to open the slide in the same frame as it was clicked (this is default) or select Blank open the slide in a new window or tab.', 'shapla'),
		            'id' => '_shapla_slide_link_target',
		            'type' => 'select',
		            'std' => '_self',
		            'options' => array(
		            	'_self' 	=> __('Self', 'shapla'),
		            	'_blank' 	=> __('Blank', 'shapla')
		            )
		        ),
		        array(
		            'name' => __('Slide Transition:', 'shapla'),
		            'desc' => __('Select transition for this slide.', 'shapla'),
		            'id' => '_shapla_slide_transition',
		            'type' => 'select',
		            'std' => 'random',
		            'options' => array(
		            	'random' 			=> __('random', 'shapla'),
		            	'sliceDown' 		=> __('sliceDown', 'shapla'),
		            	'sliceDownLeft' 	=> __('sliceDownLeft', 'shapla'),
		            	'sliceUp' 			=> __('sliceUp', 'shapla'),
		            	'sliceUpLeft' 		=> __('sliceUpLeft', 'shapla'),
		            	'sliceUpDown' 		=> __('sliceUpDown', 'shapla'),
		            	'sliceUpDownLeft' 	=> __('sliceUpDownLeft', 'shapla'),
		            	'fold' 				=> __('fold', 'shapla'),
		            	'fade' 				=> __('fade', 'shapla'),
		            	'slideInRight' 		=> __('slideInRight', 'shapla'),
		            	'slideInLeft' 		=> __('slideInLeft', 'shapla'),
		            	'boxRandom' 		=> __('boxRandom', 'shapla'),
		            	'boxRain' 			=> __('boxRain', 'shapla'),
		            	'boxRainReverse' 	=> __('boxRainReverse', 'shapla'),
		            	'boxRainGrow' 		=> __('boxRainGrow', 'shapla'),
		            	'boxRainGrowReverse'	=> __('boxRainGrowReverse', 'shapla')
		            )
		        ),
		    )
		);
		$ShaplaTools_Metaboxs = new ShaplaTools_Metaboxs();
		$ShaplaTools_Metaboxs->shapla_add_meta_box($meta_box);
	}

	public function columns_head( $defaults ) {
		unset( $defaults['date'] );

		$defaults['slide_caption'] = __( 'Slide Caption', 'shaplatools' );
		$defaults['slide_link'] = __( 'Slide Link', 'shaplatools' );
		$defaults['link_target'] = __( 'Link Target', 'shaplatools' );
		$defaults['slide_transition'] = __( 'Slide Transition', 'shaplatools' );

		return $defaults;
	}

	public function columns_content( $column_name ) {

		$caption 		= get_post_meta( get_the_ID(), '_shapla_slide_caption', true );
		$link 			= get_post_meta( get_the_ID(), '_shapla_slide_link', true );
		$target 		= get_post_meta( get_the_ID(), '_shapla_slide_link_target', true );
		$transition 	= get_post_meta( get_the_ID(), '_shapla_slide_transition', true );

		if ( 'slide_caption' == $column_name ) {
			if ( ! empty( $caption ) )
			echo $caption;
		}

		if ( 'slide_link' == $column_name ) {
			if ( ! empty( $link ) )
			echo $link;
		}

		if ( 'link_target' == $column_name ) {
			if ( ! empty( $target ) )
			echo $target;
		}

		if ( 'slide_transition' == $column_name ) {

			if ( ! empty( $transition ) ){
				echo $transition;
			} else {
				echo 'Random';
			}
		}
	}
}

function run_shaplatools_slide_default_meta(){
	if (is_admin())
		ShaplaTools_Slide_Metabox::get_instance();
}
run_shaplatools_slide_default_meta();
endif;