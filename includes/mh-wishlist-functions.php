<?php
/**
 * MH Plug WooCommerce Wishlist - Core Functions
 *
 * Handles all AJAX actions, cookie-based guest identification,
 * and WooCommerce hooks for the wishlist feature.
 *
 * @package MH_Plug
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ============================================================
// COOKIE-BASED GUEST IDENTIFICATION
// Replaces PHP sessions (which break page caching) with a
// secure, first-party cookie that persists for 30 days.
// ============================================================

/**
 * Return (or lazily create) the guest UUID stored in a cookie.
 *
 * The cookie is written on 'init' (before output) so headers are
 * still available. On subsequent AJAX calls the cookie value is
 * already present in $_COOKIE, so we read it directly.
 *
 * @return string UUID v4 string.
 */
function mh_wishlist_get_guest_cookie_id() {
    $cookie_name = 'mh_wishlist_guest_id';

    if ( isset( $_COOKIE[ $cookie_name ] ) ) {
        // Validate it looks like a UUID before trusting it.
        $value = sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) );
        if ( preg_match( '/^[0-9a-f\-]{36}$/', $value ) ) {
            return $value;
        }
    }

    // Generate a fresh UUID and persist it.
    $uuid = wp_generate_uuid4();

    if ( ! headers_sent() ) {
        // 30 days, same-site Strict, no JS access.
        setcookie(
            $cookie_name,
            $uuid,
            [
                'expires'  => time() + ( 30 * DAY_IN_SECONDS ),
                'path'     => COOKIEPATH ?: '/',
                'domain'   => COOKIE_DOMAIN ?: '',
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Strict',
            ]
        );
        // Make it readable in the current request without a page reload.
        $_COOKIE[ $cookie_name ] = $uuid;
    }

    return $uuid;
}

/**
 * Initialise the guest cookie early (before wp_head) so headers
 * are still available for setcookie(). This guarantees the UUID
 * exists by the time any wishlist function is called.
 */
function mh_wishlist_init_guest_cookie() {
    if ( ! is_user_logged_in() ) {
        mh_wishlist_get_guest_cookie_id();
    }
}
add_action( 'init', 'mh_wishlist_init_guest_cookie' );

// ============================================================
// HELPER FUNCTIONS
// ============================================================

/**
 * Get the current visitor's identifier (user ID or session ID).
 *
 * @return array { type: 'user'|'session', id: int|string }
 */
function mh_wishlist_get_identifier() {
    $user_id = get_current_user_id();
    if ( $user_id > 0 ) {
        return [ 'type' => 'user', 'id' => $user_id ];
    }
    return [ 'type' => 'session', 'id' => mh_wishlist_get_guest_cookie_id() ];
}

/**
 * Fetch all product IDs in the wishlist for the current visitor.
 *
 * @return int[] Array of product IDs.
 */
function mh_wishlist_get_items() {
    global $wpdb;
    $table = $wpdb->prefix . 'mh_woocommerce_wishlist';
    $ident = mh_wishlist_get_identifier();

    // Try to get from cache first
    $cache_key = 'mh_wishlist_items_' . $ident['type'] . '_' . $ident['id'];
    $cached = wp_cache_get( $cache_key, 'mh_wishlist' );
    if ( false !== $cached ) {
        return $cached;
    }

    if ( $ident['type'] === 'user' ) {
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT product_id, variation_id FROM {$table} WHERE user_id = %d",
                (int) $ident['id']
            )
        );
    } else {
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT product_id, variation_id FROM {$table} WHERE session_id = %s AND user_id = 0",
                sanitize_text_field( $ident['id'] )
            )
        );
    }

    $items = $results ? array_column( $results, 'product_id' ) : [];
    wp_cache_set( $cache_key, $items, 'mh_wishlist', HOUR_IN_SECONDS );

    return $items;
}

/**
 * Clear wishlist cache for the current visitor.
 */
function mh_wishlist_clear_cache() {
    $ident = mh_wishlist_get_identifier();
    $cache_key = 'mh_wishlist_items_' . $ident['type'] . '_' . $ident['id'];
    wp_cache_delete( $cache_key, 'mh_wishlist' );
}

/**
 * Count wishlist items for the current visitor.
 *
 * @return int
 */
function mh_wishlist_count() {
    return count( mh_wishlist_get_items() );
}

/**
 * Check if a product is already in the wishlist.
 *
 * @param int $product_id
 * @return bool
 */
function mh_wishlist_has_product( $product_id ) {
    return in_array( (int) $product_id, mh_wishlist_get_items(), true );
}

// ============================================================
// AJAX HANDLERS
// ============================================================

/**
 * AJAX: Add a product to the wishlist.
 * Handles both logged-in users and guests.
 */
function mh_wishlist_ajax_add() {
    // 🚀 FIX: Look for 'security' instead of 'nonce' to match JavaScript
    if ( ! check_ajax_referer( 'mh_wishlist_nonce', 'security', false ) ) {
        wp_send_json_error( [ 'message' => __( 'Security check failed.', 'mh-plug' ) ], 403 );
    }

    $product_id   = isset( $_POST['product_id'] )   ? absint( $_POST['product_id'] )   : 0;
    $variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;

    if ( ! $product_id ) {
        wp_send_json_error( [ 'message' => __( 'Invalid product.', 'mh-plug' ) ], 400 );
    }

    global $wpdb;
    $table = $wpdb->prefix . 'mh_woocommerce_wishlist';
    $ident = mh_wishlist_get_identifier();

    // Avoid duplicates
    if ( $ident['type'] === 'user' ) {
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE user_id = %d AND product_id = %d",
                (int) $ident['id'],
                $product_id
            )
        );
    } else {
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE session_id = %s AND user_id = 0 AND product_id = %d",
                sanitize_text_field( $ident['id'] ),
                $product_id
            )
        );
    }

    if ( $exists ) {
        wp_send_json_success( [
            'status'  => 'already_added',
            'count'   => mh_wishlist_count(),
            'message' => __( 'Already in wishlist.', 'mh-plug' ),
        ] );
    }

    $inserted = $wpdb->insert(
        $table,
        [
            'user_id'      => $ident['type'] === 'user' ? (int) $ident['id'] : 0,
            'session_id'   => $ident['type'] === 'session' ? sanitize_text_field( $ident['id'] ) : '',
            'product_id'   => $product_id,
            'variation_id' => $variation_id,
            'date_added'   => current_time( 'mysql' ),
        ],
        [ '%d', '%s', '%d', '%d', '%s' ]
    );

    if ( false === $inserted ) {
        wp_send_json_error( [ 'message' => __( 'Could not add to wishlist.', 'mh-plug' ) ], 500 );
    }

    mh_wishlist_clear_cache();

    wp_send_json_success( [
        'status'  => 'added',
        'count'   => mh_wishlist_count(),
        'message' => __( 'Added to wishlist!', 'mh-plug' ),
    ] );
}
add_action( 'wp_ajax_mh_wishlist_add',        'mh_wishlist_ajax_add' );
add_action( 'wp_ajax_nopriv_mh_wishlist_add', 'mh_wishlist_ajax_add' );

/**
 * AJAX: Remove a product from the wishlist.
 */
function mh_wishlist_ajax_remove() {
    // 🚀 FIX: Look for 'security'
    if ( ! check_ajax_referer( 'mh_wishlist_nonce', 'security', false ) ) {
        wp_send_json_error( [ 'message' => __( 'Security check failed.', 'mh-plug' ) ], 403 );
    }

    $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    if ( ! $product_id ) {
        wp_send_json_error( [ 'message' => __( 'Invalid product.', 'mh-plug' ) ], 400 );
    }

    global $wpdb;
    $table = $wpdb->prefix . 'mh_woocommerce_wishlist';
    $ident = mh_wishlist_get_identifier();

    if ( $ident['type'] === 'user' ) {
        $wpdb->delete(
            $table,
            [ 'user_id' => (int) $ident['id'], 'product_id' => $product_id ],
            [ '%d', '%d' ]
        );
    } else {
        $wpdb->delete(
            $table,
            [ 'session_id' => sanitize_text_field( $ident['id'] ), 'user_id' => 0, 'product_id' => $product_id ],
            [ '%s', '%d', '%d' ]
        );
    }

    mh_wishlist_clear_cache();

    wp_send_json_success( [
        'status'  => 'removed',
        'count'   => mh_wishlist_count(),
        'message' => __( 'Removed from wishlist.', 'mh-plug' ),
    ] );
}
add_action( 'wp_ajax_mh_wishlist_remove',        'mh_wishlist_ajax_remove' );
add_action( 'wp_ajax_nopriv_mh_wishlist_remove', 'mh_wishlist_ajax_remove' );

/**
 * AJAX: Unified toggle — adds if absent, removes if present.
 */
function mh_wishlist_ajax_toggle() {
    // 🚀 FIX: Look for 'security'
    if ( ! check_ajax_referer( 'mh_wishlist_nonce', 'security', false ) ) {
        wp_send_json_error( [ 'message' => __( 'Security check failed.', 'mh-plug' ) ], 403 );
    }

    $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    if ( ! $product_id ) {
        wp_send_json_error( [ 'message' => __( 'Invalid product.', 'mh-plug' ) ], 400 );
    }

    global $wpdb;
    $table = $wpdb->prefix . 'mh_woocommerce_wishlist';
    $ident = mh_wishlist_get_identifier();

    // ── Check if already in wishlist ──────────────────────────────────────────
    if ( $ident['type'] === 'user' ) {
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE user_id = %d AND product_id = %d",
                (int) $ident['id'],
                $product_id
            )
        );
    } else {
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE session_id = %s AND user_id = 0 AND product_id = %d",
                sanitize_text_field( $ident['id'] ),
                $product_id
            )
        );
    }

    if ( $exists ) {
        // ── REMOVE ────────────────────────────────────────────────────────────
        if ( $ident['type'] === 'user' ) {
            $wpdb->delete(
                $table,
                [ 'user_id' => (int) $ident['id'], 'product_id' => $product_id ],
                [ '%d', '%d' ]
            );
        } else {
            $wpdb->delete(
                $table,
                [ 'session_id' => sanitize_text_field( $ident['id'] ), 'user_id' => 0, 'product_id' => $product_id ],
                [ '%s', '%d', '%d' ]
            );
        }

        mh_wishlist_clear_cache();

        wp_send_json_success( [
            'status'  => 'removed',
            'added'   => false,
            'count'   => mh_wishlist_count(),
            'message' => __( 'Removed from wishlist.', 'mh-plug' ),
        ] );
    }

    // ── ADD ───────────────────────────────────────────────────────────────────
    $variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;

    $inserted = $wpdb->insert(
        $table,
        [
            'user_id'      => $ident['type'] === 'user' ? (int) $ident['id'] : 0,
            'session_id'   => $ident['type'] === 'session' ? sanitize_text_field( $ident['id'] ) : '',
            'product_id'   => $product_id,
            'variation_id' => $variation_id,
            'date_added'   => current_time( 'mysql' ),
        ],
        [ '%d', '%s', '%d', '%d', '%s' ]
    );

    if ( false === $inserted ) {
        wp_send_json_error( [ 'message' => __( 'Could not add to wishlist.', 'mh-plug' ) ], 500 );
    }

    mh_wishlist_clear_cache();

    wp_send_json_success( [
        'status'  => 'added',
        'added'   => true,
        'count'   => mh_wishlist_count(),
        'message' => __( 'Added to wishlist!', 'mh-plug' ),
    ] );
}
// 🚀 FIX: Sync Action names with the JS file (mh_wishlist_toggle)
add_action( 'wp_ajax_mh_wishlist_toggle',        'mh_wishlist_ajax_toggle' );
add_action( 'wp_ajax_nopriv_mh_wishlist_toggle', 'mh_wishlist_ajax_toggle' );

/**
 * AJAX: Return the full wishlist data (for dynamic rendering).
 */
function mh_wishlist_ajax_load() {
    // 🚀 FIX: Look for 'security'
    if ( ! check_ajax_referer( 'mh_wishlist_nonce', 'security', false ) ) {
        wp_send_json_error( [ 'message' => __( 'Security check failed.', 'mh-plug' ) ], 403 );
    }

    $items    = mh_wishlist_get_items();
    $products = [];

    foreach ( $items as $product_id ) {
        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            continue;
        }
        $products[] = [
            'id'           => $product_id,
            'name'         => $product->get_name(),
            'price'        => $product->get_price_html(),
            'stock_status' => $product->get_stock_status(),
            'image'        => get_the_post_thumbnail_url( $product_id, 'thumbnail' ),
            'cart_url'     => $product->add_to_cart_url(),
            'permalink'    => get_permalink( $product_id ),
        ];
    }

    wp_send_json_success( [
        'count'    => count( $products ),
        'products' => $products,
    ] );
}
add_action( 'wp_ajax_mh_wishlist_load',        'mh_wishlist_ajax_load' );
add_action( 'wp_ajax_nopriv_mh_wishlist_load', 'mh_wishlist_ajax_load' );

// ============================================================
// FRONTEND BUTTON INJECTION
// ============================================================

/**
 * Render the Add to Wishlist button HTML.
 */
function mh_wishlist_render_button( $product_id, $context = 'single' ) {
    $in_wishlist = mh_wishlist_has_product( $product_id );
    $btn_class   = 'mh-wishlist-btn mh-wishlist-ctx-' . esc_attr( $context );
    if ( $in_wishlist ) {
        $btn_class .= ' mh-added';
    }
    $icon_class = $in_wishlist ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
    $label      = $in_wishlist
        ? __( 'Remove from Wishlist', 'mh-plug' )
        : __( 'Add to Wishlist', 'mh-plug' );
    $nonce      = wp_create_nonce( 'mh_wishlist_nonce' );
    ?>
    <button
        class="<?php echo esc_attr( $btn_class ); ?>"
        data-product-id="<?php echo esc_attr( (int) $product_id ); ?>"
        data-nonce="<?php echo esc_attr( $nonce ); ?>"
        title="<?php echo esc_attr( $label ); ?>"
        aria-label="<?php echo esc_attr( $label ); ?>"
        type="button"
    >
        <i class="<?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i>
    </button>
    <?php
}

/**
 * Hook: Single product page — after Add to Cart button.
 */
function mh_wishlist_single_product_button() {
    global $product;
    if ( ! $product ) {
        return;
    }
    mh_wishlist_render_button( $product->get_id(), 'single' );
}
add_action( 'woocommerce_after_add_to_cart_button', 'mh_wishlist_single_product_button' );

/**
 * Hook: Shop archive / loop — after the shop loop item buttons.
 */
function mh_wishlist_loop_button() {
    global $product;
    if ( ! $product ) {
        return;
    }
    mh_wishlist_render_button( $product->get_id(), 'loop' );
}
add_action( 'woocommerce_after_shop_loop_item', 'mh_wishlist_loop_button', 15 );