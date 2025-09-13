<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package SumpView
 */

?>
    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <!-- Any standard footer content like copyright notices can go here in the future. -->
    </footer><!-- #colophon -->

</div><!-- #page -->

<?php
// Load the persistent audio player from its own template part file.
// This keeps the footer clean and the player's code modular.
get_template_part( 'template-parts/player' );

// Load the fullscreen menu from its own template part file.
get_template_part( 'template-parts/menu-fullscreen' );
?>

<?php wp_footer(); ?>
</body>
</html>

