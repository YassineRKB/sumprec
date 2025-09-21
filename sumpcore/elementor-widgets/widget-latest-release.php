<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Latest Release Widget.
 *
 * A tiny widget to display the latest release.
 *
 * @since 1.0.0
 */
class SumpCore_Latest_Release_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sump_latest_release';
    }

    public function get_title() {
        return esc_html__( 'Latest Release', 'sumpcore' );
    }

    public function get_icon() {
        return 'eicon-star';
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

        $latest_release = new \WP_Query([
            'post_type' => 'release',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        if ($latest_release->have_posts()) {
            while ($latest_release->have_posts()) {
                $latest_release->the_post();
                $artists = get_field('associated_artists');
                ?>
                <div class="sump-latest-release-widget">
                    <div class="latest-release-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium'); ?>
                            <?php else : ?>
                                <img src="https://placehold.co/300x300/14182b/ffffff?text=<?php echo urlencode(get_the_title()); ?>" alt="<?php the_title(); ?>">
                            <?php endif; ?>
                        </a>
                        <?php if ($settings['show_play_button'] === 'yes') : ?>
                            <div class="latest-release-overlay">
                                <button class="play-release-btn" data-release-id="<?php echo get_the_ID(); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                                        <path fill="none" d="M0 0h24v24H0z"/>
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="latest-release-content">
                        <h3 class="latest-release-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <?php if ($settings['show_artist'] === 'yes' && $artists) : ?>
                            <div class="latest-release-artist">
                                <?php
                                $artist_links = array();
                                foreach ($artists as $artist_id) {
                                    $artist_links[] = '<a href="' . get_permalink($artist_id) . '">' . get_the_title($artist_id) . '</a>';
                                }
                                echo 'by ' . implode(', ', $artist_links);
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
            \wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__('No releases found.', 'sumpcore') . '</p>';
        }
    }
}