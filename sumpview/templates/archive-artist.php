<?php
/**
 * The template for displaying the Artist custom post type archive.
 *
 * @package SumpView
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="artists-archive-header">
        <h1>Artists</h1>
        <p>Discover the talented artists in our catalog</p>
    </div>

    <div class="artists-grid">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                
                // Get the number of releases for this artist
                $releases_count = get_posts(array(
                    'post_type' => 'release',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'associated_artists',
                            'value' => '"' . get_the_ID() . '"',
                            'compare' => 'LIKE',
                        ),
                    ),
                    'fields' => 'ids'
                ));
                $release_count = count($releases_count);
                ?>
                <a href="<?php the_permalink(); ?>" class="artist-card">
                    <div class="artist-card-image">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php else : ?>
                            <img src="https://placehold.co/400x400/14182b/ffffff?text=<?php echo urlencode(get_the_title()); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="artist-card-content">
                        <h2 class="artist-card-name"><?php the_title(); ?></h2>
                        <p class="artist-card-releases">
                            <?php echo $release_count; ?> <?php echo $release_count === 1 ? 'Release' : 'Releases'; ?>
                        </p>
                    </div>
                </a>
                <?php
            endwhile;
        else :
            echo '<div style="grid-column: 1 / -1; text-align: center; padding: 40px;"><p>No artists found.</p></div>';
        endif;
        ?>
    </div>

    <?php
    // Pagination
    the_posts_pagination(array(
        'mid_size' => 2,
        'prev_text' => '← Previous',
        'next_text' => 'Next →',
    ));
    ?>

</main><!-- #main -->

<?php
get_footer();