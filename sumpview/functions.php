<?php
/**
 * SumpView functions and definitions
 *
 * @package SumpView
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * SumpView Theme Setup
 */
function sumpview_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support( 'post-thumbnails' );

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(
        array(
            'primary' => esc_html__( 'Primary Menu', 'sumpview' ),
        )
    );

    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );
}
add_action( 'after_setup_theme', 'sumpview_setup' );

/**
 * Enqueue scripts and styles.
 */
function sumpview_scripts() {
    // Main stylesheet.
    wp_enqueue_style( 'sumpview-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );

    // Howler.js for audio playback from a CDN.
    wp_enqueue_script( 'howler', 'https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js', array(), '2.2.3', true );
    
    // GSAP for animations from a CDN.
    wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js', array(), '3.11.4', true );

    // Main theme javascript file. This will handle general animations and UI.
    wp_enqueue_script( 'sumpview-main', get_template_directory_uri() . '/assets/js/main.js', array('gsap'), wp_get_theme()->get( 'Version' ), true );

    // Player javascript file.
    wp_enqueue_script( 'sumpview-player', get_template_directory_uri() . '/assets/js/player.js', array( 'howler' ), wp_get_theme()->get( 'Version' ), true );
    
    // Pass data from PHP to our player.js script.
    wp_localize_script(
        'sumpview-player',
        'sumpViewApiSettings',
        array(
            'root'  => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'namespace' => 'sumpcore/v1'
        )
    );
}
add_action( 'wp_enqueue_scripts', 'sumpview_scripts' );

/**
 * Include theme options panel.
 */
// We will uncomment this later when the file is created.
// require_once get_template_directory() . '/includes/class-sumpview-options.php';
