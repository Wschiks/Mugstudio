<?php
/**
 * Terms and Privacy text for Surf Tycoon (copied from the game's legal texts, contact filled in).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function dobbel_legal_contact_sentence() {
    $mail = dobbel_contact_email();
    return 'Questions? Contact MugStudio at <a href="mailto:' . esc_attr( $mail ) . '">' . esc_html( $mail ) . '</a>.';
}

function dobbel_legal_terms() {
    return '<p><em>Last updated 21 September 2026.</em></p>
<h2>1. About this game</h2><p>Surf Tycoon is a free idle game about running a water-sports spot. By playing it you agree to these terms. If you do not agree, please do not play.</p>
<h2>2. Free to play and the shop</h2><p>The game is free to play. In the phone apps you can choose to watch video ads for bonuses (gems, coins x2), and you can buy optional items in the shop: packs of gems (which you can buy as often as you like) and two one-time items, &ldquo;Remove ads&rdquo; and &ldquo;Coins x5&rdquo;. Prices are shown in the shop in your own currency. Purchases are paid through the App Store or Google Play, are for your own use on your own account, and the one-time items can be restored with &ldquo;Restore purchases&rdquo; in the shop. Gems that were bought and spent cannot be restored or refunded by us.</p>
<p>The Surf Club is a monthly subscription (price shown in the shop, in your own currency): it gives coins x2, ad rewards without ads, 3 gems a day and 2 extra hours of away time for as long as it is active. It renews automatically every month until you cancel it in the settings of your App Store or Google Play account (cancel at least 24 hours before the renewal date to avoid the next charge); you keep the benefits until the paid period ends. We cannot cancel it for you. Refunds follow the rules of the store you bought from. Coins, gems and everything else in the game have no value outside the game and cannot be exchanged for money.</p>
<h2>3. Your progress</h2><p>Your progress is saved on your own device. If you clear the data of the app or the browser, or use &ldquo;Start over&rdquo; in the menu, your progress is gone. We cannot restore lost progress.</p>
<h2>4. Fair play</h2><p>You may play the game for your own enjoyment. Please do not copy, sell or pass off the game as your own, and do not try to break or misuse it.</p>
<h2>5. No promises</h2><p>The game is provided &ldquo;as is&rdquo;. It is still being built, so things can change, get rebalanced or contain mistakes. We do not promise that it will always work or be available, and we are not responsible for any loss that comes from playing it, as far as the law allows.</p>
<h2>6. Changes</h2><p>We may change the game or these terms. When we do, the date above changes. If you keep playing after a change, you accept the new terms.</p>
<h2>7. Contact</h2><p>' . dobbel_legal_contact_sentence() . '</p>';
}

function dobbel_legal_privacy() {
    return '<p><em>Last updated 21 September 2026.</em></p>
<h2>The short version</h2><p>Surf Tycoon does not collect any personal data itself and has no account or server. Everything you build stays on your device. The only exception is the optional video ad in the phone apps, which is shown by Google AdMob (see &ldquo;Ads&rdquo;).</p>
<h2>What is stored</h2><p>Your game progress (coins, zones, upgrades and so on), the time it was last saved, and your sound setting. This is stored on your device only, in the local storage of the app or browser.</p>
<h2>What is not collected</h2><p>The game itself collects no name, no email address, no account, no location, no contacts and no analytics. The game does not connect to any server of ours to run.</p>
<h2>Ads</h2><p>The phone apps have optional rewarded video ads (the &ldquo;Watch ad&rdquo; button and the ad streak in the shop). They are provided by Google AdMob. When you watch one, Google and its ad partners may use your device advertising ID and collect device and usage information to show and measure ads and to prevent fraud, as described in <a href="https://policies.google.com/privacy">Google&rsquo;s privacy policy</a>. On iPhone the app first asks whether apps may track you: if you say no, you still get ads but they are not personalised. In the European Economic Area, the UK and Switzerland a consent form asks about your choices before any ad is loaded. If you never watch an ad, no ad is requested. We do not receive any of this data.</p>
<h2>Purchases</h2><p>Payments for &ldquo;Remove ads&rdquo; and &ldquo;Coins x5&rdquo; are handled entirely by Apple or Google. We never see your name, card or other payment details. The game only learns whether the store says you own an item, and remembers that on your device.</p>
<h2>Third parties</h2><p>Apart from Google AdMob for the optional ads and the App Store or Google Play for purchases, there are no third-party services in the game. If you play in a web browser, the website that hosts the game can see normal technical information (like your IP address) in its own logs, as with any website.</p>
<h2>Your choices</h2><p>You can delete all data the game stores at any time with &ldquo;Start over&rdquo; in the menu, or by clearing the site or app data.</p>
<h2>Children</h2><p>The game does not ask for any personal information. Ads are optional and only start when you choose to watch one.</p>
<h2>Changes and contact</h2><p>If this ever changes (for example when online features are added), this policy will be updated before it does. ' . dobbel_legal_contact_sentence() . '</p>';
}
