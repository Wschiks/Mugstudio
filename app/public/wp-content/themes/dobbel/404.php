<?php
/**
 * 404: page not found.
 */
get_header();
$latest = new WP_Query( array( 'posts_per_page' => 3, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
?>
<div class="wrap">
  <section class="lost">
    <div class="big" aria-hidden="true"><span>4</span><span class="o">0</span><span>4</span></div>
    <h1><?php esc_html_e( 'Wiped out!', 'dobbel' ); ?></h1>
    <p><?php esc_html_e( 'The page you are looking for washed away, or it never existed. Let us paddle you back to shore.', 'dobbel' ); ?></p>
    <div class="btn-row">
      <a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the beach', 'dobbel' ); ?></a>
    </div>
  </section>

  <?php if ( $latest->have_posts() ) : ?>
    <section class="section">
      <div class="section-head"><h2><?php esc_html_e( 'Maybe you wanted to read', 'dobbel' ); ?></h2></div>
      <div class="grid grid--3">
        <?php
        while ( $latest->have_posts() ) {
            $latest->the_post();
            get_template_part( 'template-parts/card' );
        }
        wp_reset_postdata();
        ?>
      </div>
    </section>
  <?php endif; ?>
</div>
<?php get_footer(); ?>
