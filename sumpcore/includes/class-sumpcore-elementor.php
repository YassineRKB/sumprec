<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

final class SumpCore_Elementor {

    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_widget_category' ] );
    }

    public function register_widget_category( $elements_manager ) {
        $elements_manager->add_category(
            'sump-elements',
            [
                'title' => esc_html__( 'Sump Elements', 'sumpcore' ),
                'icon' => 'eicon-pro-icon',
            ]
        );
    }

    public function register_widgets( $widgets_manager ) {
        // Corrected path constant to the one defined in the main plugin file.
        require_once SUMPCORE_PLUGIN_PATH . 'elementor-widgets/widget-release.php';
        $widgets_manager->register( new SumpCore_Release_Widget() );
    }
}

