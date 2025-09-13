<?php
/**
 * Handles the registration of custom REST API endpoints for SumpCore.
 *
 * @package SumpCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class SumpCore_API {

    protected $namespace = 'sumpcore/v1';

    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/release/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_release_playlist' ),
            // NOTE: Switched to __return_true for easier development. 
            // We will re-implement the nonce/JWT check later.
            'permission_callback' => '__return_true', 
        ) );
    }

    public function get_release_playlist( $request ) {
        $release_id = (int) $request['id'];
        $release = get_post( $release_id );

        if ( empty( $release ) || 'release' !== $release->post_type ) {
            return new WP_Error( 'rest_post_invalid_id', 'Invalid release ID.', array( 'status' => 404 ) );
        }
        
        // Correctly query for tracks where the 'associated_release' post object field matches the release ID.
        $tracks_query = new WP_Query( array(
            'post_type'      => 'track',
            'posts_per_page' => -1,
            'meta_key'       => 'associated_release',
            'meta_value'     => $release_id
        ) );
        
        $tracks_data = array();
        if ( $tracks_query->have_posts() ) {
            while ( $tracks_query->have_posts() ) {
                $tracks_query->the_post();
                $audio_file = get_field( 'audio_file', get_the_ID() );
                if ( $audio_file && isset($audio_file['url']) ) {
                    $tracks_data[] = array(
                        'title' => get_the_title(),
                        'src'   => $audio_file['url'],
                    );
                }
            }
        }
        wp_reset_postdata();

        $artists = get_field( 'associated_artists', $release_id );
        $artist_name = 'Various Artists';
        if( ! empty( $artists ) ) {
            // Since ACF is returning IDs, we get the title from the first ID.
            $artist_name = get_the_title( $artists[0] );
        }

        $response_data = array(
            'releaseId' => $release->post_name,
            'artist'    => $artist_name,
            'album'     => get_the_title( $release_id ),
            'cover'     => get_the_post_thumbnail_url( $release_id, 'large' ),
            'tracks'    => $tracks_data,
        );

        return new WP_REST_Response( $response_data, 200 );
    }
}

