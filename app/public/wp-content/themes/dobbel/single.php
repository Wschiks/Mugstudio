<?php get_header(); ?>
<div class="wrap">
<?php while ( have_posts() ) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
    <header class="article__head">
      <?php dobbel_post_meta(); ?>
      <h1><?php the_title(); ?></h1>
    </header>
    <?php if ( has_post_thumbnail() ) : ?>
      <figure class="article__thumb"><?php the_post_thumbnail( 'large' ); ?></figure>
    <?php endif; ?>

    <div class="prose"><?php the_content(); ?></div>

    <footer class="post-foot">
      <?php
      $tags = get_the_tags();
      if ( $tags ) {
          foreach ( $tags as $tag ) {
              printf( '<a class="tag-pill" href="%s">#%s</a>', esc_url( get_tag_link( $tag ) ), esc_html( $tag->name ) );
          }
      }
      ?>
      <nav class="post-nav" aria-label="<?php esc_attr_e( 'More posts', 'dobbel' ); ?>">
        <?php
        $prev = get_previous_post();
        $next = get_next_post();
        if ( $prev ) {
            printf( '<a class="prev" href="%s" rel="prev"><small>&larr; %s</small>%s</a>', esc_url( get_permalink( $prev ) ), esc_html__( 'Previous', 'dobbel' ), esc_html( get_the_title( $prev ) ) );
        }
        if ( $next ) {
            printf( '<a class="next" href="%s" rel="next"><small>%s &rarr;</small>%s</a>', esc_url( get_permalink( $next ) ), esc_html__( 'Next', 'dobbel' ), esc_html( get_the_title( $next ) ) );
        }
        ?>
      </nav>
    </footer>
  </article>
<?php endwhile; ?>
</div>
<?php get_footer(); ?>
