<?php
/** App Store / Google Play buttons for one game. Pass array( 'game' => 'surf-tycoon' ). */
$links = dobbel_game_links( isset( $args['game'] ) ? $args['game'] : '' );
if ( $links['ios'] ) {
    printf( '<a class="btn" href="%s">%s</a>', esc_url( $links['ios'] ), esc_html__( 'App Store', 'dobbel' ) );
}
if ( $links['android'] ) {
    printf( '<a class="btn" href="%s">%s</a>', esc_url( $links['android'] ), esc_html__( 'Google Play', 'dobbel' ) );
}
if ( ! $links['ios'] && ! $links['android'] ) {
    printf( '<span class="btn btn--disabled">%s</span>', esc_html__( 'Coming soon to iPhone and Android', 'dobbel' ) );
}
