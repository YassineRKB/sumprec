<?php
/**
 * The template part for displaying the persistent vertical sidebar navigation.
 *
 * @package SumpView
 */

?>
<aside id="sidebar-nav" class="site-sidebar">
    <div class="sidebar-header">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-logo">
            <?php
            $logo_url = get_theme_mod('sump_logo');
            if ($logo_url) : ?>
                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>" class="site-logo-image">
            <?php else : ?>
                <span class="site-title"><?php bloginfo('name'); ?></span>
            <?php endif; ?>
        </a>
    </div>

    <nav class="sidebar-navigation">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'sidebar',
            'container'      => false,
            'menu_class'     => 'sidebar-menu-items',
            'walker'         => new SumpView_Sidebar_Walker(),
            'fallback_cb'    => 'sumpview_sidebar_fallback_menu',
        ) );
        ?>
    </nav>
</aside>
