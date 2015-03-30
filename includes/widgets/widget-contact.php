<?php
class ShaplaContactFormAJAX extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'shapla_ajax_contact',
            __('Shapla Contact Form', 'shaplatools' ),
            array( 'description' => __( 'A simple AJAX contact form.', 'shaplatools' ), )
        );

        add_action( 'wp_ajax_the_ajax_hook', array( &$this, 'shapla_contact_form_widget_function' ) );
        add_action( 'wp_ajax_nopriv_the_ajax_hook', array( &$this, 'shapla_contact_form_widget_function' ) );
    }// end constructor

	function widget( $args, $instance ) {
	    /* Our variables from the widget settings. */
	    $title = apply_filters('widget_title', $instance['title'] );
	 
	    /* Display the widget title if one was input (before and after defined by themes). */
	    echo $args['before_widget'];
	 
	    if ( ! empty( $title ) ) {
	        echo $args['before_title'] . $title . $args['after_title'];
	    }

	    // ADD EG A FORM TO THE PAGE
		?>
		<style type="text/css">
			#theForm {margin: 10px 0;}#theForm input,#theForm textarea {margin-bottom: 10px;width: 100%;}#response_area .error{display: block;}
		</style>
		<div id="response_area"></div>
		<form id="theForm">
			<input type="text" name="fullname" id="fullname" placeholder="<?php _e('NAME','shapla'); ?>">
		 	<input type="email" name="email" id="email" placeholder="<?php _e('EMAIL','shapla'); ?>">
		 	<textarea name="message" id="message" cols="30" rows="5" placeholder="<?php _e('MESSAGE','shapla'); ?>"></textarea>

		 	<input name="action" type="hidden" value="the_ajax_hook" />
		 	<!-- this puts the action the_ajax_hook into the serialized form -->
		 	<input id="submit_button" value = "<?php _e('SEND MESSAGE','shapla'); ?>" type="button" onClick="submit_me();" />
		 	<?php wp_nonce_field( 'shapla_contact_widget_action', 'shapla_contact_widget_nonce' ); ?>
		</form>
		<script type="text/javascript">
			function submit_me(){
				jQuery.post(
					ajaxurl,
					jQuery("#theForm").serialize(),
					function(response_from_the_action_function){
						jQuery("#response_area").html(response_from_the_action_function);
					}
				);
			}
		</script>
		<?php

	    echo $args['after_widget'];
	}

	// THE FUNCTION
	function shapla_contact_form_widget_function( $instance ){
	/* this area is very simple but being serverside it affords the possibility of retreiving data from the server and passing it back to the javascript function */
		if ( isset( $_POST['shapla_contact_widget_nonce'] ) || wp_verify_nonce( $_POST['shapla_contact_widget_nonce'], 'shapla_contact_widget_action' )) {

			$fullname		= sanitize_text_field($_POST['fullname']);
			$email			= sanitize_email($_POST['email']);
			$message 		= esc_textarea($_POST['message']);

			$error = '';
			// Validate fullname with PHP
			if ( strlen($fullname) < 3 ) {
				$error .= '<span class="error">'.__( 'Your name should be at least 3 characters.', 'shapla' ).'</span>';
		        $hasError = true;
			}

			// Validate email address with PHP
			if(!is_email($email)){
				$error .= '<span class="error">'.__( 'You entered an invalid email address.', 'shapla' ).'</span>';
		        $hasError = true;
			}

			// Validate message with PHP
			if ( strlen($message) < 15 ) {
				$error .= '<span class="error">'.__( 'Your message should be at least 15 characters.', 'shapla' ).'</span>';
		        $hasError = true;
			}

			// If all validation are true than send mail
			if ( !isset($hasError) ) {

				$to = ! empty( $instance['email'] ) ? $instance['email'] : get_option('admin_email');

		        $subject = 'Someone sent you a message from '.get_bloginfo('name');

		        $body = "Name: $fullname \n\nEmail: $email \n\nMessage: $message \n\n";
		        $body .= "--\n";
		        $body .= "This mail is sent via contact form ".get_bloginfo('name')."\n";
		        $body .= home_url();

				$headers = 'From: '.$fullname.' <'.$email.'>' . "\r\n" . 'Reply-To: ' . $email;

				wp_mail($to, $subject, $body, $headers);
		        $emailSent = true;
			}

			// Show message to user
			if ( isset($emailSent) && $emailSent == true ) {
				echo '<div class="shapla-alert shapla-alert--green">'.__( 'Thanks, your email was sent successfully.', 'shapla' ).'</div>';
			} else {
				$result = '<div class="shapla-alert shapla-alert--red">';
				$result .= '<span class="error">'.__('Please check the error bellow.', 'shapla' ).'</span>';
				$result .= $error;
				$result .= '</div>';

				echo $result;
			}

		   	die();

		}
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
	    $instance = $old_instance;

	    $instance['title'] 		= strip_tags( $new_instance['title'] );
	    $instance['email'] 		= sanitize_email( $new_instance['email'] );
	 
	    return $instance;
	}

	function form( $instance ) {
		// Output admin widget options form
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Send a Direct Message', 'shaplatools' );
		$email = ! empty( $instance['email'] ) ? $instance['email'] : get_option('admin_email');
		?>
	    <p>
	    	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'shaplatools') ?></label>
	    	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php if(isset($title)){echo esc_attr( $title );} ?>" />
	    </p>
	    <p>
	    	<label for="<?php echo $this->get_field_id( 'email' ); ?>"><?php _e('Contact Form Email Address:', 'shaplatools') ?></label>
	    	<input type="email" class="widefat" id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" value="<?php if(isset($email)){echo esc_attr( $email );} ?>" />
	    	<small><?php _e('Enter the email address where you would like to receive emails from the contact form.', 'shaplatools'); ?></small>
	    </p>
		<?php
	}
}

// Register the Widget
add_action( 'widgets_init', create_function( '', 'register_widget("ShaplaContactFormAJAX");' ) );