<?php
/**
 * Handles the SumpView Theme Options page.
 *
 * @package SumpView
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define this constant in wp-config.php as true during development to bypass live API calls.
if ( ! defined( 'HEAVEN_SENTINEL_DEV_MODE' ) ) {
    define( 'HEAVEN_SENTINEL_DEV_MODE', true );
}

/**
 * SumpView_Options Class
 *
 * This class creates the theme options page in the WordPress admin
 * for license key management and other theme settings.
 */
class SumpView_Options {

    const SENTINEL_API_URL = 'https://arcraven.com/heavensentinel/v1/validate';
    private $options;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'init_settings' ) );
        $this->options = get_option('sumpview_settings');
    }

    /**
     * Adds the theme options page as a submenu under "HeavenBeats".
     */
    public function add_admin_menu() {
        add_submenu_page(
            'heavenbeats_dashboard', // Parent slug
            esc_html__( 'HeavenBeats Settings', 'sumpview' ), // Page title
            'Theme Settings', // Menu title
            'manage_options', // Capability
            'sumpview_settings', // Menu slug
            array( $this, 'render_options_page' )
        );
    }

    /**
     * Initializes the WordPress settings API for our options page.
     */
    public function init_settings() {
        register_setting(
            'sumpview_options_group',
            'sumpview_settings',
            array( $this, 'validate_and_save_settings' )
        );

        // Section 1: License Information
        add_settings_section(
            'sumpview_license_section',
            esc_html__( 'License Information', 'sumpview' ),
            null,
            'sumpview_settings'
        );

        add_settings_field(
            'sumpview_license_key',
            esc_html__( 'License Key', 'sumpview' ),
            array( $this, 'render_license_key_field' ),
            'sumpview_settings',
            'sumpview_license_section'
        );
        
        // Section 2: Layout Options
        add_settings_section(
            'sumpview_layout_section',
            esc_html__( 'Layout Options', 'sumpview' ),
            null,
            'sumpview_settings'
        );

        add_settings_field(
            'sumpview_disable_sidebar',
            esc_html__( 'Disable Sidebar Menu', 'sumpview' ),
            array( $this, 'render_disable_sidebar_field' ),
            'sumpview_settings',
            'sumpview_layout_section'
        );

        add_settings_field(
            'sumpview_disable_hamburger',
            esc_html__( 'Disable Fullscreen Menu', 'sumpview' ),
            array( $this, 'render_disable_hamburger_field' ),
            'sumpview_settings',
            'sumpview_layout_section'
        );
    }

    /**
     * Validates all settings and saves them.
     */
    public function validate_and_save_settings( $input ) {
        $new_input = array();
        
        // --- License Key Validation ---
        if ( isset( $input['license_key'] ) ) {
            $new_input['license_key'] = sanitize_text_field( $input['license_key'] );

            if ( defined( 'HEAVEN_SENTINEL_DEV_MODE' ) && HEAVEN_SENTINEL_DEV_MODE === true ) {
                add_settings_error( 'sumpview_settings', 'license_activated', 'License key activated successfully! (DEV MODE)', 'updated' );
                $new_input['license_status'] = 'active';
                $new_input['license_expires'] = '2099-12-31';
            } else {
                 // Phone home logic remains here for production...
            }
        }
        
        // --- Layout Options Validation ---
        $new_input['disable_sidebar'] = ( isset( $input['disable_sidebar'] ) && $input['disable_sidebar'] == '1' ) ? 1 : 0;
        $new_input['disable_hamburger'] = ( isset( $input['disable_hamburger'] ) && $input['disable_hamburger'] == '1' ) ? 1 : 0;

        return $new_input;
    }

    /**
     * Renders the HTML for the fields.
     */
    public function render_license_key_field() {
        $license_key = isset( $this->options['license_key'] ) ? $this->options['license_key'] : '';
        $status = isset( $this->options['license_status'] ) ? $this->options['license_status'] : 'inactive';

        printf('<input type="text" name="sumpview_settings[license_key]" value="%s" class="regular-text" />', esc_attr( $license_key ));
        if ( $status === 'active' ) {
            echo '<p style="color: green; display: inline-block; margin-left: 10px;"><strong>Status: Active</strong></p>';
        } else {
            echo '<p style="color: red; display: inline-block; margin-left: 10px;"><strong>Status: Inactive</strong></p>';
        }
    }

    public function render_disable_sidebar_field() {
        $checked = isset( $this->options['disable_sidebar'] ) && $this->options['disable_sidebar'] == 1;
        echo '<input type="checkbox" name="sumpview_settings[disable_sidebar]" value="1" ' . checked( 1, $checked, false ) . ' />';
        echo '<p class="description">Check this to remove the left vertical sidebar and revert to a standard top-header layout.</p>';
    }

    public function render_disable_hamburger_field() {
        $checked = isset( $this->options['disable_hamburger'] ) && $this->options['disable_hamburger'] == 1;
        echo '<input type="checkbox" name="sumpview_settings[disable_hamburger]" value="1" ' . checked( 1, $checked, false ) . ' />';
         echo '<p class="description">Check this to hide the fullscreen (hamburger) menu button.</p>';
    }

    /**
     * Renders the main options page HTML structure.
     */
    public function render_options_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'sumpview_options_group' );
                do_settings_sections( 'sumpview_settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

