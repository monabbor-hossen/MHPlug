<?php
/**
 * MH Product Price Widget (Advanced)
 *
 * Fully customizable WooCommerce Product Price with Perfect Strikethrough.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

class MH_Product_Price_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_price'; }
    public function get_title() { return __( 'MH Product Price', 'mh-plug' ); }
    public function get_icon() { return 'eicon-product-price'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'price', 'sale', 'woocommerce', 'mh', 'advanced' ]; }

    protected function register_controls() {

        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Price Layout', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'align', [
            'label'        => __( 'Alignment', 'mh-plug' ),
            'type'         => Controls_Manager::CHOOSE,
            'options'      => [
                'left'   => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default'      => 'left',
            'selectors'    => [
                '{{WRAPPER}} .mh-product-price' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'layout_style', [
            'label'   => __( 'Layout Style', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'inline',
            'options' => [
                'inline'  => __( 'Inline (Side by Side)', 'mh-plug' ),
                'stacked' => __( 'Stacked (Top & Bottom)', 'mh-plug' ),
            ],
            'selectors' => [
                '{{WRAPPER}} .mh-product-price del' => 'display: {{VALUE}} === "stacked" ? "block" : "inline-block"; margin-bottom: {{VALUE}} === "stacked" ? "5px" : "0";',
                '{{WRAPPER}} .mh-product-price ins' => 'display: {{VALUE}} === "stacked" ? "block" : "inline-block";',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: MAIN PRICE ── */
        $this->start_controls_section( 'style_main_price', [
            'label' => __( 'Main Price (or Sale Price)', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'main_price_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [
                '{{WRAPPER}} .mh-product-price > .amount' => 'color: {{VALUE}};', 
                '{{WRAPPER}} .mh-product-price ins .amount' => 'color: {{VALUE}};', 
                '{{WRAPPER}} .mh-product-price ins' => 'text-decoration: none;',
                '{{WRAPPER}} .mh-product-price > bdi' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'main_price_typography',
            'selector' => '{{WRAPPER}} .mh-product-price > .amount, {{WRAPPER}} .mh-product-price ins .amount, {{WRAPPER}} .mh-product-price ins, {{WRAPPER}} .mh-product-price > bdi',
        ] );

        $this->add_group_control( Group_Control_Text_Shadow::get_type(), [
            'name'     => 'main_price_shadow',
            'selector' => '{{WRAPPER}} .mh-product-price > .amount, {{WRAPPER}} .mh-product-price ins .amount',
        ] );

        $this->end_controls_section();

        /* ── STYLE: OLD PRICE (STRIKETHROUGH) ── */
        $this->start_controls_section( 'style_old_price', [
            'label' => __( 'Old Price (Original)', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'old_price_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#a8a8a8',
            'selectors' => [
                '{{WRAPPER}} .mh-product-price del' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-product-price del .amount' => 'color: {{VALUE}};',
                // Apply the color to our custom strikethrough line
                '{{WRAPPER}} .mh-product-price del::after' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'old_price_typography',
            'selector' => '{{WRAPPER}} .mh-product-price del, {{WRAPPER}} .mh-product-price del .amount, {{WRAPPER}} .mh-product-price del bdi',
        ] );

        $this->add_control( 'strikethrough_thickness', [
            'label'      => __( 'Line Thickness', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 1, 'max' => 5 ] ],
            'default'    => [ 'size' => 1.5, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-price del::after' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'old_price_gap', [
            'label'      => __( 'Gap (Distance from Main Price)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'    => [ 'size' => 8, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-price del' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'layout_style' => 'inline',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: CURRENCY SYMBOL ── */
        $this->start_controls_section( 'style_currency', [
            'label' => __( 'Currency Symbol ($)', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'currency_color', [
            'label'     => __( 'Symbol Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .mh-product-price .woocommerce-Price-currencySymbol' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'currency_typography',
            'selector' => '{{WRAPPER}} .mh-product-price .woocommerce-Price-currencySymbol',
        ] );

        $this->add_responsive_control( 'currency_spacing', [
            'label'      => __( 'Spacing (from numbers)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-price .woocommerce-Price-currencySymbol' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'currency_align', [
            'label'      => __( 'Vertical Adjust', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => -20, 'max' => 20 ] ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-price .woocommerce-Price-currencySymbol' => 'position: relative; top: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                $mock_products = wc_get_products( [ 'limit' => 1, 'status' => 'publish' ] );
                if ( ! empty( $mock_products ) ) $product = $mock_products[0];
            }
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 15px; border: 1px dashed #d63638; color: #d63638; text-align: center;"><strong>MH Plug:</strong> Please create a product to preview the price.</div>';
            }
            return;
        }

        $price_html = $product->get_price_html();
        
        if ( ! empty( $price_html ) ) {
            // 🚀 THE FIX: Disable native text-decoration and use a perfect center-aligned pseudo-element
            echo '<style>
                .mh-product-price del { 
                    text-decoration: none !important; 
                    position: relative; 
                    display: inline-block; 
                }
                .mh-product-price del::after { 
                    content: ""; 
                    position: absolute; 
                    top: 50%; 
                    left: 0; 
                    width: 100%; 
                    background-color: currentColor; 
                    transform: translateY(-50%);
                    pointer-events: none;
                }
            </style>';
            echo '<div class="mh-product-price" style="display: inline-block; line-height: 1.2;">' . $price_html . '</div>';
        }
    }
}