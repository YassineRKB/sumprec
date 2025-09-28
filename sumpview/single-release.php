<?php
/**
 * The template for displaying all single 'release' posts.
 * This file is required for the Elementor Theme Builder to detect and apply a Single Release template,
 * fulfilling the structure for /release/{arg}.
 *
 * @package SumpView
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php
    while ( have_posts() ) :
        the_post();
        
        // Elementor Theme Builder will inject its content here.
        the_content();
        
    endwhile;
    ?>
</main><?php
get_footer();