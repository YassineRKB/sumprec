<?php
/**
 * Registers the Custom Post Types for SumpCore.
 *
 * @package SumpCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * SumpCore_CPTs Class
 *
 * This class handles the registration of custom post types:
 * Artist, Release, and Track.
 */
class SumpCore_CPTs {

    /**
     * Constructor. Hooks into WordPress initialization.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_post_types' ) );
    }

    /**
     * Registers all the custom post types for the plugin.
     */
    public function register_post_types() {
        $this->register_artist_cpt();
        $this->register_release_cpt();
        $this->register_track_cpt();
    }

    /**
     * Registers the 'Artist' Custom Post Type.
     */
    private function register_artist_cpt() {
        $labels = array(
            'name'                  => _x( 'Artists', 'Post Type General Name', 'sumpcore' ),
            'singular_name'         => _x( 'Artist', 'Post Type Singular Name', 'sumpcore' ),
            'menu_name'             => __( 'Artists', 'sumpcore' ),
            'name_admin_bar'        => __( 'Artist', 'sumpcore' ),
            'archives'              => __( 'Artist Archives', 'sumpcore' ),
            'attributes'            => __( 'Artist Attributes', 'sumpcore' ),
            'parent_item_colon'     => __( 'Parent Artist:', 'sumpcore' ),
            'all_items'             => __( 'All Artists', 'sumpcore' ),
            'add_new_item'          => __( 'Add New Artist', 'sumpcore' ),
            'add_new'               => __( 'Add New', 'sumpcore' ),
            'new_item'              => __( 'New Artist', 'sumpcore' ),
            'edit_item'             => __( 'Edit Artist', 'sumpcore' ),
            'update_item'           => __( 'Update Artist', 'sumpcore' ),
            'view_item'             => __( 'View Artist', 'sumpcore' ),
            'view_items'            => __( 'View Artists', 'sumpcore' ),
            'search_items'          => __( 'Search Artist', 'sumpcore' ),
        );
        $args = array(
            'label'                 => __( 'Artist', 'sumpcore' ),
            'description'           => __( 'For storing artist information', 'sumpcore' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => 'heavenbeats_dashboard', // Changed from true
            'menu_icon'             => 'dashicons-admin-users',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );
        register_post_type( 'artist', $args );
    }

    /**
     * Registers the 'Release' Custom Post Type.
     */
    private function register_release_cpt() {
        $labels = array(
            'name'                  => _x( 'Releases', 'Post Type General Name', 'sumpcore' ),
            'singular_name'         => _x( 'Release', 'Post Type Singular Name', 'sumpcore' ),
            'menu_name'             => __( 'Releases', 'sumpcore' ),
            'name_admin_bar'        => __( 'Release', 'sumpcore' ),
            'archives'              => __( 'Release Archives', 'sumpcore' ),
            'attributes'            => __( 'Release Attributes', 'sumpcore' ),
            'parent_item_colon'     => __( 'Parent Release:', 'sumpcore' ),
            'all_items'             => __( 'All Releases', 'sumpcore' ),
            'add_new_item'          => __( 'Add New Release', 'sumpcore' ),
            'add_new'               => __( 'Add New', 'sumpcore' ),
            'new_item'              => __( 'New Release', 'sumpcore' ),
            'edit_item'             => __( 'Edit Release', 'sumpcore' ),
            'update_item'           => __( 'Update Release', 'sumpcore' ),
            'view_item'             => __( 'View Release', 'sumpcore' ),
            'view_items'            => __( 'View Releases', 'sumpcore' ),
            'search_items'          => __( 'Search Release', 'sumpcore' ),
        );
        $args = array(
            'label'                 => __( 'Release', 'sumpcore' ),
            'description'           => __( 'For storing releases, albums, and EPs', 'sumpcore' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => 'heavenbeats_dashboard', // Changed from true
            'menu_icon'             => 'dashicons-album',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );
        register_post_type( 'release', $args );
    }

    /**
     * Registers the 'Track' Custom Post Type.
     */
    private function register_track_cpt() {
        $labels = array(
            'name'                  => _x( 'Tracks', 'Post Type General Name', 'sumpcore' ),
            'singular_name'         => _x( 'Track', 'Post Type Singular Name', 'sumpcore' ),
            'menu_name'             => __( 'Tracks', 'sumpcore' ),
            'name_admin_bar'        => __( 'Track', 'sumpcore' ),
            'archives'              => __( 'Track Archives', 'sumpcore' ),
            'attributes'            => __( 'Track Attributes', 'sumpcore' ),
            'parent_item_colon'     => __( 'Parent Track:', 'sumpcore' ),
            'all_items'             => __( 'All Tracks', 'sumpcore' ),
            'add_new_item'          => __( 'Add New Track', 'sumpcore' ),
            'add_new'               => __( 'Add New', 'sumpcore' ),
            'new_item'              => __( 'New Track', 'sumpcore' ),
            'edit_item'             => __( 'Edit Track', 'sumpcore' ),
            'update_item'           => __( 'Update Track', 'sumpcore' ),
            'view_item'             => __( 'View Track', 'sumpcore' ),
            'view_items'            => __( 'View Tracks', 'sumpcore' ),
            'search_items'          => __( 'Search Track', 'sumpcore' ),
        );
        $args = array(
            'label'                 => __( 'Track', 'sumpcore' ),
            'description'           => __( 'For storing individual tracks', 'sumpcore' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => 'heavenbeats_dashboard', // Changed from true
            'menu_icon'             => 'dashicons-media-audio',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );
        register_post_type( 'track', $args );
    }
}

