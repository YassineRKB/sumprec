<?php
/**
 * Template Name: Releases Archive (Elementor Compatible)
 * 
 * This template can be used for a custom page that displays releases
 * and can be edited with Elementor while maintaining archive functionality.
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
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <?php
                // Display Elementor content if it exists
                the_content();
                
                // If no Elementor content, show default releases archive
                $content = get_the_content();
                if (empty(trim($content))) :
                ?>
                    <header class="page-header" style="text-align: center; padding: 40px 20px;">
                        <h1 class="page-title">Releases</h1>
                        <p>Discover our latest music releases</p>
                    </header>

                    <div class="sump-release-grid" style="padding: 20px;">
                        <?php
                        $releases_query = new WP_Query(array(
                            'post_type' => 'release',
                            'posts_per_page' => 12,
                            'orderby' => 'date',
                            'order' => 'DESC'
                        ));

                        if ( $releases_query->have_posts() ) :
                            while ( $releases_query->have_posts() ) :
                                $releases_query->the_post();
                                ?>
                                <div class="sump-release-item">
                                    <div class="release-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php if ( has_post_thumbnail() ) : ?>
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
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<p>No releases found.</p>';
                        endif;
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</main>

<?php
get_footer();