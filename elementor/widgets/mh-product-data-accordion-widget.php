<?php
/**
 * MH Product Data Widget (Tabs & Accordion)
 *
 * Displays Description, Additional Info, Shipping, and Reviews in either an Accordion or Tabs layout.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

class MH_Product_Data_Accordion_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_data_accordion'; }
    public function get_title() { return __( 'MH Product Data (Tabs & Accordion)', 'mh-plug' ); }
    public function get_icon() { return 'eicon-tabs'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'accordion', 'tabs', 'description', 'shipping', 'reviews', 'woocommerce', 'mh' ]; }

    protected function register_controls() {

        /* ── CONTENT: LAYOUT & DATA ── */
        $this->start_controls_section( 'section_data_content', [
            'label' => __( 'Layout & Content', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'layout_style', [
            'label'   => __( 'Layout Style', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'accordion',
            'options' => [
                'accordion' => __( 'Accordion (Vertical)', 'mh-plug' ),
                'tabs'      => __( 'Tabs (Horizontal)', 'mh-plug' ),
            ],
        ] );

        // 1. Description
        $this->add_control( 'show_desc', [
            'label'     => __( 'Show Description', 'mh-plug' ),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes',
            'separator' => 'before',
        ] );
        $this->add_control( 'title_desc', [
            'label'     => __( 'Description Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Description', 'mh-plug' ),
            'condition' => [ 'show_desc' => 'yes' ],
        ] );

        // 2. Additional Info
        $this->add_control( 'show_info', [
            'label'     => __( 'Show Additional Info', 'mh-plug' ),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes',
            'separator' => 'before',
        ] );
        $this->add_control( 'title_info', [
            'label'     => __( 'Additional Info Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Additional information', 'mh-plug' ),
            'condition' => [ 'show_info' => 'yes' ],
        ] );

        // 3. Shipping
        $this->add_control( 'show_shipping', [
            'label'     => __( 'Show Shipping & Delivery', 'mh-plug' ),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes',
            'separator' => 'before',
        ] );
        $this->add_control( 'title_shipping', [
            'label'     => __( 'Shipping Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Shipping & Delivery', 'mh-plug' ),
            'condition' => [ 'show_shipping' => 'yes' ],
        ] );
        $this->add_control( 'content_shipping', [
            'label'     => __( 'Shipping Content', 'mh-plug' ),
            'type'      => Controls_Manager::WYSIWYG,
            'default'   => __( 'Enter your shipping and delivery policy here.', 'mh-plug' ),
            'condition' => [ 'show_shipping' => 'yes' ],
        ] );

        // 4. Reviews
        $this->add_control( 'show_reviews', [
            'label'     => __( 'Show Reviews', 'mh-plug' ),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes',
            'separator' => 'before',
        ] );
        $this->add_control( 'title_reviews', [
            'label'     => __( 'Reviews Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Reviews', 'mh-plug' ),
            'condition' => [ 'show_reviews' => 'yes' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: ACCORDION SPECIFIC ── */
        $this->start_controls_section( 'style_accordion_header', [
            'label'     => __( 'Accordion Headers', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'layout_style' => 'accordion' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'acc_header_typography',
            'selector' => '{{WRAPPER}} .mh-accordion-header',
        ] );

        $this->start_controls_tabs( 'acc_header_style_tabs' );
        $this->start_controls_tab( 'acc_header_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'acc_header_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-accordion-header' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'acc_icon_color', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#999999',
            'selectors' => [ '{{WRAPPER}} .mh-accordion-icon' => 'color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'acc_header_active', [ 'label' => __( 'Active', 'mh-plug' ) ] );
        $this->add_control( 'acc_header_color_active', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-accordion-item.active .mh-accordion-header' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'acc_icon_color_active', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-accordion-item.active .mh-accordion-icon' => 'color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control( 'acc_header_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => 15, 'right' => 0, 'bottom' => 15, 'left' => 0, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-accordion-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
            'separator'  => 'before',
        ] );

        $this->add_control( 'acc_border_color', [
            'label'     => __( 'Separator Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#eeeeee',
            'selectors' => [ 
                '{{WRAPPER}} .mh-accordion-item' => 'border-bottom: 1px solid {{VALUE}};',
                '{{WRAPPER}} .mh-accordion-container' => 'border-top: 1px solid {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: TABS SPECIFIC ── */
        $this->start_controls_section( 'style_tabs_nav', [
            'label'     => __( 'Tabs Navigation', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'layout_style' => 'tabs' ],
        ] );

        $this->add_responsive_control( 'tabs_nav_align', [
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
            'name'     => 'tabs_nav_typography',
            'selector' => '{{WRAPPER}} .mh-tab-btn',
        ] );

        $this->add_responsive_control( 'tabs_nav_spacing', [
            'label'      => __( 'Spacing Between Tabs', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 10 ],
            'selectors'  => [ '{{WRAPPER}} .mh-tabs-nav' => 'gap: {{SIZE}}{{UNIT}};', ],
        ] );

        $this->start_controls_tabs( 'tabs_nav_style_tabs' );
        $this->start_controls_tab( 'tabs_nav_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'tabs_nav_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#777777',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'tabs_nav_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5f5f5',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tabs_nav_active', [ 'label' => __( 'Active', 'mh-plug' ) ] );
        $this->add_control( 'tabs_nav_color_active', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn.mh-active-tab' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'tabs_nav_bg_active', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn.mh-active-tab' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_control( 'tabs_nav_border_active', [
            'label'     => __( 'Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-tab-btn.mh-active-tab' => 'border-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control( 'tabs_nav_border_width', [
            'label'      => __( 'Border Width', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 2, 'left' => 0, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tab-btn' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
            'separator'  => 'before',
        ] );

        $this->add_responsive_control( 'tabs_nav_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => 12, 'right' => 20, 'bottom' => 12, 'left' => 20, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tab-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: SHARED CONTENT AREA ── */
        $this->start_controls_section( 'style_shared_content', [
            'label' => __( 'Inner Content Text', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'content_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#555555',
            'selectors' => [ 
                '{{WRAPPER}} .mh-accordion-content' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-tab-content-panel' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'content_typography',
            'selector' => '{{WRAPPER}} .mh-accordion-content, {{WRAPPER}} .mh-tab-content-panel',
        ] );

        $this->add_control( 'content_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => 15, 'right' => 0, 'bottom' => 15, 'left' => 0, 'unit' => 'px' ],
            'selectors'  => [ 
                '{{WRAPPER}} .mh-accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .mh-tab-content-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
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
                echo '<div style="padding: 15px; border: 1px dashed #d63638; text-align: center;">Please create a product to preview the Data.</div>';
            }
            return;
        }

        $widget_id = $this->get_id();
        $layout    = $settings['layout_style'];

        // 1. Gather Description
        $has_desc = false;
        $desc_content = '';
        if ( $settings['show_desc'] === 'yes' ) {
            $post = get_post( $product->get_id() );
            if ( $post && ! empty( $post->post_content ) ) {
                $has_desc = true;
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

        // 4. Gather Reviews (Native WooCommerce Template)
        $has_reviews = false;
        $reviews_content = '';
        if ( $settings['show_reviews'] === 'yes' && comments_open() ) {
            $has_reviews = true;
            ob_start();
            comments_template();
            $reviews_content = ob_get_clean();
        }

        if ( ! $has_desc && ! $has_info && ! $has_shipping && ! $has_reviews ) return;

        $first_active = '';
        if ( $has_desc ) $first_active = 'desc';
        elseif ( $has_info ) $first_active = 'info';
        elseif ( $has_shipping ) $first_active = 'shipping';
        elseif ( $has_reviews ) $first_active = 'reviews';

        // Add some basic styling to make sure native WooCommerce reviews look okay inside our containers
        $custom_css = "
            .mh-review-container h2.woocommerce-Reviews-title { display: none; }
            .mh-review-container ol.commentlist { padding-left: 0; list-style: none; }
            .mh-review-container .comment_container { display: flex; align-items: flex-start; margin-bottom: 20px; }
            .mh-review-container .comment_container img.avatar { margin-right: 15px; border-radius: 50%; max-width: 50px; }
            .mh-review-container .comment-text { flex-grow: 1; padding: 15px; border: 1px solid #eee; border-radius: 5px; }
            .mh-review-container .star-rating { float: right; }
        ";

        // ==========================================
        // RENDER ACCORDION LAYOUT
        // ==========================================
        if ( $layout === 'accordion' ) {
            $is_first = true; 
            ?>
            <style>
                <?php echo $custom_css; ?>
                .mh-accordion-header { display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-weight: 500; transition: color 0.3s ease; }
                .mh-accordion-icon { font-size: 14px; transition: 0.3s ease; display: inline-flex; align-items: center; justify-content: center; }
                .mh-accordion-item.active .fa-plus { display: none; }
                .mh-accordion-item:not(.active) .fa-minus { display: none; }
                .mh-accordion-content h2 { display: none; }
                .mh-accordion-content .woocommerce-product-attributes { width: 100%; border-collapse: collapse; margin: 0; }
                .mh-accordion-content .woocommerce-product-attributes th,
                .mh-accordion-content .woocommerce-product-attributes td { padding: 8px 0; border-bottom: 1px dotted #eaeaea; text-align: left; }
                .mh-accordion-content .woocommerce-product-attributes th { width: 40%; font-weight: 600; color: #111; }
                .mh-accordion-content p:last-child { margin-bottom: 0; }
            </style>

            <div class="mh-accordion-container mh-accordion-<?php echo esc_attr( $widget_id ); ?>">
                <?php if ( $has_desc ) : ?>
                    <div class="mh-accordion-item <?php echo $is_first ? 'active' : ''; ?>">
                        <div class="mh-accordion-header">
                            <span class="mh-accordion-title"><?php echo esc_html( $settings['title_desc'] ); ?></span>
                            <span class="mh-accordion-icon"><i class="fas fa-plus"></i><i class="fas fa-minus"></i></span>
                        </div>
                        <div class="mh-accordion-content" style="display: <?php echo $is_first ? 'block' : 'none'; ?>;">
                            <?php echo wp_kses_post( $desc_content ); ?>
                        </div>
                    </div>
                    <?php $is_first = false; ?>
                <?php endif; ?>

                <?php if ( $has_info ) : ?>
                    <div class="mh-accordion-item <?php echo $is_first ? 'active' : ''; ?>">
                        <div class="mh-accordion-header">
                            <span class="mh-accordion-title"><?php echo esc_html( $settings['title_info'] ); ?></span>
                            <span class="mh-accordion-icon"><i class="fas fa-plus"></i><i class="fas fa-minus"></i></span>
                        </div>
                        <div class="mh-accordion-content" style="display: <?php echo $is_first ? 'block' : 'none'; ?>;">
                            <?php echo $info_content; ?>
                        </div>
                    </div>
                    <?php $is_first = false; ?>
                <?php endif; ?>

                <?php if ( $has_shipping ) : ?>
                    <div class="mh-accordion-item <?php echo $is_first ? 'active' : ''; ?>">
                        <div class="mh-accordion-header">
                            <span class="mh-accordion-title"><?php echo esc_html( $settings['title_shipping'] ); ?></span>
                            <span class="mh-accordion-icon"><i class="fas fa-plus"></i><i class="fas fa-minus"></i></span>
                        </div>
                        <div class="mh-accordion-content" style="display: <?php echo $is_first ? 'block' : 'none'; ?>;">
                            <?php echo wp_kses_post( $shipping_content ); ?>
                        </div>
                    </div>
                    <?php $is_first = false; ?>
                <?php endif; ?>

                <?php if ( $has_reviews ) : ?>
                    <div class="mh-accordion-item <?php echo $is_first ? 'active' : ''; ?>">
                        <div class="mh-accordion-header">
                            <span class="mh-accordion-title"><?php echo esc_html( $settings['title_reviews'] ); ?></span>
                            <span class="mh-accordion-icon"><i class="fas fa-plus"></i><i class="fas fa-minus"></i></span>
                        </div>
                        <div class="mh-accordion-content mh-review-container" style="display: <?php echo $is_first ? 'block' : 'none'; ?>;">
                            <?php echo $reviews_content; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <script>
                jQuery(document).ready(function($) {
                    $('.mh-accordion-<?php echo esc_attr( $widget_id ); ?> .mh-accordion-header').on('click', function() {
                        var $this = $(this);
                        var $item = $this.parent('.mh-accordion-item');
                        var $content = $this.next('.mh-accordion-content');
                        var $container = $this.closest('.mh-accordion-container');

                        if ($item.hasClass('active')) {
                            $item.removeClass('active');
                            $content.slideUp(300);
                        } else {
                            $container.find('.mh-accordion-item').removeClass('active');
                            $container.find('.mh-accordion-content').slideUp(300);
                            $item.addClass('active');
                            $content.slideDown(300);
                        }
                    });
                });
            </script>
            <?php
        } 
        
        // ==========================================
        // RENDER TABS LAYOUT
        // ==========================================
        else { 
            ?>
            <style>
                <?php echo $custom_css; ?>
                .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .mh-tabs-nav { display: flex; flex-wrap: wrap; list-style: none; padding: 0; margin: 0 0 10px 0; border-bottom: 1px solid #eee; }
                .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .mh-tab-btn { cursor: pointer; font-weight: 600; transition: all 0.3s ease; margin-bottom: -1px; border-color: transparent; }
                .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .mh-tab-content-panel { display: none; animation: mhTabFadeIn 0.4s ease; }
                .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .mh-tab-content-panel.mh-active-content { display: block; }
                
                .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .woocommerce-product-attributes { width: 100%; border-collapse: collapse; margin-bottom: 0; }
                .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .woocommerce-product-attributes th,
                .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .woocommerce-product-attributes td { padding: 10px 0; border-bottom: 1px dotted #e2e2e2; text-align: left; }
                .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> .woocommerce-product-attributes th { width: 30%; font-weight: 600; color: #111; }
                .mh-tabs-container-<?php echo esc_attr( $widget_id ); ?> h2 { display: none; }

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
                    <?php if ( $has_reviews ) : ?>
                        <li class="mh-tab-btn <?php echo ( $first_active === 'reviews' ) ? 'mh-active-tab' : ''; ?>" data-target="mh-tab-reviews-<?php echo esc_attr( $widget_id ); ?>">
                            <?php echo esc_html( $settings['title_reviews'] ); ?>
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
                            <?php echo $info_content; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ( $has_shipping ) : ?>
                        <div id="mh-tab-shipping-<?php echo esc_attr( $widget_id ); ?>" class="mh-tab-content-panel <?php echo ( $first_active === 'shipping' ) ? 'mh-active-content' : ''; ?>">
                            <?php echo wp_kses_post( $shipping_content ); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ( $has_reviews ) : ?>
                        <div id="mh-tab-reviews-<?php echo esc_attr( $widget_id ); ?>" class="mh-tab-content-panel mh-review-container <?php echo ( $first_active === 'reviews' ) ? 'mh-active-content' : ''; ?>">
                            <?php echo $reviews_content; ?>
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
                        
                        $container.find('.mh-tab-btn').removeClass('mh-active-tab');
                        $container.find('.mh-tab-content-panel').removeClass('mh-active-content');
                        
                        $this.addClass('mh-active-tab');
                        $('#' + targetID).addClass('mh-active-content');
                    });
                });
            </script>
            <?php
        }
    }
}