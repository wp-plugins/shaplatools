<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    ShaplaTools
 * @subpackage ShaplaTools/classes
 * @author     Sayful Islam <sayful.islam001@gmail.com>
 */
class ShaplaTools_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		ShaplaTools_Portfolio::portfolio_post_type();
		ShaplaTools_Event::event_post_type();

		flush_rewrite_rules();

	}

}
