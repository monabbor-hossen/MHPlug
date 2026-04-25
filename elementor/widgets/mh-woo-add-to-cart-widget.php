<?php
/**
 * MH Woo Add to Cart Widget
 *
 * Renders an inline Quantity Selector + Add to Cart button + Buy Now Button
 * for WooCommerce simple products.
 *
 * @package MH_Plug
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class MH_Woo_Add_To_Cart_Widget extends Widget_Base {

    public function get_name()        { return 'mh_woo_add_to_cart'; }
    public function get_title()       { return __( 'MH Add to Cart', 'mh-plug' ); }
    public function get_icon()        { return 'eicon-cart-solid'; }
    public function get_categories()  { return [ 'mh-plug-widgets' ]; }
    public function get_keywords()    { return [ 'add to cart', 'quantity', 'woocommerce', 'buy', 'buy now', 'mh' ]; }

    public function get_script_depends() {
        return [ 'mh-woo-scripts', 'mh-widgets-js' ];
    }

    public function get_style_depends() {
        return [ 'woocommerce-general', 'mh-woo-add-to-cart', 'mh-widgets-css' ];
    }

    // =========================================================
    // CONTROLS
    // =========================================================
    protected function register_controls() {

        /* ── CONTENT ─────────────────────────────────────────── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Buttons Content', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'btn_text', [
            'label'   => __( 'Add to Cart Text', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Add to Cart', 'mh-plug' ),
        ] );

        $this->add_control( 'show_buy_now', [
            'label'        => __( 'Show "Buy Now" Button', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'no',
            'separator'    => 'before',
        ] );

        $this->add_control( 'buy_now_text', [
            'label'     => __( 'Buy Now Text', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Buy Now', 'mh-plug' ),
            'condition' => [ 'show_buy_now' => 'yes' ],
        ] );

        $this->add_control( 'quantity_default', [
            'label'     => __( 'Default Quantity', 'mh-plug' ),
            'type'      => Controls_Manager::NUMBER,
            'default'   => 1,
            'min'       => 1,
            'separator' => 'before',
        ] );

        $this->add_control( 'heading_qty_icons', [
            'label'     => __( 'Quantity Icons', 'mh-plug' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_control( 'minus_icon', [
            'label'       => __( 'Minus Icon', 'mh-plug' ),
            'type'        => Controls_Manager::ICONS,
            'default'     => [ 'value' => 'fas fa-minus', 'library' => 'fa-solid' ],
        ] );

        $this->add_control( 'plus_icon', [
            'label'       => __( 'Plus Icon', 'mh-plug' ),
            'type'        => Controls_Manager::ICONS,
            'default'     => [ 'value' => 'fas fa-plus', 'library' => 'fa-solid' ],
        ] );

        $this->add_responsive_control( 'layout_align', [
            'label'     => __( 'Alignment', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => __( 'Left',   'mh-plug' ), 'icon' => 'eicon-h-align-left' ],
                'center'     => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-h-align-center' ],
                'flex-end'   => [ 'title' => __( 'Right',  'mh-plug' ), 'icon' => 'eicon-h-align-right' ],
            ],
            'default'   => 'flex-start',
            'selectors' => [ '{{WRAPPER}} .mh-atc-wrap' => 'justify-content: {{VALUE}};', ],
            'separator' => 'before',
        ] );

        $this->add_control( 'gap', [
            'label'      => __( 'Gap Between Items', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'    => [ 'size' => 12, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-atc-wrap' => 'gap: {{SIZE}}{{UNIT}};', ],
        ] );

        $this->end_controls_section();


        /* ── STYLE: ADD TO CART BUTTON ───────────────────────── */
        $this->start_controls_section( 'style_btn', [ 'label' => __( 'Add to Cart Button', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'btn_typography', 'selector' => '{{WRAPPER}} .mh-atc-btn' ] );
        $this->start_controls_tabs( 'btn_tabs' );
        $this->start_controls_tab( 'btn_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'btn_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-atc-btn' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_bg', [ 'label' => __( 'Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#1d2327', 'selectors' => [ '{{WRAPPER}} .mh-atc-btn' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'btn_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'btn_color_hover', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-atc-btn:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_bg_hover', [ 'label' => __( 'Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => [ '{{WRAPPER}} .mh-atc-btn:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_border', 'selector' => '{{WRAPPER}} .mh-atc-btn', 'separator' => 'before' ] );
        $this->add_control( 'btn_border_radius', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%' ], 'default' => [ 'top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .mh-atc-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_shadow', 'selector' => '{{WRAPPER}} .mh-atc-btn' ] );
        $this->add_responsive_control( 'btn_padding', [ 'label' => __( 'Padding', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'default' => [ 'top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .mh-atc-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ], 'separator' => 'before' ] );
        $this->end_controls_section();


        /* ── STYLE: BUY NOW BUTTON ───────────────────────── */
        $this->start_controls_section( 'style_buy_now_btn', [ 'label' => __( 'Buy Now Button', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_buy_now' => 'yes' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'buy_now_typography', 'selector' => '{{WRAPPER}} .mh-buy-now-btn' ] );
        $this->start_controls_tabs( 'buy_now_tabs' );
        $this->start_controls_tab( 'buy_now_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'buy_now_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-buy-now-btn' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'buy_now_bg', [ 'label' => __( 'Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => [ '{{WRAPPER}} .mh-buy-now-btn' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'buy_now_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'buy_now_color_hover', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-buy-now-btn:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'buy_now_bg_hover', [ 'label' => __( 'Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#a82a2b', 'selectors' => [ '{{WRAPPER}} .mh-buy-now-btn:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'buy_now_border', 'selector' => '{{WRAPPER}} .mh-buy-now-btn', 'separator' => 'before' ] );
        $this->add_control( 'buy_now_border_radius', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%' ], 'default' => [ 'top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .mh-buy-now-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'buy_now_shadow', 'selector' => '{{WRAPPER}} .mh-buy-now-btn' ] );
        $this->add_responsive_control( 'buy_now_padding', [ 'label' => __( 'Padding', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'default' => [ 'top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .mh-buy-now-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ], 'separator' => 'before' ] );
        $this->end_controls_section();


        /* ── STYLE: QUANTITY WRAPPER ─────────────────────────────── */
        $this->start_controls_section( 'style_qty_wrapper', [
            'label' => __( 'Quantity: Main Wrapper', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'qty_height', [
            'label'      => __( 'Overall Height', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 30, 'max' => 80 ] ],
            'default'    => [ 'size' => 46, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-qty-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-qty-btn'     => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-qty-input.qty' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'qty_wrapper_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5f5f5',
            'selectors' => [ '{{WRAPPER}} .mh-qty-wrapper' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'qty_wrapper_border',
            'selector'  => '{{WRAPPER}} .mh-qty-wrapper',
        ] );

        $this->add_control( 'qty_wrapper_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-qty-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;' ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'qty_wrapper_shadow',
            'selector' => '{{WRAPPER}} .mh-qty-wrapper',
        ] );

        $this->end_controls_section();


        /* ── STYLE: QUANTITY INPUT (NUMBER) ─────────────────────────────── */
        $this->start_controls_section( 'style_qty_input', [
            'label' => __( 'Quantity: Number Input', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'qty_input_width', [
            'label'      => __( 'Input Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [ 'px' => [ 'min' => 30, 'max' => 150 ] ],
            'default'    => [ 'size' => 50, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-qty-input.qty' => 'width: {{SIZE}}{{UNIT}} !important; max-width: {{SIZE}}{{UNIT}} !important;' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'qty_input_typography',
            'selector' => '{{WRAPPER}} .mh-qty-input.qty',
        ] );

        $this->start_controls_tabs( 'qty_input_tabs' );
        
        $this->start_controls_tab( 'qty_input_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'qty_input_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-qty-input.qty' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'qty_input_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'transparent',
            'selectors' => [ '{{WRAPPER}} .mh-qty-input.qty' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'qty_input_border',
            'selector'  => '{{WRAPPER}} .mh-qty-input.qty',
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'qty_input_focus', [ 'label' => __( 'Focus', 'mh-plug' ) ] );
        $this->add_control( 'qty_input_color_focus', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-qty-input.qty:focus' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'qty_input_bg_focus', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-qty-input.qty:focus' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'qty_input_border_focus',
            'selector'  => '{{WRAPPER}} .mh-qty-input.qty:focus',
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control( 'qty_input_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-qty-input.qty' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
            'separator'  => 'before',
        ] );

        $this->end_controls_section();


        /* ── STYLE: QUANTITY BUTTONS (+ / -) ─────────────────────────────── */
        $this->start_controls_section( 'style_qty_buttons', [
            'label' => __( 'Quantity: ± Buttons', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'qty_btn_width', [
            'label'      => __( 'Button Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [ 'px' => [ 'min' => 20, 'max' => 80 ] ],
            'default'    => [ 'size' => 40, 'unit' => 'px' ],
            'selectors'  => [ 
                // 🚀 FIX: Display Flex ensures perfectly centered icons no matter the width
                '{{WRAPPER}} .mh-qty-btn' => 'width: {{SIZE}}{{UNIT}} !important; display: flex; align-items: center; justify-content: center;' 
            ],
        ] );

        // 🚀 FIX: Hyper-specific selectors with !important to overpower default WooCommerce CSS
        $this->add_responsive_control( 'qty_btn_icon_size', [
            'label'      => __( 'Icon / Text Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 50 ] ],
            'selectors'  => [ 
                '{{WRAPPER}} .mh-qty-btn i' => 'font-size: {{SIZE}}{{UNIT}} !important;', 
                '{{WRAPPER}} .mh-qty-btn svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important; fill: currentColor;', 
                '{{WRAPPER}} .mh-qty-btn span' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                '{{WRAPPER}} .mh-qty-btn' => 'font-size: {{SIZE}}{{UNIT}} !important;' // Fallback
            ],
        ] );

        $this->start_controls_tabs( 'qty_btn_tabs' );
        
        $this->start_controls_tab( 'qty_btn_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'qty_btn_color', [
            'label'     => __( 'Icon / Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-qty-btn' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;' ],
        ] );
        $this->add_control( 'qty_btn_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'transparent',
            'selectors' => [ '{{WRAPPER}} .mh-qty-btn' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'qty_btn_border',
            'selector'  => '{{WRAPPER}} .mh-qty-btn',
        ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'qty_btn_shadow',
            'selector' => '{{WRAPPER}} .mh-qty-btn',
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'qty_btn_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'qty_btn_color_hover', [
            'label'     => __( 'Icon / Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-qty-btn:hover' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;' ],
        ] );
        $this->add_control( 'qty_btn_bg_hover', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-qty-btn:hover' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'qty_btn_border_hover',
            'selector'  => '{{WRAPPER}} .mh-qty-btn:hover',
        ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'qty_btn_shadow_hover',
            'selector' => '{{WRAPPER}} .mh-qty-btn:hover',
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control( 'qty_btn_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-qty-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
            'separator'  => 'before',
        ] );

        $this->end_controls_section();
    }

    // =========================================================
    // RENDER
    // =========================================================
    protected function render() {
        $settings = $this->get_settings_for_display();

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
            return;
        }

        $default_qty = max( 1, absint( $settings['quantity_default'] ?? 1 ) );
        $btn_text    = ! empty( $settings['btn_text'] ) ? $settings['btn_text'] : __( 'Add to Cart', 'mh-plug' );
        $buy_now_text = ! empty( $settings['buy_now_text'] ) ? $settings['buy_now_text'] : __( 'Buy Now', 'mh-plug' );
        
        $max_qty = $product->get_max_purchase_quantity();
        $max_attr = ( $max_qty > 0 ) ? 'max="' . esc_attr( $max_qty ) . '"' : '';

        // Pre-fetch the checkout URL for the Buy Now redirect
        $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '';

        ?>
        <div class="mh-atc-widget">
            <form class="cart mh-custom-cart-form mh-atc-form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">

                <div class="mh-atc-wrap">

                    <div class="mh-qty-wrapper" role="group" aria-label="<?php esc_attr_e( 'Product quantity', 'mh-plug' ); ?>">
                        
                        <button type="button" class="mh-qty-btn mh-qty-minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'mh-plug' ); ?>">
                            <?php 
                            if ( ! empty( $settings['minus_icon']['value'] ) ) {
                                \Elementor\Icons_Manager::render_icon( $settings['minus_icon'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                echo '<span aria-hidden="true">&#8722;</span>';
                            }
                            ?>
                        </button>
                        
                        <input
                            type="number"
                            class="mh-qty-input input-text qty text"
                            name="quantity"
                            value="<?php echo esc_attr( $default_qty ); ?>"
                            min="1"
                            <?php echo $max_attr; ?>
                            step="1"
                            autocomplete="off"
                            aria-label="<?php esc_attr_e( 'Quantity', 'mh-plug' ); ?>"
                        />
                        
                        <button type="button" class="mh-qty-btn mh-qty-plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'mh-plug' ); ?>">
                            <?php 
                            if ( ! empty( $settings['plus_icon']['value'] ) ) {
                                \Elementor\Icons_Manager::render_icon( $settings['plus_icon'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                echo '<span aria-hidden="true">&#43;</span>';
                            }
                            ?>
                        </button>

                    </div>

                    <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt mh-atc-btn">
                        <?php echo esc_html( $btn_text ); ?>
                    </button>

                    <?php if ( 'yes' === $settings['show_buy_now'] && ! empty( $checkout_url ) ) : ?>
                        <button type="button" class="button mh-buy-now-btn" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" data-checkout-url="<?php echo esc_url( $checkout_url ); ?>">
                            <?php echo esc_html( $buy_now_text ); ?>
                        </button>
                    <?php endif; ?>

                </div>

            </form>
        </div>
        <?php
    }
}