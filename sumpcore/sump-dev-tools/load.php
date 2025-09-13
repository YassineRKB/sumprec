<?php
/**
 * SumpCore Developer Tools
 *
 * This file is only loaded during development and can be removed for production.
 *
 * @package SumpCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Example: Add a developer-only admin menu page.
function sumpcore_add_dev_tools_page() {
    add_submenu_page(
        'tools.php', // Parent slug
        'SumpCore Dev Tools',      // Page title
        'SumpCore Dev',      // Menu title
        'manage_options',          // Capability
        'sumpcore-dev-tools',      // Menu slug
        function() {
            echo '<div class="wrap"><h1>SumpCore Developer Tools</h1><p>This is where the demo content exporter will be built.</p></div>';
        }
    );
}
add_action( 'admin_menu', 'sumpcore_add_dev_tools_page' );