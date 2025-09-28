<?php
/**
 * The template for displaying all single 'artist' posts.
 * This is the crucial file that enables Elementor Theme Builder to apply a
 * template to a single artist page, matching the /artist/{arg} structure.
 *
 * @package SumpView
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php
    while ( have_posts() ) :
        the_post();

        // Elementor Theme Builder content will be injected via this function.
        // Your SumpCore_Single_Artist_Widget should be placed in the Theme Builder template.
        the_content();
        
    endwhile; // End of the loop.
    ?>
</main><?php
get_footer();