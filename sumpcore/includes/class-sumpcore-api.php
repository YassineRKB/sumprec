<?php
/**
 * Handles the registration of custom REST API endpoints for SumpCore.
 *
 * @package SumpCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * SumpCore_API Class
 *
 * This class sets up and manages the custom API endpoints required
 * for the SumpView theme and player functionality.
 */
class SumpCore_API {

    /**
     * The namespace for the custom API endpoints.
     */
    protected $namespace = 'sumpcore/v1';

    /**
     * Constructor. Hooks into WordPress REST API initialization.
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Registers all the custom routes for the plugin.
     */
    public function register_routes() {
        // Endpoint to get a playlist for a single release.
        register_rest_route( $this->namespace, '/release/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_release_playlist' ),
            'permission_callback' => array( $this, 'permissions_check' ),
            'args'                => array(
                'id' => array(
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    }
                ),
            ),
        ) );

        // Endpoint to get all tracks by an artist.
        register_rest_route( $this->namespace, '/artist/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_artist_playlist' ),
            'permission_callback' => array( $this, 'permissions_check' ),
            'args'                => array(
                'id' => array(
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    }
                ),
            ),
        ) );
    }

    /**
     * Permission check for the API endpoints.
     *
     * Validates either a WordPress nonce for web-based requests or a
     * JWT for external applications.
     *
     * @param WP_REST_Request $request The current request object.
     * @return bool|WP_Error True if authorized, WP_Error otherwise.
     */
    public function permissions_check( $request ) {
        // Priority 1: Check for JWT in Authorization header (for mobile apps, etc.)
        // Placeholder for JWT validation logic.
        $auth_header = $request->get_header( 'authorization' );
        if ( ! empty( $auth_header ) && preg_match( '/Bearer\s(\S+)/', $auth_header, $matches ) ) {
            $token = $matches[1];
            // In a real application, you would validate the JWT here.
            // For now, we'll just check if a token exists.
            if ( $this->is_jwt_valid( $token ) ) {
                return true;
            }
        }

        // Priority 2: Check for WordPress Nonce (for website frontend)
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! empty( $nonce ) && wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return true;
        }

        // If neither method is successful, deny access.
        return new WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not allowed to do that.', 'sumpcore' ), array( 'status' => 401 ) );
    }

    /**
     * Placeholder function for JWT validation.
     * In a real implementation, this would use a library to decode and verify the token signature and expiration.
     *
     * @param string $token The JWT.
     * @return bool
     */
    private function is_jwt_valid( $token ) {
        // This is where you would integrate your JWT validation logic.
        // For this project, we'll return true if the token is not empty.
        return ! empty( $token );
    }

    /**
     * Callback to get the playlist data for a specific release.
     *
     * @param WP_REST_Request $request The current request object.
     * @return WP_REST_Response|WP_Error
     */
    public function get_release_playlist( $request ) {
        $release_id = (int) $request['id'];
        $release = get_post( $release_id );

        if ( empty( $release ) || 'release' !== $release->post_type ) {
            return new WP_Error( 'rest_post_invalid_id', 'Invalid release ID.', array( 'status' => 404 ) );
        }
        
        // This assumes ACF is installed and fields are set up.
        // We will query for 'track' posts where the 'associated_release' field matches this release ID.
        $tracks_query = new WP_Query( array(
            'post_type'      => 'track',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'   => 'associated_release', // The name of your ACF Post Object field
                    'value' => '"' . $release_id . '"',
                    'compare' => 'LIKE',
                ),
            ),
        ) );
        
        $tracks_data = array();
        if ( $tracks_query->have_posts() ) {
            while ( $tracks_query->have_posts() ) {
                $tracks_query->the_post();
                $audio_file = get_field( 'audio_file', get_the_ID() ); // ACF File field
                if ( $audio_file ) {
                    $tracks_data[] = array(
                        'title' => get_the_title(),
                        'src'   => $audio_file['url'],
                    );
                }
            }
        }
        wp_reset_postdata();

        // Get associated artist data
        $artists = get_field( 'associated_artists', $release_id );
        $artist_name = 'Various Artists';
        if( ! empty( $artists ) ) {
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

    /**
     * Callback to get the playlist data for a specific artist.
     * This is a placeholder and would need more complex logic.
     *
     * @param WP_REST_Request $request The current request object.
     * @return WP_REST_Response|WP_Error
     */
    public function get_artist_playlist( $request ) {
        // This is more complex as it involves fetching multiple releases
        // and then all tracks for those releases. We will implement this later.
        $response_data = array( 'message' => 'This endpoint is under construction.' );
        return new WP_REST_Response( $response_data, 501 ); // 501 Not Implemented
    }
}
