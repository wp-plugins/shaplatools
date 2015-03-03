<?php

/** 
 * The Class.
 */
class ShaplaTools_Gallery {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {

    	//do_action( 'save_post', $post_id );
		add_action( 'add_meta_boxes', array( $this, 'gallery_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	public function gallery_meta_box() {

	    $screens = array('post', 'page', 'portfolio');

	    foreach ( $screens as $screen) {

	        add_meta_box(
	            'shaplatools_gallery',
	            __( 'Image Gallery (will be used as slider in single post or page instate of Featured Image)', 'shaplatools' ),
	            array( $this, 'shaplatools_image_gallery_callback'),
	            $screen,
	            'normal',
	            'low'
	        );
	    }

	}

	public function shaplatools_image_gallery_callback(){

	    global $post;
	?>
	<table class="form-table">
	    <tr valign="top">
	        <div id="shaplatools_gallery_container">

	            <ul class="gallery_images">
	                <?php

	                $image_gallery = get_post_meta( $post->ID, '_shaplatools_image_gallery', true );
	                $attachments = array_filter( explode( ',', $image_gallery ) );

	                if ( $attachments )
	                    foreach ( $attachments as $attachment_id ) {
	                        echo '<li class="image attachment details" data-attachment_id="' . $attachment_id . '">
	                                <div class="attachment-preview">
	                                    <div class="thumbnail">
	                                        ' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
	                                    </div>
	                                    <a href="#" class="delete check" title="' . __( 'Remove image', 'shaplatools' ) . '">
	                                        <div class="media-modal-icon"></div>
	                                    </a>
	                                       
	                                </div>
	                            </li>';
	                    }
	                ?>
	            </ul>

	            <input type="hidden" id="image_gallery" name="image_gallery" value="<?php echo esc_attr( $image_gallery ); ?>" />
	            <?php wp_nonce_field( 'shaplatools_image_gallery', 'shaplatools_image_gallery' ); ?>

	        </div><!-- #shaplatools_gallery_container -->
	    </tr>
	    <tr valign="top">
	        <p class="add_gallery_images hide-if-no-js">
	            <a href="#">
	                <?php _e( 'Add gallery images', 'shaplatools' ); ?>
	            </a>
	        </p>
	    </tr>
	    <tr valign="top">
	        <th scope="row">
	            <label for="shaplatools_available_image_size">
	                <?php _e('Select Gallery Images Size', 'shaplatools'); ?>
	            </label>
	        </th>
	        <td>
	            <select id='shaplatools_available_image_size' name='shaplatools_available_image_size'>
	                <?php
	                    $shaplatools_img_size = get_intermediate_image_sizes();
	                    array_push($shaplatools_img_size, 'full');

	                    foreach($shaplatools_img_size as $code => $label) :

	                        if( $label == get_post_meta( $post->ID, '_shaplatools_available_image_size', true ) ){ 
	                            $selected = "selected"; 
	                        } else { 
	                            $selected = ''; 
	                        }

	                        echo "<option {$selected} value='{$label}'>{$label}</option>";
	                    endforeach; 
	                ?>
	            </select>
	            <p><?php _e('You can change the default size for thumbnail, medium and large from <a href="'.get_admin_url().'options-media.php">Settings >> Media</a>.','nivo-image-slider'); ?></p>
	        </td>
	    </tr>
	</table>

	<?php
	    /**
	     * Props to WooCommerce for the following JS code
	     */
	?>
	    <script type="text/javascript">
	        jQuery(document).ready(function($){

	            // Uploading files
	            var shaplatools_image_gallery_frame;
	            var $shaplatools_image_gallery_ids = $('#shaplatools_gallery_container #image_gallery');
	            var $shaplatools_gallery_images = $('#shaplatools_gallery_container ul.gallery_images');

	            jQuery('.add_gallery_images').on( 'click', 'a', function( event ) {

	                var $el = $(this);
	                var attachment_ids = $shaplatools_image_gallery_ids.val();

	                event.preventDefault();

	                // If the media frame already exists, reopen it.
	                if ( shaplatools_image_gallery_frame ) {
	                    shaplatools_image_gallery_frame.open();
	                    return;
	                }

	                // Create the media frame.
	                shaplatools_image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
	                    // Set the title of the modal.
	                    title: '<?php _e( 'Add Images to Gallery', 'shaplatools' ); ?>',
	                    button: {
	                        text: '<?php _e( 'Add to gallery', 'shaplatools' ); ?>',
	                    },
	                    multiple: true
	                });

	                // When an image is selected, run a callback.
	                shaplatools_image_gallery_frame.on( 'select', function() {

	                    var selection = shaplatools_image_gallery_frame.state().get('selection');

	                    selection.map( function( attachment ) {

	                        attachment = attachment.toJSON();

	                        if ( attachment.id ) {
	                            attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;

	                             $shaplatools_gallery_images.append('\
	                                <li class="image attachment details" data-attachment_id="' + attachment.id + '">\
	                                    <div class="attachment-preview">\
	                                        <div class="thumbnail">\
	                                            <img src="' + attachment.url + '" />\
	                                        </div>\
	                                       <a href="#" class="delete check" title="<?php _e( 'Remove image', 'shaplatools' ); ?>"><div class="media-modal-icon"></div></a>\
	                                    </div>\
	                                </li>');

	                        }

	                    } );

	                    $shaplatools_image_gallery_ids.val( attachment_ids );
	                });

	                // Finally, open the modal.
	                shaplatools_image_gallery_frame.open();
	            });

	            // Image ordering
	            $shaplatools_gallery_images.sortable({
	                items: 'li.image',
	                cursor: 'move',
	                scrollSensitivity:40,
	                forcePlaceholderSize: true,
	                forceHelperSize: false,
	                helper: 'clone',
	                opacity: 0.65,
	                placeholder: 'eig-metabox-sortable-placeholder',
	                start:function(event,ui){
	                    ui.item.css('background-color','#f6f6f6');
	                },
	                stop:function(event,ui){
	                    ui.item.removeAttr('style');
	                },
	                update: function(event, ui) {
	                    var attachment_ids = '';

	                    $('#shaplatools_gallery_container ul li.image').css('cursor','default').each(function() {
	                        var attachment_id = jQuery(this).attr( 'data-attachment_id' );
	                        attachment_ids = attachment_ids + attachment_id + ',';
	                    });

	                    $shaplatools_image_gallery_ids.val( attachment_ids );
	                }
	            });

	            // Remove images
	            $('#shaplatools_gallery_container').on( 'click', 'a.delete', function() {

	                $(this).closest('li.image').remove();

	                var attachment_ids = '';

	                $('#shaplatools_gallery_container ul li.image').css('cursor','default').each(function() {
	                    var attachment_id = jQuery(this).attr( 'data-attachment_id' );
	                    attachment_ids = attachment_ids + attachment_id + ',';
	                });

	                $shaplatools_image_gallery_ids.val( attachment_ids );

	                return false;
	            } );

	        });
	    </script>
	<?php
	}

	public function save_post( $post_id ){

	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	        return;

	    if ( ! isset( $_POST[ 'shaplatools_image_gallery' ] ) || ! wp_verify_nonce( $_POST[ 'shaplatools_image_gallery' ], 'shaplatools_image_gallery' ) )
	        return;

	    if ( isset( $_POST[ 'image_gallery' ] ) && !empty( $_POST[ 'image_gallery' ] ) ) {

	        $attachment_ids = sanitize_text_field( $_POST['image_gallery'] );

	        // turn comma separated values into array
	        $attachment_ids = explode( ',', $attachment_ids );

	        // clean the array
	        $attachment_ids = array_filter( $attachment_ids  );

	        // return back to comma separated list with no trailing comma. This is common when deleting the images
	        $attachment_ids =  implode( ',', $attachment_ids );

	        update_post_meta( $post_id, '_shaplatools_image_gallery', $attachment_ids );
	    } else {
	        delete_post_meta( $post_id, '_shaplatools_image_gallery' );
	    }

	    // Available Images Size
	    if ( isset( $_POST[ 'shaplatools_available_image_size' ] ) ){
	        update_post_meta( $post_id, '_shaplatools_available_image_size', $_POST[ 'shaplatools_available_image_size' ] );
	    }
	}

} // end class ShaplaTools_Gallery()

new ShaplaTools_Gallery();
