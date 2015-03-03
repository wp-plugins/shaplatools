<?php

/**
 * Plugin Name:       ShaplaTools
 * Plugin URI:        https://wordpress.org/plugins/shaplatools/
 * Description:       ShaplaTools is a powerful plugin to extend functionality to your WordPress themes. 
 * Version:           1.0.0
 * Author:            Sayful Islam
 * Author URI:        http://sayful.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shaplatools
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in classes/ShaplaTools_Activator.php
 */
function activate_shaplatools() {
	require_once plugin_dir_path( __FILE__ ) . 'classes/ShaplaTools_Activator.php';
	ShaplaTools_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in classes/ShaplaTools_Deactivator.php
 */
function deactivate_shaplatools() {
	require_once plugin_dir_path( __FILE__ ) . 'classes/ShaplaTools_Deactivator.php';
	ShaplaTools_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_shaplatools' );
register_deactivation_hook( __FILE__, 'deactivate_shaplatools' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'classes/ShaplaTools.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_shaplatools() {

	$plugin = new ShaplaTools();
	$plugin->run();

}
run_shaplatools();
