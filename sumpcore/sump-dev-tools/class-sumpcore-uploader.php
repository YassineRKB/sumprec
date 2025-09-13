<?php
/**
 * SumpCore Developer Tools - AJAX Uploader Backend
 *
 * @package SumpCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class SumpCore_Uploader {

    public function __construct() {
        // Register the AJAX action for logged-in administrators.
        add_action( 'wp_ajax_sumpcore_process_release', array( $this, 'handle_release_upload' ) );
    }

    /**
     * The main handler for the AJAX request.
     */
    public function handle_release_upload() {
        // 1. Security Check
        check_ajax_referer( 'sumpcore_uploader_nonce', 'nonce' );

        // 2. Sanitize and Validate Input
        $release_name = isset( $_POST['release_name'] ) ? sanitize_text_field( $_POST['release_name'] ) : '';
        $artist_name = isset( $_POST['artist_name'] ) ? sanitize_text_field( $_POST['artist_name'] ) : '';
        $tracks_data = isset( $_POST['tracks'] ) ? $_POST['tracks'] : array();

        if ( empty( $release_name ) || empty( $artist_name ) ) {
            wp_send_json_error( 'Release Name and Artist Name are required.' );
        }

        // 3. Find or Create Artist
        $artist_id = $this->get_artist_id_by_name( $artist_name );
        if ( ! $artist_id ) {
            wp_send_json_error( "Artist '{$artist_name}' not found. Please create the artist first." );
        }

        // 4. Create the Release Post
        $release_post_data = array(
            'post_title'  => $release_name,
            'post_status' => 'publish',
            'post_type'   => 'release',
        );
        $release_id = wp_insert_post( $release_post_data );

        if ( is_wp_error( $release_id ) ) {
            wp_send_json_error( 'Failed to create release post: ' . $release_id->get_error_message() );
        }

        // 5. Handle Featured Image Upload
        if ( isset( $_FILES['release_image'] ) ) {
            $attachment_id = $this->handle_file_upload( $_FILES['release_image'], $release_id );
            if ( is_wp_error( $attachment_id ) ) {
                wp_send_json_error( 'Image upload failed: ' . $attachment_id->get_error_message() );
            }
            set_post_thumbnail( $release_id, $attachment_id );
        }
        
        // 6. Link Artist to Release
        update_field( 'field_release_associated_artists', array( $artist_id ), $release_id );

        // 7. Process and Create Tracks
        $track_ids = array();
        if ( ! empty( $tracks_data ) ) {
            foreach ( $tracks_data as $index => $track_info ) {
                $track_name = sanitize_text_field( $track_info['name'] );
                if ( empty( $track_name ) ) continue;

                $track_post_data = array(
                    'post_title'  => $track_name,
                    'post_status' => 'publish',
                    'post_type'   => 'track',
                );
                $track_id = wp_insert_post( $track_post_data );

                if ( ! is_wp_error( $track_id ) ) {
                    // Link track to this release and artist
                    update_field( 'field_track_associated_release', $release_id, $track_id );
                    update_field( 'field_track_associated_artists', array( $artist_id ), $track_id );

                    // Handle track audio file upload
                    if ( isset( $_FILES['track_file_' . $index] ) ) {
                        $track_attachment_id = $this->handle_file_upload( $_FILES['track_file_' . $index], $track_id );
                        if ( ! is_wp_error( $track_attachment_id ) ) {
                            update_field( 'field_track_audio_file', $track_attachment_id, $track_id );
                        }
                    }
                    $track_ids[] = $track_id;
                }
            }
        }

        wp_send_json_success( array(
            'message' => "Successfully created release '{$release_name}' with " . count($track_ids) . " tracks.",
            'release_id' => $release_id,
        ) );
    }
    
    /**
     * Finds an artist post by title.
     */
    private function get_artist_id_by_name( $name ) {
        $post = get_page_by_title( $name, OBJECT, 'artist' );
        return $post ? $post->ID : null;
    }

    /**
     * Handles a file upload and adds it to the media library.
     */
    private function handle_file_upload( $file, $parent_post_id ) {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $file, $upload_overrides );

        if ( $movefile && ! isset( $movefile['error'] ) ) {
            $filename = $movefile['file'];
            $filetype = wp_check_filetype( basename( $filename ), null );
            $attachment = array(
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                'post_content'   => '',
                'post_status'    => 'inherit',
            );
            $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );
            
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            return $attach_id;
        } else {
            return new WP_Error( 'upload_error', $movefile['error'] );
        }
    }
}