<?php
/**
 * The home page (test.local/).
 */
get_header();
?>
<div class="wrap">

  <!-- Hero: the big black block at the top -->
  <section class="hero">
    <div class="hero-mark-wrap"><?php get_template_part( 'template-parts/logo' ); ?></div>
    <span class="eyebrow">Indie game studio</span>
    <h1>Small games. Big fun.</h1>
    <p>MugStudio makes simple, friendly games for your phone. Pick one and play.</p>
    <div class="btn-row">
      <a class="btn btn--light" href="#games">See our games</a>
    </div>
  </section>

  <!-- Our games -->
  <section id="games" class="section">
    <div class="section-head">
      <h2>Our games</h2>
    </div>

    <div class="games">

      <!-- Surf Tycoon -->
      <article class="card game">
        <div class="game__media">
          <img src="<?php echo dobbel_asset( 'img/surf-tycoon-banner.webp' ); ?>" width="2000" height="668" alt="Surf Tycoon: a surfer rides a big wave next to a busy beach.">
        </div>
        <div class="game__body">
          <div class="game__icon">
            <img src="<?php echo dobbel_asset( 'img/surf-tycoon-icon.webp' ); ?>" width="120" height="120" alt="Surf Tycoon app icon" loading="lazy">
          </div>
          <div>
            <span class="tag tag--fill">Idle tycoon</span>
            <h3>Surf Tycoon</h3>
            <p>Run your own water-sports spot. Guests surf, skim, windsurf, kite, foil and sail on a big map you can pan and zoom. Hire managers so the beach earns while you are away, then expand it with a giant wave and start over, stronger than before.</p>
            <ul class="chips">
              <li class="chip">Wave surfing</li>
              <li class="chip">Skimboarding</li>
              <li class="chip">Windsurfing</li>
              <li class="chip">Kitesurfing</li>
              <li class="chip">Foil and wing</li>
              <li class="chip">Sailing</li>
            </ul>
            <div class="btn-row">
              <?php get_template_part( 'template-parts/store-buttons', null, array( 'game' => 'surf-tycoon' ) ); ?>
              <a class="btn btn--ghost" href="<?php echo home_url( '/support/' ); ?>">Support</a>
            </div>
          </div>
        </div>
      </article>

      <!-- Dobbelen -->
      <article class="card game game--split">
        <div class="game__media">
          <img src="<?php echo dobbel_asset( 'img/dobbelen-logo.webp' ); ?>" width="1536" height="1024" alt="Dobbelen logo: two beer glasses cheering with two dice in front." loading="lazy">
        </div>
        <div class="game__body">
          <span class="tag tag--fill">Party game</span>
          <span class="tag">English + Nederlands</span>
          <h3>Dobbelen</h3>
          <p>A dice bluffing game for friends. Roll in secret, claim a number that beats the last one (bluffing is allowed) and pass the phone. Believe it, or check it and find out who was lying. One phone, no account, no ads, works offline.</p>
          <div class="btn-row">
            <?php get_template_part( 'template-parts/store-buttons', null, array( 'game' => 'dobbelen' ) ); ?>
            <a class="btn btn--ghost" href="<?php echo home_url( '/support/' ); ?>">Support</a>
          </div>
        </div>
      </article>

      <!-- More games soon -->
      <div class="card soon">
        <div class="q">?</div>
        <h3>More games soon</h3>
        <p class="muted" style="margin:0">The next one is already in the workshop.</p>
      </div>

    </div>
  </section>

  <!-- Latest news: the 3 newest posts -->
  <?php $latest = new WP_Query( array( 'posts_per_page' => 3 ) ); ?>
  <?php if ( $latest->have_posts() ) : ?>
    <section class="section">
      <div class="section-head">
        <h2>Latest news</h2>
        <a href="<?php echo home_url( '/news/' ); ?>">All news &rarr;</a>
      </div>
      <div class="grid grid--3">
        <?php while ( $latest->have_posts() ) : $latest->the_post(); ?>
          <?php get_template_part( 'template-parts/card' ); ?>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- Contact -->
  <section class="section">
    <div class="card contact-card">
      <div>
        <h2>Questions, ideas or a bug?</h2>
        <p>We read every message. Send us a note and we will get back to you.</p>
      </div>
      <a class="btn btn--light" href="<?php echo home_url( '/support/' ); ?>">Visit support</a>
    </div>
  </section>

</div>
<?php get_footer(); ?>
