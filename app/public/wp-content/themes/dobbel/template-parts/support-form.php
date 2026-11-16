<?php
/**
 * Support request form + confirmation. Logic lives in inc/support-form.php.
 */
$state  = dobbel_form_state();
$v      = wp_parse_args( $state['values'], array( 'game' => '', 'topic' => '', 'device' => '', 'message' => '', 'name' => '', 'email' => '', 'consent' => false ) );
$errors = $state['errors'];
$sent   = isset( $_GET['sent'] ) ? sanitize_text_field( wp_unslash( $_GET['sent'] ) ) : '';

/** aria + class for a field with or without an error. */
$err_attr = function ( $key ) use ( $errors ) {
    if ( isset( $errors[ $key ] ) ) {
        return ' aria-invalid="true" aria-describedby="e-' . esc_attr( $key ) . '"';
    }
    return '';
};
$err_msg = function ( $key ) use ( $errors ) {
    if ( isset( $errors[ $key ] ) ) {
        printf( '<p class="field-error" id="e-%s"><span aria-hidden="true">!</span> %s</p>', esc_attr( $key ), esc_html( $errors[ $key ] ) );
    }
};
?>
<section id="contact" class="support-form-wrap" aria-labelledby="contact-title">

<?php if ( $sent ) : ?>
  <?php $done = get_transient( 'dobbel_sent_' . $sent ); ?>
  <div class="card done" tabindex="-1" id="done" role="status">
    <div class="done__icon" aria-hidden="true">&#10003;</div>
    <h2 id="contact-title">Thanks, we got your request!</h2>
    <?php if ( $done ) : ?>
      <p>
        Your reference number is <strong class="ref"><?php echo esc_html( $sent ); ?></strong>.
        We reply within 3 working days to <strong><?php echo esc_html( $done['values']['email'] ); ?></strong>.
        Check your spam folder if you hear nothing.
      </p>
      <dl class="summary">
        <div><dt>Game</dt><dd><?php echo esc_html( $done['labels']['game'] ); ?></dd></div>
        <div><dt>Topic</dt><dd><?php echo esc_html( $done['labels']['topic'] ); ?></dd></div>
        <div><dt>Device</dt><dd><?php echo esc_html( $done['labels']['device'] ); ?></dd></div>
        <div class="summary__wide"><dt>Your message</dt><dd><?php echo nl2br( esc_html( $done['values']['message'] ) ); ?></dd></div>
      </dl>
    <?php else : ?>
      <p>We reply within 3 working days. Check your spam folder if you hear nothing.</p>
    <?php endif; ?>
    <div class="btn-row">
      <a class="btn" href="<?php echo esc_url( home_url( '/#games' ) ); ?>">Back to the games</a>
      <a class="btn btn--ghost" href="<?php echo esc_url( get_permalink() . '#contact' ); ?>">Send another request</a>
    </div>
  </div>

<?php else : ?>
  <div class="form-head">
    <span class="eyebrow">Still stuck?</span>
    <h2 id="contact-title">Send us a request</h2>
    <p class="muted">Takes about 2 minutes. Fields marked <span class="req" aria-hidden="true">*</span><span class="screen-reader-text">with a star</span> are required.</p>
  </div>

  <form class="card support-form" id="support-form" method="post" action="<?php echo esc_url( get_permalink() . '#contact' ); ?>" novalidate>
    <input type="hidden" name="dobbel_support" value="1">
    <?php wp_nonce_field( 'dobbel_support', '_dobbel_nonce', false ); ?>
    <?php $err_msg( 'form' ); ?>
    <div class="hp" aria-hidden="true">
      <label for="f-website">Leave this empty</label>
      <input type="text" id="f-website" name="website" tabindex="-1" autocomplete="off">
    </div>

    <!-- Step 1 -->
    <fieldset class="step">
      <legend><span class="step__num">1</span> What is it about?</legend>

      <fieldset class="field<?php echo isset( $errors['game'] ) ? ' has-error' : ''; ?>"<?php echo $err_attr( 'game' ); ?>>
        <legend class="label">Game <span class="req" aria-hidden="true">*</span></legend>
        <?php $err_msg( 'game' ); ?>
        <div class="choices">
          <?php foreach ( dobbel_form_options( 'game' ) as $key => $label ) : ?>
            <label class="choice" for="f-game-<?php echo esc_attr( $key ); ?>">
              <input type="radio" id="f-game-<?php echo esc_attr( $key ); ?>" name="game" value="<?php echo esc_attr( $key ); ?>" <?php checked( $v['game'], $key ); ?> required>
              <span><?php echo esc_html( $label ); ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <div class="field-row">
        <div class="field<?php echo isset( $errors['topic'] ) ? ' has-error' : ''; ?>">
          <label class="label" for="f-topic">Topic <span class="req" aria-hidden="true">*</span></label>
          <?php $err_msg( 'topic' ); ?>
          <select id="f-topic" name="topic" required<?php echo $err_attr( 'topic' ); ?>>
            <option value="">Choose a topic…</option>
            <?php foreach ( dobbel_form_options( 'topic' ) as $key => $label ) : ?>
              <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $v['topic'], $key ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field<?php echo isset( $errors['device'] ) ? ' has-error' : ''; ?>">
          <label class="label" for="f-device">Device <span class="req" aria-hidden="true">*</span></label>
          <?php $err_msg( 'device' ); ?>
          <select id="f-device" name="device" required<?php echo $err_attr( 'device' ); ?>>
            <option value="">Choose a device…</option>
            <?php foreach ( dobbel_form_options( 'device' ) as $key => $label ) : ?>
              <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $v['device'], $key ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </fieldset>

    <!-- Step 2 -->
    <fieldset class="step">
      <legend><span class="step__num">2</span> Tell us what happened</legend>
      <div class="field<?php echo isset( $errors['message'] ) ? ' has-error' : ''; ?>">
        <label class="label" for="f-message">Your message <span class="req" aria-hidden="true">*</span></label>
        <p class="hint" id="h-message">What did you do, what did you expect, and what happened instead?</p>
        <?php $err_msg( 'message' ); ?>
        <textarea id="f-message" name="message" rows="6" minlength="<?php echo (int) DOBBEL_MSG_MIN; ?>" maxlength="<?php echo (int) DOBBEL_MSG_MAX; ?>" required aria-describedby="h-message<?php echo isset( $errors['message'] ) ? ' e-message' : ''; ?> c-message"<?php echo isset( $errors['message'] ) ? ' aria-invalid="true"' : ''; ?>><?php echo esc_textarea( $v['message'] ); ?></textarea>
        <p class="counter" id="c-message" aria-live="polite" data-min="<?php echo (int) DOBBEL_MSG_MIN; ?>" data-max="<?php echo (int) DOBBEL_MSG_MAX; ?>">Minimum <?php echo (int) DOBBEL_MSG_MIN; ?> characters.</p>
      </div>
    </fieldset>

    <!-- Step 3 -->
    <fieldset class="step">
      <legend><span class="step__num">3</span> How can we reach you?</legend>
      <div class="field-row">
        <div class="field<?php echo isset( $errors['your_name'] ) ? ' has-error' : ''; ?>">
          <label class="label" for="f-name">Name <span class="opt">(optional)</span></label>
          <?php $err_msg( 'your_name' ); ?>
          <input type="text" id="f-name" name="your_name" value="<?php echo esc_attr( $v['name'] ); ?>" autocomplete="name" maxlength="80"<?php echo $err_attr( 'your_name' ); ?>>
        </div>
        <div class="field<?php echo isset( $errors['email'] ) ? ' has-error' : ''; ?>">
          <label class="label" for="f-email">Email <span class="req" aria-hidden="true">*</span></label>
          <?php $err_msg( 'email' ); ?>
          <input type="email" id="f-email" name="email" value="<?php echo esc_attr( $v['email'] ); ?>" autocomplete="email" inputmode="email" placeholder="name@mail.nl" required<?php echo $err_attr( 'email' ); ?>>
        </div>
      </div>

      <div class="field<?php echo isset( $errors['consent'] ) ? ' has-error' : ''; ?>">
        <?php $err_msg( 'consent' ); ?>
        <label class="check" for="f-consent">
          <input type="checkbox" id="f-consent" name="consent" value="1" <?php checked( $v['consent'] ); ?> required<?php echo $err_attr( 'consent' ); ?>>
          <span>MugStudio may use my email address to reply to this request. We delete it after 12 months. <span class="req" aria-hidden="true">*</span>
          <?php
          $privacy = get_page_by_path( 'privacy' );
          if ( $privacy ) {
              printf( '<a href="%s">Privacy policy</a>', esc_url( get_permalink( $privacy ) ) );
          }
          ?>
          </span>
        </label>
      </div>
    </fieldset>

    <div class="form-foot">
      <button type="submit" class="btn" data-sending="Sending…">Send request</button>
      <p class="muted small">We reply within 3 working days.</p>
    </div>
  </form>
<?php endif; ?>

</section>
