<?php
/**
 * Archives: category, tag, date (year/month/day) and author.
 */
get_header();
?>
<div class="wrap">
  <header class="page-head">
    <span class="eyebrow"><?php esc_html_e( 'Archive', 'dobbel' ); ?></span>
    <?php the_archive_title( '<h1>', '</h1>' ); ?>
    <?php
    $desc = get_the_archive_description();
    if ( $desc ) {
        echo '<div class="muted">' . wp_kses_post( $desc ) . '</div>';
    } else {
        echo '<p>' . esc_html( sprintf( /* translators: %d: post count */ _n( '%d post', '%d posts', (int) $GLOBALS['wp_query']->found_posts, 'dobbel' ), (int) $GLOBALS['wp_query']->found_posts ) ) . '</p>';
    }
    ?>
  </header>

  <?php if ( have_posts() ) : ?>
    <div class="grid grid--3">
      <?php
      while ( have_posts() ) {
          the_post();
          get_template_part( 'template-parts/card' );
      }
      ?>
    </div>
    <?php
    the_posts_pagination( array(
        'mid_size'  => 1,
        'prev_text' => '&larr;<span class="screen-reader-text"> ' . esc_html__( 'Newer posts', 'dobbel' ) . '</span>',
        'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Older posts', 'dobbel' ) . ' </span>&rarr;',
    ) );
    ?>
  <?php else : ?>
    <div class="card">
      <p style="margin:0"><?php esc_html_e( 'Nothing here yet.', 'dobbel' ); ?></p>
    </div>
  <?php endif; ?>
</div>
<?php get_footer(); ?>
