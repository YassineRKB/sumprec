<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Track Widget.
 *
 * A custom widget to display a single track.
 *
 * @since 1.0.0
 */
class SumpCore_Track_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sump_track';
    }

    public function get_title() {
        return esc_html__( 'Sump Track', 'sumpcore' );
    }

    public function get_icon() {
        return 'eicon-play';
    }

    public function get_categories() {
        return [ 'sump-elements' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Track Selection', 'sumpcore' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
			'selected_track',
			[
				'label' => esc_html__( 'Search & Select Track', 'sumpcore' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $this->get_all_tracks(),
				'label_block' => true,
			]
		);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $track_id = $settings['selected_track'];

        if ( empty( $track_id ) ) {
            echo '<p>' . esc_html__('Please select a track.', 'sumpcore') . '</p>';
            return;
        }

        $track_post = get_post( $track_id );
        if ( ! $track_post ) {
            echo '<p>' . esc_html__('Track not found.', 'sumpcore') . '</p>';
            return;
        }

        $release_id = get_field('associated_release', $track_id);
        $artists = get_field('associated_artists', $track_id);
        ?>
        <div class="sump-track-widget">
            <h3 class="track-title"><?php echo esc_html( $track_post->post_title ); ?></h3>
            <?php if ( $artists ) : ?>
                <div class="track-artist">
                    by
                    <?php
                    $artist_links = array();
                    foreach ($artists as $artist_id) {
                        $artist_links[] = '<a href="' . get_permalink($artist_id) . '">' . get_the_title($artist_id) . '</a>';
                    }
                    echo implode(', ', $artist_links);
                    ?>
                </div>
            <?php endif; ?>
             <?php if ( $release_id ) : ?>
                <div class="track-release">
                    from the release <a href="<?php echo get_permalink($release_id); ?>"><?php echo get_the_title($release_id); ?></a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    private function get_all_tracks() {
        $options = [];
        $query = new \WP_Query([
            'post_type' => 'track',
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
