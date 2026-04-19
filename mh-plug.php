<?php
/**
 * Plugin Name:       MH Plug
 * Description:       A custom Elementor addon with a dedicated dashboard for managing widgets and features.
 * Plugin URI:        https://your-website.com/
 * Version:           1.0.0
 * Author:            MHutin
 * Author URI:        https://mhutin.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mh-plug
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MH_PLUG_VERSION', '1.0.0');
define('MH_PLUG_PATH', plugin_dir_path(__FILE__));
define('MH_PLUG_URL', plugin_dir_url(__FILE__));

/**
 * Bulletproof File Loader
 */
if ( ! function_exists('mh_plug_safe_require') ) {
    function mh_plug_safe_require( $file_path ) {
        if ( file_exists( $file_path ) ) {
            require_once $file_path;
        }
    }
}

/**
 * Activation Hook: Create the Wishlist custom DB table.
 */
function mh_plug_create_wishlist_table() {
    global $wpdb;
    $table_name      = $wpdb->prefix . 'mh_woocommerce_wishlist';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id            BIGINT(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id       BIGINT(20)   UNSIGNED NOT NULL DEFAULT 0,
        session_id    VARCHAR(255) NOT NULL DEFAULT '',
        product_id    BIGINT(20)   UNSIGNED NOT NULL,
        variation_id  BIGINT(20)   UNSIGNED NOT NULL DEFAULT 0,
        date_added    DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY  (id),
        KEY user_id      (user_id),
        KEY session_id   (session_id(191)),
        KEY product_id   (product_id)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'mh_plug_create_wishlist_table' );


/**
 * Delay Loading Until WordPress is Ready
 */
add_action( 'plugins_loaded', 'mh_plug_initialize_all_files' );

function mh_plug_initialize_all_files() {
    
    // 1. Load Admin & Core Features
    mh_plug_safe_require( MH_PLUG_PATH . 'admin/admin-menu.php' );
    mh_plug_safe_require( MH_PLUG_PATH . 'includes/theme-builder-cpt.php' );
    mh_plug_safe_require( MH_PLUG_PATH . 'includes/theme-builder-display.php' );

    // 2. Load Elementor ONLY if Elementor is installed and active
    if ( did_action( 'elementor/loaded' ) ) {
        mh_plug_safe_require( MH_PLUG_PATH . 'elementor/elementor-loader.php' );
    }

    // 3. Load Dashboard Settings Features
    $settings = get_option('mh_plug_widgets_settings', []);

    if ( isset($settings['enable_menu_icons']) && $settings['enable_menu_icons'] ) {
        mh_plug_safe_require( MH_PLUG_PATH . 'includes/menu-icon-fields.php' );
    }

    mh_plug_safe_require( MH_PLUG_PATH . 'includes/age-gate/class-mh-plug-age-gate-admin.php' );
    mh_plug_safe_require( MH_PLUG_PATH . 'includes/age-gate/class-mh-plug-age-gate-meta.php' );
    mh_plug_safe_require( MH_PLUG_PATH . 'includes/age-gate/class-mh-plug-age-gate-front.php' );

    if ( isset($settings['enable_wc_wishlist']) && $settings['enable_wc_wishlist'] && class_exists('WooCommerce') ) {
        mh_plug_safe_require( MH_PLUG_PATH . 'includes/mh-wishlist-functions.php' );
    }
}

/**
 * Enqueue Frontend Scripts
 */
function mh_plug_enqueue_frontend_scripts() {
    wp_enqueue_style('slick-css',       MH_PLUG_URL . 'assets/slick/slick.css',       [], MH_PLUG_VERSION);
    wp_enqueue_style('slick-theme-css', MH_PLUG_URL . 'assets/slick/slick-theme.css', [], MH_PLUG_VERSION);
    wp_enqueue_script('slick-js',       MH_PLUG_URL . 'assets/slick/slick.min.js', ['jquery'], MH_PLUG_VERSION, true);

    wp_enqueue_style('mhi-icons',        MH_PLUG_URL . 'elementor/assets/css/style.css',         [], MH_PLUG_VERSION);
    wp_enqueue_style('fontawesome-icons',MH_PLUG_URL . 'assets/fontawesome-7/css/all.min.css',   [], MH_PLUG_VERSION);

    $settings = get_option('mh_plug_widgets_settings', []);
    if ( isset($settings['enable_wc_wishlist']) && $settings['enable_wc_wishlist'] && class_exists('WooCommerce') ) {
        $css_path = MH_PLUG_PATH . 'assets/css/mh-wishlist.css';
        $js_path  = MH_PLUG_PATH . 'assets/js/mh-wishlist.js';

        if ( file_exists($css_path) ) {
            wp_enqueue_style( 'mh-wishlist-css', MH_PLUG_URL . 'assets/css/mh-wishlist.css', [], filemtime($css_path) );
        }

        if ( file_exists($js_path) ) {
            wp_enqueue_script( 'mh-wishlist-js', MH_PLUG_URL . 'assets/js/mh-wishlist.js', ['jquery'], filemtime($js_path), true );
            $wishlist_data = [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('mh_wishlist_nonce'),
                'i18n'    => [
                    'addLabel'     => __('Add to Wishlist', 'mh-plug'),
                    'removeLabel'  => __('Remove from Wishlist', 'mh-plug'),
                    'emptyMessage' => __('Your wishlist is empty.', 'mh-plug'),
                ],
            ];
            wp_add_inline_script('mh-wishlist-js', 'var mhWishlist = ' . wp_json_encode($wishlist_data) . ';', 'before');
        }
    }
}
add_action('wp_enqueue_scripts', 'mh_plug_enqueue_frontend_scripts');


// ─────────────────────────────────────────────────────────────────────────────
// WOOCOMMERCE LIVE PRODUCT SEARCH AJAX HANDLER
// ─────────────────────────────────────────────────────────────────────────────
add_action( 'wp_ajax_mh_live_product_search', 'mh_plug_ajax_live_search' );
add_action( 'wp_ajax_nopriv_mh_live_product_search', 'mh_plug_ajax_live_search' );

function mh_plug_ajax_live_search() {
    $keyword = isset( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '';

    if ( empty( $keyword ) || ! class_exists( 'WooCommerce' ) ) {
        wp_send_json_error(); 
    }

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 5,
        's'              => $keyword,
    ];

    $query = new WP_Query( $args );
    $html  = '';

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $product = wc_get_product( get_the_ID() );
            
            $html .= '<a href="' . esc_url( get_permalink() ) . '" style="display: flex; align-items: center; padding: 10px; border-bottom: 1px solid #eee; text-decoration: none; color: #333;">';
            $html .= '<div style="width: 40px; height: 40px; margin-right: 15px; flex-shrink: 0;">' . $product->get_image( [40, 40] ) . '</div>';
            $html .= '<div style="flex-grow: 1;">';
            $html .= '<strong style="display: block; font-size: 14px;">' . get_the_title() . '</strong>';
            $html .= '<span style="color: #d63638; font-size: 13px;">' . $product->get_price_html() . '</span>';
            $html .= '</div></a>';
        }
    }
    wp_reset_postdata();
    wp_send_json_success( $html );
}

// ─────────────────────────────────────────────────────────────────────────────
// WOOCOMMERCE SMART WISHLIST SYSTEM (AJAX & HELPER FUNCTIONS)
// ─────────────────────────────────────────────────────────────────────────────

// 1. Helper function to check if a product is already in the wishlist
if ( ! function_exists( 'mh_wishlist_has_product' ) ) {
    function mh_wishlist_has_product( $product_id ) {
        if ( is_user_logged_in() ) {
            // Check logged-in user database
            $wishlist = get_user_meta( get_current_user_id(), '_mh_wishlist', true );
            return is_array( $wishlist ) && in_array( $product_id, $wishlist );
        } else {
            // Check guest cookies
            $wishlist = isset($_COOKIE['mh_guest_wishlist']) ? json_decode(stripslashes($_COOKIE['mh_guest_wishlist']), true) : [];
            return is_array( $wishlist ) && in_array( $product_id, $wishlist );
        }
    }
}

// 2. AJAX endpoint to Add/Remove items when the button is clicked
add_action( 'wp_ajax_mh_wishlist_toggle', 'mh_ajax_wishlist_toggle' );
add_action( 'wp_ajax_nopriv_mh_wishlist_toggle', 'mh_ajax_wishlist_toggle' );

function mh_ajax_wishlist_toggle() {
    // Verify Security Nonce
    check_ajax_referer( 'mh_wishlist_nonce', 'security' );

    $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
    
    if ( ! $product_id ) {
        wp_send_json_error( [ 'message' => 'Invalid Product ID' ] );
    }

    $status = 'added';

    // Logic for Logged-In Users
    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $wishlist = get_user_meta( $user_id, '_mh_wishlist', true );
        
        if ( ! is_array( $wishlist ) ) {
            $wishlist = [];
        }

        if ( in_array( $product_id, $wishlist ) ) {
            $wishlist = array_diff( $wishlist, [ $product_id ] );
            $status = 'removed';
        } else {
            $wishlist[] = $product_id;
        }
        update_user_meta( $user_id, '_mh_wishlist', array_values( $wishlist ) );
        
    } 
    // Logic for Guest Users (Saved in Cookies for 30 Days)
    else {
        $wishlist = isset($_COOKIE['mh_guest_wishlist']) ? json_decode(stripslashes($_COOKIE['mh_guest_wishlist']), true) : [];
        
        if ( ! is_array( $wishlist ) ) {
            $wishlist = [];
        }

        if ( in_array( $product_id, $wishlist ) ) {
            $wishlist = array_diff( $wishlist, [ $product_id ] );
            $status = 'removed';
        } else {
            $wishlist[] = $product_id;
        }
        
        // Save the cookie
        setcookie( 'mh_guest_wishlist', wp_json_encode( array_values( $wishlist ) ), time() + (86400 * 30), COOKIEPATH, COOKIE_DOMAIN );
    }

    // Send success message back to the widget button
    wp_send_json_success( [ 'status' => $status ] );
}
// 3. Helper function to GET all items in the wishlist (Used by the Table Widget)
if ( ! function_exists( 'mh_wishlist_get_items' ) ) {
    function mh_wishlist_get_items() {
        if ( is_user_logged_in() ) {
            $wishlist = get_user_meta( get_current_user_id(), '_mh_wishlist', true );
        } else {
            $wishlist = isset($_COOKIE['mh_guest_wishlist']) ? json_decode(stripslashes($_COOKIE['mh_guest_wishlist']), true) : [];
        }
        // Return as an array of integers, or an empty array
        return is_array( $wishlist ) ? array_filter( array_map( 'intval', $wishlist ) ) : [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// FORCE WOOCOMMERCE TO SAVE CUSTOM ATTRIBUTES ON SIMPLE PRODUCTS
// ─────────────────────────────────────────────────────────────────────────────

// 1. Catch the data from the widget and save it to the Cart Session
add_filter( 'woocommerce_add_cart_item_data', 'mh_save_custom_attributes_to_cart', 10, 3 );
function mh_save_custom_attributes_to_cart( $cart_item_data, $product_id, $variation_id ) {
    $product = wc_get_product( $product_id );
    if ( ! $product ) return $cart_item_data;

    $attributes = $product->get_attributes();
    
    foreach ( $attributes as $attribute ) {
        $attr_name = 'attribute_' . sanitize_title( $attribute->get_name() );
        
        // If our widget sent this data, save it!
        if ( isset( $_POST[$attr_name] ) && ! empty( $_POST[$attr_name] ) ) {
            $cart_item_data['mh_custom_attributes'][$attr_name] = [
                'label' => wc_attribute_label( $attribute->get_name() ),
                'value' => sanitize_text_field( wp_unslash( $_POST[$attr_name] ) )
            ];
        }
    }
    return $cart_item_data;
}

// 2. Display the selected attributes in the Cart & Checkout pages
add_filter( 'woocommerce_get_item_data', 'mh_display_custom_attributes_in_cart', 10, 2 );
function mh_display_custom_attributes_in_cart( $item_data, $cart_item ) {
    if ( isset( $cart_item['mh_custom_attributes'] ) ) {
        foreach ( $cart_item['mh_custom_attributes'] as $attr ) {
            $item_data[] = [
                'key'   => $attr['label'],
                'value' => $attr['value']
            ];
        }
    }
    return $item_data;
}

// 3. Save the attributes permanently to the Order Dashboard! (What you see in wp-admin)
add_action( 'woocommerce_checkout_create_order_line_item', 'mh_save_custom_attributes_to_order', 10, 4 );
function mh_save_custom_attributes_to_order( $item, $cart_item_key, $values, $order ) {
    if ( isset( $values['mh_custom_attributes'] ) ) {
        foreach ( $values['mh_custom_attributes'] as $attr ) {
            // This adds it right under the product name in the order details
            $item->add_meta_data( $attr['label'], $attr['value'], true );
        }
    }
}