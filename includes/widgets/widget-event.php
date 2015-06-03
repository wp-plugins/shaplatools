<?php
/**
 * Shapla_Event Widget
 */
class Shapla_Event extends WP_Widget {
	public function __construct() {
		$widget_ops = array( 'classname' => 'shapla_event', 'description' => 'Display event post type widget' );
		parent::__construct( 'shapla_event', 'Shapla Events', $widget_ops );
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$posts_per_page = (int) $instance['posts_per_page'];
		$orderby = strip_tags( $instance['orderby'] );

		echo $before_widget;

		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

		$args = array(
			'posts_per_page' => (int) $posts_per_page,
			'post_type' => 'event',
			'orderby' => $orderby,
			'no_found_rows' => true,
		);

		$query = new WP_Query( $args  );

		$events = '';
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();

				$event_image= wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
		
				$event = get_post_meta( get_the_ID(), '_shaplatools_event', true );

				$start_date = ( empty( $event['start_date'] ) ) ? '' : $event['start_date'];
				$end_date = ( empty( $event['end_date'] ) ) ? '' : $event['end_date'];
				$venue = ( empty( $event['venue'] ) ) ? '' : $event['venue'];

				$events .= '<div class="event">';

				$events .= '<div><a href="'.get_the_permalink().'"><img class="event_image" src="'.$event_image[0].'"></a><div>';
				$events .= '<h4><a href="'.get_the_permalink().'" class="sis_event_title">'.get_the_title().'</a><span class="event_venue">at '.$venue.'</span></h4>';
				$events .= '<div>' . get_the_excerpt() . '</div>';
				$events .= '<time class="sis_event_date">'.date_i18n( get_option( 'date_format' ), $start_date ).' &ndash; '.date_i18n( get_option( 'date_format' ), $end_date ).'</time>';
				$events .= '<div><a href="'.get_post_type_archive_link( 'event' ).'">'. __( 'View All Events', 'upcoming-events' ).'</a></div>';

				$events .= '</div>';

			endwhile;
			wp_reset_postdata();
		}

		echo $events;

		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'posts_per_page' => '1', 'orderby' => 'none', 'event_id' => null ) );
		$title = strip_tags( $instance['title'] );
		$posts_per_page = (int) $instance['posts_per_page'];
		$orderby = strip_tags( $instance['orderby'] );
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>">Number of events: </label>
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
		<?php
	}
}
// Register the Widget
function shaplatools_register_widget_event() {
	register_widget( 'Shapla_Event' );
}
