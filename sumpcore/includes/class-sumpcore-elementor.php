<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * SumpCore Elementor Integration
 *
 * This class handles the registration of custom Elementor widgets and categories.
 *
 * @since 1.0.0
 */
final class SumpCore_Elementor {

    /**
     * The single instance of the class.
     * @var SumpCore_Elementor
     */
    private static $_instance = null;

    /**
     * Ensures only one instance of the class is loaded.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_widget_category' ] );
    }

    /**
     * Register our custom widget category.
     */
    public function register_widget_category( $elements_manager ) {
        $elements_manager->add_category(
            'sump-elements',
            [
                'title' => esc_html__( 'Sump Elements', 'sumpcore' ),
                'icon' => 'eicon-pro-icon',
            ]
        );
    }

    /**
     * Include widget files and register them.
     */
    public function register_widgets( $widgets_manager ) {
        // Include the widget files
        require_once SUMPCORE_PLUGIN_PATH . 'elementor-widgets/widget-release.php';
        require_once SUMPCORE_PLUGIN_PATH . 'elementor-widgets/widget-artist.php';
        require_once SUMPCORE_PLUGIN_PATH . 'elementor-widgets/widget-track.php';
        require_once SUMPCORE_PLUGIN_PATH . 'elementor-widgets/widget-latest-release.php';
        require_once SUMPCORE_PLUGIN_PATH . 'elementor-widgets/widget-latest-track.php';
        require_once SUMPCORE_PLUGIN_PATH . 'elementor-widgets/widget-tracks-grid.php';

        // Register the widgets
        $widgets_manager->register( new SumpCore_Release_Widget() );
        $widgets_manager->register( new SumpCore_Artist_Widget() );
        $widgets_manager->register( new SumpCore_Track_Widget() );
        $widgets_manager->register( new SumpCore_Latest_Release_Widget() );
        $widgets_manager->register( new SumpCore_Latest_Track_Widget() );
        $widgets_manager->register( new SumpCore_Tracks_Grid_Widget() );
    }
}

// Instantiate the loader
SumpCore_Elementor::instance();

