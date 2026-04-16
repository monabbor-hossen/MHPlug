<?php
/**
 * MH Product Data Tabs Widget
 *
 * Displays Description, Additional Info, and a custom Shipping & Delivery tab.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class MH_Product_Data_Tabs_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_data_tabs'; }
    public function get_title() { return __( 'MH Product Data Tabs', 'mh-plug' ); }
    public function get_icon() { return 'eicon-tabs'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'tabs', 'description', 'additional information', 'shipping', 'woocommerce', 'mh' ]; }

    protected function register_controls() {

        /* ── CONTENT: TAB SETTINGS ── */
        $this->start_controls_section( 'section_tabs_content', [
            'label' => __( 'Tabs Content', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        // 1. Description Tab
        $this->add_control( 'show_desc', [
            'label'        => __( 'Show Description Tab', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
        ] );
        $this->add_control( 'title_desc', [
            'label'     => __( 'Description Tab Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Description', 'mh-plug' ),
            'condition' => [ 'show_desc' => 'yes' ],
        ] );

        // 2. Additional Info Tab
        $this->add_control( 'show_info', [
            'label'        => __( 'Show Additional Info Tab', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'separator'    => 'before',
        ] );
        $this->add_control( 'title_info', [
            'label'     => __( 'Additional Info Tab Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Additional information', 'mh-plug' ),
            'condition' => [ 'show_info' => 'yes' ],
        ] );

        // 3. Shipping Tab
        $this->add_control( 'show_shipping', [
            'label'        => __( 'Show Shipping Tab', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'separator'    => 'before',
        ] );
        $this->add_control( 'title_shipping', [
            'label'     => __( 'Shipping Tab Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Shipping & Delivery', 'mh-plug' ),
            'condition' => [ 'show_shipping' => 'yes' ],
        ] );
        $this->add_control( 'content_shipping', [
            'label'     => __( 'Shipping Content', 'mh-plug' ),
            'type'      => Controls_Manager::WYSIWYG,
            'default'   => __( 'Enter your shipping and delivery policy here. You can update this text to apply to all products using this layout.', 'mh-plug' ),
            'condition' => [ 'show_shipping' => 'yes' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: TAB NAVIGATION ── */
        $this->start_controls_section( 'style_tabs_nav', [
            'label' => __( 'Tab Navigation (Buttons)', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'nav_align', [
            'label'     => __( 'Alignment', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center'     => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'flex-end'   => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default'   => 'flex-start',
            'selectors' => [ '{{WRAPPER}} .mh-tabs-nav' => 'justify-content: {{VALUE}};', ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'nav_typography',
            'selector' => '{{WRAPPER}} .mh-tab-btn',
        ] );

        $this->add_responsive_control( 'nav_spacing', [
            'label'      => __( 'Spacing Between Tabs', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 10 ],
            'selectors'  => [ '{{WRAPPER}} .mh-tabs-nav' => 'gap: {{SIZE}}{{UNIT}};', ],
        ] );

        $this->start_controls_tabs( 'nav_style_tabs' );

        // Normal
        $this->start_controls_tab( 'nav_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'nav_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#777777',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'nav_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5f5f5',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_control( 'nav_border_color', [
            'label'     => __( 'Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn' => 'border-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab( 'nav_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'nav_color_hover', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn:hover' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'nav_bg_hover', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#e5e5e5',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn:hover' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_control( 'nav_border_color_hover', [
            'label'     => __( 'Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn:hover' => 'border-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        // Active
        $this->start_controls_tab( 'nav_active', [ 'label' => __( 'Active', 'mh-plug' ) ] );
        $this->add_control( 'nav_color_active', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn.mh-active-tab' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'nav_bg_active', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn.mh-active-tab' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_control( 'nav_border_color_active', [
            'label'     => __( 'Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638', // Accent border on active
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn.mh-active-tab' => 'border-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control( 'nav_border_width', [
            'label'      => __( 'Border Width', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 2, 'left' => 0, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tab-btn' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
            'separator'  => 'before',
        ] );

        $this->add_control( 'nav_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tab-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
        ] );

        $this->add_responsive_control( 'nav_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => 12, 'right' => 20, 'bottom' => 12, 'left' => 20, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tab-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: TAB CONTENT ── */
        $this->start_controls_section( 'style_content', [
            'label' => __( 'Content Area', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'content_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-tab-content-panel' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'content_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#555555',
            'selectors' => [ '{{WRAPPER}} .mh-tab-content-panel' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'content_typography',
            'selector' => '{{WRAPPER}} .mh-tab-content-panel',
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'content_border',
            'selector' => '{{WRAPPER}} .mh-tab-content-panel',
        ] );

        $this->add_control( 'content_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tab-content-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
        ] );

        $this->add_responsive_control( 'content_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => 30, 'right' => 0, 'bottom' => 30, 'left' => 0, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tab-content-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
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
                echo '<div style="padding: 15px; border: 1px dashed #d63638; text-align: center;">Please create a product to preview the Tabs.</div>';
            }
            return;
        }

        $widget_id = $this->get_id();

        // 1. Gather Description
        $has_desc = false;
        $desc_content = '';
        if ( $settings['show_desc'] === 'yes' ) {
            $post = get_post( $product->get_id() );
            if ( $post && ! empty( $post->post_content ) ) {
                $has_desc = true;
                // Apply the_content filter safely
                $desc_content = apply_filters( 'the_content', $post->post_content );
            }
        }

        // 2. Gather Additional Info
        $has_info = false;
        $info_content = '';
        if ( $settings['show_info'] === 'yes' && ( $product->has_attributes() || $product->has_dimensions() || $product->has_weight() ) ) {
            $has_info = true;
            ob_start();
            do_action( 'woocommerce_product_additional_information', $product );
            $info_content = ob_get_clean();
        }

        // 3. Gather Shipping
        $has_shipping = false;
        $shipping_content = '';
        if ( $settings['show_shipping'] === 'yes' && ! empty( $settings['content_shipping'] ) ) {
            $has_shipping = true;
            $shipping_content = $settings['content_shipping'];
        }

        // If no tabs have content, don't output anything
        if ( ! $has_desc && ! $has_info && ! $has_shipping ) {
            return;
        }

        // Determine which tab is active first
        $first_active = '';
        if ( $has_desc ) $first_active = 'desc';
        elseif ( $has_info ) $first_active = 'info';
        elseif ( $has_shipping ) $first_active = 'shipping';

        ?>
        <style>
            .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .mh-tabs-nav { display: flex; flex-wrap: wrap; list-style: none; padding: 0; margin: 0 0 10px 0; border-bottom: 1px solid #eee; }
            .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .mh-tab-btn { cursor: pointer; font-weight: 600; transition: all 0.3s ease; margin-bottom: -1px; }
            .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .mh-tab-content-panel { display: none; animation: mhTabFadeIn 0.4s ease; }
            .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .mh-tab-content-panel.mh-active-content { display: block; }
            
            /* WooCommerce Table Fixes for Additional Info */
            .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .woocommerce-product-attributes { width: 100%; border-collapse: collapse; margin-bottom: 0; }
            .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .woocommerce-product-attributes th,
            .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .woocommerce-product-attributes td { padding: 10px; border-bottom: 1px dotted #e2e2e2; text-align: left; }
            .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .woocommerce-product-attributes th { width: 30%; font-weight: 600; color: #1d2327; }
            .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> h2 { display: none; } /* Hide default Woo heading inside tabs */

            @keyframes mhTabFadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        </style>

        <div class="mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> mh-product-data-tabs">
            
            <ul class="mh-tabs-nav">
                <?php if ( $has_desc ) : ?>
                    <li class="mh-tab-btn <?php echo ( $first_active === 'desc' ) ? 'mh-active-tab' : ''; ?>" data-target="mh-tab-desc-<?php echo esc_attr( $widget_id ); ?>">
                        <?php echo esc_html( $settings['title_desc'] ); ?>
                    </li>
                <?php endif; ?>

                <?php if ( $has_info ) : ?>
                    <li class="mh-tab-btn <?php echo ( $first_active === 'info' ) ? 'mh-active-tab' : ''; ?>" data-target="mh-tab-info-<?php echo esc_attr( $widget_id ); ?>">
                        <?php echo esc_html( $settings['title_info'] ); ?>
                    </li>
                <?php endif; ?>

                <?php if ( $has_shipping ) : ?>
                    <li class="mh-tab-btn <?php echo ( $first_active === 'shipping' ) ? 'mh-active-tab' : ''; ?>" data-target="mh-tab-shipping-<?php echo esc_attr( $widget_id ); ?>">
                        <?php echo esc_html( $settings['title_shipping'] ); ?>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="mh-tabs-content-wrapper">
                <?php if ( $has_desc ) : ?>
                    <div id="mh-tab-desc-<?php echo esc_attr( $widget_id ); ?>" class="mh-tab-content-panel <?php echo ( $first_active === 'desc' ) ? 'mh-active-content' : ''; ?>">
                        <?php echo wp_kses_post( $desc_content ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( $has_info ) : ?>
                    <div id="mh-tab-info-<?php echo esc_attr( $widget_id ); ?>" class="mh-tab-content-panel <?php echo ( $first_active === 'info' ) ? 'mh-active-content' : ''; ?>">
                        <?php echo $info_content; // Contains safe WooCommerce HTML output ?>
                    </div>
                <?php endif; ?>

                <?php if ( $has_shipping ) : ?>
                    <div id="mh-tab-shipping-<?php echo esc_attr( $widget_id ); ?>" class="mh-tab-content-panel <?php echo ( $first_active === 'shipping' ) ? 'mh-active-content' : ''; ?>">
                        <?php echo wp_kses_post( $shipping_content ); ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <script>
            jQuery(document).ready(function($) {
                var $container = $('.mh-tabs-container-<?php echo esc_attr( $widget_id ); ?>');
                
                $container.find('.mh-tab-btn').on('click', function() {
                    var $this = $(this);
                    var targetID = $this.data('target');
                    
                    // Remove active from all tabs
                    $container.find('.mh-tab-btn').removeClass('mh-active-tab');
                    $container.find('.mh-tab-content-panel').removeClass('mh-active-content');
                    
                    // Add active to clicked tab
                    $this.addClass('mh-active-tab');
                    $('#' + targetID).addClass('mh-active-content');
                });
            });
        </script>
        <?php
    }
}