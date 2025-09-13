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

    /**
     * The URL for the Heaven Sentinel license validation API.
     */
    const SENTINEL_API_URL = 'https://arcraven.com/heavensentinel/v1/validate';

    /**
     * Constructor. Hooks into WordPress admin actions.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'init_settings' ) );
    }

    /**
     * Adds the theme options page to the admin menu.
     */
    public function add_admin_menu() {
        add_menu_page(
            esc_html__( 'SumpView Settings', 'sumpview' ),
            'SumpView',
            'manage_options',
            'sumpview_settings',
            array( $this, 'render_options_page' ),
            'dashicons-admin-settings',
            60
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

        add_settings_section(
            'sumpview_license_section',
            esc_html__( 'License Information', 'sumpview' ),
            array( $this, 'render_license_section_text' ),
            'sumpview_settings'
        );

        add_settings_field(
            'sumpview_license_key',
            esc_html__( 'License Key', 'sumpview' ),
            array( $this, 'render_license_key_field' ),
            'sumpview_settings',
            'sumpview_license_section'
        );
    }

    /**
     * Validates the license key against the Sentinel API and saves settings.
     *
     * @param array $input The settings array from the form.
     * @return array The sanitized and validated settings array.
     */
    public function validate_and_save_settings( $input ) {
        $new_input = array();
        $current_options = get_option('sumpview_settings');
        
        if ( isset( $input['license_key'] ) ) {
            $new_input['license_key'] = sanitize_text_field( $input['license_key'] );

            // If in developer mode, simulate a successful activation.
            if ( defined( 'HEAVEN_SENTINEL_DEV_MODE' ) && HEAVEN_SENTINEL_DEV_MODE === true ) {
                add_settings_error( 'sumpview_settings', 'license_activated', 'License key activated successfully! (DEV MODE)', 'updated' );
                $new_input['license_status'] = 'active';
                $new_input['license_expires'] = '2099-12-31';
                return $new_input;
            }

            // Phone home to the Heaven Sentinel validation server.
            $response = wp_remote_post( self::SENTINEL_API_URL, [
                'timeout' => 15,
                'body'    => [
                    'license_key' => $new_input['license_key'],
                    'site_url'    => home_url(),
                ],
            ]);

            if ( is_wp_error( $response ) ) {
                add_settings_error( 'sumpview_settings', 'api_error', 'Could not connect to the license server.', 'error' );
                // Keep the old status if connection fails
                $new_input['license_status'] = isset($current_options['license_status']) ? $current_options['license_status'] : 'inactive';
            } else {
                $body = wp_remote_retrieve_body( $response );
                $data = json_decode( $body );

                if ( isset( $data->status ) && $data->status === 'active' ) {
                    add_settings_error( 'sumpview_settings', 'license_activated', 'License key activated successfully!', 'updated' );
                    $new_input['license_status'] = 'active';
                    $new_input['license_expires'] = isset($data->expires) ? sanitize_text_field($data->expires) : '';
                } else {
                    $error_message = isset($data->message) ? esc_html($data->message) : 'Invalid license key.';
                    add_settings_error( 'sumpview_settings', 'license_invalid', $error_message, 'error' );
                    $new_input['license_status'] = 'inactive';
                }
            }
        }

        return $new_input;
    }

    /**
     * Renders the description text for the license section.
     */
    public function render_license_section_text() {
        echo '<p>' . esc_html__( 'Enter your theme license key to activate premium features and receive updates.', 'sumpview' ) . '</p>';
    }

    /**
     * Renders the HTML for the license key input field and status.
     */
    public function render_license_key_field() {
        $options = get_option( 'sumpview_settings' );
        $license_key = isset( $options['license_key'] ) ? $options['license_key'] : '';
        $status = isset( $options['license_status'] ) ? $options['license_status'] : 'inactive';

        printf(
            '<input type="text" id="sumpview_license_key" name="sumpview_settings[license_key]" value="%s" class="regular-text" />',
            esc_attr( $license_key )
        );

        if ( $status === 'active' ) {
            echo '<p style="color: green; display: inline-block; margin-left: 10px;"><strong>' . esc_html__( 'Status: Active', 'sumpview' ) . '</strong></p>';
            if( !empty($options['license_expires']) ){
                echo '<p class="description">Your license expires on: ' . esc_html($options['license_expires']) . '</p>';
            }
        } else {
            echo '<p style="color: red; display: inline-block; margin-left: 10px;"><strong>' . esc_html__( 'Status: Inactive', 'sumpview' ) . '</strong></p>';
        }
    }

    /**
     * Renders the main options page HTML structure.
     */
    public function render_options_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <?php settings_errors(); ?>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'sumpview_options_group' );
                do_settings_sections( 'sumpview_settings' );
                submit_button( 'Save and Validate Key' );
                ?>
            </form>
        </div>
        <?php
    }
}

