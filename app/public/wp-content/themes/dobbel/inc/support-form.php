<?php
/**
 * Support request form on the Support page.
 *
 * Flow: choose a game -> choose a topic -> choose a device -> describe the problem
 * -> fill in your email -> agree -> send -> confirmation with a reference number.
 *
 * Every request is saved as a "Support request" (wp-admin > Support requests)
 * and mailed to dobbel_contact_email(). On a mistake the form comes back with
 * everything still filled in and a message per field that says how to fix it.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Lowest / highest length of the message. */
const DOBBEL_MSG_MIN = 20;
const DOBBEL_MSG_MAX = 1500;

/** The choices in the form. Key = stored value, value = label. */
function dobbel_form_options( $field ) {
    $options = array(
        'game'   => array(
            'surf-tycoon' => 'Surf Tycoon',
            'dobbelen'    => 'Dobbelen',
            'other'       => 'Something else / general',
        ),
        'topic'  => array(
            'bug'      => 'Bug or crash',
            'purchase' => 'Purchase or Surf Club subscription',
            'progress' => 'Lost progress',
            'question' => 'Question about playing',
            'idea'     => 'Idea or feedback',
        ),
        'device' => array(
            'iphone'  => 'iPhone or iPad',
            'android' => 'Android phone or tablet',
            'browser' => 'Web browser (computer)',
            'none'    => 'Does not matter for my question',
        ),
    );
    return isset( $options[ $field ] ) ? $options[ $field ] : array();
}

/** Saved requests show up in wp-admin, not on the website. */
function dobbel_register_support_requests() {
    register_post_type( 'support_request', array(
        'labels'          => array(
            'name'          => 'Support requests',
            'singular_name' => 'Support request',
            'menu_name'     => 'Support requests',
            'all_items'     => 'All requests',
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-email-alt',
        'menu_position'   => 25,
        'supports'        => array( 'title', 'editor', 'custom-fields' ),
        'capability_type' => 'post',
        'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
        'map_meta_cap'    => true,
    ) );
}
add_action( 'init', 'dobbel_register_support_requests' );

/** Readable value of a saved field (label instead of key). */
function dobbel_request_field( $post_id, $field ) {
    $value = get_post_meta( $post_id, '_' . $field, true );
    $opts  = dobbel_form_options( $field );
    return isset( $opts[ $value ] ) ? $opts[ $value ] : $value;
}

/** Extra columns in the list of requests. */
add_filter( 'manage_support_request_posts_columns', function ( $cols ) {
    return array(
        'cb'     => $cols['cb'],
        'title'  => 'Reference',
        'game'   => 'Game',
        'topic'  => 'Topic',
        'device' => 'Device',
        'name'   => 'Name',
        'email'  => 'Email',
        'date'   => $cols['date'],
    );
} );
add_action( 'manage_support_request_posts_custom_column', function ( $col, $post_id ) {
    $value = dobbel_request_field( $post_id, $col );
    if ( 'email' === $col && $value ) {
        printf( '<a href="mailto:%1$s">%1$s</a>', esc_html( $value ) );
        return;
    }
    echo '' === $value ? '&mdash;' : esc_html( $value );
}, 10, 2 );

/** Box with all details (and a reply button) when you open a request. */
add_action( 'add_meta_boxes_support_request', function () {
    add_meta_box( 'dobbel-request-details', 'Request details', function ( $post ) {
        $email = get_post_meta( $post->ID, '_email', true );
        $ref   = get_post_meta( $post->ID, '_reference', true );
        echo '<table class="widefat striped" style="border:0"><tbody>';
        foreach ( array( 'reference' => 'Reference', 'game' => 'Game', 'topic' => 'Topic', 'device' => 'Device', 'name' => 'Name', 'email' => 'Email' ) as $key => $label ) {
            $value = dobbel_request_field( $post->ID, $key );
            printf( '<tr><th style="width:140px">%s</th><td>%s</td></tr>', esc_html( $label ), '' === $value ? '&mdash;' : esc_html( $value ) );
        }
        echo '</tbody></table>';
        if ( $email ) {
            printf(
                '<p><a class="button button-primary" href="mailto:%s?subject=%s">Reply by email</a></p>',
                esc_attr( $email ),
                rawurlencode( 'Re: your MugStudio request ' . $ref )
            );
        }
        echo '<p class="description">Requests are deleted automatically 12 months after they were sent (see the privacy policy).</p>';
    }, 'support_request', 'normal', 'high' );
} );

/** Delete requests older than 12 months, once a day (promised in the privacy policy). */
function dobbel_purge_support_requests() {
    $old = get_posts( array(
        'post_type'      => 'support_request',
        'post_status'    => 'any',
        'fields'         => 'ids',
        'posts_per_page' => 200,
        'date_query'     => array( array( 'before' => '12 months ago' ) ),
    ) );
    foreach ( $old as $id ) {
        wp_delete_post( $id, true );
    }
}
add_action( 'dobbel_purge_support_requests', 'dobbel_purge_support_requests' );
add_action( 'init', function () {
    if ( ! wp_next_scheduled( 'dobbel_purge_support_requests' ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'dobbel_purge_support_requests' );
    }
} );
add_action( 'switch_theme', function () {
    wp_clear_scheduled_hook( 'dobbel_purge_support_requests' );
} );

/**
 * State of the form for this page view: values, errors, sent reference.
 * Filled by dobbel_handle_support_form() and read by the template part.
 */
function dobbel_form_state( $set = null ) {
    static $state = array( 'values' => array(), 'errors' => array() );
    if ( null !== $set ) {
        $state = $set;
    }
    return $state;
}

/** Check the posted data. Returns array( values, errors ). */
function dobbel_validate_support_form( $raw ) {
    $v = array(
        'game'    => isset( $raw['game'] ) ? sanitize_key( wp_unslash( $raw['game'] ) ) : '',
        'topic'   => isset( $raw['topic'] ) ? sanitize_key( wp_unslash( $raw['topic'] ) ) : '',
        'device'  => isset( $raw['device'] ) ? sanitize_key( wp_unslash( $raw['device'] ) ) : '',
        'message' => isset( $raw['message'] ) ? trim( sanitize_textarea_field( wp_unslash( $raw['message'] ) ) ) : '',
        'name'    => isset( $raw['your_name'] ) ? trim( sanitize_text_field( wp_unslash( $raw['your_name'] ) ) ) : '',
        'email'   => isset( $raw['email'] ) ? trim( sanitize_text_field( wp_unslash( $raw['email'] ) ) ) : '',
        'consent' => ! empty( $raw['consent'] ),
    );
    $e = array();

    if ( ! isset( dobbel_form_options( 'game' )[ $v['game'] ] ) ) {
        $e['game'] = 'Choose the game your question is about.';
    }
    if ( ! isset( dobbel_form_options( 'topic' )[ $v['topic'] ] ) ) {
        $e['topic'] = 'Choose what your question is about.';
    }
    if ( ! isset( dobbel_form_options( 'device' )[ $v['device'] ] ) ) {
        $e['device'] = 'Choose the device you play on, or "Does not matter for my question".';
    }

    $len = mb_strlen( $v['message'] );
    if ( 0 === $len ) {
        $e['message'] = 'Describe your question or problem.';
    } elseif ( $len < DOBBEL_MSG_MIN ) {
        /* translators: 1: minimum, 2: current length */
        $e['message'] = sprintf( 'Write at least %1$d characters so we understand the problem (you have %2$d). For example: what did you do, and what happened?', DOBBEL_MSG_MIN, $len );
    } elseif ( $len > DOBBEL_MSG_MAX ) {
        $e['message'] = sprintf( 'Keep it under %1$d characters (you have %2$d).', DOBBEL_MSG_MAX, $len );
    }

    if ( mb_strlen( $v['name'] ) > 80 ) {
        $e['your_name'] = 'Keep your name under 80 characters.';
    }

    if ( '' === $v['email'] ) {
        $e['email'] = 'Fill in your email address so we can reply, for example name@mail.nl.';
    } elseif ( ! is_email( $v['email'] ) ) {
        $e['email'] = 'This email address is not complete. Use the form name@mail.nl.';
    }

    if ( ! $v['consent'] ) {
        $e['consent'] = 'Tick the box so we are allowed to use your email address to reply.';
    }

    return array( $v, $e );
}

/** A short reference the user can quote, e.g. MS-4821. */
function dobbel_new_reference() {
    $n = (int) get_option( 'dobbel_support_counter', 1000 ) + 1;
    update_option( 'dobbel_support_counter', $n, false );
    return 'MS-' . $n;
}

/** Handle a posted form on the Support page. */
function dobbel_handle_support_form() {
    if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || empty( $_POST['dobbel_support'] ) || ! is_page( 'support' ) ) {
        return;
    }

    // Spam trap: a hidden field that people never fill in.
    if ( ! empty( $_POST['website'] ) ) {
        wp_safe_redirect( add_query_arg( 'sent', 'MS-0', get_permalink() ) . '#contact' );
        exit;
    }

    list( $values, $errors ) = dobbel_validate_support_form( $_POST );

    if ( ! isset( $_POST['_dobbel_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_dobbel_nonce'] ), 'dobbel_support' ) ) {
        $errors = array( 'form' => 'The form was open too long and has expired. Your text is still here: press "Send request" again.' ) + $errors;
    }

    if ( $errors ) {
        dobbel_form_state( array( 'values' => $values, 'errors' => $errors ) );
        status_header( 422 );
        return;
    }

    $ref    = dobbel_new_reference();
    $labels = array(
        'game'   => dobbel_form_options( 'game' )[ $values['game'] ],
        'topic'  => dobbel_form_options( 'topic' )[ $values['topic'] ],
        'device' => dobbel_form_options( 'device' )[ $values['device'] ],
    );

    $post_id = wp_insert_post( array(
        'post_type'    => 'support_request',
        'post_status'  => 'private',
        'post_title'   => $ref . ' · ' . $labels['game'] . ' · ' . $labels['topic'],
        'post_content' => $values['message'],
    ) );
    if ( $post_id && ! is_wp_error( $post_id ) ) {
        foreach ( array( 'game', 'topic', 'device', 'name', 'email' ) as $key ) {
            update_post_meta( $post_id, '_' . $key, $values[ $key ] );
        }
        update_post_meta( $post_id, '_reference', $ref );
    }

    $body = "Reference: $ref\nGame: {$labels['game']}\nTopic: {$labels['topic']}\nDevice: {$labels['device']}\nName: {$values['name']}\nEmail: {$values['email']}\n\n{$values['message']}\n";
    wp_mail(
        dobbel_contact_email(),
        "[$ref] {$labels['game']}: {$labels['topic']}",
        $body,
        array( 'Reply-To: ' . $values['email'] )
    );

    // Remember the summary for the confirmation (10 minutes), then redirect so a refresh does not send it twice.
    set_transient( 'dobbel_sent_' . $ref, array( 'values' => $values, 'labels' => $labels ), 10 * MINUTE_IN_SECONDS );
    wp_safe_redirect( add_query_arg( 'sent', $ref, get_permalink() ) . '#contact' );
    exit;
}
add_action( 'template_redirect', 'dobbel_handle_support_form' );

/** Script for the form (character counter, "Sending…" state). Only on the Support page. */
function dobbel_enqueue_support_form() {
    if ( is_page( 'support' ) ) {
        wp_enqueue_script(
            'dobbel-support-form',
            get_template_directory_uri() . '/assets/js/support-form.js',
            array(),
            wp_get_theme()->get( 'Version' ),
            array( 'strategy' => 'defer', 'in_footer' => true )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'dobbel_enqueue_support_form' );
