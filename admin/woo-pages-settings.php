<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'mh_plug_woo_pages_defaults' ) ) {
    function mh_plug_woo_pages_defaults() {
        return [
            // Typography
            'font_family'        => '',
            // Cart - Heading
            'cart_heading_color'  => '#111111',
            'cart_heading_size'   => '28',
            'cart_heading_weight' => '700',
            // Cart - Table
            'cart_table_header_bg'    => '#f8f9fa',
            'cart_table_header_color' => '#333333',
            'cart_product_name_color' => '#333333',
            'cart_product_name_hover' => '#d63638',
            'cart_price_color'        => '#111111',
            'cart_remove_color'       => '#999999',
            'cart_remove_hover'       => '#d63638',
            'cart_qty_bg'             => '#ffffff',
            'cart_qty_border'         => '#dddddd',
            // Cart - Buttons
            'cart_update_btn_bg'         => '#333333',
            'cart_update_btn_color'      => '#ffffff',
            'cart_update_btn_hover_bg'   => '#d63638',
            'cart_update_btn_hover_color'=> '#ffffff',
            'cart_coupon_btn_bg'         => '#333333',
            'cart_coupon_btn_color'      => '#ffffff',
            'cart_coupon_btn_hover_bg'   => '#555555',
            'cart_coupon_btn_hover_color'=> '#ffffff',
            'cart_checkout_btn_bg'       => '#333333',
            'cart_checkout_btn_color'    => '#ffffff',
            'cart_checkout_btn_hover_bg' => '#d63638',
            'cart_checkout_btn_hover_color'=>'#ffffff',
            'cart_checkout_btn_radius'   => '8',
            'cart_checkout_btn_size'     => '16',
            'cart_btn_animation'         => 'lift',
            'cart_totals_bg'             => '#fafafa',
            // Checkout - Heading
            'co_heading_color'  => '#111111',
            'co_heading_size'   => '20',
            'co_heading_weight' => '700',
            // Checkout - Form
            'co_label_color'       => '#333333',
            'co_label_size'        => '13',
            'co_input_bg'          => '#ffffff',
            'co_input_color'       => '#333333',
            'co_input_border'      => '#dddddd',
            'co_input_focus_border'=> '#333333',
            // Checkout - Order Review
            'co_order_bg'     => '#fafafa',
            'co_order_border' => '#eeeeee',
            // Checkout - Place Order
            'co_place_btn_bg'         => '#333333',
            'co_place_btn_color'      => '#ffffff',
            'co_place_btn_hover_bg'   => '#d63638',
            'co_place_btn_hover_color'=> '#ffffff',
            'co_place_btn_radius'     => '8',
            'co_place_btn_size'       => '16',
            'co_btn_animation'        => 'lift',
            // Notices
            'notice_error_bg'     => '#fef2f2',
            'notice_error_border' => '#d63638',
            'notice_success_bg'   => '#f0fdf4',
            'notice_success_border'=>'#22c55e',
            'notice_info_bg'      => '#eff6ff',
            'notice_info_border'  => '#3b82f6',
        ];
    }
}

// Stop execution if we are not in the WordPress admin panel
// This prevents the HTML settings UI from leaking into the frontend WooCommerce pages
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( ! is_admin() || ! isset( $_GET['page'] ) || $_GET['page'] !== 'mh-plug-woo-pages' ) {
    return;
}

$opts = wp_parse_args( get_option( 'mh_plug_woo_pages_settings', [] ), mh_plug_woo_pages_defaults() );


// Helper to render a color field
function mh_woo_color( $key, $label, $opts ) {
    ?>
    <div class="mh-tb-form-group">
        <label class="mh-tb-form-label"><?php echo esc_html( $label ); ?></label>
        <div class="mh-tb-form-field-control">
            <input type="text" class="mh-color-picker" name="mh_plug_woo_pages_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr( $opts[$key] ); ?>" />
        </div>
    </div>
    <?php
}

// Helper to render a number field
function mh_woo_number( $key, $label, $opts, $min = 0, $max = 100, $suffix = 'px' ) {
    ?>
    <div class="mh-tb-form-group">
        <label class="mh-tb-form-label"><?php echo esc_html( $label ); ?> (<?php echo $suffix; ?>)</label>
        <div class="mh-tb-form-field-control">
            <input type="number" name="mh_plug_woo_pages_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr( $opts[$key] ); ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>" />
        </div>
    </div>
    <?php
}

// Helper for select
function mh_woo_select( $key, $label, $opts, $choices ) {
    ?>
    <div class="mh-tb-form-group">
        <label class="mh-tb-form-label"><?php echo esc_html( $label ); ?></label>
        <div class="mh-tb-form-field-control">
            <select name="mh_plug_woo_pages_settings[<?php echo esc_attr($key); ?>]">
                <?php foreach ( $choices as $val => $lbl ) : ?>
                    <option value="<?php echo esc_attr($val); ?>" <?php selected( $opts[$key], $val ); ?>><?php echo esc_html($lbl); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php
}

$fonts = [
    ''              => '— Default (Inherit) —',
    'Inter'         => 'Inter',
    'Roboto'        => 'Roboto',
    'Outfit'        => 'Outfit',
    'Poppins'       => 'Poppins',
    'Open Sans'     => 'Open Sans',
    'Lato'          => 'Lato',
    'Montserrat'    => 'Montserrat',
    'Nunito'        => 'Nunito',
    'Raleway'       => 'Raleway',
    'Playfair Display' => 'Playfair Display',
];

$animations = [
    'none'   => 'None',
    'lift'   => 'Lift Up',
    'scale'  => 'Scale Up',
    'glow'   => 'Glow Shadow',
    'pulse'  => 'Pulse',
];

$weights = [
    '400' => 'Normal (400)',
    '500' => 'Medium (500)',
    '600' => 'Semi-Bold (600)',
    '700' => 'Bold (700)',
    '800' => 'Extra Bold (800)',
];
?>
<div class="wrap mh-plug-admin-wrap">
    <div class="mh-tb-header">
        <div class="mh-tb-header-text">
            <h1 class="mh-plug-title"><?php esc_html_e( 'WooCommerce Customizer', 'mh-plug-ecommerce-builder-widgets' ); ?></h1>
            <p class="mh-tb-description"><?php esc_html_e( 'Style your Cart and Checkout pages', 'mh-plug-ecommerce-builder-widgets' ); ?></p>
        </div>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields( 'mh_plug_woo_pages_group' ); ?>

        <div class="mh-accordion">

            <!-- TYPOGRAPHY -->
            <div class="mh-accordion-item mh-active">
                <div class="mh-accordion-header">
                    <span class="mh-accordion-title"><?php esc_html_e( 'Global Typography', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                    <span class="mh-header-controls">
                        <span class="mh-accordion-icon">+</span>
                    </span>
                </div>
                <div class="mh-accordion-content" style="display:block;">
                    <div class="mh-settings-grid">
                        <?php mh_woo_select( 'font_family', 'Font Family', $opts, $fonts ); ?>
                    </div>
                </div>
            </div>

            <!-- CART PAGE - LAYOUT -->
            <div class="mh-accordion-item">
                <div class="mh-accordion-header">
                    <span class="mh-accordion-title"><?php esc_html_e( 'Cart Page — Headings & Table', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                    <span class="mh-header-controls">
                        <span class="mh-accordion-icon">+</span>
                    </span>
                </div>
                <div class="mh-accordion-content">
                    <div class="mh-settings-grid">
                        <h3 class="mh-tb-section-heading">Page Heading</h3>
                        <?php
                        mh_woo_color( 'cart_heading_color', 'Color', $opts );
                        mh_woo_number( 'cart_heading_size', 'Font Size', $opts, 14, 60 );
                        mh_woo_select( 'cart_heading_weight', 'Font Weight', $opts, $weights );
                        ?>
                        <h3 class="mh-tb-section-heading">Table</h3>
                        <?php
                        mh_woo_color( 'cart_table_header_bg', 'Header Background', $opts );
                        mh_woo_color( 'cart_table_header_color', 'Header Text Color', $opts );
                        mh_woo_color( 'cart_product_name_color', 'Product Name Color', $opts );
                        mh_woo_color( 'cart_product_name_hover', 'Product Name Hover', $opts );
                        mh_woo_color( 'cart_price_color', 'Price Color', $opts );
                        mh_woo_color( 'cart_remove_color', 'Remove Icon Color', $opts );
                        mh_woo_color( 'cart_remove_hover', 'Remove Icon Hover', $opts );
                        mh_woo_color( 'cart_qty_bg', 'Quantity Input Background', $opts );
                        mh_woo_color( 'cart_qty_border', 'Quantity Input Border', $opts );
                        mh_woo_color( 'cart_totals_bg', 'Cart Totals Background', $opts );
                        ?>
                    </div>
                </div>
            </div>

            <!-- CART PAGE - BUTTONS -->
            <div class="mh-accordion-item">
                <div class="mh-accordion-header">
                    <span class="mh-accordion-title"><?php esc_html_e( 'Cart Page — Buttons', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                    <span class="mh-header-controls">
                        <span class="mh-accordion-icon">+</span>
                    </span>
                </div>
                <div class="mh-accordion-content">
                    <div class="mh-settings-grid">
                        <?php mh_woo_select( 'cart_btn_animation', 'Hover Animation', $opts, $animations ); ?>

                        <h3 class="mh-tb-section-heading">Update Cart Button</h3>
                        <?php
                        mh_woo_color( 'cart_update_btn_bg', 'Background', $opts );
                        mh_woo_color( 'cart_update_btn_color', 'Text Color', $opts );
                        mh_woo_color( 'cart_update_btn_hover_bg', 'Hover Background', $opts );
                        mh_woo_color( 'cart_update_btn_hover_color', 'Hover Text Color', $opts );
                        ?>

                        <h3 class="mh-tb-section-heading">Apply Coupon Button</h3>
                        <?php
                        mh_woo_color( 'cart_coupon_btn_bg', 'Background', $opts );
                        mh_woo_color( 'cart_coupon_btn_color', 'Text Color', $opts );
                        mh_woo_color( 'cart_coupon_btn_hover_bg', 'Hover Background', $opts );
                        mh_woo_color( 'cart_coupon_btn_hover_color', 'Hover Text Color', $opts );
                        ?>

                        <h3 class="mh-tb-section-heading">Proceed to Checkout Button</h3>
                        <?php
                        mh_woo_color( 'cart_checkout_btn_bg', 'Background', $opts );
                        mh_woo_color( 'cart_checkout_btn_color', 'Text Color', $opts );
                        mh_woo_color( 'cart_checkout_btn_hover_bg', 'Hover Background', $opts );
                        mh_woo_color( 'cart_checkout_btn_hover_color', 'Hover Text Color', $opts );
                        mh_woo_number( 'cart_checkout_btn_radius', 'Border Radius', $opts, 0, 50 );
                        mh_woo_number( 'cart_checkout_btn_size', 'Font Size', $opts, 10, 30 );
                        ?>
                    </div>
                </div>
            </div>

            <!-- CHECKOUT - FORM -->
            <div class="mh-accordion-item">
                <div class="mh-accordion-header">
                    <span class="mh-accordion-title"><?php esc_html_e( 'Checkout Page — Headings & Form', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                    <span class="mh-header-controls">
                        <span class="mh-accordion-icon">+</span>
                    </span>
                </div>
                <div class="mh-accordion-content">
                    <div class="mh-settings-grid">
                        <h3 class="mh-tb-section-heading">Section Headings</h3>
                        <?php
                        mh_woo_color( 'co_heading_color', 'Color', $opts );
                        mh_woo_number( 'co_heading_size', 'Font Size', $opts, 12, 48 );
                        mh_woo_select( 'co_heading_weight', 'Font Weight', $opts, $weights );
                        ?>
                        <h3 class="mh-tb-section-heading">Form Labels</h3>
                        <?php
                        mh_woo_color( 'co_label_color', 'Label Color', $opts );
                        mh_woo_number( 'co_label_size', 'Label Font Size', $opts, 10, 24 );
                        ?>
                        <h3 class="mh-tb-section-heading">Form Inputs</h3>
                        <?php
                        mh_woo_color( 'co_input_bg', 'Background', $opts );
                        mh_woo_color( 'co_input_color', 'Text Color', $opts );
                        mh_woo_color( 'co_input_border', 'Border Color', $opts );
                        mh_woo_color( 'co_input_focus_border', 'Focus Border Color', $opts );
                        ?>
                        <h3 class="mh-tb-section-heading">Order Review Section</h3>
                        <?php
                        mh_woo_color( 'co_order_bg', 'Background', $opts );
                        mh_woo_color( 'co_order_border', 'Border Color', $opts );
                        ?>
                    </div>
                </div>
            </div>

            <!-- CHECKOUT - BUTTON -->
            <div class="mh-accordion-item">
                <div class="mh-accordion-header">
                    <span class="mh-accordion-title"><?php esc_html_e( 'Checkout Page — Place Order Button', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                    <span class="mh-header-controls">
                        <span class="mh-accordion-icon">+</span>
                    </span>
                </div>
                <div class="mh-accordion-content">
                    <div class="mh-settings-grid">
                        <?php
                        mh_woo_select( 'co_btn_animation', 'Hover Animation', $opts, $animations );
                        mh_woo_color( 'co_place_btn_bg', 'Background', $opts );
                        mh_woo_color( 'co_place_btn_color', 'Text Color', $opts );
                        mh_woo_color( 'co_place_btn_hover_bg', 'Hover Background', $opts );
                        mh_woo_color( 'co_place_btn_hover_color', 'Hover Text Color', $opts );
                        mh_woo_number( 'co_place_btn_radius', 'Border Radius', $opts, 0, 50 );
                        mh_woo_number( 'co_place_btn_size', 'Font Size', $opts, 10, 30 );
                        ?>
                    </div>
                </div>
            </div>

            <!-- NOTICES -->
            <div class="mh-accordion-item">
                <div class="mh-accordion-header">
                    <span class="mh-accordion-title"><?php esc_html_e( 'WooCommerce Notices', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                    <span class="mh-header-controls">
                        <span class="mh-accordion-icon">+</span>
                    </span>
                </div>
                <div class="mh-accordion-content">
                    <div class="mh-settings-grid">
                        <h3 class="mh-tb-section-heading">Error Notice</h3>
                        <?php
                        mh_woo_color( 'notice_error_bg', 'Background', $opts );
                        mh_woo_color( 'notice_error_border', 'Border Color', $opts );
                        ?>
                        <h3 class="mh-tb-section-heading">Success Notice</h3>
                        <?php
                        mh_woo_color( 'notice_success_bg', 'Background', $opts );
                        mh_woo_color( 'notice_success_border', 'Border Color', $opts );
                        ?>
                        <h3 class="mh-tb-section-heading">Info Notice</h3>
                        <?php
                        mh_woo_color( 'notice_info_bg', 'Background', $opts );
                        mh_woo_color( 'notice_info_border', 'Border Color', $opts );
                        ?>
                    </div>
                </div>
            </div>

        </div>

        <button type="submit" class="mh-button">
            <span class="mh-button-content-wrapper">
                <i class="dashicons dashicons-saved"></i>
                <span class="mh-button-text"><?php esc_html_e( 'Save Changes', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
            </span>
        </button>
    </form>
</div>
