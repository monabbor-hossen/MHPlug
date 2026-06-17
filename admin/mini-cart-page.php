<?php
/**
 * MH Plug - Mini Cart Settings Admin Page
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$opts = get_option( 'mh_plug_mini_cart_settings', [] );

// Defaults
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
?>
<div class="wrap mh-plug-admin-wrap">

    <div class="mh-tb-header">
        <div class="mh-tb-header-text">
            <h1 class="mh-plug-title"><?php esc_html_e( 'Mini Cart Settings', 'mh-plug-ecommerce-builder-widgets' ); ?></h1>
            <p class="mh-tb-description"><?php esc_html_e( 'Configure the off-canvas sidebar panel that appears on your storefront.', 'mh-plug-ecommerce-builder-widgets' ); ?></p>
        </div>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields( 'mh_plug_mini_cart_group' ); ?>

        <div class="mh-accordion">

            <!-- ─── GENERAL ─── -->
            <div class="mh-accordion-item mh-active">
                <div class="mh-accordion-header">
                    <span class="mh-accordion-title"><?php esc_html_e( 'General', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                    <span class="mh-accordion-icon">+</span>
                </div>
                <div class="mh-accordion-content" style="display:block;">
                    <div class="mh-settings-grid" style="max-width:600px;">

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Enable Off-Canvas Mini Cart', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <label class="switch">
                                    <input class="cb" type="checkbox" name="mh_plug_mini_cart_settings[enable_mini_cart]" value="1" <?php checked( $opts['enable_mini_cart'], '1' ); ?> />
                                    <span class="toggle"><span class="left">off</span><span class="right">on</span></span>
                                </label>
                            </div>
                        </div>

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Cart Page URL', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <input type="url" name="mh_plug_mini_cart_settings[cart_url]" value="<?php echo esc_attr( $opts['cart_url'] ); ?>" placeholder="<?php echo esc_attr( class_exists('WooCommerce') ? wc_get_cart_url() : '/cart/' ); ?>" />
                                <p class="description"><?php esc_html_e( 'Leave empty to use the default WooCommerce cart page URL.', 'mh-plug-ecommerce-builder-widgets' ); ?></p>
                            </div>
                        </div>

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Checkout Page URL', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <input type="url" name="mh_plug_mini_cart_settings[checkout_url]" value="<?php echo esc_attr( $opts['checkout_url'] ); ?>" placeholder="<?php echo esc_attr( class_exists('WooCommerce') ? wc_get_checkout_url() : '/checkout/' ); ?>" />
                                <p class="description"><?php esc_html_e( 'Leave empty to use the default WooCommerce checkout page URL.', 'mh-plug-ecommerce-builder-widgets' ); ?></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>



            <!-- ─── PANEL STYLE ─── -->
            <div class="mh-accordion-item">
                <div class="mh-accordion-header">
                    <span class="mh-accordion-title"><?php esc_html_e( 'Sidebar Panel', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                    <span class="mh-accordion-icon">+</span>
                </div>
                <div class="mh-accordion-content">
                    <div class="mh-settings-grid" style="max-width:600px;">

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Panel Background', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <input type="text" class="mh-color-picker" name="mh_plug_mini_cart_settings[panel_bg]" value="<?php echo esc_attr( $opts['panel_bg'] ); ?>" />
                            </div>
                        </div>

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Panel Text Color', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <input type="text" class="mh-color-picker" name="mh_plug_mini_cart_settings[panel_text_color]" value="<?php echo esc_attr( $opts['panel_text_color'] ); ?>" />
                            </div>
                        </div>

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Panel Width (px)', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <input type="number" name="mh_plug_mini_cart_settings[panel_width]" value="<?php echo esc_attr( $opts['panel_width'] ); ?>" min="280" max="600" />
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ─── BUTTON STYLES ─── -->
            <div class="mh-accordion-item">
                <div class="mh-accordion-header">
                    <span class="mh-accordion-title"><?php esc_html_e( 'Button Styles', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                    <span class="mh-accordion-icon">+</span>
                </div>
                <div class="mh-accordion-content">
                    <div class="mh-settings-grid" style="max-width:600px;">

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Button Border Radius (px)', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <input type="number" name="mh_plug_mini_cart_settings[btn_border_radius]" value="<?php echo esc_attr( $opts['btn_border_radius'] ); ?>" min="0" max="50" />
                            </div>
                        </div>

                        <h3 style="margin:20px 0 5px; color:#004265;"><?php esc_html_e( '"View Cart" Button', 'mh-plug-ecommerce-builder-widgets' ); ?></h3>

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <input type="text" class="mh-color-picker" name="mh_plug_mini_cart_settings[btn_view_cart_bg]" value="<?php echo esc_attr( $opts['btn_view_cart_bg'] ); ?>" />
                            </div>
                        </div>

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Text Color', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <input type="text" class="mh-color-picker" name="mh_plug_mini_cart_settings[btn_view_cart_text]" value="<?php echo esc_attr( $opts['btn_view_cart_text'] ); ?>" />
                            </div>
                        </div>

                        <h3 style="margin:20px 0 5px; color:#004265;"><?php esc_html_e( '"Checkout" Button', 'mh-plug-ecommerce-builder-widgets' ); ?></h3>

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <input type="text" class="mh-color-picker" name="mh_plug_mini_cart_settings[btn_checkout_bg]" value="<?php echo esc_attr( $opts['btn_checkout_bg'] ); ?>" />
                            </div>
                        </div>

                        <div class="mh-form-group">
                            <label class="mh-form-label"><?php esc_html_e( 'Text Color', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                            <div class="mh-form-field-control">
                                <input type="text" class="mh-color-picker" name="mh_plug_mini_cart_settings[btn_checkout_text]" value="<?php echo esc_attr( $opts['btn_checkout_text'] ); ?>" />
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- .mh-accordion -->

        <?php submit_button(); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($){
    // Init color pickers
    if ( $.fn.wpColorPicker ) {
        $('.mh-color-picker').wpColorPicker();
    }
});
</script>
