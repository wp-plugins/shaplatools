<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    ShaplaTools
 * @subpackage ShaplaTools/classes
 * @author     Sayful Islam <sayful.islam001@gmail.com>
 */
class ShaplaTools_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		ShaplaTools_Portfolio::portfolio_post_type();
		ShaplaTools_Event::event_post_type();
		ShaplaTools_Testimonial::testimonial_post_type();
		
		flush_rewrite_rules();
		
	}

}
