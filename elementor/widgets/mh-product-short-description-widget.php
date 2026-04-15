<?php
/**
 * MH Product Short Description Widget
 *
 * Displays the WooCommerce Product Short Description dynamically.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

class MH_Product_Short_Description_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_short_description'; }
    public function get_title() { return __( 'MH Short Description', 'mh-plug' ); }
    public function get_icon() { return 'eicon-text-area'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'short', 'description', 'woocommerce', 'mh', 'excerpt' ]; }

    protected function register_controls() {

        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Description Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'align', [
            'label'        => __( 'Alignment', 'mh-plug' ),
            'type'         => Controls_Manager::CHOOSE,
            'options'      => [
                'left'    => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center'  => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'   => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
                'justify' => [ 'title' => __( 'Justified', 'mh-plug' ), 'icon' => 'eicon-text-align-justify' ],
            ],
            'default'      => 'left',
            'selectors'    => [
                '{{WRAPPER}} .mh-product-short-description' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE ── */
        $this->start_controls_section( 'style_section', [
            'label' => __( 'Text Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'text_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#555555',
            'selectors' => [
                '{{WRAPPER}} .mh-product-short-description' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-product-short-description p' => 'color: {{VALUE}};', // Ensure paragraph tags inherit the color
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'text_typography',
            'selector' => '{{WRAPPER}} .mh-product-short-description, {{WRAPPER}} .mh-product-short-description p',
        ] );

        $this->add_group_control( Group_Control_Text_Shadow::get_type(), [
            'name'     => 'text_shadow',
            'selector' => '{{WRAPPER}} .mh-product-short-description',
        ] );

        $this->add_responsive_control( 'text_margin', [
            'label'      => __( 'Margin', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-short-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        // 2. Elementor Editor Mock Product Context
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
                echo '<div style="padding: 15px; border: 1px dashed #d63638; color: #d63638; text-align: center;"><strong>MH Plug:</strong> Please create a product to preview the short description.</div>';
            }
            return;
        }

        // 4. Fetch and filter the description
        $short_description = apply_filters( 'woocommerce_short_description', $product->get_short_description() );

        // Provide a helpful placeholder in the editor if the product happens to be missing a description
        if ( empty( $short_description ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                $short_description = '<p style="color:#999; font-style:italic;">' . esc_html__( 'This product does not have a short description. Add one in the WooCommerce product data settings to see it here.', 'mh-plug' ) . '</p>';
            } else {
                return; // Hide completely on the frontend if empty
            }
        }

        // 5. Render
        echo '<div class="mh-product-short-description woocommerce-product-details__short-description">';
        echo wp_kses_post( $short_description );
        echo '</div>';
    }
}