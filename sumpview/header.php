<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until the main content.
 * It now includes conditional logic for the sidebar layout.
 *
 * @package SumpView
 */

// Get the theme options from the database.
$sumpview_options = get_option('sumpview_settings');
$is_sidebar_disabled = ! empty( $sumpview_options['disable_sidebar'] );
$is_hamburger_disabled = ! empty( $sumpview_options['disable_hamburger'] );

?>
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

    <?php
    // Conditionally load the persistent vertical sidebar navigation.
    if ( ! $is_sidebar_disabled ) {
        get_template_part( 'template-parts/sidebar-nav' );
    }
    ?>

    <div id="content" class="main-content-area">
        <div class="main-content-header">
            <?php
            // If the sidebar is disabled, show a traditional site title/logo in the top bar.
            if ( $is_sidebar_disabled ) :
            ?>
                <div class="site-branding">
                     <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-title">SumpView</a>
                </div>
            <?php endif; ?>

            <?php
            // Conditionally display the hamburger menu toggle button.
            if ( ! $is_hamburger_disabled ) :
            ?>
                 <button id="menu-toggle" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            <?php endif; ?>
        </div>

