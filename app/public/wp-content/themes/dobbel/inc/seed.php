<?php
/**
 * One-time setup of the MugStudio site: pages, a first post, pretty links and the reading settings.
 * Runs once (see the `dobbel_seed_version` option). Bump DOBBEL_SEED_VERSION to run it again;
 * it never overwrites or duplicates anything that already exists.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const DOBBEL_SEED_VERSION = 2;

function dobbel_seed_page( $slug, $title, $content = '' ) {
    $existing = get_page_by_path( $slug );
    if ( $existing ) {
        return (int) $existing->ID;
    }
    $id = wp_insert_post( array(
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_name'    => $slug,
        'post_title'   => $title,
        'post_content' => $content,
    ) );
    return is_wp_error( $id ) ? 0 : (int) $id;
}

function dobbel_seed() {
    if ( (int) get_option( 'dobbel_seed_version' ) >= DOBBEL_SEED_VERSION ) {
        return;
    }
    if ( 'dobbel' !== get_template() ) {
        return; // only when this theme is the active one
    }
    require_once get_template_directory() . '/inc/legal.php';

    // Site name
    $name = get_option( 'blogname' );
    if ( ! $name || in_array( strtolower( $name ), array( 'test', 'my wordpress site', 'wordpress' ), true ) ) {
        update_option( 'blogname', 'MugStudio' );
        update_option( 'blogdescription', 'Small games. Big fun.' );
    }

    // Pretty links (/support, /news/...) when none are set yet
    global $wp_rewrite;
    if ( ! get_option( 'permalink_structure' ) ) {
        $wp_rewrite->set_permalink_structure( '/%postname%/' );
    }

    // Pages
    $home    = dobbel_seed_page( 'home', 'Home' );
    $news    = dobbel_seed_page( 'news', 'News' );
    dobbel_seed_page( 'support', 'Support' );
    dobbel_seed_page( 'privacy', 'Privacy Policy', dobbel_legal_privacy() );
    dobbel_seed_page( 'terms', 'Terms of Service', dobbel_legal_terms() );

    if ( $home && $news ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $home );
        update_option( 'page_for_posts', $news );
    }

    // The default sample post goes to the trash if it is still untouched
    $hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
    if ( $hello && 0 === strpos( $hello->post_content, '<!-- wp:paragraph -->' ) && false !== strpos( $hello->post_content, 'Welcome to WordPress' ) ) {
        wp_trash_post( $hello->ID );
    }

    // First post, only when the site has no posts at all
    $counts = wp_count_posts( 'post' );
    if ( 0 === (int) $counts->publish ) {
        wp_insert_post( array(
            'post_status'  => 'publish',
            'post_title'   => 'Welcome to MugStudio',
            'post_content' => "<!-- wp:paragraph -->\n<p>Hi! We are MugStudio, a small studio that makes friendly, colourful games. This is where we share news, release notes and stories about how our games are made.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Our first game is <strong>Surf Tycoon</strong>: an idle game about running your own water-sports spot, from surfing and skimboarding to kitesurfing and offshore sailing. More games are on the way.</p>\n<!-- /wp:paragraph -->",
        ) );
    }

    // v2: the contact address changed to support@mugstudio.nl; refresh legal pages that still carry the old one
    foreach ( array( 'privacy' => 'dobbel_legal_privacy', 'terms' => 'dobbel_legal_terms' ) as $slug => $fn ) {
        $page = get_page_by_path( $slug );
        if ( $page && false !== strpos( $page->post_content, 'woutschiks@gmail.com' ) ) {
            wp_update_post( array( 'ID' => $page->ID, 'post_content' => $fn() ) );
        }
    }

    update_option( 'dobbel_seed_version', DOBBEL_SEED_VERSION );
    flush_rewrite_rules( false );
}
add_action( 'wp_loaded', 'dobbel_seed' );
