<?php
/**
 * SumpCore Developer Tools - Mass Uploader
 *
 * @package SumpCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Include and instantiate the AJAX handler.
require_once __DIR__ . '/class-sumpcore-uploader.php';

class SumpCore_Dev_Tools {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_dev_tools_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        
        // The uploader class is now responsible for its own AJAX hook.
        new SumpCore_Uploader();
    }

    public function add_dev_tools_page() {
        add_submenu_page(
            'tools.php',
            'SumpCore Mass Uploader',
            'SumpCore Uploader',
            'manage_options',
            'sumpcore-dev-tools',
            array( $this, 'render_uploader_page' )
        );
    }

    public function enqueue_scripts( $hook ) {
        if ( 'tools_page_sumpcore-dev-tools' !== $hook ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_script( 'sumpcore-uploader-js', plugin_dir_url( __FILE__ ) . 'uploader.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'sumpcore-uploader-js', 'sumpUploader', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'sumpcore_uploader_nonce' ),
        ) );
    }

    public function render_uploader_page() {
        ?>
        <div class="wrap" id="sumpcore-uploader-wrapper">
            <h1>SumpCore Mass Uploader</h1>
            <p>Use this tool to quickly create releases and their associated tracks. <strong>Note:</strong> The artist must already exist in the database.</p>

            <div id="uploader-container">
                </div>

            <button class="button button-secondary" id="add-release-section">+ Add Another Release</button>
            <hr>
            <button class="button button-primary button-hero" id="start-upload-btn">Process All Releases</button>
            <div id="upload-status" style="margin-top: 20px;"></div>
        </div>
        <?php
        $this->uploader_page_styles();
    }

    public function uploader_page_styles() {
        ?>
        <style>
            #sumpcore-uploader-wrapper .release-section { background: #fff; border: 1px solid #ccd0d4; padding: 20px; margin-bottom: 20px; border-radius: 4px; }
            #sumpcore-uploader-wrapper .release-header { font-size: 1.2em; margin-top: 0; }
            #sumpcore-uploader-wrapper .form-field { margin-bottom: 15px; }
            #sumpcore-uploader-wrapper .form-field label { display: block; margin-bottom: 5px; font-weight: bold; }
            #sumpcore-uploader-wrapper .form-field input[type="text"] { width: 100%; max-width: 400px; }
            #sumpcore-uploader-wrapper .tracks-container { margin-top: 20px; border-top: 1px dashed #ccd0d4; padding-top: 20px; }
            #sumpcore-uploader-wrapper .track-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; padding: 10px; background: #f9f9f9; border-radius: 3px; }
            #upload-status .status-item { padding: 10px; border-bottom: 1px solid #eee; }
            #upload-status .status-item.success strong { color: #227122; }
            #upload-status .status-item.error strong { color: #d63638; }
        </style>
        <?php
    }
}

new SumpCore_Dev_Tools();