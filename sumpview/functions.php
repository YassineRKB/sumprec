<?php
/**
 * SumpView functions and definitions
 *
 * @package SumpView
 */

// Include the Theme Options panel class file.
require_once get_template_directory() . '/includes/class-sumpview-options.php';


if ( ! function_exists( 'sumpview_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	function sumpview_setup() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary Menu', 'sumpview' ),
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'sumpview_setup' );


/**
 * Enqueue scripts and styles.
 */
function sumpview_scripts() {
	wp_enqueue_style( 'sumpview-style', get_stylesheet_uri(), array(), '1.0.2' );

    // Enqueue Howler.js from a CDN for audio playback.
    wp_enqueue_script( 'howler-js', 'https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js', array(), '2.2.3', true );
    
    // Enqueue our custom player and main interaction scripts.
    wp_enqueue_script( 'sumpview-player', get_template_directory_uri() . '/assets/js/player.js', array( 'howler-js' ), '1.0.2', true );
    wp_enqueue_script( 'sumpview-main', get_template_directory_uri() . '/assets/js/main.js', array( 'sumpview-player' ), '1.0.2', true );

    // Pass data from PHP to our JavaScript to interact with the API.
    wp_localize_script( 'sumpview-main', 'sumpViewApiSettings', array(
        'apiUrl' => esc_url_raw( rest_url( 'sumpcore/v1/' ) ),
        'nonce'  => wp_create_nonce( 'wp_rest' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'sumpview_scripts' );


/**
 * Initialize theme options panel only when in the admin area.
 */
if ( is_admin() ) {
    new SumpView_Options();
}

// Load developer tools if they exist.
if ( file_exists( get_template_directory() . '/sump-dev-tools/load.php' ) ) {
    require_once get_template_directory() . '/sump-dev-tools/load.php';
}