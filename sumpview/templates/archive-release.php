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

    <div class="release-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; padding: 20px;">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="text-align: center;">
                    <a href="<?php the_permalink(); ?>">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail('medium'); ?>
                        <?php endif; ?>
                        <h2 class="entry-title"><?php the_title(); ?></h2>
                    </a>
                    
                    <!-- The button that triggers the player -->
                    <button class="play-release-btn" data-release-id="<?php echo get_the_ID(); ?>">
                        Play Album
                    </button>
                </article>
                <?php
            endwhile;
        else :
            echo '<p>No releases found.</p>';
        endif;
        ?>
    </div>

</main><!-- #main -->

<?php
get_footer();
