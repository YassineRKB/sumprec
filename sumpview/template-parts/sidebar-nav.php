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
            <!-- You can replace this with your SVG or an <img> tag -->
            <span class="site-title">SumpView</span>
        </a>
    </div>

    <nav class="sidebar-navigation">
        <ul class="sidebar-menu-items">
            <li>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="Home">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span class="nav-text">Home</span>
                </a>
            </li>
            <li>
                <a href="/artists" title="Artists"> <!-- Replace with dynamic link if needed -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span class="nav-text">Artists</span>
                </a>
            </li>
            <li>
                <a href="/release" title="Releases"> <!-- Replace with dynamic link if needed -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg>
                    <span class="nav-text">Releases</span>
                </a>
            </li>
            <li>
                <a href="/contact" title="Contact"> <!-- Replace with dynamic link if needed -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span class="nav-text">Contact</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
