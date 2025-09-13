<?php
/**
 * The template for displaying a single Release custom post type.
 *
 * @package SumpView
 */

get_header();
?>

<main id="primary" class="site-main">

    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="padding: 40px; text-align: center;">
            <header class="entry-header">
                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <div class="post-thumbnail" style="margin-bottom: 20px;">
                    <?php the_post_thumbnail('medium_large'); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>

                <!-- The button that triggers the player -->
                <button class="play-release-btn" data-release-id="<?php echo get_the_ID(); ?>">
                    Play Album
                </button>
            </div>

        </article>
        <?php
    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<?php
get_footer();
