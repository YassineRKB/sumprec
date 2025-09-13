<?php
/**
 * The template for displaying the front page.
 *
 * @package SumpView
 */

get_header();
?>

<main id="primary" class="site-main">

    <div style="padding: 40px; text-align: center;">
        <h1>SumpView Homepage</h1>
        <p>Content for the homepage goes here.</p>

        <?php
        // Example: Display a "Play" button for a specific release (e.g., ID 123)
        // In a real scenario, you would fetch this ID dynamically.
        $args = array(
            'post_type' => 'release',
            'posts_per_page' => 1
        );
        $latest_release = new WP_Query($args);

        if ($latest_release->have_posts()) {
            while ($latest_release->have_posts()) {
                $latest_release->the_post();
                echo '<h2>Play Latest Release: ' . get_the_title() . '</h2>';
                echo '<button class="play-release-btn" data-release-id="' . get_the_ID() . '">Play Album</button>';
            }
            wp_reset_postdata();
        } else {
            echo '<p>No releases found. Please add a release in the admin panel.</p>';
        }
        ?>
    </div>

</main><!-- #main -->

<?php
get_footer();
