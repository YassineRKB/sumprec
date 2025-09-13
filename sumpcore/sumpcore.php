<?php
/**
 * Plugin Name:       SumpCore
 * Plugin URI:        https://arcraven.com/label-solutions/heavenBeats/v1/
 * Description:       Core functionality plugin for SumpView theme. Manages artists, releases, tracks, and the audio player API.
 * Version:           1.0.1
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

final class SumpCore {

    public $version = '1.0.1';
    protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    private function define_constants() {
        define( 'SUMPCORE_PLUGIN_FILE', __FILE__ );
        define( 'SUMPCORE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
        define( 'SUMPCORE_VERSION', $this->version );
    }

    public function includes() {
        require_once SUMPCORE_PLUGIN_PATH . 'includes/class-sumpcore-cpts.php';
        require_once SUMPCORE_PLUGIN_PATH . 'includes/class-sumpcore-api.php';
        require_once SUMPCORE_PLUGIN_PATH . 'includes/class-sumpcore-acf.php';
        require_once SUMPCORE_PLUGIN_PATH . 'includes/class-sumpcore-elementor.php';
    }

    private function init_hooks() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    public function init() {
        new SumpCore_CPTs();
        new SumpCore_API();
        new SumpCore_ACF();
        SumpCore_Elementor::instance(); // Elementor loader is a singleton
    }
}

function SumpCore() {
    return SumpCore::instance();
}

$GLOBALS['sumpcore'] = SumpCore();

// Load developer tools if they exist.
if ( file_exists( SUMPCORE_PLUGIN_PATH . 'sump-dev-tools/load.php' ) ) {
    require_once SUMPCORE_PLUGIN_PATH . 'sump-dev-tools/load.php';
}