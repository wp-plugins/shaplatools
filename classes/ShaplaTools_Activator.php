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

		flush_rewrite_rules();
		
	}

}
