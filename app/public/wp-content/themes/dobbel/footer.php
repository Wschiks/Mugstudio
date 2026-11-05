</main>

<footer class="site-footer">
  <div class="wrap">
    <div class="footer-top">
      <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <?php get_template_part( 'template-parts/logo' ); ?>
        <span>Mug<b>Studio</b></span>
      </a>
      <nav aria-label="<?php esc_attr_e( 'Footer menu', 'dobbel' ); ?>">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'footer',
            'container'      => false,
            'menu_class'     => 'footer-nav',
            'depth'          => 1,
            'fallback_cb'    => function () { dobbel_fallback_menu( 'footer', 'footer-nav' ); },
        ) );
        ?>
      </nav>
    </div>
    <p class="copyright">
      &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> MugStudio. <?php esc_html_e( 'All rights reserved.', 'dobbel' ); ?>
      <?php esc_html_e( 'Games by MugStudio.', 'dobbel' ); ?>
      <br>
      <?php esc_html_e( 'Help:', 'dobbel' ); ?> <a href="mailto:<?php echo esc_attr( antispambot( dobbel_contact_email() ) ); ?>"><?php echo esc_html( antispambot( dobbel_contact_email() ) ); ?></a>
      &middot; <?php esc_html_e( 'General:', 'dobbel' ); ?> <a href="mailto:<?php echo esc_attr( antispambot( dobbel_info_email() ) ); ?>"><?php echo esc_html( antispambot( dobbel_info_email() ) ); ?></a>
    </p>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
