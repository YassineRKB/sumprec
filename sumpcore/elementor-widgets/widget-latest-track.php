<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Latest Track Widget.
 *
 * A tiny widget to display the latest track.
 *
 * @since 1.0.0
 */
class SumpCore_Latest_Track_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sump_latest_track';
    }

    public function get_title() {
        return esc_html__( 'Latest Track', 'sumpcore' );
    }

    public function get_icon() {
        return 'eicon-headphones';
    }

    public function get_categories() {
        return [ 'sump-elements' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'sumpcore' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_artist',
            [
                'label' => esc_html__( 'Show Artist Name', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'sumpcore' ),
                'label_off' => esc_html__( 'Hide', 'sumpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_release',
            [
                'label' => esc_html__( 'Show Release Name', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'sumpcore' ),
                'label_off' => esc_html__( 'Hide', 'sumpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_play_button',
            [
                'label' => esc_html__( 'Show Play Button', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'sumpcore' ),
                'label_off' => esc_html__( 'Hide', 'sumpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $latest_track = new \WP_Query([
            'post_type' => 'track',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        if ($latest_track->have_posts()) {
            while ($latest_track->have_posts()) {
                $latest_track->the_post();
                $release_id = get_field('associated_release');
                $artists = get_field('associated_artists');
                $audio_file = get_field('audio_file');
                
                // Get release cover art
                $cover_image = '';
                if ($release_id) {
                    $cover_image = get_the_post_thumbnail_url($release_id, 'medium');
                }
                if (!$cover_image) {
                    $cover_image = 'https://placehold.co/300x300/14182b/ffffff?text=' . urlencode(get_the_title());
                }
                ?>
                <div class="sump-latest-track-widget">
                    <div class="latest-track-thumbnail">
                        <img src="<?php echo esc_url($cover_image); ?>" alt="<?php the_title(); ?>">
                        <?php if ($settings['show_play_button'] === 'yes' && $audio_file) : ?>
                            <div class="latest-track-overlay">
                                <button class="track-play-btn" data-track-src="<?php echo esc_url($audio_file['url']); ?>" data-track-title="<?php the_title(); ?>" data-track-artist="<?php echo $artists ? get_the_title($artists[0]) : 'Unknown Artist'; ?>" data-track-cover="<?php echo esc_url($cover_image); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                                        <path fill="none" d="M0 0h24v24H0z"/>
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="latest-track-content">
                        <h3 class="latest-track-title"><?php the_title(); ?></h3>
                        <?php if ($settings['show_artist'] === 'yes' && $artists) : ?>
                            <div class="latest-track-artist">
                                <?php
                                $artist_links = array();
                                foreach ($artists as $artist_id) {
                                    $artist_links[] = '<a href="' . get_permalink($artist_id) . '">' . get_the_title($artist_id) . '</a>';
                                }
                                echo 'by ' . implode(', ', $artist_links);
                                ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($settings['show_release'] === 'yes' && $release_id) : ?>
                            <div class="latest-track-release">
                                from <a href="<?php echo get_permalink($release_id); ?>"><?php echo get_the_title($release_id); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
            \wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__('No tracks found.', 'sumpcore') . '</p>';
        }
    }
}