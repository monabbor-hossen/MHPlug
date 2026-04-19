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
// ─────────────────────────────────────────────────────────────────────────────
// MAKE ORDER ATTRIBUTES BIGGER IN THE ADMIN DASHBOARD (Forced 20px Version)
// ─────────────────────────────────────────────────────────────────────────────
add_action('admin_head', 'mh_make_order_attributes_bigger');

function mh_make_order_attributes_bigger() {
    // We removed the strict Screen ID check so WooCommerce HPOS doesn't block it.
    // Instead, we use highly specific CSS classes that only exist on the Order page.
    echo '<style>
        /* Target the order item metadata in both legacy and new WooCommerce orders */
        .woocommerce_order_items table.display_meta th, 
        .woocommerce_order_items table.display_meta td,
        .woocommerce_order_items table.display_meta p,
        .wc-item-meta, 
        .wc-item-meta li, 
        .wc-item-meta p,
        .wc-item-meta strong {
            font-size: 20px !important;
            line-height: 1.6 !important;
            color: #000000 !important; 
            font-weight: 600 !important;
        }
        
        /* Add some breathing room so the big text isn\'t cramped */
        .woocommerce_order_items table.display_meta {
            margin-top: 12px !important;
        }
    </style>';
}

// ─────────────────────────────────────────────────────────────────────────────
// WOOCOMMERCE QUICK VIEW AJAX HANDLER & CUSTOM ADD TO CART
// ─────────────────────────────────────────────────────────────────────────────

// 1. Inject Custom Attributes into Simple Products inside Quick View
function mh_qv_output_simple_attributes() {
    global $product;
    if ( ! $product || ! $product->is_type('simple') ) return;

    $attributes = $product->get_attributes();
    if ( empty( $attributes ) ) return;

    echo '<div class="mh-qv-attributes" style="margin-bottom: 20px; width: 100%;">';
    foreach ( $attributes as $attribute ) {
        $attribute_name = $attribute->get_name();
        $label          = wc_attribute_label( $attribute_name );
        $select_name    = 'attribute_' . sanitize_title( $attribute_name );
        
        echo '<div style="margin-bottom: 10px;">';
        echo '<label style="display:block; font-weight:600; margin-bottom: 5px; color:#333;">' . esc_html( $label ) . '</label>';
        echo '<select name="' . esc_attr( $select_name ) . '" class="mh-qv-attr-select" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9; color:#333; outline:none;">';
        echo '<option value="">' . esc_html( sprintf( __( 'Choose %s', 'mh-plug' ), $label ) ) . '</option>';

        if ( $attribute->is_taxonomy() ) {
            $terms = wc_get_product_terms( $product->get_id(), $attribute_name, ['fields' => 'all'] );
            foreach ( $terms as $term ) {
                echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
            }
        } else {
            $options = $attribute->get_options();
            foreach ( $options as $option ) {
                echo '<option value="' . esc_attr( trim( $option ) ) . '">' . esc_html( trim( $option ) ) . '</option>';
            }
        }
        echo '</select></div>';
    }
    echo '</div>';
}

// 2. Load the Quick View HTML
add_action( 'wp_ajax_mh_quick_view_load', 'mh_quick_view_ajax_handler' );
add_action( 'wp_ajax_nopriv_mh_quick_view_load', 'mh_quick_view_ajax_handler' );

function mh_quick_view_ajax_handler() {
    $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
    if ( ! $product_id ) wp_send_json_error();

    global $post, $product;
    $post = get_post( $product_id );
    setup_postdata( $post );
    $product = wc_get_product( $product_id );

    ob_start();
    ?>
    <div class="mh-qv-grid">
        <div class="mh-qv-image">
            <?php echo $product->get_image('woocommerce_single'); ?>
        </div>
        <div class="mh-qv-details">
            <h2 class="mh-qv-title"><?php echo $product->get_name(); ?></h2>
            <div class="mh-qv-price"><?php echo $product->get_price_html(); ?></div>
            <div class="mh-qv-excerpt"><?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?></div>
            
            <div class="mh-qv-add-to-cart-wrap">
                <?php 
                // Temporarily hook our custom attributes into the form
                add_action( 'woocommerce_before_add_to_cart_button', 'mh_qv_output_simple_attributes' );
                
                woocommerce_template_single_add_to_cart(); 
                
                remove_action( 'woocommerce_before_add_to_cart_button', 'mh_qv_output_simple_attributes' );
                ?>
            </div>
        </div>
    </div>
    <?php
    wp_send_json_success( ob_get_clean() );
}

// 3. Custom AJAX Add to Cart Handler (Prevents Page Reloads & Fixes Errors!)
add_action('wp_ajax_mh_qv_add_to_cart', 'mh_qv_ajax_add_to_cart');
add_action('wp_ajax_nopriv_mh_qv_add_to_cart', 'mh_qv_ajax_add_to_cart');

function mh_qv_ajax_add_to_cart() {
    $product_id   = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity     = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
    $variation    = [];
    
    // Grab any variable attributes selected
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'attribute_') === 0) { $variation[$key] = sanitize_text_field($value); }
    }
    
    $product = wc_get_product($product_id);
    if ( $product && $product->is_type('variable') && empty($variation_id) ) {
        wp_send_json_error(['message' => 'Please select product options.']);
        wp_die();
    }

    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation)) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);
        WC_AJAX::get_refreshed_fragments(); 
    } else {
        wp_send_json_error(['message' => 'Please select all attributes.']);
    }
    wp_die();
}

// 4. Quick View Global CSS
add_action('wp_footer', function() {
    ?>
    <style>
        .mh-qv-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; opacity: 0; visibility: hidden; transition: 0.3s; display: flex; align-items: center; justify-content: center; }
        .mh-qv-overlay.mh-open { opacity: 1; visibility: visible; }
        .mh-qv-content { background: #fff; width: 900px; max-width: 95%; max-height: 90vh; overflow-y: auto; border-radius: 10px; position: relative; transform: translateY(30px); transition: 0.3s; box-shadow: 0 15px 40px rgba(0,0,0,0.2); }
        .mh-qv-overlay.mh-open .mh-qv-content { transform: translateY(0); }
        .mh-qv-close { position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: #888; z-index: 10; transition: 0.2s; }
        .mh-qv-close:hover { color: #d63638; transform: rotate(90deg); }
        
        .mh-qv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; padding: 40px; }
        .mh-qv-image img { width: 100%; height: auto; border-radius: 8px; }
        .mh-qv-title { margin: 0 0 15px; font-size: 26px; font-weight: 700; color: #111; }
        .mh-qv-price { font-size: 22px; color: #d63638; font-weight: 700; margin-bottom: 20px; }
        .mh-qv-price del { color: #aaa; font-weight: 400; font-size: 18px; margin-right: 10px; }
        .mh-qv-excerpt { color: #555; line-height: 1.6; margin-bottom: 30px; }
        
        .mh-qv-add-to-cart-wrap form.cart { display: flex; flex-wrap: wrap; gap: 15px; align-items: center; }
        .mh-qv-add-to-cart-wrap .quantity { display: flex; align-items: center; background: #f7f7f7; border-radius: 5px; border: 1px solid #ddd; }
        .mh-qv-add-to-cart-wrap .quantity input.qty { width: 50px; text-align: center; border: none; background: transparent; font-size: 16px; font-weight: 600; padding: 10px 0; -moz-appearance: textfield; }
        .mh-qv-add-to-cart-wrap .quantity input::-webkit-outer-spin-button, .mh-qv-add-to-cart-wrap .quantity input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .mh-qty-btn { width: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 20px; font-weight: bold; color: #555; user-select: none; transition: 0.2s; }
        .mh-qty-btn:hover { color: #d63638; }
        
        .mh-qv-add-to-cart-wrap button.button { background: #111; color: #fff; padding: 12px 30px; border-radius: 5px; border: none; cursor: pointer; transition: 0.3s; font-weight: 600; font-size: 16px; flex-grow: 1; }
        .mh-qv-add-to-cart-wrap button.button:hover { background: #d63638; }
        .mh-qv-add-to-cart-wrap button.button.loading { opacity: 0.5; pointer-events: none; }

        @media (max-width: 768px) { .mh-qv-grid { grid-template-columns: 1fr; padding: 25px; gap: 20px; } }
    </style>
    <?php
});