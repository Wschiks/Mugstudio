<?php get_header(); ?>
<div class="wrap">
<?php while ( have_posts() ) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
    <header class="article__head"><h1><?php the_title(); ?></h1></header>
    <div class="prose"><?php the_content(); ?></div>
  </article>
<?php endwhile; ?>
</div>
<?php get_footer(); ?>
