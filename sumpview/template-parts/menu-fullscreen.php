<?php
/**
 * The template part for displaying the fullscreen overlay menu.
 *
 * @package SumpView
 */
?>

<div id="fullscreen-menu-overlay" class="fullscreen-menu-overlay">
    <button id="fullscreen-menu-close" class="menu-close-btn">
        <span class="hamburger-box">
            <span class="hamburger-inner"></span>
        </span>
    </button>
    <nav class="fullscreen-nav">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'fullscreen-menu-items',
            'fallback_cb'    => function() {
                echo '<ul class="fullscreen-menu-items">';
                echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
                echo '<li><a href="' . get_post_type_archive_link('artist') . '">Artists</a></li>';
                echo '<li><a href="' . get_post_type_archive_link('release') . '">Releases</a></li>';
                echo '<li><a href="' . get_post_type_archive_link('track') . '">Tracks</a></li>';
                echo '<li><a href="' . (get_permalink(get_option('page_for_posts')) ?: home_url('/blog')) . '">Blog</a></li>';
                echo '</ul>';
            },
        ) );
        ?>
    </nav>
</div>