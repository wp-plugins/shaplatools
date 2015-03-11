<?php

add_action( 'widgets_init', create_function( '', 'return register_widget( "Shapla_Tweet_Widget" );' ) );

class Shapla_Tweet_Widget extends WP_Widget{

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'shapla-latest-tweets', // Base ID
			__( 'Shapla Latest Tweets', 'shapla' ), // Name
			array( 'description' => __( 'Displays your latest tweets from Twitter.', 'shapla' ), ) // Args
		);
	}

    /**
     * To make the tweet time more user-friendly
     */
	public function tweet_time( $time ) {
	    // Get current timestamp.
	    $now = strtotime( 'now' );
	 
	    // Get timestamp when tweet created.
	    $created = strtotime( $time );
	 
	    // Get difference.
	    $difference = $now - $created;
	 
	    // Calculate different time values.
	    $minute = 60;
	    $hour = $minute * 60;
	    $day = $hour * 24;
	    $week = $day * 7;
	 
	    if ( is_numeric( $difference ) && $difference > 0 ) {
	 
	        // If less than 3 seconds.
	        if ( $difference < 3 ) {
	            return __( 'right now', 'sistweets' );
	        }
	 
	        // If less than minute.
	        if ( $difference < $minute ) {
	            return floor( $difference ) . ' ' . __( 'seconds ago', 'sistweets' );;
	        }
	 
	        // If less than 2 minutes.
	        if ( $difference < $minute * 2 ) {
	            return __( 'about 1 minute ago', 'sistweets' );
	        }
	 
	        // If less than hour.
	        if ( $difference < $hour ) {
	            return floor( $difference / $minute ) . ' ' . __( 'minutes ago', 'sistweets' );
	        }
	 
	        // If less than 2 hours.
	        if ( $difference < $hour * 2 ) {
	            return __( 'about 1 hour ago', 'sistweets' );
	        }
	 
	        // If less than day.
	        if ( $difference < $day ) {
	            return floor( $difference / $hour ) . ' ' . __( 'hours ago', 'sistweets' );
	        }
	 
	        // If more than day, but less than 2 days.
	        if ( $difference > $day && $difference < $day * 2 ) {
	            return __( 'yesterday', 'sistweets' );;
	        }
	 
	        // If less than year.
	        if ( $difference < $day * 365 ) {
	            return floor( $difference / $day ) . ' ' . __( 'days ago', 'sistweets' );
	        }
	 
	        // Else return more than a year.
	        return __( 'over a year ago', 'sistweets' );
	    }
	}

	function widget( $args, $instance ) {
		extract($args);

	    $title                     = apply_filters( 'widget_title', $instance['title'] );
	    $twitter_username          = $instance['twitter_username'];
	    $limit                     = $instance['update_count'];
	    $oauth_access_token        = $instance['oauth_access_token'];
	    $oauth_access_token_secret = $instance['oauth_access_token_secret'];
	    $consumer_key              = $instance['consumer_key'];
	    $consumer_secret           = $instance['consumer_secret'];

		echo $before_widget;

	?>

	<div class="shapla-twitter-widget">
		<?php

		if ( $title ) { echo $before_title . $title . $after_title; }

		if( empty($consumer_key) || empty($consumer_secret) || empty($oauth_access_token) || empty($oauth_access_token_secret) ) {
			echo '<strong>' . __( 'Please fill all widget settings.', 'shapla' ) . '</strong></div>' . $after_widget;
			return;
		}

		$transient = 'shapla_twitter_widget_' . $twitter_username;

		if ( false === get_transient( $transient ) ) {

			$connection = new TwitterOAuth( $consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret );

			$url = add_query_arg( array( 'screen_name' => $twitter_username, 'count' => $limit ), 'https://api.twitter.com/1.1/statuses/user_timeline.json' );

			$tweets = $connection->get( $url );

			if( !$tweets ) {
				_e( "Couldn't retrieve tweets! Wrong username!", "shapla" );
				echo '</div>' . $after_widget;
				return;
			}


			if( !empty( $tweets->errors ) ) {
				if( $tweets->errors[0]->message == 'Invalid or expired token' ) {
					echo '<strong>' . $tweets->errors[0]->message . '!</strong>' . __( 'You will need to regenerate it <a href="//dev.twitter.com/apps">here</a>.', 'shapla' ) . $after_widget;
				}else{
					echo '<strong>' . $tweets->errors[0]->message . '</strong>' . $after_widget;
				}
				return;
			}

			for( $i = 0; $i <= count($tweets); $i++) {
				if( !empty($tweets[$i]) ) {
					$tweets_array[$i]['created_at'] = $tweets[$i]->created_at;
					$tweets_array[$i]['text']       = $tweets[$i]->text;
					$tweets_array[$i]['status_id']  = $tweets[$i]->id_str;
				}
			}

			set_transient( $transient, serialize($tweets_array), $timeout );
		}

		$widget_tweets = maybe_unserialize( get_transient( $transient ) );

		if( !empty( $widget_tweets ) ) {
			$output = '<ul>';
			$count = 1;
			$protocol = is_ssl() ? 'https:' : 'http:';
	 
	        // Add links to URL and username mention in tweets.
	        $patterns = array( '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '/@([A-Za-z0-9_]{1,15})/' );
	        $replace = array( '<a href="$1">$1</a>', '<a href="http://twitter.com/$1">@$1</a>' );

			foreach( $widget_tweets as $tweet ) {
				$result = preg_replace( $patterns, $replace, $tweet['text'] );
				
				$output .= '<li><p>';
				$output .= $result;
				$output .= '</p>';
				$output .= '<p><time datetime="'. $tweet['created_at'] .'" class="time"><a href="'. $protocol .'//twitter.com/'. $twitter_username .'/statuses/'. $tweet['status_id'] .'" target="_blank">'. $this->tweet_time( $tweet['created_at'] ) .'</a></time></p>';
				$output .= '</li>';

				if( $count == $limit ) { break; }
				$count++;
			}

			$output .= '</ul>';

			echo $output;
		}

		?>

	</div>

	<?php

	echo $after_widget;

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	    $instance['twitter_username'] = ( ! empty( $new_instance['twitter_username'] ) ) ? strip_tags( $new_instance['twitter_username'] ) : '';
	    $instance['update_count'] = ( ! empty( $new_instance['update_count'] ) ) ? strip_tags( $new_instance['update_count'] ) : '';
	    $instance['oauth_access_token'] = ( ! empty( $new_instance['oauth_access_token'] ) ) ? strip_tags( $new_instance['oauth_access_token'] ) : '';
	    $instance['oauth_access_token_secret'] = ( ! empty( $new_instance['oauth_access_token_secret'] ) ) ? strip_tags( $new_instance['oauth_access_token_secret'] ) : '';
	    $instance['consumer_key'] = ( ! empty( $new_instance['consumer_key'] ) ) ? strip_tags( $new_instance['consumer_key'] ) : '';
	    $instance['consumer_secret'] = ( ! empty( $new_instance['consumer_secret'] ) ) ? strip_tags( $new_instance['consumer_secret'] ) : '';

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
     	$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Latest Tweets', 'shapla' );
     	$twitter_username = ! empty( $instance['twitter_username'] ) ? $instance['twitter_username'] : '';
     	$update_count = ! empty( $instance['update_count'] ) ? $instance['update_count'] : '';
     	$oauth_access_token = ! empty( $instance['oauth_access_token'] ) ? $instance['oauth_access_token'] : '';
     	$oauth_access_token_secret = ! empty( $instance['oauth_access_token_secret'] ) ? $instance['oauth_access_token_secret'] : '';
     	$consumer_key = ! empty( $instance['consumer_key'] ) ? $instance['consumer_key'] : '';
     	$consumer_secret = ! empty( $instance['consumer_secret'] ) ? $instance['consumer_secret'] : '';
		?>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'title' ); ?>">
	            <?php echo __( 'Title', 'shapla' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php if(isset($title)){echo esc_attr( $title );} ?>" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'twitter_username' ); ?>">
	            <?php echo __( 'Twitter Username (without @)', 'shapla' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'twitter_username' ); ?>" name="<?php echo $this->get_field_name( 'twitter_username' ); ?>" type="text" value="<?php if(isset($twitter_username)){echo esc_attr( $twitter_username );} ?>" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'update_count' ); ?>">
	            <?php echo __( 'Number of Tweets to Display', 'shapla' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'update_count' ); ?>" name="<?php echo $this->get_field_name( 'update_count' ); ?>" type="number" value="<?php if(isset($update_count)){echo esc_attr( $update_count );} ?>" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'consumer_key' ); ?>">
	            <?php echo __( 'Consumer Key', 'shapla' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'consumer_key' ); ?>" name="<?php echo $this->get_field_name( 'consumer_key' ); ?>" type="text" value="<?php if(isset($consumer_key)){echo esc_attr( $consumer_key );} ?>" />
	        <small>Don't know your Consumer Key, Consumer Secret, Access Token and Access Token Secret? <a target="_blank" href="http://sayful1.wordpress.com/2014/06/14/how-to-generate-twitter-api-key-api-secret-access-token-access-token-secret/">Click here to get help.</a></small>
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'consumer_secret' ); ?>">
	            <?php echo __( 'Consumer Secret', 'shapla' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'consumer_secret' ); ?>" name="<?php echo $this->get_field_name( 'consumer_secret' ); ?>" type="text" value="<?php if(isset($consumer_secret)){echo esc_attr( $consumer_secret );} ?>" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'oauth_access_token' ); ?>">
	            <?php echo __( 'Access Token', 'shapla' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'oauth_access_token' ); ?>" name="<?php echo $this->get_field_name( 'oauth_access_token' ); ?>" type="text" value="<?php if(isset($oauth_access_token)){echo esc_attr( $oauth_access_token );} ?>" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'oauth_access_token_secret' ); ?>">
	            <?php echo __( 'Access Token Secret', 'shapla' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'oauth_access_token_secret' ); ?>" name="<?php echo $this->get_field_name( 'oauth_access_token_secret' ); ?>" type="text" value="<?php if(isset($oauth_access_token_secret)){echo esc_attr( $oauth_access_token_secret );} ?>" />
	    </p>
		<?php 
	}
}

include_once('twitteroauth.php');
