<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Single Release Widget.
 *
 * A widget to display a single release page content.
 *
 * @since 1.0.0
 */
class SumpCore_Single_Release_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sump_single_release';
    }

    public function get_title() {
        return esc_html__( 'Single Release', 'sumpcore' );
    }

    public function get_icon() {
        return 'eicon-single-post';
    }

    public function get_categories() {
        return [ 'sump-elements' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Release Selection', 'sumpcore' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'source',
            [
                'label' => esc_html__( 'Source', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'current',
                'options' => [
                    'current' => esc_html__( 'Current Release (Auto-detect)', 'sumpcore' ),
                    'latest' => esc_html__( 'Latest Release', 'sumpcore' ),
                    'manual' => esc_html__( 'Manual Selection', 'sumpcore' ),
                ],
            ]
        );

        $this->add_control(
            'selected_release',
            [
                'label' => esc_html__( 'Select Release', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_all_releases(),
                'label_block' => true,
                'condition' => [
                    'source' => 'manual',
                ],
            ]
        );

        $this->add_control(
            'show_cover',
            [
                'label' => esc_html__( 'Show Cover Art', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'sumpcore' ),
                'label_off' => esc_html__( 'Hide', 'sumpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_tracklist',
            [
                'label' => esc_html__( 'Show Tracklist', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'sumpcore' ),
                'label_off' => esc_html__( 'Hide', 'sumpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_store_links',
            [
                'label' => esc_html__( 'Show Store Links', 'sumpcore' ),
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
        
        // Determine which release to show
        $release_id = null;
        if ($settings['source'] === 'current') {
            $release_id = get_the_ID();
            if (get_post_type($release_id) !== 'release') {
                $release_id = null;
            }
        } elseif ($settings['source'] === 'latest') {
            $latest = get_posts(['post_type' => 'release', 'posts_per_page' => 1, 'fields' => 'ids']);
            $release_id = !empty($latest) ? $latest[0] : null;
        } elseif ($settings['source'] === 'manual' && !empty($settings['selected_release'])) {
            $release_id = $settings['selected_release'];
        }

        if (!$release_id) {
            echo '<p>' . esc_html__('No release found.', 'sumpcore') . '</p>';
            return;
        }

        $release = get_post($release_id);
        $artists = get_field('associated_artists', $release_id);
        $store_links = get_field('store_links', $release_id);
        $release_date = get_field('release_date', $release_id);
        ?>
        <div class="sump-single-release">
            <div class="release-container">
                <?php if ($settings['show_cover'] === 'yes') : ?>
                    <div class="release-sidebar">
                        <?php if (has_post_thumbnail($release_id)) : ?>
                            <div class="release-cover-art">
                                <?php echo get_the_post_thumbnail($release_id, 'large'); ?>
                            </div>
                        <?php endif; ?>

                        <button class="play-release-btn large-play-btn" data-release-id="<?php echo $release_id; ?>">
                            Play Album
                        </button>

                        <?php if ($settings['show_store_links'] === 'yes' && $store_links) : ?>
                            <div class="store-links">
                                <h3 class="links-title">Available On</h3>
                                <?php foreach ($store_links as $link) : ?>
                                    <a href="<?php echo esc_url($link['url']); ?>" target="_blank" class="store-link-btn platform-<?php echo esc_attr($link['platform']); ?>">
                                        <?php echo esc_html(ucfirst($link['platform'])); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="release-content">
                    <header class="entry-header">
                        <h1 class="entry-title"><?php echo esc_html($release->post_title); ?></h1>
                        <?php if ($artists) : ?>
                            <h2 class="entry-artist">
                                <?php
                                $artist_links = array();
                                foreach ($artists as $artist_id) {
                                    $artist_links[] = '<a href="' . get_permalink($artist_id) . '">' . get_the_title($artist_id) . '</a>';
                                }
                                echo implode(', ', $artist_links);
                                ?>
                            </h2>
                        <?php endif; ?>
                        <?php if ($release_date) : ?>
                            <div class="release-date">
                                Released: <?php echo date('F j, Y', strtotime($release_date)); ?>
                            </div>
                        <?php endif; ?>
                    </header>

                    <div class="entry-content">
                        <?php echo apply_filters('the_content', $release->post_content); ?>
                    </div>

                    <?php if ($settings['show_tracklist'] === 'yes') : ?>
                        <div class="track-list">
                            <h3 class="tracklist-title">Tracklist</h3>
                            <?php
                            $tracks_query = new \WP_Query([
                                'post_type' => 'track',
                                'posts_per_page' => -1,
                                'meta_key' => 'associated_release',
                                'meta_value' => $release_id,
                                'orderby' => 'menu_order',
                                'order' => 'ASC'
                            ]);

                            if ($tracks_query->have_posts()) :
                                echo '<ol>';
                                while ($tracks_query->have_posts()) : $tracks_query->the_post();
                                    $audio_file = get_field('audio_file');
                                    $track_artists = get_field('associated_artists');
                                    echo '<li class="track-item" data-track-src="' . ($audio_file ? esc_url($audio_file['url']) : '') . '">';
                                    echo '<span class="track-title">' . get_the_title() . '</span>';
                                    if ($track_artists && count($track_artists) > 0) {
                                        echo ' <span class="track-artist-info">by ';
                                        $track_artist_links = array();
                                        foreach ($track_artists as $artist_id) {
                                            $track_artist_links[] = '<a href="' . get_permalink($artist_id) . '">' . get_the_title($artist_id) . '</a>';
                                        }
                                        echo implode(', ', $track_artist_links);
                                        echo '</span>';
                                    }
                                    echo '</li>';
                                endwhile;
                                echo '</ol>';
                                \wp_reset_postdata();
                            else :
                                echo '<p>No tracks found for this release.</p>';
                            endif;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle track clicks in the tracklist
            document.querySelectorAll('.track-item').forEach(function(item, index) {
                item.style.cursor = 'pointer';
                item.addEventListener('click', function() {
                    const trackSrc = this.dataset.trackSrc;
                    if (trackSrc && window.sumpPlayer) {
                        const releaseId = <?php echo $release_id; ?>;
                        if (window.sumpPlayer.loadPlaylistById) {
                            window.sumpPlayer.loadPlaylistById(releaseId).then(() => {
                                window.sumpPlayer.loadTrack(index, true);
                            });
                        }
                    }
                });
            });
        });
        </script>
        <?php
    }

    private function get_all_releases() {
        $options = [];
        $query = new \WP_Query([
            'post_type' => 'release',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $options[get_the_ID()] = get_the_title();
            }
            \wp_reset_postdata();
        }
        return $options;
    }
}