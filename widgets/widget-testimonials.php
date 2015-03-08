<?php
/**
 * Testimonials Widget
 */
class Shapla_Testimonial_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array( 'classname' => 'shapla_testimonial', 'description' => 'Display testimonial post type' );
		parent::__construct( 'shapla_testimonial', 'Shapla Testimonials', $widget_ops );
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$posts_per_page = (int) $instance['posts_per_page'];
		$orderby = strip_tags( $instance['orderby'] );
		$testimonial_id = ( null == $instance['testimonial_id'] ) ? '' : strip_tags( $instance['testimonial_id'] );

		echo $before_widget;

		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

		$args = array(
			'posts_per_page' => (int) $posts_per_page,
			'post_type' => 'testimonial',
			'orderby' => $orderby,
			'no_found_rows' => true,
		);
		if ( $testimonial_id )
			$args['post__in'] = array( $testimonial_id );

		$query = new WP_Query( $args  );

		$testimonials = '';
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();
				$post_id = get_the_ID();
				$testimonial_data = get_post_meta( $post_id, '_shaplatools_testimonial', true );
				$client_name = ( empty( $testimonial_data['client_name'] ) ) ? '' : $testimonial_data['client_name'];
				$client_source = ( empty( $testimonial_data['client_source'] ) ) ? '' : ' - ' . $testimonial_data['client_source'];
				$client_link = ( empty( $testimonial_data['client_link'] ) ) ? '' : $testimonial_data['client_link'];
				$cite = ( $client_link ) ? '<a href="' . esc_url( $client_link ) . '" target="_blank">' . $client_name . $client_source . '</a>' : $client_name . $client_source;

				$testimonials .= '<aside class="testimonial">';
				$testimonials .= '<span class="quote">&ldquo;</span>';
				$testimonials .= '<div class="entry-content">';
				$testimonials .= '<p class="testimonial-text">' . get_the_content() . '<span></span></p>';
				$testimonials .= '<p class="testimonial-client-name"><cite>' . $cite . '</cite>';
				$testimonials .= '</div>';
				$testimonials .= '</aside>';

			endwhile;
			wp_reset_postdata();
		}

		echo $testimonials;

		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		$instance['testimonial_id'] = ( null == $new_instance['testimonial_id'] ) ? '' : strip_tags( $new_instance['testimonial_id'] );

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'posts_per_page' => '1', 'orderby' => 'none', 'testimonial_id' => null ) );
		$title = strip_tags( $instance['title'] );
		$posts_per_page = (int) $instance['posts_per_page'];
		$orderby = strip_tags( $instance['orderby'] );
		$testimonial_id = ( null == $instance['testimonial_id'] ) ? '' : strip_tags( $instance['testimonial_id'] );
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>">Number of Testimonials: </label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text" value="<?php echo esc_attr( $posts_per_page ); ?>" />
		</p>

		<p><label for="<?php echo $this->get_field_id( 'orderby' ); ?>">Order By</label>
		<select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
			<option value="none" <?php selected( $orderby, 'none' ); ?>>None</option>
			<option value="ID" <?php selected( $orderby, 'ID' ); ?>>ID</option>
			<option value="date" <?php selected( $orderby, 'date' ); ?>>Date</option>
			<option value="modified" <?php selected( $orderby, 'modified' ); ?>>Modified</option>
			<option value="rand" <?php selected( $orderby, 'rand' ); ?>>Random</option>
		</select></p>

		<p><label for="<?php echo $this->get_field_id( 'testimonial_id' ); ?>">Testimonial ID</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'testimonial_id' ); ?>" name="<?php echo $this->get_field_name( 'testimonial_id' ); ?>" type="text" value="<?php echo $testimonial_id; ?>" /></p>
		<?php
	}
}

add_action( 'widgets_init', 'register_testimonials_widget' );
/**
 * Register widget
 *
 * This functions is attached to the 'widgets_init' action hook.
 */
function register_testimonials_widget() {
	register_widget( 'Shapla_Testimonial_Widget' );
}
