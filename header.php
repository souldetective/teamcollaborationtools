<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
    <div class="container nav">
        <div class="site-branding">
            <?php
            if ( has_custom_logo() ) {
                the_custom_logo();
            } else {
                ?>
                <a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
                <?php
            }
            ?>
        </div>
        <nav class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'aichatbotfree' ); ?>">
            <?php
            wp_nav_menu(
                [
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'primary-menu',
                    'fallback_cb'    => '__return_empty_string',
                    'depth'          => 3,
                ]
            );
            ?>
        </nav>
    </div>
</header>
<main class="site-main">
