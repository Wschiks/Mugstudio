<?php /* One post card (news, archive, 404, latest posts on the home page). */ ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card post-card' ); ?>>
  <a class="post-card__thumb<?php echo has_post_thumbnail() ? '' : ' post-card__thumb--empty'; ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
    <?php
    if ( has_post_thumbnail() ) {
        the_post_thumbnail( 'dobbel-card', array( 'loading' => 'lazy' ) );
    } else {
        get_template_part( 'template-parts/logo' );
    }
    ?>
  </a>
  <div class="post-card__body">
    <?php dobbel_post_meta(); ?>
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <p><?php echo esc_html( get_the_excerpt() ); ?></p>
    <a class="more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'dobbel' ); ?> &rarr;</a>
  </div>
</article>
