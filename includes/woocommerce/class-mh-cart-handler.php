<?php
/**
 * MH_Cart_Handler
 *
 * Handles custom variation pricing and cart data for MH-Plug.
 *
 * @package MH_Plug
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MH_Cart_Handler {

    /**
     * Register all hooks.
     */
    public static function init() {
        add_filter( 'woocommerce_add_cart_item_data',              [ __CLASS__, 'add_cart_item_data' ],         10, 3 );
        add_filter( 'woocommerce_get_cart_item_from_session',      [ __CLASS__, 'get_cart_item_from_session' ], 10, 3 );
        add_action( 'woocommerce_before_calculate_totals',         [ __CLASS__, 'calculate_totals' ],           10, 1 );
        add_filter( 'woocommerce_get_item_data',                   [ __CLASS__, 'display_cart_item_data' ],     10, 2 );
        add_action( 'woocommerce_checkout_create_order_line_item', [ __CLASS__, 'save_order_item_meta' ],       10, 4 );

        // PRG handler for the custom 'combo' product type.
        add_action( 'woocommerce_add_to_cart_handler_combo', [ __CLASS__, 'handle_combo_add_to_cart' ] );

        // Global PRG redirect — only on real page-load POSTs, never on AJAX.
        add_filter( 'woocommerce_add_to_cart_redirect', [ __CLASS__, 'force_prg_redirect' ], 99, 1 );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPER: Safe check whether the current request is a real POST page-load.
    // Returns false for AJAX, REST, CLI, and GET requests.
    // ─────────────────────────────────────────────────────────────────────────
    private static function is_real_post_request() {
        // Never touch AJAX or REST API calls.
        if ( wp_doing_ajax() ) {
            return false;
        }
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            return false;
        }
        // $_SERVER['REQUEST_METHOD'] may be absent in CLI (wp-cron, WP-CLI).
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( $_SERVER['REQUEST_METHOD'] ) : 'GET';
        return $method === 'POST';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. ADD CART ITEM DATA
    // Save the user's selected dropdown values into the cart session data.
    // SECURITY FENCE: Only process on a genuine POST add-to-cart request.
    // ─────────────────────────────────────────────────────────────────────────
    public static function add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
        // Must be a genuine non-AJAX POST add-to-cart request.
        if ( ! self::is_real_post_request() || empty( $_POST['add-to-cart'] ) ) {
            return $cart_item_data;
        }

        $sanitized_attributes = [];

        // ── Source A: mh_custom_attr[color] = 'green'  (MH_Combo_Frontend widget) ──
        if ( isset( $_POST['mh_custom_attr'] ) && is_array( $_POST['mh_custom_attr'] ) ) {
            foreach ( $_POST['mh_custom_attr'] as $key => $value ) {
                if ( ! empty( $value ) ) {
                    // Cast key to string before wc_clean() — PHP 8 TypeError guard.
                    $sanitized_attributes[ wc_clean( (string) $key ) ] = is_array( $value )
                        ? implode( ',', array_map( 'wc_clean', array_map( 'strval', $value ) ) )
                        : wc_clean( (string) $value );
                }
            }
        }

        // ── Source B: attribute_color = 'green'  (MH_Woo_Attributes_Widget) ──
        foreach ( $_POST as $key => $value ) {
            if ( strpos( $key, 'attribute_' ) === 0 && ! empty( $value ) ) {
                $attr_key = substr( $key, strlen( 'attribute_' ) );
                if ( ! isset( $sanitized_attributes[ $attr_key ] ) ) {
                    $sanitized_attributes[ $attr_key ] = wc_clean( $value );
                }
            }
        }

        if ( ! empty( $sanitized_attributes ) ) {
            ksort( $sanitized_attributes );
            $cart_item_data['mh_custom_attributes'] = $sanitized_attributes;
            $cart_item_data['unique_key']           = md5( wp_json_encode( $sanitized_attributes ) );
        }

        return $cart_item_data;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. COMBO ADD TO CART HANDLER
    // PRG handler for the 'combo' product type.
    // ─────────────────────────────────────────────────────────────────────────
    public static function handle_combo_add_to_cart() {
        if ( ! self::is_real_post_request() || empty( $_POST['add-to-cart'] ) ) {
            return;
        }

        $product_id = absint( wp_unslash( $_POST['add-to-cart'] ) );
        $quantity   = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );

        $product = wc_get_product( $product_id );
        if ( ! $product || ! $product->is_type( 'combo' ) ) {
            return;
        }

        $cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity );

        if ( $cart_item_key ) {
            wc_add_notice( __( 'Item successfully added to your cart.', 'mh-plug' ), 'success' );
        } else {
            if ( ! wc_notice_count( 'error' ) ) {
                wc_add_notice( __( 'Could not add this item to your cart. Please try again.', 'mh-plug' ), 'error' );
            }
        }

        $redirect_url = apply_filters(
            'woocommerce_add_to_cart_redirect',
            get_permalink( $product_id ),
            $product
        );

        wp_safe_redirect( $redirect_url );
        exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. FORCE PRG REDIRECT
    // Only runs on real page-load POSTs — never on AJAX add-to-cart calls.
    // ─────────────────────────────────────────────────────────────────────────
    public static function force_prg_redirect( $url ) {
        // CRITICAL: Skip completely on AJAX — this filter can fire during
        // WooCommerce AJAX add-to-cart. Returning a URL would break the AJAX response.
        if ( ! self::is_real_post_request() ) {
            return $url;
        }

        if ( empty( $_POST['add-to-cart'] ) ) {
            return $url;
        }

        // Buy Now → always redirect to checkout safely.
        if ( ! empty( $_POST['mh_buy_now'] ) ) {
            return function_exists( 'wc_get_checkout_url' ) 
                ? wc_get_checkout_url() 
                : home_url( '/checkout/' );
        }

        // If WooCommerce already wants to redirect somewhere, honour it.
        if ( ! empty( $url ) ) {
            return $url;
        }

        // For simple products with custom attributes, force PRG to clear POST.
        if ( is_numeric( $_POST['add-to-cart'] ) ) {
            $has_custom_attrs = false;
            if ( isset( $_POST['mh_custom_attr'] ) ) {
                $has_custom_attrs = true;
            } else {
                foreach ( $_POST as $key => $value ) {
                    if ( strpos( $key, 'attribute_' ) === 0 && ! empty( $value ) ) {
                        $has_custom_attrs = true;
                        break;
                    }
                }
            }

            if ( $has_custom_attrs ) {
                return wp_get_referer() ?: get_permalink( absint( $_POST['add-to-cart'] ) );
            }
        }

        return $url;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. RESTORE CART DATA FROM SESSION
    // ─────────────────────────────────────────────────────────────────────────
    public static function get_cart_item_from_session( $cart_item, $values, $key ) {
        if ( isset( $values['mh_custom_attributes'] ) && is_array( $values['mh_custom_attributes'] ) ) {
            $cart_item['mh_custom_attributes'] = $values['mh_custom_attributes'];
            // Apply price override — data is an object so set_price() mutates it in place.
            self::apply_custom_price( $cart_item );
        }
        return $cart_item;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. CALCULATE TOTALS
    // Runs on woocommerce_before_calculate_totals.
    // NOTE: WooCommerce fires this hook multiple times per page load (AJAX
    // shipping updates, coupon checks, etc.). Do NOT block it with did_action().
    // ─────────────────────────────────────────────────────────────────────────
    public static function calculate_totals( $cart ) {
        // Skip in admin panel (but allow admin-ajax.php).
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return;
        }

        // Defensive: make sure we received a proper WC_Cart object.
        if ( ! is_object( $cart ) || ! method_exists( $cart, 'get_cart' ) ) {
            return;
        }

        $cart_contents = $cart->get_cart();
        if ( ! is_array( $cart_contents ) ) {
            return;
        }

        foreach ( $cart_contents as $cart_item_key => $cart_item ) {
            self::apply_custom_price( $cart_item );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. APPLY CUSTOM PRICE (private helper)
    // NOTE: $cart_item['data'] is a WC_Product object. Calling ->set_price()
    // on it mutates the object in-place — no pass-by-reference needed on the array.
    // ─────────────────────────────────────────────────────────────────────────
    private static function apply_custom_price( $cart_item ) {
        // Bail early if no custom attributes or no product data object.
        if (
            empty( $cart_item['mh_custom_attributes'] ) ||
            ! is_array( $cart_item['mh_custom_attributes'] ) ||
            empty( $cart_item['data'] ) ||
            ! is_object( $cart_item['data'] )
        ) {
            return;
        }

        $product_id = isset( $cart_item['product_id'] ) ? (int) $cart_item['product_id'] : 0;
        if ( $product_id <= 0 ) {
            return;
        }

        $rules = get_post_meta( $product_id, '_mh_custom_variation_rules', true );
        if ( empty( $rules ) || ! is_array( $rules ) ) {
            return;
        }

        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            return;
        }

        // Build the list of attribute names that participate in variation pricing.
        $variation_keys = [];
        $all_attributes = $product->get_attributes();
        if ( is_array( $all_attributes ) ) {
            foreach ( $all_attributes as $attr ) {
                if ( ! is_object( $attr ) ) {
                    continue;
                }
                if ( $attr->get_variation() ) {
                    $attr_name = $attr->get_name();
                    if ( ! empty( $attr_name ) ) {
                        $variation_keys[] = strtolower( (string) $attr_name );
                    }
                }
            }
        }

        if ( empty( $variation_keys ) ) {
            return;
        }

        // Build a normalised combination string from the cart item's attributes.
        $flat_combination = [];
        foreach ( $cart_item['mh_custom_attributes'] as $attr_key => $attr_val ) {
            if ( empty( $attr_key ) ) {
                continue;
            }
            if ( in_array( strtolower( (string) $attr_key ), $variation_keys, true ) ) {
                $flat_val = is_array( $attr_val )
                    ? implode( ',', $attr_val )
                    : (string) $attr_val;
                $flat_combination[ (string) $attr_key ] = strtolower( $flat_val );
            }
        }

        if ( empty( $flat_combination ) ) {
            return;
        }

        ksort( $flat_combination );
        $combination_string = implode( '|', $flat_combination );

        // Find a matching rule and apply its price.
        foreach ( $rules as $rule ) {
            if ( ! is_array( $rule ) ) {
                continue;
            }

            $rule_combo = isset( $rule['combination_string'] )
                ? strtolower( (string) $rule['combination_string'] )
                : '';

            if ( $rule_combo === $combination_string ) {
                $custom_price = ( ! empty( $rule['sale_price'] ) )
                    ? $rule['sale_price']
                    : ( isset( $rule['regular_price'] ) ? $rule['regular_price'] : '' );

                if ( $custom_price !== '' ) {
                    $cart_item['data']->set_price( (float) $custom_price );
                }
                break;
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 7. DISPLAY CART ITEM DATA
    // Shows the selected attributes under the product name in cart & checkout.
    // ─────────────────────────────────────────────────────────────────────────
    public static function display_cart_item_data( $item_data, $cart_item ) {
        if ( ! is_array( $item_data ) ) {
            $item_data = [];
        }

        if ( empty( $cart_item['mh_custom_attributes'] ) || ! is_array( $cart_item['mh_custom_attributes'] ) ) {
            return $item_data;
        }

        foreach ( $cart_item['mh_custom_attributes'] as $attr_key => $attr_value ) {
            if ( empty( $attr_key ) ) {
                continue;
            }

            $label = wc_attribute_label( (string) $attr_key );
            if ( $label === (string) $attr_key ) {
                $label = ucfirst( str_replace( [ '_', '-' ], ' ', (string) $attr_key ) );
            }

            $val_str = is_array( $attr_value )
                ? implode( ', ', array_map( 'strval', $attr_value ) )
                : (string) $attr_value;

            $item_data[] = [
                'key'   => esc_html( $label ),
                'value' => esc_html( ucfirst( $val_str ) ),
            ];
        }

        return $item_data;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 8. SAVE ORDER ITEM META
    // Persists custom attributes to the WooCommerce order line item.
    // ─────────────────────────────────────────────────────────────────────────
    public static function save_order_item_meta( $item, $cart_item_key, $cart_item, $order ) {
        if ( empty( $cart_item['mh_custom_attributes'] ) || ! is_array( $cart_item['mh_custom_attributes'] ) ) {
            return;
        }

        if ( ! is_object( $item ) || ! method_exists( $item, 'add_meta_data' ) ) {
            return;
        }

        foreach ( $cart_item['mh_custom_attributes'] as $attr_key => $attr_value ) {
            if ( empty( $attr_key ) ) {
                continue;
            }

            $label = wc_attribute_label( (string) $attr_key );
            if ( $label === (string) $attr_key ) {
                $label = ucfirst( str_replace( [ '_', '-' ], ' ', (string) $attr_key ) );
            }

            $val_str = is_array( $attr_value )
                ? implode( ', ', array_map( 'strval', $attr_value ) )
                : (string) $attr_value;

            $item->add_meta_data( $label, ucfirst( $val_str ), true );
        }
    }
}
