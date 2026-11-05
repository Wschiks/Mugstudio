<?php
/**
 * The page with the slug "support". This is the Support URL for the app stores.
 * Text typed into the page editor is shown between the contact card and the FAQ.
 */
get_header();
$email = dobbel_contact_email();
?>
<div class="wrap">
<?php while ( have_posts() ) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
    <header class="article__head">
      <span class="eyebrow"><?php esc_html_e( 'Support', 'dobbel' ); ?></span>
      <h1><?php the_title(); ?></h1>
      <p class="muted"><?php esc_html_e( 'Need help with Surf Tycoon, Dobbelen or any other MugStudio game? You are in the right place.', 'dobbel' ); ?></p>
    </header>

    <div class="card contact-card">
      <div>
        <h2><?php esc_html_e( 'Contact us', 'dobbel' ); ?></h2>
        <p><?php esc_html_e( 'Send us an email and mention the game and your device. We usually reply within a few days.', 'dobbel' ); ?></p>
      </div>
      <a class="btn" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
    </div>

    <?php if ( trim( get_the_content() ) ) : ?>
      <div class="prose" style="margin-top:32px"><?php the_content(); ?></div>
    <?php endif; ?>

    <h2 style="margin-top:44px"><?php esc_html_e( 'Surf Tycoon: common questions', 'dobbel' ); ?></h2>
    <div class="faq">
      <h3 class="screen-reader-text">Surf Tycoon</h3>
      <details>
        <summary><?php esc_html_e( 'Where is my progress saved?', 'dobbel' ); ?></summary>
        <div><?php esc_html_e( 'On your own device only. There is no account and no server. If you delete the app, clear its data or use “Start over” in the menu, your progress is gone and we cannot bring it back.', 'dobbel' ); ?></div>
      </details>
      <details>
        <summary><?php esc_html_e( 'I bought something but it is not there. What now?', 'dobbel' ); ?></summary>
        <div><?php esc_html_e( 'Open the shop in the game and tap “Restore purchases”. That brings back “Remove ads”, “Coins x5” and your Surf Club membership. Gems that were bought and spent cannot be restored. Refunds are handled by the App Store or Google Play.', 'dobbel' ); ?></div>
      </details>
      <details>
        <summary><?php esc_html_e( 'How do I cancel the Surf Club subscription?', 'dobbel' ); ?></summary>
        <div><?php esc_html_e( 'Cancel it in the settings of your App Store or Google Play account, at least 24 hours before the renewal date. You keep the benefits until the paid period ends. We cannot cancel it for you.', 'dobbel' ); ?></div>
      </details>
      <details>
        <summary><?php esc_html_e( 'Why do my zones not earn while the game is closed?', 'dobbel' ); ?></summary>
        <div><?php esc_html_e( 'Hire a manager for the zone. Away time earns for 2 hours to start with; skills in the Beach tree make that longer, and so does the Surf Club.', 'dobbel' ); ?></div>
      </details>
      <details>
        <summary><?php esc_html_e( 'What data does the game collect?', 'dobbel' ); ?></summary>
        <div>
          <?php esc_html_e( 'None ourselves. Only the optional video ads in the phone apps are shown by Google AdMob.', 'dobbel' ); ?>
          <?php
          $privacy = get_page_by_path( 'privacy' );
          if ( $privacy ) {
              printf( ' <a href="%s">%s</a>', esc_url( get_permalink( $privacy ) ), esc_html__( 'Read the privacy policy.', 'dobbel' ) );
          }
          ?>
        </div>
      </details>
    </div>

    <h2 style="margin-top:44px"><?php esc_html_e( 'Dobbelen: common questions', 'dobbel' ); ?></h2>
    <div class="faq">
      <details>
        <summary><?php esc_html_e( 'Do I need an account or internet?', 'dobbel' ); ?></summary>
        <div><?php esc_html_e( 'No. Dobbelen is played on one phone that you pass around. There is no account, no ads, and it works offline.', 'dobbel' ); ?></div>
      </details>
      <details>
        <summary><?php esc_html_e( 'Can I play in Dutch?', 'dobbel' ); ?></summary>
        <div><?php esc_html_e( 'Yes. The game comes in English and Dutch.', 'dobbel' ); ?></div>
      </details>
      <details>
        <summary><?php esc_html_e( 'Is any data collected?', 'dobbel' ); ?></summary>
        <div><?php esc_html_e( 'No. The game collects nothing and makes no network requests.', 'dobbel' ); ?></div>
      </details>
    </div>
  </article>
<?php endwhile; ?>
</div>
<?php get_footer(); ?>
