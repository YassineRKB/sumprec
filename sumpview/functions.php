<?php
/**
 * SumpView theme functions and definitions
 *
 * @package SumpView
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

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
	// Enqueue theme stylesheet.
	wp_enqueue_style( 'sumpview-style', get_stylesheet_uri(), array(), '1.0.2' );

    // Enqueue Google Fonts (Inter).
    wp_enqueue_style( 'sumpview-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap', array(), null );
    
    // Enqueue GSAP Animation Library from CDN
    wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js', array(), '3.11.3', true );

    // Enqueue Howler.js for the player.
    wp_enqueue_script( 'howler', 'https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js', array(), '2.2.3', true );

    // Enqueue our custom player and main scripts.
    wp_enqueue_script( 'sumpview-player', get_template_directory_uri() . '/assets/js/player.js', array('howler'), '1.0.2', true );
    wp_enqueue_script( 'sumpview-main', get_template_directory_uri() . '/assets/js/main.js', array('sumpview-player', 'gsap'), '1.0.2', true );

    // Pass data from PHP to our JavaScript. Note: Localized to 'sumpview-player' as that's where the class is defined.
    wp_localize_script( 'sumpview-player', 'sumpViewApiSettings', array(
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

/**
 * Add Customizer settings for logo
 */
function sumpview_customize_register( $wp_customize ) {
    // Add a section for the logo
    $wp_customize->add_section( 'sumpview_logo_section', array(
        'title'    => __( 'Logo Settings', 'sumpview' ),
        'priority' => 30,
    ) );

    // Add logo image setting
    $wp_customize->add_setting( 'sump_logo', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    // Add logo image control
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'sump_logo', array(
        'label'    => __( 'Logo Image', 'sumpview' ),
        'section'  => 'sumpview_logo_section',
        'settings' => 'sump_logo',
    ) ) );
}
add_action( 'customize_register', 'sumpview_customize_register' );

/**
 * Add body class for sidebar layout
 */
function sumpview_body_classes( $classes ) {
    $sumpview_options = get_option('sumpview_settings');
    $is_sidebar_disabled = ! empty( $sumpview_options['disable_sidebar'] );
    
    if ( ! $is_sidebar_disabled ) {
        $classes[] = 'sidebar-enabled';
    }
    
    return $classes;
}
add_filter( 'body_class', 'sumpview_body_classes' );

/**
 * Load developer tools if they exist.
 * This should be the last thing included.
 */
if ( file_exists( get_template_directory() . '/sump-dev-tools/load.php' ) ) {
    require_once get_template_directory() . '/sump-dev-tools/load.php';
}