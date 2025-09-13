<?php
/**
 * The template part for displaying the fullscreen overlay menu.
 *
 * @package SumpView
 */

?>
<div id="fullscreen-menu" class="fullscreen-menu-overlay">
    <button id="fullscreen-menu-close" class="menu-close-btn">&times;</button>
    <nav class="fullscreen-nav">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'fullscreen-menu-items',
            'fallback_cb'    => false, // Do not show anything if menu is not set
        ) );
        ?>
    </nav>
</div>
