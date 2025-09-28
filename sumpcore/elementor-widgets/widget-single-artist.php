<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Single Artist Widget.
 *
 * A widget to display a single artist page content.
 *
 * @since 1.0.0
 */
class SumpCore_Single_Artist_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sump_single_artist';
    }

    public function get_title() {
        return esc_html__( 'Single Artist', 'sumpcore' );
    }

    public function get_icon() {
        return 'eicon-person';
    }

    public function get_categories() {
        return [ 'sump-elements' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Artist Selection', 'sumpcore' ),
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
                    'current' => esc_html__( 'Current Artist (Auto-detect)', 'sumpcore' ),
                    'latest' => esc_html__( 'Latest Artist', 'sumpcore' ),
                    'manual' => esc_html__( 'Manual Selection', 'sumpcore' ),
                ],
            ]
        );

        $this->add_control(
            'selected_artist',
            [
                'label' => esc_html__( 'Select Artist', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_all_artists(),
                'label_block' => true,
                'condition' => [
                    'source' => 'manual',
                ],
            ]
        );

        $this->add_control(
            'show_header',
            [
                'label' => esc_html__( 'Show Artist Header', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'sumpcore' ),
                'label_off' => esc_html__( 'Hide', 'sumpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_bio',
            [
                'label' => esc_html__( 'Show Biography', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'sumpcore' ),
                'label_off' => esc_html__( 'Hide', 'sumpcore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_discography',
            [
                'label' => esc_html__( 'Show Discography', 'sumpcore' ),
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
        
        // Determine which artist to show
        $artist_id = null;
        
        if ($settings['source'] === 'current') {
            $current_id = get_the_ID();
            $is_elementor_edit_mode = class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode();
            
            // 1. Check for valid live post
            if ($current_id && get_post_type($current_id) === 'artist') {
                $artist_id = $current_id;
            }
            
            // 2. Elementor Fallback: If no valid post is found but we are in the editor, load the latest one for preview.
            if (!$artist_id && $is_elementor_edit_mode) {
                 $latest = get_posts([
                     'post_type' => 'artist',
                     'posts_per_page' => 1, 
                     'fields' => 'ids', 
                     'post_status' => 'publish'
                 ]);
                 $artist_id = !empty($latest) ? $latest[0] : null;
            }

        } elseif ($settings['source'] === 'latest') {
            $latest = get_posts(['post_type' => 'artist', 'posts_per_page' => 1, 'fields' => 'ids']);
            $artist_id = !empty($latest) ? $latest[0] : null;
        } elseif ($settings['source'] === 'manual' && !empty($settings['selected_artist'])) {
            $artist_id = $settings['selected_artist'];
        }

        if (!$artist_id) {
            $is_elementor_edit_mode = class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode();
            
            if ($is_elementor_edit_mode) {
                // Display a clear, styled message to the editor user
                echo '<div style="background-color: #333; color: #fff; padding: 15px; border-left: 5px solid #c70000; font-family: sans-serif;">' . 
                     esc_html__('Single Artist Widget: No target artist found. The widget is using the LATEST artist for preview. For accurate preview, set Elementor\'s "Preview Settings" to a specific artist post.', 'sumpcore') . 
                     '</div>';
            } else {
                 echo '<p>' . esc_html__('No artist found.', 'sumpcore') . '</p>';
            }
            return;
        }

        $artist = get_post($artist_id);
        ?>
        <div class="sump-single-artist">
            <?php if ($settings['show_header'] === 'yes') : ?>
                <div class="artist-header">
                    <?php if (has_post_thumbnail($artist_id)) : ?>
                        <div class="artist-featured-image">
                            <?php echo get_the_post_thumbnail($artist_id, 'large'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="artist-title-wrapper">
                        <h1 class="entry-title"><?php echo esc_html($artist->post_title); ?></h1>
                    </div>
                </div>
            <?php endif; ?>

            <div class="artist-content-area">
                <?php if ($settings['show_bio'] === 'yes' && !empty($artist->post_content)) : ?>
                    <div class="artist-bio">
                        <h2 class="section-title">Biography</h2>
                        <div class="entry-content">
                            <?php echo apply_filters('the_content', $artist->post_content); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($settings['show_discography'] === 'yes') : ?>
                    <div class="artist-discography">
                        <h2 class="section-title">Discography</h2>
                        <?php
                        $releases_query = new \WP_Query([
                            'post_type' => 'release',
                            'posts_per_page' => -1,
                            'meta_query' => [
                                [
                                    'key' => 'associated_artists',
                                    'value' => '"' . $artist_id . '"',
                                    'compare' => 'LIKE',
                                ],
                            ],
                        ]);

                        if ($releases_query->have_posts()) :
                            echo '<div class="sump-release-grid">';
                            while ($releases_query->have_posts()) : $releases_query->the_post();
                                ?>
                                <div class="sump-release-item">
                                    <div class="release-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php the_post_thumbnail('medium_large'); ?>
                                            <?php else : ?>
                                                <img src="https://placehold.co/300x300/14182b/ffffff?text=<?php echo urlencode(get_the_title()); ?>" alt="<?php the_title(); ?>">
                                            <?php endif; ?>
                                        </a>
                                        <div class="release-overlay">
                                            <button class="play-release-btn" data-release-id="<?php echo get_the_ID(); ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48">
                                                    <path fill="none" d="M0 0h24v24H0z"/>
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <h3 class="release-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                </div>
                                <?php
                            endwhile;
                            echo '</div>';
                            \wp_reset_postdata();
                        else :
                            echo '<p>' . esc_html($artist->post_title) . ' has no releases yet.</p>';
                        endif;
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    private function get_all_artists() {
        $options = [];
        $query = new \WP_Query([
            'post_type' => 'artist',
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