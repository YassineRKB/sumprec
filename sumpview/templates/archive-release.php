<?php
/**
 * The template for displaying the Release custom post type archive.
 *
 * @package SumpView
 */

get_header();
?>

<main id="primary" class="site-main">
    <header class="page-header" style="text-align: center; padding: 20px;">
        <h1 class="page-title">Releases</h1>
    </header>

    <div class="sump-release-grid" style="padding: 20px;">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                ?>
                <div class="sump-release-item">
                    <div class="release-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
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
        else :
            echo '<p>No releases found.</p>';
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