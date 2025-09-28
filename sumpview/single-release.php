<?php
/**
 * The template for displaying all single 'release' posts.
 * This is the crucial file that enables Elementor Theme Builder to apply a
 * template to a single release page, matching the /release/{arg} structure.
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
        // Your SumpCore_Single_Release_Widget should be placed in the Theme Builder template.
        the_content();
        
    endwhile; // End of the loop.
    ?>
</main><?php
get_footer();