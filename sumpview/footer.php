<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the main content area and all content after.
 *
 * @package SumpView
 */

?>
    </div><!-- #content.main-content-area -->

</div><!-- #page.site -->

<?php
// The persistent audio player and the fullscreen menu overlay are included here.
get_template_part( 'template-parts/player' );
get_template_part( 'template-parts/menu-fullscreen' );
?>

<?php wp_footer(); ?>
</body>
</html>

