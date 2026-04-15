<?php
/**
 * MH Product Category Widget
 *
 * Displays the WooCommerce Product Categories dynamically.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Product_Category_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_category'; }
    public function get_title() { return __( 'MH Product Category', 'mh-plug' ); }
    public function get_icon() { return 'eicon-product-categories'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'category', 'categories', 'woocommerce', 'mh' ]; }

    protected function register_controls() {

        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Category Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        // 🚀 NEW: Toggle to show/hide the prefix text
        $this->add_control( 'show_prefix', [
            'label'        => __( 'Show Prefix', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'prefix_text', [
            'label'       => __( 'Prefix Text', 'mh-plug' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Categories: ', 'mh-plug' ),
            'placeholder' => __( 'e.g. Categories:', 'mh-plug' ),
            'condition'   => [ 'show_prefix' => 'yes' ], // Only show if switch is ON
        ] );

        $this->add_control( 'separator', [
            'label'   => __( 'Separator', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => ', ',
        ] );

        $this->add_responsive_control( 'align', [
            'label'        => __( 'Alignment', 'mh-plug' ),
            'type'         => Controls_Manager::CHOOSE,
            'options'      => [
                'left'    => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center'  => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'   => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default'      => 'left',
            'selectors'    => [
                '{{WRAPPER}} .mh-product-category-wrap' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: PREFIX TEXT ── */
        $this->start_controls_section( 'style_prefix_section', [
            'label'     => __( 'Prefix Style', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_prefix' => 'yes' ], // Hide these style settings if prefix is hidden
        ] );

        $this->add_control( 'prefix_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [
                '{{WRAPPER}} .mh-category-prefix' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'prefix_typography',
            'selector' => '{{WRAPPER}} .mh-category-prefix',
        ] );

        $this->add_responsive_control( 'prefix_spacing', [
            'label'      => __( 'Spacing (Distance to categories)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
            'default'    => [ 'size' => 5, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-category-prefix' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: CATEGORY LINKS ── */
        $this->start_controls_section( 'style_links_section', [
            'label' => __( 'Category Links Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'links_typography',
            'selector' => '{{WRAPPER}} .mh-category-list, {{WRAPPER}} .mh-category-list a',
        ] );

        $this->start_controls_tabs( 'links_color_tabs' );

        /* Normal */
        $this->start_controls_tab( 'links_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'links_color', [
            'label'     => __( 'Link Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [
                '{{WRAPPER}} .mh-category-list a' => 'color: {{VALUE}}; transition: all 0.3s ease;',
            ],
        ] );
        $this->add_control( 'separator_color', [
            'label'     => __( 'Separator Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#888888',
            'selectors' => [
                '{{WRAPPER}} .mh-category-list' => 'color: {{VALUE}};', // Applies to the comma/separator text
            ],
        ] );
        $this->end_controls_tab();

        /* Hover */
        $this->start_controls_tab( 'links_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'links_hover_color', [
            'label'     => __( 'Link Hover Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [
                '{{WRAPPER}} .mh-category-list a:hover' => 'color: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control( 'wrap_margin', [
            'label'      => __( 'Widget Margin', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-category-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator'  => 'before',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

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
                echo '<div style="padding: 15px; border: 1px dashed #d63638; color: #d63638; text-align: center;"><strong>MH Plug:</strong> Please create a product to preview categories.</div>';
            }
            return;
        }

        $separator = $settings['separator'] ? esc_html( $settings['separator'] ) : ', ';
        $categories_html = wc_get_product_category_list( $product->get_id(), $separator );

        if ( empty( $categories_html ) ) {
            return; 
        }

        ?>
        <div class="mh-product-category-wrap">
            <?php 
            // 🚀 THE FIX: Only output the prefix if the switch is set to 'yes'
            if ( $settings['show_prefix'] === 'yes' && ! empty( $settings['prefix_text'] ) ) : 
            ?>
                <span class="mh-category-prefix"><?php echo esc_html( $settings['prefix_text'] ); ?></span>
            <?php endif; ?>
            <span class="mh-category-list"><?php echo wp_kses_post( $categories_html ); ?></span>
        </div>
        <?php
    }
}