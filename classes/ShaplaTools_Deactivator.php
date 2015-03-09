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

		// add to your plugin deactivation function
		global $wp_rewrite;
		$wp_rewrite->flush_rules();

	}

}
