<?php
/**
 * Handles the programmatic registration of ACF field groups for SumpCore.
 *
 * @package SumpCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * SumpCore_ACF Class
 */
class SumpCore_ACF {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('acf/init', array($this, 'register_field_groups'));
    }

    /**
     * Registers all the field groups for the plugin.
     */
    public function register_field_groups() {
        if ( ! function_exists('acf_add_local_field_group') ) {
            return;
        }

        // Field Group: Release Details
        acf_add_local_field_group(array(
            'key' => 'group_release_details',
            'title' => 'Release Details',
            'fields' => array(
                array(
                    'key' => 'field_release_associated_artists',
                    'label' => 'Associated Artist(s)',
                    'name' => 'associated_artists',
                    'type' => 'relationship',
                    'post_type' => array('artist'),
                    'min' => 1,
                    'return_format' => 'id', // Return post ID for efficiency
                ),
            ),
            'location' => array(
                array(
                    array( 'param' => 'post_type', 'operator' => '==', 'value' => 'release' ),
                ),
            ),
        ));

        // Field Group: Track Details
        acf_add_local_field_group(array(
            'key' => 'group_track_details',
            'title' => 'Track Details',
            'fields' => array(
                array(
                    'key' => 'field_track_audio_file',
                    'label' => 'Audio File',
                    'name' => 'audio_file',
                    'type' => 'file',
                    'return_format' => 'array',
                    'mime_types' => 'mp3,wav,m4a',
                ),
                array(
                    'key' => 'field_track_associated_release',
                    'label' => 'Associated Release',
                    'name' => 'associated_release',
                    'type' => 'post_object',
                    'post_type' => array('release'),
                    'allow_null' => 0,
                    'return_format' => 'id', // Return post ID
                ),
            ),
            'location' => array(
                array(
                    array( 'param' => 'post_type', 'operator' => '==', 'value' => 'track' ),
                ),
            ),
        ));
    }
}

