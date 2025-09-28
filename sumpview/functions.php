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
				'sidebar' => esc_html__( 'Sidebar Menu', 'sumpview' ),
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
 * Custom Walker for Sidebar Navigation
 */
class SumpView_Sidebar_Walker extends Walker_Nav_Menu {
    
    function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
        
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        
        // Get icon for menu item
        $icon = $this->get_menu_icon( $item );
        
        $item_output = isset( $args->before ) ? $args->before : '';
        $item_output .= '<a' . $attributes .'>';
        $item_output .= $icon;
        $item_output .= '<span class="nav-text">';
        $item_output .= isset( $args->link_before ) ? $args->link_before : '';
        $item_output .= apply_filters( 'the_title', $item->title, $item->ID );
        $item_output .= isset( $args->link_after ) ? $args->link_after : '';
        $item_output .= '</span></a>';
        $item_output .= isset( $args->after ) ? $args->after : '';
        
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
    
    private function get_menu_icon( $item ) {
        $icons = array(
            'home' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
            'artists' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
            'releases' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg>',
            'tracks' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>',
            'blog' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
        );
        
        $url = strtolower( $item->url );
        $title = strtolower( $item->title );
        
        if ( strpos( $url, home_url() ) === 0 && strlen( $url ) === strlen( home_url() ) ) {
            return $icons['home'];
        } elseif ( strpos( $title, 'artist' ) !== false || strpos( $url, 'artist' ) !== false ) {
            return $icons['artists'];
        } elseif ( strpos( $title, 'release' ) !== false || strpos( $url, 'release' ) !== false ) {
            return $icons['releases'];
        } elseif ( strpos( $title, 'track' ) !== false || strpos( $url, 'track' ) !== false ) {
            return $icons['tracks'];
        } elseif ( strpos( $title, 'blog' ) !== false || strpos( $url, 'blog' ) !== false ) {
            return $icons['blog'];
        }
        
        return $icons['home']; // Default icon
    }
/**
 * Fallback menu for sidebar when no menu is assigned
 */
function sumpview_sidebar_fallback_menu() {
    echo '<ul class="sidebar-menu-items">';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '" title="Home">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
    echo '<span class="nav-text">Home</span></a></li>';
    echo '<li><a href="' . get_post_type_archive_link('artist') . '" title="Artists">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
    echo '<span class="nav-text">Artists</span></a></li>';
    echo '<li><a href="' . get_post_type_archive_link('release') . '" title="Releases">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg>';
    echo '<span class="nav-text">Releases</span></a></li>';
    echo '<li><a href="' . get_post_type_archive_link('track') . '" title="Tracks">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>';
    echo '<span class="nav-text">Tracks</span></a></li>';
    echo '<li><a href="' . (get_permalink(get_option('page_for_posts')) ?: home_url('/blog')) . '" title="Blog">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>';
    echo '<span class="nav-text">Blog</span></a></li>';
    echo '</ul>';
}
}

/**
 * REDIRECTION FIX: Redirects access to single CPT base URLs (e.g., /release/) 
 * to their respective archive pages (e.g., /releases/).
 */
function sumpview_redirect_empty_singles() {
    global $wp_query;

    // Safety check: Only run on the main query and if WordPress is classifying it as a 404 (failed lookup).
    if ( ! is_admin() && is_main_query() && is_404() ) {

        $cpts_to_check = array('release', 'artist');
        
        // This checks if the query failed because no specific post was found.
        if ( isset( $wp_query->query['name'] ) ) {
            $queried_slug = $wp_query->query['name'];
            
            foreach ($cpts_to_check as $post_type) {
                $post_type_object = get_post_type_object( $post_type );
                
                // If the requested slug (which led to the 404) matches the singular CPT slug, redirect.
                if ( $queried_slug === $post_type_object->rewrite['slug'] ) {
                    $archive_link = get_post_type_archive_link( $post_type );
                    
                    if ( $archive_link ) {
                        // Reset the 404 header and redirect.
                        // status_header( 200 ); // No need to reset 404 if we are redirecting away
                        wp_redirect( $archive_link, 301 ); // Permanent redirect
                        exit;
                    }
                }
            }
        }
    }
}
add_action( 'template_redirect', 'sumpview_redirect_empty_singles', 1 );

/**
 * Load developer tools if they exist.
 * This should be the last thing included.
 */
if ( file_exists( get_template_directory() . '/sump-dev-tools/load.php' ) ) {
    require_once get_template_directory() . '/sump-dev-tools/load.php';
}