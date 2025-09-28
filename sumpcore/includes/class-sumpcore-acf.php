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
 *
 * This class registers all necessary ACF fields via PHP to avoid
 * manual setup and ensure the plugin is self-contained.
 */
class SumpCore_ACF {

    /**
     * Constructor. Hooks into ACF initialization.
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

        // --- Store Link Definitions ---
        // Define all store fields here to inject them into the main array
        $store_link_fields = array(
            array(
                'key' => 'field_store_link_spotify',
                'label' => 'Spotify Link',
                'name' => 'store_link_spotify',
                'type' => 'url',
                'placeholder' => 'https://open.spotify.com/...',
            ),
            array(
                'key' => 'field_store_link_junodownload',
                'label' => 'Juno Download Link',
                'name' => 'store_link_junodownload',
                'type' => 'url',
                'placeholder' => 'https://www.junodownload.com/...',
            ),
            array(
                'key' => 'field_store_link_bandcamp',
                'label' => 'Bandcamp Link',
                'name' => 'store_link_bandcamp',
                'type' => 'url',
                'placeholder' => 'https://yourband.bandcamp.com/...',
            ),
            array(
                'key' => 'field_store_link_apple_music',
                'label' => 'Apple Music Link',
                'name' => 'store_link_apple_music',
                'type' => 'url',
                'placeholder' => 'https://music.apple.com/...',
            ),
            array(
                'key' => 'field_store_link_tidal',
                'label' => 'Tidal Link',
                'name' => 'store_link_tidal',
                'type' => 'url',
                'placeholder' => 'https://tidal.com/browse/album/...',
            ),
            array(
                'key' => 'field_store_link_youtube',
                'label' => 'YouTube Link',
                'name' => 'store_link_youtube',
                'type' => 'url',
                'placeholder' => 'https://www.youtube.com/watch?v=...',
            ),
        );
        
        // Base fields array (excluding the old repeater)
        $base_fields = array(
            array(
                'key' => 'field_release_associated_artists',
                'label' => 'Associated Artist(s)',
                'name' => 'associated_artists',
                'type' => 'relationship',
                'post_type' => array('artist'),
                'filters' => array('search'),
                'elements' => array('featured_image'),
                'min' => 1,
                'return_format' => 'id',
            ),
            array(
                'key' => 'field_release_date',
                'label' => 'Release Date',
                'name' => 'release_date',
                'type' => 'date_picker',
                'display_format' => 'F j, Y',
                'return_format' => 'Y-m-d',
            ),
        );
        
        // Merge the base fields with the new store link fields
        $release_details_fields = array_merge($base_fields, $store_link_fields);


        // Field Group: Release Details
        acf_add_local_field_group(array(
            'key' => 'group_release_details',
            'title' => 'Release Details',
            'fields' => $release_details_fields, // Use the merged array
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'release',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
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
                    'instructions' => 'Upload the audio file for this track (e.g., MP3, WAV).',
                    'return_format' => 'array',
                    'library' => 'all',
                    'mime_types' => 'mp3,wav,m4a',
                ),
                array(
                    'key' => 'field_track_associated_release',
                    'label' => 'Associated Release',
                    'name' => 'associated_release',
                    'type' => 'post_object',
                    'post_type' => array('release'),
                    'allow_null' => 0,
                    'multiple' => 0,
                    'return_format' => 'id',
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_track_associated_artists',
                    'label' => 'Associated Artist(s)',
                    'name' => 'associated_artists',
                    'type' => 'relationship',
                    'instructions' => 'Link the artist(s) for this specific track. This can be different from the main release artist for compilations.',
                    'post_type' => array('artist'),
                    'filters' => array('search'),
                    'min' => 1,
                    'return_format' => 'id',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'track',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
        ));
    }
}