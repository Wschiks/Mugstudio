<?php
/**
 * Dobbel theme functions (MugStudio website).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Help address: Support page, legal pages, FAQ. Override with the `dobbel_contact_email` filter. */
function dobbel_contact_email() {
    return apply_filters( 'dobbel_contact_email', 'support@mugstudio.nl' );
}

/** General address (business, press): shown in the footer. Override with the `dobbel_info_email` filter. */
function dobbel_info_email() {
    return apply_filters( 'dobbel_info_email', 'info@mugstudio.nl' );
}

/**
 * Store links per game. Leave a URL empty and the button shows "Coming soon".
 * Fill these in once a game is live in the App Store / Google Play.
 */
function dobbel_game_links( $game ) {
    $links = array(
        'surf-tycoon' => array(
            'ios'     => 'https://apps.apple.com/nl/app/surf-tycoon/id6815999467',
            'android' => '', // e.g. https://play.google.com/store/apps/details?id=nl.mugstudio.surftycoon
        ),
        'dobbelen'    => array(
            'ios'     => '',
            'android' => '',
        ),
    );
    $links = apply_filters( 'dobbel_game_links', $links );
    return isset( $links[ $game ] ) ? $links[ $game ] : array( 'ios' => '', 'android' => '' );
}

/** URL of a file in the theme's assets folder. */
function dobbel_asset( $path ) {
    return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

/** Browser-tab icon: the black mosquito. */
function dobbel_favicon() {
    if ( has_site_icon() ) {
        return;
    }
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><g fill="#0a0a0a"><ellipse cx="21" cy="11" rx="3.4" ry="10" transform="rotate(-32 21 11)"/><ellipse cx="29" cy="10" rx="3.2" ry="9" transform="rotate(14 29 10)"/><ellipse cx="15" cy="27.5" rx="11" ry="4.4" transform="rotate(-24 15 27.5)"/><circle cx="27" cy="24" r="5"/><circle cx="34" cy="25" r="3.6"/></g><path d="M37 26.5 46 33M22 28l-8 8-1 7M26 29l-3 10-1 6M29 28l6 8 3 7" fill="none" stroke="#0a0a0a" stroke-width="2" stroke-linecap="round"/></svg>';
    echo '<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,' . rawurlencode( $svg ) . '">' . "\n";
    // Safari ignores SVG icons, so also offer a PNG.
    echo '<link rel="icon" type="image/png" sizes="512x512" href="' . esc_url( dobbel_asset( 'favicon.png' ) ) . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url( dobbel_asset( 'favicon.png' ) ) . '">' . "\n";
}
add_action( 'wp_head', 'dobbel_favicon');

/** Answer /favicon.ico with the mosquito instead of the WordPress logo (browsers ask for it by default). */
function dobbel_serve_favicon_ico() {
    if ( has_site_icon() ) {
        return;
    }
    $file = get_template_directory() . '/assets/favicon.png';
    if ( is_readable( $file ) ) {
        header( 'Content-Type: image/png' );
        header( 'Cache-Control: public, max-age=86400' );
        readfile( $file );
        exit;
    }
}
add_action( 'do_faviconico', 'dobbel_serve_favicon_ico' );

function dobbel_setup() {
    // lets WordPress build the <title> tag for us (used via wp_head() in header.php)
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array( 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );

    add_image_size( 'dobbel-card', 720, 405, true );

    register_nav_menus( array(
        'primary' => __( 'Main menu', 'dobbel' ),
        'footer'  => __( 'Footer menu', 'dobbel' ),
    ) );
}
add_action( 'after_setup_theme', 'dobbel_setup' );

function dobbel_enqueue_styles() {
    wp_enqueue_style(
        'dobbel-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'dobbel_enqueue_styles' );

/** Short excerpts. */
add_filter( 'excerpt_length', function () { return 28; } );
add_filter( 'excerpt_more', function () { return '…'; } );

/** The site has no search: a search URL (?s=...) shows the 404 page. */
function dobbel_disable_search() {
    if ( is_search() ) {
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
    }
}
add_action( 'template_redirect', 'dobbel_disable_search' );

/** Archive titles without the "Category:" / "Tag:" prefix. */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/** Fallback menus (used until a menu is assigned under Appearance > Menus). */
function dobbel_menu_items( $location ) {
    $support = get_page_by_path( 'support' );
    $posts   = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/news/' );
    $items   = array(
        array( __( 'Home', 'dobbel' ), home_url( '/' ) ),
        array( __( 'Games', 'dobbel' ), home_url( '/#games' ) ),
        array( __( 'News', 'dobbel' ), $posts ),
        array( __( 'Support', 'dobbel' ), $support ? get_permalink( $support ) : home_url( '/support/' ) ),
    );
    if ( 'footer' === $location ) {
        $items = array( $items[2], $items[3] );
        foreach ( array( 'privacy' => __( 'Privacy', 'dobbel' ), 'terms' => __( 'Terms', 'dobbel' ) ) as $slug => $label ) {
            $p = get_page_by_path( $slug );
            if ( $p ) {
                $items[] = array( $label, get_permalink( $p ) );
            }
        }
    }
    return $items;
}

function dobbel_fallback_menu( $location, $class ) {
    echo '<ul class="' . esc_attr( $class ) . '">';
    foreach ( dobbel_menu_items( $location ) as $item ) {
        $current = untrailingslashit( $item[1] ) === untrailingslashit( home_url( add_query_arg( array() ) ) ) ? ' aria-current="page"' : '';
        printf( '<li><a href="%s"%s>%s</a></li>', esc_url( $item[1] ), $current, esc_html( $item[0] ) );
    }
    echo '</ul>';
}

/** "Posted on <date> · in <category>" line. */
function dobbel_post_meta() {
    echo '<div class="meta">';
    printf( '<time datetime="%s">%s</time>', esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() ) );
    $cats = get_the_category_list( ', ' );
    if ( $cats ) {
        echo '<span>' . $cats . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- core escapes
    }
    echo '</div>';
}

/** Inline mug icon. */
function dobbel_logo_svg() {
    get_template_part( 'template-parts/logo' );
}

require get_template_directory() . '/inc/seed.php';
