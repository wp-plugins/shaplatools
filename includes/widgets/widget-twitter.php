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
     * Making request to Twitter API
     */
	public function twitter_timeline( $username, $limit, $oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret ) {
	    require_once 'TwitterAPIExchange.php';
	 
	    /** Set access tokens here - see: https://dev.twitter.com/apps/ */
	    $settings = array(
	        'oauth_access_token'        => $oauth_access_token,
	        'oauth_access_token_secret' => $oauth_access_token_secret,
	        'consumer_key'              => $consumer_key,
	        'consumer_secret'           => $consumer_secret
	    );
	 
	    $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	    $getfield = '?screen_name=' . $username . '&count=' . $limit;
	    $request_method = 'GET';
	     
	    $twitter_instance = new TwitterAPIExchange( $settings );
	     
	    $query = $twitter_instance
	        ->setGetfield( $getfield )
	        ->buildOauth( $url, $request_method )
	        ->performRequest();
	     
	    $timeline = json_decode($query);
	 
	    return $timeline;
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

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
	    $title                     = apply_filters( 'widget_title', $instance['title'] );
	    $username                  = $instance['twitter_username'];
	    $limit                     = $instance['update_count'];
	    $oauth_access_token        = $instance['oauth_access_token'];
	    $oauth_access_token_secret = $instance['oauth_access_token_secret'];
	    $consumer_key              = $instance['consumer_key'];
	    $consumer_secret           = $instance['consumer_secret'];
	 
	    echo $args['before_widget'];
	 
	    if ( ! empty( $title ) ) {
	        echo $args['before_title'] . $title . $args['after_title'];
	    }
	 
	    // Get the tweets.
	    $timelines = $this->twitter_timeline( $username, $limit, $oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret );
	 
	    if ( $timelines ) {
	 
	        // Add links to URL and username mention in tweets.
	        $patterns = array( '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '/@([A-Za-z0-9_]{1,15})/' );
	        $replace = array( '<a href="$1">$1</a>', '<a href="http://twitter.com/$1">@$1</a>' );
	 		
	        ob_start();
	        ?>
			<style type="text/css">
				.widget_shapla-latest-tweets ul {margin: 0;padding: 0;list-style: none;}
				.widget_shapla-latest-tweets ul li{border-top: 1px solid rgba(51, 51, 51, 0.2);padding: 12px 0;}
				.widget_shapla-latest-tweets ul li:first-child{border-top: 0 none;padding-top: 0;}
				.widget_shapla-latest-tweets ul li a{display: block;}
			</style>
	        <?php
	        echo ob_get_clean();
	        
	 		echo '<ul>';
	        foreach ( $timelines as $timeline ) {
	            $result = preg_replace( $patterns, $replace, $timeline->text );
	 
	            echo '<li>';
	                echo $result;
	                echo '<span>'.$this->tweet_time( $timeline->created_at ).'</span>';
	            echo '</li>';
	        }
	        echo '</ul>';
	 
	    } else {
	        _e( 'Error fetching feeds. Please verify the Twitter settings in the widget.', 'twitter_tweets_widget' );
	    }
	 
	    echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
     	$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Latest Tweets', 'sistweets' );
     	$twitter_username = ! empty( $instance['twitter_username'] ) ? $instance['twitter_username'] : '';
     	$update_count = ! empty( $instance['update_count'] ) ? $instance['update_count'] : '';
     	$oauth_access_token = ! empty( $instance['oauth_access_token'] ) ? $instance['oauth_access_token'] : '';
     	$oauth_access_token_secret = ! empty( $instance['oauth_access_token_secret'] ) ? $instance['oauth_access_token_secret'] : '';
     	$consumer_key = ! empty( $instance['consumer_key'] ) ? $instance['consumer_key'] : '';
     	$consumer_secret = ! empty( $instance['consumer_secret'] ) ? $instance['consumer_secret'] : '';
		?>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'title' ); ?>">
	            <?php echo __( 'Title', 'sistweets' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php if(isset($title)){echo esc_attr( $title );} ?>" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'twitter_username' ); ?>">
	            <?php echo __( 'Twitter Username (without @)', 'sistweets' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'twitter_username' ); ?>" name="<?php echo $this->get_field_name( 'twitter_username' ); ?>" type="text" value="<?php if(isset($twitter_username)){echo esc_attr( $twitter_username );} ?>" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'update_count' ); ?>">
	            <?php echo __( 'Number of Tweets to Display', 'sistweets' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'update_count' ); ?>" name="<?php echo $this->get_field_name( 'update_count' ); ?>" type="number" value="<?php if(isset($update_count)){echo esc_attr( $update_count );} ?>" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'consumer_key' ); ?>">
	            <?php echo __( 'Consumer Key', 'sistweets' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'consumer_key' ); ?>" name="<?php echo $this->get_field_name( 'consumer_key' ); ?>" type="text" value="<?php if(isset($consumer_key)){echo esc_attr( $consumer_key );} ?>" />
	        <small>Don't know your Consumer Key, Consumer Secret, Access Token and Access Token Secret? <a target="_blank" href="http://sayful1.wordpress.com/2014/06/14/how-to-generate-twitter-api-key-api-secret-access-token-access-token-secret/">Click here to get help.</a></small>
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'consumer_secret' ); ?>">
	            <?php echo __( 'Consumer Secret', 'sistweets' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'consumer_secret' ); ?>" name="<?php echo $this->get_field_name( 'consumer_secret' ); ?>" type="text" value="<?php if(isset($consumer_secret)){echo esc_attr( $consumer_secret );} ?>" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'oauth_access_token' ); ?>">
	            <?php echo __( 'Access Token', 'sistweets' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'oauth_access_token' ); ?>" name="<?php echo $this->get_field_name( 'oauth_access_token' ); ?>" type="text" value="<?php if(isset($oauth_access_token)){echo esc_attr( $oauth_access_token );} ?>" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'oauth_access_token_secret' ); ?>">
	            <?php echo __( 'Access Token Secret', 'sistweets' ) . ':'; ?>
	        </label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'oauth_access_token_secret' ); ?>" name="<?php echo $this->get_field_name( 'oauth_access_token_secret' ); ?>" type="text" value="<?php if(isset($oauth_access_token_secret)){echo esc_attr( $oauth_access_token_secret );} ?>" />
	    </p>
		<?php 
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
}
