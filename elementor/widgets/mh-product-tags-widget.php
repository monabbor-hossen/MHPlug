<?php
/**
 * MH Product Tags Widget
 *
 * Displays the WooCommerce Product Tags dynamically.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Product_Tags_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_tags'; }
    public function get_title() { return __( 'MH Product Tags', 'mh-plug' ); }
    public function get_icon() { return 'eicon-tags'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'tag', 'tags', 'woocommerce', 'mh' ]; }

    protected function register_controls() {

        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Tags Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
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
            'default'     => __( 'Tags: ', 'mh-plug' ),
            'placeholder' => __( 'e.g. Tags:', 'mh-plug' ),
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
                '{{WRAPPER}} .mh-product-tags-wrap' => 'text-align: {{VALUE}};',
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
                '{{WRAPPER}} .mh-tags-prefix' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'prefix_typography',
            'selector' => '{{WRAPPER}} .mh-tags-prefix',
        ] );

        $this->add_responsive_control( 'prefix_spacing', [
            'label'      => __( 'Spacing (Distance to tags)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
            'default'    => [ 'size' => 5, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-tags-prefix' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: TAG LINKS ── */
        $this->start_controls_section( 'style_links_section', [
            'label' => __( 'Tag Links Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'links_typography',
            'selector' => '{{WRAPPER}} .mh-tags-list, {{WRAPPER}} .mh-tags-list a',
        ] );

        $this->start_controls_tabs( 'links_color_tabs' );

        /* Normal */
        $this->start_controls_tab( 'links_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'links_color', [
            'label'     => __( 'Link Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [
                '{{WRAPPER}} .mh-tags-list a' => 'color: {{VALUE}}; transition: all 0.3s ease;',
            ],
        ] );
        $this->add_control( 'separator_color', [
            'label'     => __( 'Separator Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#888888',
            'selectors' => [
                '{{WRAPPER}} .mh-tags-list' => 'color: {{VALUE}};',
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
                '{{WRAPPER}} .mh-tags-list a:hover' => 'color: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control( 'wrap_margin', [
            'label'      => __( 'Widget Margin', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-tags-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                echo '<div style="padding: 15px; border: 1px dashed #d63638; color: #d63638; text-align: center;"><strong>MH Plug:</strong> Please create a product to preview tags.</div>';
            }
            return;
        }

        // Fetch the Tags natively
        $separator = $settings['separator'] ? esc_html( $settings['separator'] ) : ', ';
        $tags_html = wc_get_product_tag_list( $product->get_id(), $separator );

        // If the product doesn't have any tags, hide the widget
        if ( empty( $tags_html ) ) {
            return; 
        }

        ?>
        <div class="mh-product-tags-wrap">
            <?php 
            if ( $settings['show_prefix'] === 'yes' && ! empty( $settings['prefix_text'] ) ) : 
            ?>
                <span class="mh-tags-prefix"><?php echo esc_html( $settings['prefix_text'] ); ?></span>
            <?php endif; ?>
            <span class="mh-tags-list"><?php echo wp_kses_post( $tags_html ); ?></span>
        </div>
        <?php
    }
}