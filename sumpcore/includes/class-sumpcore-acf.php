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
                array(
                    'key' => 'field_release_store_links',
                    'label' => 'Store & Streaming Links',
                    'name' => 'store_links',
                    'type' => 'repeater',
                    'instructions' => 'Add links to external stores like Juno Download, Spotify, etc.',
                    'layout' => 'table',
                    'button_label' => 'Add Link',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_store_name',
                            'label' => 'Platform',
                            'name' => 'platform',
                            'type' => 'select',
                            'choices' => array(
                                'spotify' => 'Spotify',
                                'junodownload' => 'Juno Download',
                                'youtube' => 'YouTube',
                                'beatport' => 'Beatport',
                                'apple' => 'Apple Music',
                                'bandcamp' => 'Bandcamp',
                                'soundcloud' => 'SoundCloud',
                                'amazon' => 'Amazon Music',
                            ),
                        ),
                        array(
                            'key' => 'field_store_url',
                            'label' => 'URL',
                            'name' => 'url',
                            'type' => 'url',
                            'placeholder' => 'https://...',
                        ),
                    ),
                ),
            ),
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

