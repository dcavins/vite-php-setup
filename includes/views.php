<?php
/**
 * Vite PLugin Loader - functions
 *
 * @package   Vite PLugin Loader
 * @author    dcavins
 * @license   GPL-2.0+
 */

namespace Vite_Load;

/**
 * Output html for the shortcode.
 *
 * @since   1.0.0
 *
 * @param   array $atts An array of variables that change the behavior of the shortcode.
 *
 * @return  html The pane.
 */
function render_shortcode( $atts ) {
	$a = shortcode_atts( array(
		'style'  => 'main',
	), $atts );

	// Fall back to the core map.
	$c = new Core_Render( $a );

	ob_start();

	$c->render();

	return ob_get_clean();
}
add_shortcode( 'vite-shortcode-test', __NAMESPACE__ . '\\render_shortcode' );
