<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="site-branding">
            <!-- You can replace this with an <img> tag for your logo -->
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-title">SumpView</a>
        </div>

        <div class="menu-toggle-wrapper">
            <button id="menu-toggle" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </header><!-- #masthead -->

    <div id="content" class="site-content">

