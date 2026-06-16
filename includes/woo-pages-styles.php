<?php
/**
 * MH Plug - WooCommerce Pages Dynamic Styles
 * Generates CSS from admin settings and injects it on the frontend.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function mh_plug_woo_pages_dynamic_css() {
    if ( is_admin() || ! class_exists( 'WooCommerce' ) ) return;

    // Only load on WooCommerce pages
    if ( ! is_cart() && ! is_checkout() && ! is_account_page() && ! is_wc_endpoint_url() ) return;

    if ( ! function_exists( 'mh_plug_woo_pages_defaults' ) ) {
        require_once MH_PLUG_PATH . 'admin/woo-pages-settings.php';
    }

    $o = wp_parse_args( get_option( 'mh_plug_woo_pages_settings', [] ), mh_plug_woo_pages_defaults() );

    // Build hover animation CSS
    $cart_anim = mh_plug_get_animation_css( $o['cart_btn_animation'] );
    $co_anim   = mh_plug_get_animation_css( $o['co_btn_animation'] );

    // Google Font import
    $font_import = '';
    $font_css    = '';
    if ( ! empty( $o['font_family'] ) ) {
        $font_slug   = str_replace( ' ', '+', $o['font_family'] );
        $font_import = "@import url('https://fonts.googleapis.com/css2?family={$font_slug}:wght@400;500;600;700;800&display=swap');";
        $font_css    = "font-family: '{$o['font_family']}', sans-serif;";
    }

    $css = "
{$font_import}

/* ─── GLOBAL FONT ─── */
.woocommerce-cart .woocommerce,
.woocommerce-checkout .woocommerce,
.woocommerce-account .woocommerce { {$font_css} }

/* ─── CART PAGE ─── */
.woocommerce-cart .woocommerce { max-width:1200px; margin:0 auto; padding:30px 20px; }
.woocommerce-cart .woocommerce h1,
.woocommerce-cart .woocommerce .page-title,
.woocommerce-cart .woocommerce .entry-title {
    color: {$o['cart_heading_color']} !important;
    font-size: {$o['cart_heading_size']}px !important;
    font-weight: {$o['cart_heading_weight']} !important;
}
.woocommerce table.shop_table { border:1px solid #eee !important; border-radius:8px; overflow:hidden; border-collapse:collapse; width:100%; }
.woocommerce table.shop_table th {
    background: {$o['cart_table_header_bg']} !important;
    color: {$o['cart_table_header_color']} !important;
    padding:14px 16px; font-weight:600; text-align:left; border-bottom:2px solid #eee; font-size:14px;
}
.woocommerce table.shop_table td { padding:16px; border-bottom:1px solid #f0f0f0; vertical-align:middle; font-size:14px; }
.woocommerce table.shop_table td.product-name a {
    color: {$o['cart_product_name_color']} !important;
    text-decoration: none;
    transition: color 0.2s;
}
.woocommerce table.shop_table td.product-name a:hover { color: {$o['cart_product_name_hover']} !important; }
.woocommerce table.shop_table td.product-price,
.woocommerce table.shop_table td.product-subtotal { color: {$o['cart_price_color']} !important; font-weight:600; }
.woocommerce table.shop_table img { width:70px; height:70px; object-fit:cover; border-radius:6px; }
.woocommerce a.remove { color:{$o['cart_remove_color']} !important; font-size:20px; text-decoration:none; transition:color 0.2s; }
.woocommerce a.remove:hover { color:{$o['cart_remove_hover']} !important; background:transparent !important; }
.woocommerce .quantity .qty {
    width:60px; padding:8px; text-align:center; font-size:14px;
    background: {$o['cart_qty_bg']} !important;
    border: 1px solid {$o['cart_qty_border']} !important;
    border-radius:4px;
}
.woocommerce .cart_totals { background:{$o['cart_totals_bg']}; border-radius:8px; padding:20px; border:1px solid #eee; max-width:450px; float:right; width:100%; }
.woocommerce .cart_totals h2 { font-size:20px; font-weight:700; margin-bottom:15px; color:{$o['cart_heading_color']}; }

/* Cart - Update Button */
.woocommerce button[name='update_cart'] {
    background: {$o['cart_update_btn_bg']} !important;
    color: {$o['cart_update_btn_color']} !important;
    border:none !important; padding:12px 24px !important; border-radius:6px !important;
    font-weight:600 !important; cursor:pointer !important; transition:all 0.3s !important;
}
.woocommerce button[name='update_cart']:hover {
    background: {$o['cart_update_btn_hover_bg']} !important;
    color: {$o['cart_update_btn_hover_color']} !important;
    {$cart_anim}
}

/* Cart - Coupon Button */
.woocommerce .coupon button, .woocommerce .coupon input[type='submit'] {
    background: {$o['cart_coupon_btn_bg']} !important;
    color: {$o['cart_coupon_btn_color']} !important;
    border:none !important; padding:12px 24px !important; border-radius:6px !important;
    font-weight:600 !important; cursor:pointer !important; transition:all 0.3s !important;
}
.woocommerce .coupon button:hover, .woocommerce .coupon input[type='submit']:hover {
    background: {$o['cart_coupon_btn_hover_bg']} !important;
    color: {$o['cart_coupon_btn_hover_color']} !important;
    {$cart_anim}
}
.woocommerce .coupon input[type='text'] { padding:10px 14px; border:1px solid #ddd; border-radius:6px; font-size:14px; min-width:200px; }

/* Cart - Proceed to Checkout */
.woocommerce .wc-proceed-to-checkout .checkout-button {
    width:100%;
    background: {$o['cart_checkout_btn_bg']} !important;
    color: {$o['cart_checkout_btn_color']} !important;
    padding:14px !important;
    font-size: {$o['cart_checkout_btn_size']}px !important;
    border-radius: {$o['cart_checkout_btn_radius']}px !important;
    border:none !important; font-weight:700 !important; text-align:center !important;
    display:block !important; text-decoration:none !important;
    transition:all 0.3s !important;
}
.woocommerce .wc-proceed-to-checkout .checkout-button:hover {
    background: {$o['cart_checkout_btn_hover_bg']} !important;
    color: {$o['cart_checkout_btn_hover_color']} !important;
    {$cart_anim}
}

/* ─── CHECKOUT PAGE ─── */
.woocommerce-checkout .woocommerce { max-width:1200px; margin:0 auto; padding:30px 20px; }
.woocommerce-checkout .col2-set { display:flex; gap:30px; flex-wrap:wrap; }
.woocommerce-checkout .col2-set .col-1,
.woocommerce-checkout .col2-set .col-2 { flex:1; min-width:300px; }
.woocommerce-checkout h3,
.woocommerce-checkout #order_review_heading {
    color: {$o['co_heading_color']} !important;
    font-size: {$o['co_heading_size']}px !important;
    font-weight: {$o['co_heading_weight']} !important;
    margin-bottom:20px; padding-bottom:10px; border-bottom:2px solid #f0f0f0;
}
.woocommerce-checkout .form-row label {
    display:block; margin-bottom:6px;
    font-weight:600;
    font-size: {$o['co_label_size']}px !important;
    color: {$o['co_label_color']} !important;
}
.woocommerce-checkout .form-row input.input-text,
.woocommerce-checkout .form-row textarea,
.woocommerce-checkout .form-row select,
.woocommerce-checkout .select2-container .select2-selection--single {
    width:100%; padding:10px 14px;
    border:1px solid {$o['co_input_border']} !important;
    border-radius:6px;
    font-size:14px;
    background: {$o['co_input_bg']} !important;
    color: {$o['co_input_color']} !important;
    transition:border-color 0.3s, box-shadow 0.3s;
    box-sizing:border-box;
}
.woocommerce-checkout .form-row input.input-text:focus,
.woocommerce-checkout .form-row textarea:focus,
.woocommerce-checkout .form-row select:focus {
    border-color: {$o['co_input_focus_border']} !important;
    outline:none;
    box-shadow:0 0 0 2px {$o['co_input_focus_border']}22;
}
.woocommerce-checkout #order_review {
    background: {$o['co_order_bg']} !important;
    border-radius:8px; padding:20px;
    border:1px solid {$o['co_order_border']} !important;
}
.woocommerce-checkout #payment { background:{$o['co_order_bg']} !important; border-radius:8px; padding:20px; }
.woocommerce-checkout #payment ul.payment_methods { list-style:none; padding:0; margin:0 0 20px; border-bottom:1px solid #eee; }
.woocommerce-checkout #payment ul.payment_methods li { padding:12px 0; border-bottom:1px solid #f0f0f0; }
.woocommerce-checkout #payment ul.payment_methods li label { font-weight:600; cursor:pointer; font-size:14px; }
.woocommerce-checkout #place_order {
    width:100%;
    background: {$o['co_place_btn_bg']} !important;
    color: {$o['co_place_btn_color']} !important;
    padding:16px !important;
    font-size: {$o['co_place_btn_size']}px !important;
    border-radius: {$o['co_place_btn_radius']}px !important;
    border:none !important; font-weight:700 !important;
    cursor:pointer !important; margin-top:15px;
    transition:all 0.3s !important;
}
.woocommerce-checkout #place_order:hover {
    background: {$o['co_place_btn_hover_bg']} !important;
    color: {$o['co_place_btn_hover_color']} !important;
    {$co_anim}
}
.woocommerce form.checkout_coupon, .woocommerce form.login, .woocommerce form.register {
    border:1px solid #eee !important; border-radius:8px; padding:20px !important; margin-bottom:20px;
}

/* ─── NOTICES ─── */
.woocommerce .woocommerce-error { background:{$o['notice_error_bg']}; border-left:4px solid {$o['notice_error_border']}; padding:12px 20px; margin-bottom:20px; border-radius:6px; font-size:14px; }
.woocommerce .woocommerce-message { background:{$o['notice_success_bg']}; border-left:4px solid {$o['notice_success_border']}; padding:12px 20px; margin-bottom:20px; border-radius:6px; font-size:14px; }
.woocommerce .woocommerce-info { background:{$o['notice_info_bg']}; border-left:4px solid {$o['notice_info_border']}; padding:12px 20px; margin-bottom:20px; border-radius:6px; font-size:14px; }

/* ─── MY ACCOUNT ─── */
.woocommerce-account .woocommerce { max-width:1200px; margin:0 auto; padding:30px 20px; }
.woocommerce-MyAccount-navigation { width:220px; float:left; margin-right:30px; }
.woocommerce-MyAccount-navigation ul { list-style:none; padding:0; margin:0; background:#f8f9fa; border-radius:8px; overflow:hidden; border:1px solid #eee; }
.woocommerce-MyAccount-navigation ul li a { display:block; padding:12px 18px; color:#333; text-decoration:none; font-weight:500; font-size:14px; border-bottom:1px solid #eee; transition:all 0.2s; }
.woocommerce-MyAccount-navigation ul li a:hover,
.woocommerce-MyAccount-navigation ul li.is-active a { background:#333; color:#fff; }
.woocommerce-MyAccount-content { overflow:hidden; }

/* ─── RESPONSIVE ─── */
@media (max-width:768px) {
    .woocommerce-checkout .col2-set { flex-direction:column; }
    .woocommerce .cart_totals { float:none; max-width:100%; }
    .woocommerce-MyAccount-navigation { width:100%; float:none; margin-right:0; margin-bottom:20px; }
}
";

    // Register a minimal handle so wp_add_inline_style has somewhere to attach.
    // We use 'woocommerce-general' which is always enqueued on WC pages.
    wp_add_inline_style( 'woocommerce-general', $css );
}
add_action( 'wp_enqueue_scripts', 'mh_plug_woo_pages_dynamic_css', 999 );

/**
 * Build hover animation CSS string.
 */
function mh_plug_get_animation_css( $type ) {
    switch ( $type ) {
        case 'lift':  return 'transform: translateY(-2px) !important; box-shadow: 0 6px 16px rgba(0,0,0,0.15) !important;';
        case 'scale': return 'transform: scale(1.03) !important;';
        case 'glow':  return 'box-shadow: 0 0 20px rgba(214,54,56,0.4) !important;';
        case 'pulse': return 'animation: mhBtnPulse 0.4s ease !important;';
        default:      return '';
    }
}

/**
 * Inject keyframes if pulse animation is used.
 */
function mh_plug_woo_pages_keyframes() {
    if ( is_admin() || ! class_exists('WooCommerce') ) return;
    $o = wp_parse_args( get_option('mh_plug_woo_pages_settings',[]), function_exists('mh_plug_woo_pages_defaults') ? mh_plug_woo_pages_defaults() : [] );
    if ( ($o['cart_btn_animation'] ?? '') === 'pulse' || ($o['co_btn_animation'] ?? '') === 'pulse' ) {
        wp_add_inline_style( 'woocommerce-general', '@keyframes mhBtnPulse{0%{transform:scale(1)}50%{transform:scale(1.05)}100%{transform:scale(1)}}' );
    }
}
add_action( 'wp_enqueue_scripts', 'mh_plug_woo_pages_keyframes', 998 );
