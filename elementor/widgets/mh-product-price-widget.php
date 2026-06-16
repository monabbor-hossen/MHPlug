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

class MH_Product_Price_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'mh_product_price'; }
    public function get_title() { return __( 'MH Product Price', 'mh-plug' ); }
    public function get_icon() { return 'eicon-product-price'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    /**
     * Injects `mh-product-price` onto Elementor's outer .elementor-widget-container
     * so the JS selector $('.mh-product-price .elementor-widget-container') resolves
     * correctly without requiring the user to add a manual CSS class in the editor.
     */
    public function get_html_wrapper_class() {
        return parent::get_html_wrapper_class() . ' mh-product-price';
    }

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
            'selectors' => [ '{{WRAPPER}} .mh-price-inner' => 'text-align: {{VALUE}};', ],
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
                '{{WRAPPER}} .mh-price-inner > .amount'     => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-price-inner ins .amount'   => 'color: {{VALUE}};',
            ],
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'main_price_typography',
            'selector' => '{{WRAPPER}} .mh-price-inner > .amount, {{WRAPPER}} .mh-price-inner ins .amount',
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
                '{{WRAPPER}} .mh-price-inner del'        => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-price-inner del::after' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'old_price_typography',
            'selector' => '{{WRAPPER}} .mh-price-inner del',
        ] );
        $this->add_responsive_control( 'old_price_gap', [
            'label'      => __( 'Gap', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'selectors'  => [ '{{WRAPPER}} .mh-price-inner del' => 'margin-right: {{SIZE}}{{UNIT}};', ],
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
            $css = '
                .mh-price-inner del { text-decoration: none !important; position: relative; display: inline-block; }
                .mh-price-inner del::after { content: ""; position: absolute; top: 50%; left: 0; width: 100%; height: 1.5px; background-color: currentColor; transform: translateY(-50%); pointer-events: none; }
            ';
            wp_register_style( 'mh-product-price-style', false );
            wp_enqueue_style( 'mh-product-price-style' );
            wp_add_inline_style( 'mh-product-price-style', $css );
            // The outer .elementor-widget-container already carries the .mh-product-price class
            // via get_html_wrapper_class(). The JS rewrites that container's innerHTML directly,
            // so the inner wrapper only needs its own structural class.
            echo '<div class="mh-price-inner" style="display: inline-block; line-height: 1.2;">' . $price_html . '</div>';
        }
    }
}