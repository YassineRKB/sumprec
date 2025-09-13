<?php
/**
 * Plugin Name:       SumpCore
 * Plugin URI:        https://arcraven.com/label-solutions/heavenBeats/v1/
 * Description:       Core functionality plugin for SumpView theme. Manages artists, releases, tracks, and the audio player API.
 * Version:           1.0.0
 * Author:            ARCRAVEN
 * Author URI:        https://arcraven.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sumpcore
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Main SumpCore Class.
 *
 * @class SumpCore
 * @version 1.0.0
 */
final class SumpCore {

    /**
     * SumpCore version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * The single instance of the class.
     *
     * @var SumpCore
     */
    protected static $_instance = null;

    /**
     * Main SumpCore Instance.
     *
     * Ensures only one instance of SumpCore is loaded or can be loaded.
     *
     * @static
     * @return SumpCore - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * SumpCore Constructor.
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define SumpCore Constants.
     */
    private function define_constants() {
        define( 'SUMPCORE_PLUGIN_FILE', __FILE__ );
        define( 'SUMPCORE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
        define( 'SUMPCORE_VERSION', $this->version );
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        require_once SUMPCORE_PLUGIN_PATH . 'includes/class-sumpcore-cpts.php';
        require_once SUMPCORE_PLUGIN_PATH . 'includes/class-sumpcore-api.php';
        // The demo importer will be included later.
        // The Elementor widgets will be included later.
    }

    /**
     * Hook into actions and filters.
     */
    private function init_hooks() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Init SumpCore when WordPress Plugins are loaded.
     */
    public function init() {
        // Init classes
        new SumpCore_CPTs();
        new SumpCore_API();
    }
}

/**
 * Function to get the main instance of SumpCore.
 *
 * @return SumpCore
 */
function SumpCore() {
    return SumpCore::instance();
}

// Global for backwards compatibility.
$GLOBALS['sumpcore'] = SumpCore();
