<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0a0a0a">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'dobbel' ); ?></a>

<header class="site-header">
  <div class="wrap">
    <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'MugStudio home', 'dobbel' ); ?>">
      <?php get_template_part( 'template-parts/logo' ); ?>
      <span>Mug<b>Studio</b></span>
    </a>
    <nav aria-label="<?php esc_attr_e( 'Main menu', 'dobbel' ); ?>">
      <?php
      wp_nav_menu( array(
          'theme_location' => 'primary',
          'container'      => false,
          'menu_class'     => 'nav',
          'fallback_cb'    => function () { dobbel_fallback_menu( 'primary', 'nav' ); },
      ) );
      ?>
    </nav>
  </div>
</header>

<main id="main" class="site-main">
