<?php
/**
 * MH Product Price Widget
 *
 * Displays the WooCommerce Product Price dynamically.
 * Automatically handles regular price vs sale price formatting.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Product_Price_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_price'; }
    public function get_title() { return __( 'MH Product Price', 'mh-plug' ); }
    public function get_icon() { return 'eicon-product-price'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'price', 'sale', 'woocommerce', 'mh' ]; }

    protected function register_controls() {

        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Price Settings', 'mh-plug' ),
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

        $this->end_controls_section();

        /* ── STYLE ── */
        $this->start_controls_section( 'style_section', [
            'label' => __( 'Price Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'price_typography',
            'label'    => __( 'Typography', 'mh-plug' ),
            'selector' => '{{WRAPPER}} .mh-product-price .amount',
        ] );

        $this->add_control( 'price_color', [
            'label'     => __( 'Main Price Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [
                // Targets standard price, and the SALE price if it's on sale
                '{{WRAPPER}} .mh-product-price .amount' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-product-price ins .amount' => 'color: {{VALUE}}; text-decoration: none;',
            ],
        ] );

        $this->add_control( 'old_price_color', [
            'label'     => __( 'Old Price Color (When on Sale)', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#a8a8a8',
            'selectors' => [
                // Targets the crossed-out price
                '{{WRAPPER}} .mh-product-price del .amount' => 'color: {{VALUE}}; font-size: 0.8em; margin-right: 8px;',
                '{{WRAPPER}} .mh-product-price del' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'price_margin', [
            'label'      => __( 'Margin', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        // 1. Get the global product
        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        // 2. Elementor Editor Mock Product Context (Protects the Template Builder)
        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                $mock_products = wc_get_products( [ 'limit' => 1, 'status' => 'publish' ] );
                if ( ! empty( $mock_products ) ) {
                    $product = $mock_products[0];
                }
            }
        }

        // 3. Final Safety Check
        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 15px; border: 1px dashed #d63638; color: #d63638; text-align: center;"><strong>MH Plug:</strong> Please create a product to preview the price.</div>';
            }
            return;
        }

        // 4. Render the native WooCommerce Price HTML
        $price_html = $product->get_price_html();
        
        if ( ! empty( $price_html ) ) {
            echo '<div class="mh-product-price" style="display: inline-block;">' . $price_html . '</div>';
        }
    }
}