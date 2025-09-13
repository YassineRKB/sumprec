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
        // Include the widget file
        require_once SUMPCORE_PLUGIN_DIR . 'elementor-widgets/widget-release.php';
        // Placeholder for future widgets
        // require_once SUMPCORE_PLUGIN_DIR . 'elementor-widgets/widget-artist.php';
        // require_once SUMPCORE_PLUGIN_DIR . 'elementor-widgets/widget-track.php';

        // Register the widget
        $widgets_manager->register( new SumpCore_Release_Widget() );
    }
}

// Instantiate the loader
SumpCore_Elementor::instance();
