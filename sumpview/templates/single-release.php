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
        
        // Get associated data using ACF
        $artists = get_field('associated_artists');
        $store_links = get_field('store_links');

        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('sump-single-release'); ?>>
            <div class="release-container">

                <!-- Left Column: Cover Art & Links -->
                <div class="release-sidebar">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="release-cover-art">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <button class="play-release-btn large-play-btn" data-release-id="<?php echo get_the_ID(); ?>">
                        Play Album
                    </button>

                    <?php if ( $store_links ) : ?>
                        <div class="store-links">
                            <h3 class="links-title">Available On</h3>
                            <?php foreach ( $store_links as $link ) : ?>
                                <a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" class="store-link-btn platform-<?php echo esc_attr($link['platform']); ?>">
                                    <?php echo esc_html( $link['platform_label'] ); // ACF uses the choice label for display ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Details & Tracklist -->
                <div class="release-content">
                    <header class="entry-header">
                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                        <?php if ( $artists ) : ?>
                            <h2 class="entry-artist">
                                <?php
                                $artist_links = array();
                                foreach ($artists as $artist_id) {
                                    $artist_links[] = '<a href="' . get_permalink($artist_id) . '">' . get_the_title($artist_id) . '</a>';
                                }
                                echo implode(', ', $artist_links);
                                ?>
                            </h2>
                        <?php endif; ?>
                    </header>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <div class="track-list">
                        <h3 class="tracklist-title">Tracklist</h3>
                        <?php
                        $tracks_query = new WP_Query( array(
                            'post_type'      => 'track',
                            'posts_per_page' => -1,
                            'meta_key'       => 'associated_release',
                            'meta_value'     => get_the_ID()
                        ) );

                        if ( $tracks_query->have_posts() ) :
                            echo '<ol>';
                            while ( $tracks_query->have_posts() ) : $tracks_query->the_post();
                                echo '<li>' . get_the_title() . '</li>';
                            endwhile;
                            echo '</ol>';
                            wp_reset_postdata();
                        else :
                            echo '<p>No tracks found for this release.</p>';
                        endif;
                        ?>
                    </div>
                </div>

            </div>
        </article>
        <?php
    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<?php
get_footer();
