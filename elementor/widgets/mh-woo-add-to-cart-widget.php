<?php
/**
 * MH Woo Add to Cart Widget
 *
 * Renders an inline Quantity Selector + Add to Cart button for any
 * WooCommerce simple product. Designed for use on single product pages
 * or any page where a product context is available.
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
    public function get_keywords()    { return [ 'add to cart', 'quantity', 'woocommerce', 'buy', 'mh' ]; }

    /**
     * Enqueue scripts / styles specific to this widget on the frontend.
     * Called automatically by Elementor when the widget is rendered.
     */
    public function get_script_depends() {
        return [ 'mh-woo-scripts' ];
    }

    public function get_style_depends() {
        return [ 'woocommerce-general', 'mh-woo-add-to-cart' ];
    }

    // =========================================================
    // CONTROLS
    // =========================================================
    protected function register_controls() {

        /* ── CONTENT ─────────────────────────────────────────── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Button Content', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'btn_text', [
            'label'   => __( 'Button Text', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Add to Cart', 'mh-plug' ),
        ] );

        $this->add_control( 'quantity_default', [
            'label'   => __( 'Default Quantity', 'mh-plug' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 1,
            'min'     => 1,
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
            'selectors' => [
                '{{WRAPPER}} .mh-atc-wrap' => 'justify-content: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'gap', [
            'label'      => __( 'Gap Between Items', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'    => [ 'size' => 12, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-atc-wrap' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: ADD TO CART BUTTON ───────────────────────── */
        $this->start_controls_section( 'style_btn', [
            'label' => __( 'Add to Cart Button', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'btn_typography',
            'selector' => '{{WRAPPER}} .mh-atc-btn',
        ] );

        $this->start_controls_tabs( 'btn_tabs' );

        /* — Normal — */
        $this->start_controls_tab( 'btn_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'btn_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-atc-btn' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'btn_bg', [
            'label'     => __( 'Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-atc-btn' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        /* — Hover — */
        $this->start_controls_tab( 'btn_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'btn_color_hover', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-atc-btn:hover' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'btn_bg_hover', [
            'label'     => __( 'Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-atc-btn:hover' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'btn_border',
            'selector'  => '{{WRAPPER}} .mh-atc-btn',
            'separator' => 'before',
        ] );

        $this->add_control( 'btn_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-atc-btn' =>
                    'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'btn_shadow',
            'selector' => '{{WRAPPER}} .mh-atc-btn',
        ] );

        $this->add_responsive_control( 'btn_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-atc-btn' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator' => 'before',
        ] );

        $this->end_controls_section();

        /* ── STYLE: QUANTITY BOX ─────────────────────────────── */
        $this->start_controls_section( 'style_qty', [
            'label' => __( 'Quantity Selector', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'qty_typography',
            'selector' => '{{WRAPPER}} .mh-qty-input, {{WRAPPER}} .mh-qty-btn',
        ] );

        $this->add_control( 'qty_color', [
            'label'     => __( 'Text / Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [
                '{{WRAPPER}} .mh-qty-input' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-qty-btn'   => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'qty_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5f5f5',
            'selectors' => [
                '{{WRAPPER}} .mh-qty-wrapper' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'qty_btn_bg', [
            'label'     => __( '±  Button Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#e8e8e8',
            'selectors' => [
                '{{WRAPPER}} .mh-qty-btn' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'qty_btn_bg_hover', [
            'label'     => __( '±  Button Hover BG', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [
                '{{WRAPPER}} .mh-qty-btn:hover' => 'background-color: {{VALUE}}; color: #fff;',
            ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'qty_border',
            'selector'  => '{{WRAPPER}} .mh-qty-wrapper',
            'separator' => 'before',
        ] );

        $this->add_control( 'qty_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-qty-wrapper' =>
                    'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        /* Height of the entire quantity row (buttons + input) */
        $this->add_responsive_control( 'qty_height', [
            'label'      => __( 'Height', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 30, 'max' => 80 ] ],
            'default'    => [ 'size' => 46, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-qty-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-qty-btn'     => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-qty-input'   => 'height: {{SIZE}}{{UNIT}};',
            ],
            'separator' => 'before',
        ] );

        $this->add_responsive_control( 'qty_btn_width', [
            'label'      => __( '±  Button Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 20, 'max' => 80 ] ],
            'default'    => [ 'size' => 40, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-qty-btn' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'qty_input_width', [
            'label'      => __( 'Input Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 30, 'max' => 120 ] ],
            'default'    => [ 'size' => 56, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-qty-input' => 'width: {{SIZE}}{{UNIT}};',
            ],
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
        
        // 🚀 THE FIX: Safely determine max quantity attribute
        $max_qty = $product->get_max_purchase_quantity();
        // If the product has unlimited stock, WooCommerce sets this to -1.
        // We only output the max attribute if it is greater than 0!
        $max_attr = ( $max_qty > 0 ) ? 'max="' . esc_attr( $max_qty ) . '"' : '';

        ?>
        <div class="mh-atc-widget">
            <form class="cart mh-custom-cart-form mh-atc-form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">

                <div class="mh-atc-wrap">

                    <div class="mh-qty-wrapper" role="group" aria-label="<?php esc_attr_e( 'Product quantity', 'mh-plug' ); ?>">
                        <button type="button" class="mh-qty-btn mh-qty-minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'mh-plug' ); ?>">
                            <span aria-hidden="true">&#8722;</span>
                        </button>
                        
                        <input
                            type="number"
                            class="mh-qty-input input-text qty text"
                            name="quantity"
                            value="<?php echo esc_attr( $default_qty ); ?>"
                            min="1"
                            <?php echo $max_attr; // Prints max="..." safely or nothing at all! ?>
                            step="1"
                            autocomplete="off"
                            aria-label="<?php esc_attr_e( 'Quantity', 'mh-plug' ); ?>"
                        />
                        
                        <button type="button" class="mh-qty-btn mh-qty-plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'mh-plug' ); ?>">
                            <span aria-hidden="true">&#43;</span>
                        </button>
                    </div><button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt mh-atc-btn">
                        <?php echo esc_html( $btn_text ); ?>
                    </button>

                </div></form>
        </div><?php
    }
}