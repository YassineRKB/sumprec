<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Artist Widget.
 *
 * A custom widget to display artists.
 *
 * @since 1.0.0
 */
class SumpCore_Artist_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sump_artist';
    }

    public function get_title() {
        return esc_html__( 'Sump Artist', 'sumpcore' );
    }

    public function get_icon() {
        return 'eicon-user-circle-o';
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
            'source',
            [
                'label' => esc_html__( 'Source', 'sumpcore' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'latest',
                'options' => [
                    'latest'  => esc_html__( 'Latest Artists', 'sumpcore' ),
                    'manual' => esc_html__( 'Manual Selection', 'sumpcore' ),
                ],
            ]
        );

        $this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Artists to Show', 'sumpcore' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 50,
				'step' => 1,
				'default' => 3,
                'condition' => [
                    'source' => 'latest',
                ],
			]
		);

        $this->add_control(
			'selected_artists',
			[
				'label' => esc_html__( 'Search & Select', 'sumpcore' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->get_all_artists(),
				'label_block' => true,
                'condition' => [
                    'source' => 'manual',
                ],
			]
		);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $source = $settings['source'];

        $query_args = [
            'post_type' => 'artist',
            'post_status' => 'publish',
        ];

        if ($source === 'manual' && !empty($settings['selected_artists'])) {
            $query_args['post__in'] = $settings['selected_artists'];
            $query_args['posts_per_page'] = count($settings['selected_artists']);
        } else {
            $query_args['posts_per_page'] = $settings['posts_per_page'];
        }

        $artists_query = new \WP_Query($query_args);

        if ($artists_query->have_posts()) {
            echo '<div class="sump-artist-grid">'; // You will need to style this class
            while ($artists_query->have_posts()) {
                $artists_query->the_post();
                ?>
                <div class="sump-artist-item">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="artist-thumbnail">
                                <?php the_post_thumbnail('medium_large'); ?>
                            </div>
                        <?php endif; ?>
                        <h3 class="artist-title"><?php the_title(); ?></h3>
                    </a>
                </div>
                <?php
            }
            echo '</div>';
            \wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__('No artists found.', 'sumpcore') . '</p>';
        }
    }

    private function get_all_artists() {
        $options = [];
        $query = new \WP_Query([
            'post_type' => 'artist',
            'posts_per_page' => -1,
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
