<?php
/**
 * MH Product Price Widget
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

    protected function register_controls() {
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Price Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'align', [
            'label'   => __( 'Alignment', 'mh-plug' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'left'   => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [ '{{WRAPPER}} .mh-product-price' => 'text-align: {{VALUE}};', ],
        ] );
        $this->end_controls_section();

        $this->start_controls_section( 'style_main_price', [
            'label' => __( 'Main Price', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );
        $this->add_control( 'main_price_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .mh-product-price > .amount' => 'color: {{VALUE}};', 
                '{{WRAPPER}} .mh-product-price ins .amount' => 'color: {{VALUE}};', 
            ],
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'main_price_typography',
            'selector' => '{{WRAPPER}} .mh-product-price > .amount, {{WRAPPER}} .mh-product-price ins .amount',
        ] );
        $this->end_controls_section();

        $this->start_controls_section( 'style_old_price', [
            'label' => __( 'Old Price (Strikethrough)', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );
        $this->add_control( 'old_price_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#a8a8a8',
            'selectors' => [
                '{{WRAPPER}} .mh-product-price del' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-product-price del::after' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'old_price_typography',
            'selector' => '{{WRAPPER}} .mh-product-price del',
        ] );
        $this->add_responsive_control( 'old_price_gap', [
            'label'      => __( 'Gap', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'selectors'  => [ '{{WRAPPER}} .mh-product-price del' => 'margin-right: {{SIZE}}{{UNIT}};', ],
        ] );
        $this->end_controls_section();
    }

    protected function render() {
        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            return;
        }

        $price_html = $product->get_price_html();
        if ( ! empty( $price_html ) ) {
            echo '<style>
                .mh-product-price del { text-decoration: none !important; position: relative; display: inline-block; }
                .mh-product-price del::after { content: ""; position: absolute; top: 50%; left: 0; width: 100%; height: 1.5px; background-color: currentColor; transform: translateY(-50%); pointer-events: none; }
            </style>';
            echo '<div class="mh-product-price" style="display: inline-block; line-height: 1.2;">' . $price_html . '</div>';
        }
    }
}