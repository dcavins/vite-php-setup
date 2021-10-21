<?php
/**
 * Vite PLugin Loader
 *
 * @package   Vite PLugin Loader
 * @author    dcavins
 * @license   GPL-2.0+
 */

namespace Vite_Load;

/**
 * Define the shortcode.
 *
 *
 * @since     1.0
 * @package   Vite PLugin Loader
 * @author    dcavins
 */
class Core_Render {

	/**
	 * Corresponds to the 'style' passed in via the shortcode.
	 *
	 * @since 1.0
	 * @var string $map_style
	 */
	public $shortcode_style = 'main';

	/**
	 * An ID for the WP register script functions.
	 * Calculated from the $style.
	 *
	 * @since 1.0
	 * @var string $script_id
	 */
	public $script_id = '';

	/**
	 * Filepath for the root of the plugin.
	 *
	 * @since 1.0
	 * @var string $base_dir
	 */
	public $base_dir = '';

	/**
	 * URI for the root of the plugin.
	 *
	 * @since 1.0
	 * @var string $base_uri
	 */
	public $base_uri = '';

	/**
	 * Initialize the class
	 *
	 * @since     1.0
	 */
	public function __construct( $args = array() ) {
		if ( ! empty( $args['style'] ) ) {
			$this->shortcode_style = $args['style'];
		}
		$this->script_id = 'vite-sc-' . $this->shortcode_style;
		$this->base_dir = get_plugin_base_path();
		$this->base_uri = get_plugin_base_uri();
	}

	/**
	 * Add hooks
	 *
	 * @since     1.0
	 */
	public function add_hooks() {

		// Enqueue styles and scripts when necessary.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 99 );

		add_filter( 'script_loader_tag', array( $this, 'script_loader_tag_filter' ), 10, 3 );

	}

	/**
	 * Add required link/tag info for Vite to work.
	 *
	 * @since     1.0
	 */
	public function script_loader_tag_filter( $tag, $handle, $src ) {
	    if ( $handle === $this->script_id ) {
	        $tag = str_replace( '<script', '<script type="module" crossorigin', $tag );
	    }

	    if ( false !== stripos( $handle, $this->script_id . '-preloads-' ) ) {
		    $tag = '<link rel="modulepreload" href="' . $src . '">';
	    }

	    return $tag;
	}


	/**
	 * Enqueue required styles for displaying the map.
	 * Styles should be added in the <head>,
	 * so this will occur before our shortcode is rendered.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {
		$c = new Vite_Bridge();
		$css_urls = $c->get_asset_urls( 'css' );

		foreach ( $css_urls as $k => $css_path ) {
			// Enqueue the main style sheet.
			wp_enqueue_style(
				$this->script_id . '-style-' . $k,
				$css_path,
				array(),
				null, // filenames are hashed, so prevent the addition of the WP ?ver param
				'all'
			);
		}
	}

	/**
	 * Enqueue required scripts for displaying the map.
	 * Scripts should be added to the footer,
	 * and will be enqueued at the time of the shortcode render.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {
		$c = new Vite_Bridge();
		$js_url = $c->get_main_js_url();
		wp_enqueue_script(
			$this->script_id,
			$js_url,
			array(),
			null, // filenames are hashed, so prevent the addition of the WP ?ver param
			'all'
		);

		$imports = $c->get_asset_urls( 'imports' );
		foreach ( $imports as $k => $path ) {
			// Enqueue the main style sheet.
			wp_enqueue_script(
				$this->script_id . '-preloads-' . $k,
				$path,
				array(),
				null, // filenames are hashed, so prevent the addition of the WP ?ver param
				'all'
			);
		}
	}

	/**
	 * Render the shortcode.
	 *
	 * @since    1.0
	 */
	public function render() {
		?>
	    <div class="vue-app">
	        <hello-world msg="header"></hello-world>
	    </div>

	    <?php echo '<p class="message">PHP output here, potentially large HTML chunks</p>' ?>

	    <div class="vue-app">
	        <hello-world msg="footer"></hello-world>
	    </div>

	    <?php echo '<p class="message">PHP output here, potentially large HTML chunks</p>' ?>

		<?php add_action( 'wp_footer', array( $this, 'enqueue_scripts' ) );
	}
}
