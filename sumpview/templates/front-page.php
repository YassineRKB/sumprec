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
        <h1>Welcome to <?php bloginfo('name'); ?></h1>
        <p>Discover amazing music from talented artists.</p>

        <?php
        // Display latest releases
        $latest_releases = new WP_Query(array(
            'post_type' => 'release',
            'posts_per_page' => 6,
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        if ($latest_releases->have_posts()) {
            echo '<h2>Latest Releases</h2>';
            echo '<div class="sump-release-grid">';
            while ($latest_releases->have_posts()) {
                $latest_releases->the_post();
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
            }
            echo '</div>';
            wp_reset_postdata();
        }

        // Display featured artists
        $featured_artists = new WP_Query(array(
            'post_type' => 'artist',
            'posts_per_page' => 4,
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        if ($featured_artists->have_posts()) {
            echo '<h2 style="margin-top: 60px;">Featured Artists</h2>';
            echo '<div class="sump-artist-grid">';
            while ($featured_artists->have_posts()) {
                $featured_artists->the_post();
                ?>
                <div class="sump-artist-item">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="artist-thumbnail">
                                <?php the_post_thumbnail('medium_large'); ?>
                            </div>
                        <?php else : ?>
                            <div class="artist-thumbnail">
                                <img src="https://placehold.co/300x300/14182b/ffffff?text=<?php echo urlencode(get_the_title()); ?>" alt="<?php the_title(); ?>">
                            </div>
                        <?php endif; ?>
                        <h3 class="artist-title"><?php the_title(); ?></h3>
                    </a>
                </div>
                <?php
            }
            echo '</div>';
            wp_reset_postdata();
        }
        ?>
    </div>

</main><!-- #main -->

<?php
get_footer();