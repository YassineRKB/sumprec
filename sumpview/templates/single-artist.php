<?php
/**
 * The template for displaying a single Artist custom post type.
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
        <article id="post-<?php the_ID(); ?>" <?php post_class('sump-single-artist'); ?>>
            <div class="artist-header">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="artist-featured-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
                <div class="artist-title-wrapper">
                     <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </div>
            </div>

            <div class="artist-content-area">
                <div class="artist-bio">
                    <h2 class="section-title">Biography</h2>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </div>

                <div class="artist-discography">
                    <h2 class="section-title">Discography</h2>
                    <?php
                    $releases_query = new WP_Query( array(
                        'post_type'      => 'release',
                        'posts_per_page' => -1,
                        'meta_query'     => array(
                            array(
                                'key'     => 'associated_artists',
                                'value'   => '"' . get_the_ID() . '"',
                                'compare' => 'LIKE',
                            ),
                        ),
                    ) );

                    if ( $releases_query->have_posts() ) :
                        echo '<div class="sump-release-grid">'; // Re-using the same grid class
                        while ( $releases_query->have_posts() ) : $releases_query->the_post();
                            ?>
                            <div class="sump-release-item">
                                <div class="release-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('medium_large'); ?>
                                        <?php endif; ?>
                                    </a>
                                    <div class="release-overlay">
                                        <button class="play-release-btn" data-release-id="<?php echo get_the_ID(); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48"><path fill="none" d="M0 0h24v24H0z"/><path d="M8 5v14l11-7z"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="release-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            </div>
                            <?php
                        endwhile;
                        echo '</div>';
                        wp_reset_postdata();
                    else :
                        echo '<p>' . get_the_title() . ' has no releases yet.</p>';
                    endif;
                    ?>
                </div>
            </div>
        </article>
        <?php
    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<?php
get_footer();