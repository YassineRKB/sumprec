<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Release Widget.
 *
 * A custom widget to display music releases.
 *
 * @since 1.0.0
 */
class SumpCore_Release_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget name.
     */
    public function get_name() {
        return 'sump_release';
    }

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Sump Release', 'sumpcore' );
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-album';
    }

    /**
     * Get widget categories.
     *
     * @since 1.0.0
     * @access public
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'sump-elements' ];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
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
                    'latest'  => esc_html__( 'Latest Releases', 'sumpcore' ),
                    'manual' => esc_html__( 'Manual Selection', 'sumpcore' ),
                ],
            ]
        );

        $this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'sumpcore' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 50,
				'step' => 1,
				'default' => 6,
                'condition' => [
                    'source' => 'latest',
                ],
			]
		);

        $this->add_control(
			'selected_releases',
			[
				'label' => esc_html__( 'Search & Select', 'sumpcore' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->get_all_releases(),
				'label_block' => true,
                'condition' => [
                    'source' => 'manual',
                ],
			]
		);

        $this->end_controls_section();

    }

    /**
     * Render widget output on the frontend.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $source = $settings['source'];

        $query_args = [
            'post_type' => 'release',
            'post_status' => 'publish',
        ];

        if ($source === 'manual' && !empty($settings['selected_releases'])) {
            $query_args['post__in'] = $settings['selected_releases'];
            $query_args['posts_per_page'] = count($settings['selected_releases']);
        } else {
            $query_args['posts_per_page'] = $settings['posts_per_page'];
        }

        $releases_query = new \WP_Query($query_args);

        if ($releases_query->have_posts()) {
            echo '<div class="sump-release-grid">';
            while ($releases_query->have_posts()) {
                $releases_query->the_post();
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
                                <!-- Simple Play SVG Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48"><path fill="none" d="M0 0h24v24H0z"/><path d="M8 5v14l11-7z"/></svg>
                            </button>
                        </div>
                    </div>
                    <h3 class="release-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                </div>
                <?php
            }
            echo '</div>';
            \wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__('No releases found.', 'sumpcore') . '</p>';
        }
    }

    /**
     * Helper function to get all releases for the select control.
     */
    private function get_all_releases() {
        $options = [];
        $query = new \WP_Query([
            'post_type' => 'release',
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
