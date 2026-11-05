<?php
/**
 * The News page: shows all posts.
 * Also the last fallback in the template hierarchy.
 */
get_header();
?>
<div class="wrap">
  <header class="page-head">
    <span class="eyebrow">From the studio</span>
    <h1>News</h1>
    <p>Updates, release notes and stories from MugStudio.</p>
  </header>

  <?php if ( have_posts() ) : ?>
    <div class="grid grid--3">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php get_template_part( 'template-parts/card' ); ?>
      <?php endwhile; ?>
    </div>

    <?php the_posts_pagination(); ?>

  <?php else : ?>
    <div class="card"><p style="margin:0">No posts yet. Check back soon!</p></div>
  <?php endif; ?>
</div>
<?php get_footer(); ?>
