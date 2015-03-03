<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.


/**
 * Shortcode
 *
 * @since 1.0
 */

function shaplatools_image_gallery_shortcode() {

	return shaplatools_image_gallery();
}
add_shortcode( 'shaplatools_image_gallery', 'shaplatools_image_gallery_shortcode' );


/**
 * Output Slider
 *
 * @since 1.0
 */
function shaplatools_image_gallery() {

	global $post;

	if( ! isset( $post->ID) )
		return;

	$attachment_ids = get_post_meta( $post->ID, '_shaplatools_image_gallery', true );
	$attachment_ids = explode( ',', $attachment_ids );

	if ( $attachment_ids ) { ?>

    <?php

		$has_gallery_images = get_post_meta( get_the_ID(), '_shaplatools_image_gallery', true );

		if ( !$has_gallery_images )
			return;

		// convert string into array
		$has_gallery_images = explode( ',', get_post_meta( get_the_ID(), '_shaplatools_image_gallery', true ) );

		// clean the array (remove empty values)
		$has_gallery_images = array_filter( $has_gallery_images );

		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
		$image_title = esc_attr( get_the_title( get_post_thumbnail_id( $post->ID ) ) );

		$id = get_the_ID();

		ob_start();
?>
	<div class="row">
    <div id="carousel_slider-<?php echo $id; ?>" class="owl-carousel">
    <?php
		foreach ( $attachment_ids as $attachment_id ) {

			$gallery_images_size = get_post_meta( $post->ID, '_shaplatools_available_image_size', true );

			$image = wp_get_attachment_image(
						$attachment_id, 
						$gallery_images_size,
						'',
						array(
							'alt' => trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ) 
						)
					);

			$image_caption = get_post( $attachment_id )->post_excerpt ? get_post( $attachment_id )->post_excerpt : '';

			$html = sprintf( '<div>%s</div>', $image );

			echo apply_filters( 'shaplatools_image_gallery_html', $html, $image_caption, $image, $attachment_id, $post->ID );
		}
?>
    </div></div>
    <script type="text/javascript">
		jQuery(document).ready(function($) {
  			$('#carousel_slider-<?php echo $id; ?>').owlCarousel({
				items : 1,
				dotsEach: true,
				loop : true,
				autoplay: true,
				autoplayHoverPause: true,
    			animateIn: 'fadeIn',
				animateOut: 'fadeOut',
			});
		});
    </script>

    <?php
		$gallery = ob_get_clean();

		return apply_filters( 'shaplatools_image_gallery', $gallery );
	?>

    <?php }
}

function shaplatools_post_thumbnail_gallery() {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	global $post;

	if ( is_singular() ) :
		
    	$image_gallery = get_post_meta( $post->ID, '_shaplatools_image_gallery', true );

		if( ! empty( $image_gallery ) ) :

			?>
				<div class="post-thumbnail">
					<?php
						if( function_exists( 'shaplatools_image_gallery' ) ) {
					    	echo shaplatools_image_gallery();
						}
					?>
				</div>
			<?php

		else : 
			?>
				<div class="post-thumbnail">
					<?php 
						if ( has_post_thumbnail() ) {
							the_post_thumbnail();
						} 
					?>
				</div><!-- .post-thumbnail -->
			<?php
		endif;
	?>


	<?php else : ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php
			the_post_thumbnail( 'post-thumbnail', array( 'alt' => get_the_title() ) );
		?>
	</a>

	<?php endif; // End is_singular()
}
