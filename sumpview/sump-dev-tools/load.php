<?php
/**
 * SumpView Developer Tools
 *
 * This file is only loaded during development and can be removed for production.
 *
 * @package SumpView
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Display a notice in the admin to confirm the dev tools are active.
function sumpview_dev_tools_admin_notice() {
    ?>
    <div class="notice notice-info is-dismissible">
        <p><?php esc_html_e( 'SumpView Developer Tools are currently active.', 'sumpview' ); ?></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'sumpview_dev_tools_admin_notice' );