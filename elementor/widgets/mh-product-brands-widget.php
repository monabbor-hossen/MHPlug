<?php
/**
 * MH Product Brands Widget
 *
 * Displays the WooCommerce Product Brands dynamically.
 * Includes a taxonomy slug option to support ANY brands plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Product_Brands_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_brands'; }
    public function get_title() { return __( 'MH Product Brands', 'mh-plug' ); }
    public function get_icon() { return 'eicon-price-list'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'brand', 'brands', 'woocommerce', 'mh', 'taxonomy' ]; }

    protected function register_controls() {

        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Brands Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'taxonomy_slug', [
            'label'       => __( 'Taxonomy Slug', 'mh-plug' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'product_brand', // Default for official Woo Brands
            'description' => __( 'Change this if your brands plugin uses a different slug (e.g. yith_product_brand, pwb-brand)', 'mh-plug' ),
        ] );

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
            'default'     => __( 'Brand: ', 'mh-plug' ),
            'placeholder' => __( 'e.g. Brand:', 'mh-plug' ),
            'condition'   => [ 'show_prefix' => 'yes' ],
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
                '{{WRAPPER}} .mh-product-brands-wrap' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: PREFIX TEXT ── */
        $this->start_controls_section( 'style_prefix_section', [
            'label'     => __( 'Prefix Style', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_prefix' => 'yes' ],
        ] );

        $this->add_control( 'prefix_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [
                '{{WRAPPER}} .mh-brands-prefix' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'prefix_typography',
            'selector' => '{{WRAPPER}} .mh-brands-prefix',
        ] );

        $this->add_responsive_control( 'prefix_spacing', [
            'label'      => __( 'Spacing (Distance to brands)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
            'default'    => [ 'size' => 5, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-brands-prefix' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: BRAND LINKS ── */
        $this->start_controls_section( 'style_links_section', [
            'label' => __( 'Brand Links Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'links_typography',
            'selector' => '{{WRAPPER}} .mh-brands-list, {{WRAPPER}} .mh-brands-list a',
        ] );

        $this->start_controls_tabs( 'links_color_tabs' );

        /* Normal */
        $this->start_controls_tab( 'links_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'links_color', [
            'label'     => __( 'Link Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [
                '{{WRAPPER}} .mh-brands-list a' => 'color: {{VALUE}}; transition: all 0.3s ease;',
            ],
        ] );
        $this->add_control( 'separator_color', [
            'label'     => __( 'Separator Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#888888',
            'selectors' => [
                '{{WRAPPER}} .mh-brands-list' => 'color: {{VALUE}};',
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
                '{{WRAPPER}} .mh-brands-list a:hover' => 'color: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control( 'wrap_margin', [
            'label'      => __( 'Widget Margin', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-brands-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                echo '<div style="padding: 15px; border: 1px dashed #d63638; color: #d63638; text-align: center;"><strong>MH Plug:</strong> Please create a product to preview brands.</div>';
            }
            return;
        }

        $taxonomy  = !empty( $settings['taxonomy_slug'] ) ? sanitize_text_field( $settings['taxonomy_slug'] ) : 'product_brand';
        $separator = $settings['separator'] ? esc_html( $settings['separator'] ) : ', ';
        
        // Fetch the custom taxonomy links
        $brands_html = '';
        if ( taxonomy_exists( $taxonomy ) ) {
            $brands_html = get_the_term_list( $product->get_id(), $taxonomy, '', $separator, '' );
        }

        if ( empty( $brands_html ) || is_wp_error( $brands_html ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="color:#999; font-size:13px;"><em>No brands assigned or taxonomy "' . esc_html( $taxonomy ) . '" not found.</em></div>';
            }
            return; 
        }

        ?>
        <div class="mh-product-brands-wrap">
            <?php 
            if ( $settings['show_prefix'] === 'yes' && ! empty( $settings['prefix_text'] ) ) : 
            ?>
                <span class="mh-brands-prefix"><?php echo esc_html( $settings['prefix_text'] ); ?></span>
            <?php endif; ?>
            <span class="mh-brands-list"><?php echo wp_kses_post( $brands_html ); ?></span>
        </div>
        <?php
    }
}