<?php
/*
Plugin Name: Vite Plugin Loader
Description: Use Vite to develop and render a WordPress shortcode. This is an example based on the code from https://github.com/andrefelipe/vite-php-setup.
Version: 1.0
*/
/**
 * Vite Plugin Loader
 *
 * @package   Vite test
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2021 CARES
 */

namespace Vite_Load;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

function plugin_init() {
	$basepath = get_plugin_base_path();

	require_once( $basepath . 'includes/views.php' );
	require_once( $basepath . 'public/class-vite-bridge.php' );
	require_once( $basepath . 'public/helpers.php' );

	// Add map configuration classes.
	require_once( $basepath . 'includes/class-core-render.php' );
	$map_class = new Core_Render();
	$map_class->add_hooks();

}
add_action( 'init', __NAMESPACE__ . '\\plugin_init' );

/**
 * Fetch the URI to the plugin's base directory.
 *
 * @return URI to the root of the plugin.
 */
function get_plugin_base_uri(){
	return plugin_dir_url( __FILE__ );
}

/**
 * Fetch the relative path to the plugin's base directory.
 *
 * @return Directory path to the root of the plugin.
 */
function get_plugin_base_name(){
	return plugin_basename( __FILE__ );
}

/**
 * Helper function.
 * @return Fully-qualified URI to the root of the plugin.
 */
function get_plugin_base_path() {
	return plugin_dir_path( __FILE__ );
}

/**
 * Get the current version of the plugin.
 *
 * @return string Current version of plugin.
 */
function get_plugin_version(){
	return '1.0';
}

/**
 * Get the current version of the plugin.
 *
 * @return string Current version of plugin.
 */
function get_plugin_slug(){
	return 'vite-plugin-loader';
}
