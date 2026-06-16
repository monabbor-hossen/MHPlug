<?php
/**
 * MH Plug - Floating Mini Cart Frontend
 * Renders the floating cart icon + slide-in sidebar panel.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Get resolved mini-cart settings with WooCommerce URL fallbacks.
 */
function mh_plug_get_mini_cart_settings() {
    $opts = get_option( 'mh_plug_mini_cart_settings', [] );

    $defaults = [
        'enable_mini_cart'   => '1',
        'cart_url'           => '',
        'checkout_url'       => '',
        'icon_class'         => 'fa-solid fa-bag-shopping',
        'icon_color'         => '#ffffff',
        'icon_bg'            => '#333333',
        'icon_size'          => '18',
        'icon_position'      => 'bottom-right',
        'counter_bg'         => '#d63638',
        'counter_color'      => '#ffffff',
        'panel_bg'           => '#ffffff',
        'panel_text_color'   => '#333333',
        'panel_width'        => '380',
        'btn_view_cart_bg'   => '#f5f5f5',
        'btn_view_cart_text' => '#333333',
        'btn_checkout_bg'    => '#333333',
        'btn_checkout_text'  => '#ffffff',
        'btn_border_radius'  => '6',
    ];
    $opts = wp_parse_args( $opts, $defaults );

    // Resolve URLs — use admin-set URLs or fall back to WooCommerce defaults
    if ( empty( $opts['cart_url'] ) && function_exists( 'wc_get_cart_url' ) ) {
        $opts['cart_url'] = wc_get_cart_url();
    }
    if ( empty( $opts['checkout_url'] ) && function_exists( 'wc_get_checkout_url' ) ) {
        $opts['checkout_url'] = wc_get_checkout_url();
    }

    return $opts;
}

/**
 * Override WooCommerce mini-cart buttons with our configured URLs.
 *
 * IMPORTANT: Only override if the admin explicitly set a custom URL.
 * The fallback-resolved URLs (from wc_get_cart_url / wc_get_checkout_url)
 * must NOT be fed back into the same filters — that creates a fragile
 * cache that can break checkout if WooCommerce resolves the URL differently
 * later in the request (e.g. after permalink flush, multi-language switch).
 */
function mh_plug_fix_mini_cart_buttons() {
    $raw_opts = get_option( 'mh_plug_mini_cart_settings', [] );

    // Only override if the user explicitly typed a custom URL in admin settings.
    if ( ! empty( $raw_opts['cart_url'] ) ) {
        $cart_url = $raw_opts['cart_url'];
        add_filter( 'woocommerce_get_cart_url', function() use ( $cart_url ) {
            return $cart_url;
        }, 9999 );
    }

    if ( ! empty( $raw_opts['checkout_url'] ) ) {
        $checkout_url = $raw_opts['checkout_url'];
        add_filter( 'woocommerce_get_checkout_url', function() use ( $checkout_url ) {
            return $checkout_url;
        }, 9999 );
    }
}
add_action( 'wp', 'mh_plug_fix_mini_cart_buttons' );

/**
 * Render the floating mini-cart in the footer.
 */
function mh_plug_render_floating_mini_cart() {
    if ( is_admin() || ! class_exists( 'WooCommerce' ) ) return;

    $opts = mh_plug_get_mini_cart_settings();
    if ( empty( $opts['enable_mini_cart'] ) ) return;

    $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $pos = $opts['icon_position'];
    $pos_css = ( $pos === 'bottom-left' ) ? 'left: 24px; right: auto;' : 'right: 24px; left: auto;';
    $panel_side = ( $pos === 'bottom-left' ) ? 'left' : 'right';
    $panel_transform_hidden = ( $panel_side === 'right' ) ? 'translateX(100%)' : 'translateX(-100%)';
    $br = intval( $opts['btn_border_radius'] );
    ?>


    <!-- Overlay -->
    <div id="mh-mini-cart-overlay" style="
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.4);
        z-index: 999991;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    "></div>

    <!-- Sidebar Panel -->
    <div id="mh-mini-cart-panel"
        data-transform-hidden="<?php echo esc_attr( $panel_transform_hidden ); ?>"
        style="
        position: fixed;
        top: 0;
        <?php echo esc_attr( $panel_side ); ?>: 0;
        width: <?php echo intval( $opts['panel_width'] ); ?>px;
        max-width: 90vw;
        height: 100%;
        background: <?php echo esc_attr( $opts['panel_bg'] ); ?>;
        color: <?php echo esc_attr( $opts['panel_text_color'] ); ?>;
        z-index: 999992;
        transform: <?php echo $panel_transform_hidden; ?>;
        transition: transform 0.35s ease;
        display: flex;
        flex-direction: column;
        box-shadow: -4px 0 20px rgba(0,0,0,0.15);
    ">
        <!-- Header -->
        <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.08);">
            <h3 style="margin:0; font-size:18px; font-weight:700; color:<?php echo esc_attr( $opts['panel_text_color'] ); ?>;">
                <?php esc_html_e( 'Shopping Cart', 'mh-plug' ); ?>
            </h3>
            <span id="mh-mini-cart-close" style="cursor:pointer; font-size:24px; line-height:1; color:#999; transition:color 0.2s;" onmouseover="this.style.color='#d63638'" onmouseout="this.style.color='#999'">&times;</span>
        </div>

        <!-- Cart Content (WooCommerce renders here) -->
        <div id="mh-mini-cart-content" style="flex:1; overflow-y:auto; padding:20px 24px;">
            <?php if ( function_exists( 'woocommerce_mini_cart' ) ) { woocommerce_mini_cart(); } ?>
        </div>

        <!-- Footer Buttons -->
        <div id="mh-mini-cart-footer" style="padding:16px 24px; border-top:1px solid rgba(0,0,0,0.08); display:flex; gap:10px; <?php if ( $cart_count === 0 ) echo 'display:none;'; ?>">
            <a href="<?php echo esc_url( $opts['cart_url'] ); ?>" style="
                flex:1; text-align:center; padding:12px 16px; text-decoration:none; font-weight:600; font-size:14px;
                border-radius:<?php echo $br; ?>px;
                background:<?php echo esc_attr( $opts['btn_view_cart_bg'] ); ?>;
                color:<?php echo esc_attr( $opts['btn_view_cart_text'] ); ?>;
                border:1px solid rgba(0,0,0,0.08);
                transition: opacity 0.2s;
            "><?php esc_html_e( 'View Cart', 'mh-plug' ); ?></a>
            <a href="<?php echo esc_url( $opts['checkout_url'] ); ?>" style="
                flex:1; text-align:center; padding:12px 16px; text-decoration:none; font-weight:600; font-size:14px;
                border-radius:<?php echo $br; ?>px;
                background:<?php echo esc_attr( $opts['btn_checkout_bg'] ); ?>;
                color:<?php echo esc_attr( $opts['btn_checkout_text'] ); ?>;
                transition: opacity 0.2s;
            "><?php esc_html_e( 'Checkout', 'mh-plug' ); ?></a>
        </div>
    </div>

    <?php
    // Pass cart JS via wp_add_inline_script (WP.org compliant — no inline <script> tags).
    $js  = '(function(){';
    $js .= "var overlay=document.getElementById('mh-mini-cart-overlay');";
    $js .= "var panel=document.getElementById('mh-mini-cart-panel');";
    $js .= "var close=document.getElementById('mh-mini-cart-close');";
    $js .= 'if(!panel)return;';
    $js .= "var transformHidden=panel.getAttribute('data-transform-hidden')||'translateX(100%)';";
    $js .= 'function openCart(){';
    $js .= 'var sw=window.innerWidth-document.documentElement.clientWidth;';
    $js .= "document.body.style.paddingRight=sw+'px';";
    $js .= "document.body.style.overflow='hidden';";
    $js .= "panel.style.transform='translateX(0)';";
    $js .= "overlay.style.opacity='1';";
    $js .= "overlay.style.visibility='visible';";
    $js .= '}';
    $js .= 'function closeCart(){';
    $js .= 'panel.style.transform=transformHidden;';
    $js .= "overlay.style.opacity='0';";
    $js .= "overlay.style.visibility='hidden';";
    $js .= "document.body.style.overflow='';";
    $js .= "document.body.style.paddingRight='';";
    $js .= '}';
    $js .= "close.addEventListener('click',closeCart);";
    $js .= "overlay.addEventListener('click',closeCart);";
    $js .= "document.body.addEventListener('mh_open_global_mini_cart',openCart);";
    $js .= "jQuery(document.body).on('wc_fragments_refreshed wc_fragments_loaded added_to_cart removed_from_cart',function(){";
    $js .= "var countEl=document.getElementById('mh-mini-cart-count');";
    $js .= "var footer=document.getElementById('mh-mini-cart-footer');";
    $js .= "var items=document.querySelectorAll('#mh-mini-cart-content .mini_cart_item');";
    $js .= 'var count=items.length;';
    $js .= "if(countEl){countEl.textContent=count;countEl.style.display=count>0?'flex':'none';}";
    $js .= "if(footer){footer.style.display=count>0?'flex':'none';}";
    $js .= '});';
    $js .= '})();';
    wp_add_inline_script( 'jquery', $js );
    ?>
    <?php
}
add_action( 'wp_footer', 'mh_plug_render_floating_mini_cart', 99 );

/**
 * Register WooCommerce cart fragments for AJAX cart updates.
 */
function mh_plug_mini_cart_fragments( $fragments ) {
    // Guard: WC()->cart can be null during early init or edge-case requests.
    // On PHP 8+ calling a method on null is a Fatal Error.
    if ( ! WC()->cart ) {
        return $fragments;
    }

    $opts = mh_plug_get_mini_cart_settings();
    $count = WC()->cart->get_cart_contents_count();

    // Update mini-cart content
    ob_start();
    woocommerce_mini_cart();
    $fragments['#mh-mini-cart-content .widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . ob_get_clean() . '</div>';

    // Update counter badge
    $fragments['#mh-mini-cart-count'] = '<span id="mh-mini-cart-count" style="
        position:absolute; top:-4px; right:-4px;
        background:' . esc_attr( $opts['counter_bg'] ) . ';
        color:' . esc_attr( $opts['counter_color'] ) . ';
        font-size:11px; font-weight:700; width:22px; height:22px;
        border-radius:50%; display:' . ( $count > 0 ? 'flex' : 'none' ) . ';
        align-items:center; justify-content:center; line-height:1;
    ">' . esc_html( $count ) . '</span>';

    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'mh_plug_mini_cart_fragments' );
