<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Tracks Grid Widget.
 *
 * A widget to display tracks in a grid with pagination.
 *
 * @since 1.0.0
 */
class SumpCore_Tracks_Grid_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sump_tracks_grid';
    }

    public function get_title() {
        return esc_html__( 'Tracks Grid', 'sumpcore' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
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
            'posts_per_page',
            [
                'label' => esc_html__( 'Tracks Per Page', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'default' => 16,
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__( 'Show Pagination', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'sumpcore' ),
                'label_off' => esc_html__( 'Hide', 'sumpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_play_all',
            [
                'label' => esc_html__( 'Show Play All Button', 'sumpcore' ),
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
        
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
        $tracks_query = new \WP_Query([
            'post_type' => 'track',
            'posts_per_page' => $settings['posts_per_page'],
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => $paged
        ]);

        if ($tracks_query->have_posts()) {
            ?>
            <div class="sump-tracks-grid-widget">
                <?php if ($settings['show_play_all'] === 'yes') : ?>
                    <div class="tracks-grid-header">
                        <button class="play-all-tracks-btn" id="widget-play-all-tracks">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            Play All Tracks
                        </button>
                    </div>
                <?php endif; ?>

                <div class="widget-tracks-grid">
                    <?php
                    while ($tracks_query->have_posts()) {
                        $tracks_query->the_post();
                        $release_id = get_field('associated_release');
                        $artists = get_field('associated_artists');
                        $audio_file = get_field('audio_file');
                        
                        // Get release cover art
                        $cover_image = '';
                        if ($release_id) {
                            $cover_image = get_the_post_thumbnail_url($release_id, 'medium_large');
                        }
                        if (!$cover_image) {
                            $cover_image = 'https://placehold.co/300x300/14182b/ffffff?text=' . urlencode(get_the_title());
                        }
                        ?>
                        <div class="widget-track-card" data-track-id="<?php echo get_the_ID(); ?>" data-track-src="<?php echo $audio_file ? esc_url($audio_file['url']) : ''; ?>">
                            <div class="widget-track-image">
                                <img src="<?php echo esc_url($cover_image); ?>" alt="<?php the_title(); ?>">
                                <div class="widget-track-overlay">
                                    <button class="widget-track-play-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="widget-track-content">
                                <h3 class="widget-track-title"><?php the_title(); ?></h3>
                                <?php if ($artists) : ?>
                                    <div class="widget-track-artist">
                                        <?php
                                        $artist_links = array();
                                        foreach ($artists as $artist_id) {
                                            $artist_links[] = '<a href="' . get_permalink($artist_id) . '">' . get_the_title($artist_id) . '</a>';
                                        }
                                        echo implode(', ', $artist_links);
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($release_id) : ?>
                                    <div class="widget-track-release">
                                        from <a href="<?php echo get_permalink($release_id); ?>"><?php echo get_the_title($release_id); ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <?php if ($settings['show_pagination'] === 'yes' && $tracks_query->max_num_pages > 1) : ?>
                    <div class="widget-tracks-pagination">
                        <?php
                        echo paginate_links([
                            'total' => $tracks_query->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '← Previous',
                            'next_text' => 'Next →',
                            'mid_size' => 2,
                        ]);
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle individual track clicks
                document.querySelectorAll('.widget-track-card').forEach(function(card) {
                    card.addEventListener('click', function() {
                        const trackSrc = this.dataset.trackSrc;
                        if (trackSrc && window.sumpPlayer) {
                            const trackData = {
                                tracks: [{
                                    title: this.querySelector('.widget-track-title').textContent,
                                    src: trackSrc
                                }],
                                artist: this.querySelector('.widget-track-artist') ? this.querySelector('.widget-track-artist').textContent : 'Unknown Artist',
                                album: this.querySelector('.widget-track-release') ? this.querySelector('.widget-track-release').textContent.replace('from ', '') : 'Single',
                                cover: this.querySelector('.widget-track-image img').src
                            };
                            window.sumpPlayer.playlist = trackData;
                            window.sumpPlayer.loadTrack(0, true);
                        }
                    });
                });

                // Handle play all button
                const playAllBtn = document.getElementById('widget-play-all-tracks');
                if (playAllBtn) {
                    playAllBtn.addEventListener('click', function() {
                        const allTracks = [];
                        document.querySelectorAll('.widget-track-card').forEach(function(card) {
                            const trackSrc = card.dataset.trackSrc;
                            if (trackSrc) {
                                allTracks.push({
                                    title: card.querySelector('.widget-track-title').textContent,
                                    src: trackSrc
                                });
                            }
                        });
                        
                        if (allTracks.length > 0 && window.sumpPlayer) {
                            const playlistData = {
                                tracks: allTracks,
                                artist: 'Various Artists',
                                album: 'All Tracks',
                                cover: 'https://placehold.co/300x300/14182b/ffffff?text=All+Tracks'
                            };
                            window.sumpPlayer.playlist = playlistData;
                            window.sumpPlayer.loadTrack(0, true);
                        }
                    });
                }
            });
            </script>
            <?php
            \wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__('No tracks found.', 'sumpcore') . '</p>';
        }
    }
}