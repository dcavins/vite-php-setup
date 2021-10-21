<?php
/**
 * Helpers to use Vite in a WP plugin.
 * Adapted from the original helpers.php file.
 *
 * @since     1.0
 * @package   Vite PLugin Loader
 * @author    dcavins
 */
namespace Vite_Load;

class Vite_Bridge {

    protected string $hostname = 'http://localhost';
    protected int $port = 3131;
    protected string $entry = 'main.js';
    protected string $out_dir = 'public/dist';
    protected bool $is_dev = true;
    protected string $dist_dir_path = '';
    protected string $dist_uri = '';

    /**
     * Initialize the class
     *
     * @since     1.0
     */
    public function __construct( $args = array() ) {
        // Override the entry point to fetch assets for a different file.
        $this->entry = $args['entry'] ?? 'main.js';

        if ( 'production' === wp_get_environment_type() ) {
            $this->is_dev = false;
        }

        $this->dist_dir_path = trailingslashit( get_plugin_base_path() . $this->out_dir );
        $this->dist_dir_uri  = trailingslashit( get_plugin_base_uri() . $this->out_dir );
    }

    /**
     * Find and parse the manifest.
     *
     * @since     1.0
     */
    function get_manifest() {
        $content = file_get_contents( $this->dist_dir_path . 'manifest.json');
        return json_decode( $content, true );
    }

    /**
     * Get the URL for the main file. If developing, load it from the vite instance.
     *
     * @since     1.0
     */
    function get_main_js_url() {
        $url = $this->is_dev
            ? $this->hostname . ':' . $this->port . '/' . $this->entry
            : current( $this->get_asset_urls( $this->entry ) );

        if ( ! $url ) {
            return '';
        }
        return $url;
    }

    /**
     * Get the URLs for assets.
     *
     * @since     1.0
     */
    function get_asset_urls( $sub_key = '' ) {
        $filenames = $this->get_asset_filenames( $sub_key );
        $urls      = array();
        foreach ( $filenames as $f ) {
            $urls[] = $this->dist_dir_uri . $f;
        }
        return $urls;
    }

    /**
     * Get the filename from the manifest for built assets.
     *
     * @since     1.0
     */
    function get_asset_filenames( $sub_key = '' ) {
        $manifest = $this->get_manifest();

        // Three cases:
        // main level
        // sub level (inside main)
        // imports (listed inside main, but object is at main level)
        $filenames = array();
        if ( 'imports' === $sub_key ) {
            // First, get the list of import object IDs.
            $imports = $manifest[ $this->entry ]['imports'] ?? array();
            foreach ( $imports as $obj_name ) {
                if ( isset( $manifest[ $obj_name ][ 'file' ] ) ) {
                    $filenames[] = $manifest[ $obj_name ][ 'file' ];
                }
            }
        } else if ( $sub_key ) {
            if ( isset( $manifest[ $this->entry ][ $sub_key ] ) ) {
                $filenames = $manifest[ $this->entry ][ $sub_key ];
            }
        } else {
            if ( isset( $manifest[ $this->entry ]['file'] ) ) {
                $filenames[] = $manifest[ $this->entry ]['file'];
            }
        }

        return $filenames;
    }

}