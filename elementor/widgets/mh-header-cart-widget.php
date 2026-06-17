<?php
/**
 * MH Header Cart Widget
 * Full customization: icon, badge, floating button, off-canvas panel styling
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

class MH_Header_Cart_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'mh_header_cart'; }
    public function get_title() { return __( 'MH Header Cart', 'mh-plug-ecommerce-builder-widgets' ); }
    public function get_icon() { return 'eicon-cart-medium'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    public function get_style_depends() { return [ 'mh-widgets-css' ]; }
    public function get_script_depends() { return [ 'mh-widgets-js' ]; }

    protected function register_controls() {

        // ── CONTENT: CART SETTINGS ──
        $this->start_controls_section( 'section_layout', [ 'label' => __( 'Cart Settings', 'mh-plug-ecommerce-builder-widgets' ) ] );

        $this->add_control( 'cart_type', [
            'label' => __( 'Cart Click Action', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'redirect' => __( 'Go to Cart Page', 'mh-plug-ecommerce-builder-widgets' ),
                'off-canvas' => __( 'Open Off-Canvas Mini Cart', 'mh-plug-ecommerce-builder-widgets' ),
            ],
            'default' => 'off-canvas',
        ] );

        $this->add_control( 'cart_icon', [
            'label' => __( 'Cart Icon', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-shopping-bag', 'library' => 'fa-solid'],
        ] );

        $this->add_control( 'show_badge', [
            'label' => __( 'Show Item Count Badge', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ] );

        $this->add_control( 'show_floating', [
            'label' => __( 'Floating Cart Button', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'description' => __( 'Show a fixed floating cart button on the page.', 'mh-plug-ecommerce-builder-widgets' ),
        ] );

        $this->add_responsive_control( 'align', [
            'label' => __( 'Alignment', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => ['title' => 'Left', 'icon' => 'eicon-text-align-left'],
                'center' => ['title' => 'Center', 'icon' => 'eicon-text-align-center'],
                'right' => ['title' => 'Right', 'icon' => 'eicon-text-align-right'],
            ],
            'selectors' => [ '{{WRAPPER}} .mh-header-cart-wrapper' => 'text-align: {{VALUE}};' ],
        ] );

        $this->end_controls_section();

        // ── STYLE: ICON BUTTON ──
        $this->start_controls_section( 'section_style_icon', [
            'label' => __( 'Icon Button', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'icon_size', [
            'label' => __( 'Icon Size', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 12, 'max' => 60]],
            'default' => [ 'size' => 24 ],
            'selectors' => [ '{{WRAPPER}} .mh-cart-action i, {{WRAPPER}} .mh-cart-action svg' => 'font-size: {{SIZE}}px; width: {{SIZE}}px; height: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'btn_padding', [
            'label' => __( 'Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em'],
            'selectors' => [ '{{WRAPPER}} .mh-cart-action' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'btn_radius', [
            'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%'],
            'selectors' => [ '{{WRAPPER}} .mh-cart-action' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->start_controls_tabs( 'tabs_icon_style' );

        // Normal
        $this->start_controls_tab( 'tab_icon_normal', [ 'label' => __( 'Normal', 'mh-plug-ecommerce-builder-widgets' ) ] );
        $this->add_control( 'icon_color', [
            'label' => __( 'Icon Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-cart-action i, {{WRAPPER}} .mh-cart-action svg' => 'color: {{VALUE}}; fill: {{VALUE}};' ],
        ] );
        $this->add_control( 'btn_bg', [
            'label' => __( 'Background', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-cart-action' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name' => 'btn_border',
            'selector' => '{{WRAPPER}} .mh-cart-action',
        ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name' => 'btn_shadow',
            'selector' => '{{WRAPPER}} .mh-cart-action',
        ] );
        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab( 'tab_icon_hover', [ 'label' => __( 'Hover', 'mh-plug-ecommerce-builder-widgets' ) ] );
        $this->add_control( 'icon_hover_color', [
            'label' => __( 'Icon Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-cart-action:hover i, {{WRAPPER}} .mh-cart-action:hover svg' => 'color: {{VALUE}}; fill: {{VALUE}};' ],
        ] );
        $this->add_control( 'btn_hover_bg', [
            'label' => __( 'Background', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-cart-action:hover' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name' => 'btn_hover_border',
            'selector' => '{{WRAPPER}} .mh-cart-action:hover',
        ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name' => 'btn_hover_shadow',
            'selector' => '{{WRAPPER}} .mh-cart-action:hover',
        ] );
        $this->add_control( 'icon_hover_transition', [
            'label' => __( 'Transition (s)', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0.1, 'max' => 1, 'step' => 0.1]],
            'default' => ['size' => 0.3],
            'selectors' => [ '{{WRAPPER}} .mh-cart-action' => 'transition: all {{SIZE}}s ease;' ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // ── STYLE: BADGE ──
        $this->start_controls_section( 'section_style_badge', [
            'label' => __( 'Count Badge', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => ['show_badge' => 'yes'],
        ] );

        $this->add_control( 'badge_bg_color', [
            'label' => __( 'Background', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'badge_text_color', [
            'label' => __( 'Text Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'badge_size', [
            'label' => __( 'Size', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 12, 'max' => 36]],
            'default' => [ 'size' => 18 ],
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'width: {{SIZE}}px; height: {{SIZE}}px; line-height: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'badge_font_size', [
            'label' => __( 'Font Size', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 8, 'max' => 18]],
            'default' => ['size' => 10],
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'font-size: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'badge_radius', [
            'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%'],
            'default' => ['top'=>50,'right'=>50,'bottom'=>50,'left'=>50,'unit'=>'%','isLinked'=>true],
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'badge_offset_x', [
            'label' => __( 'Horizontal Offset', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'min' => -20, 'max' => 20 ] ],
            'default' => [ 'size' => -8 ],
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'right: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'badge_offset_y', [
            'label' => __( 'Vertical Offset', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'min' => -20, 'max' => 20 ] ],
            'default' => [ 'size' => -8 ],
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'top: {{SIZE}}px;' ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name' => 'badge_border',
            'selector' => '{{WRAPPER}} .mh-action-badge',
        ] );

        $this->end_controls_section();

        // ── STYLE: FLOATING BUTTON ──
        $this->start_controls_section( 'section_style_floating', [
            'label' => __( 'Floating Button', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => ['show_floating' => 'yes'],
        ] );

        $this->add_control( 'float_bg', [
            'label' => __( 'Background', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-floating-cart' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'float_color', [
            'label' => __( 'Icon Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-floating-cart i, {{WRAPPER}} .mh-floating-cart svg' => 'color: {{VALUE}}; fill: {{VALUE}};' ],
        ] );

        $this->add_control( 'float_hover_bg', [
            'label' => __( 'Hover Background', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-floating-cart:hover' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'float_size', [
            'label' => __( 'Button Size', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 40, 'max' => 80]],
            'default' => ['size' => 56],
            'selectors' => [ '{{WRAPPER}} .mh-floating-cart' => 'width: {{SIZE}}px; height: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'float_icon_size', [
            'label' => __( 'Icon Size', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 14, 'max' => 40]],
            'default' => ['size' => 22],
            'selectors' => [ '{{WRAPPER}} .mh-floating-cart i, {{WRAPPER}} .mh-floating-cart svg' => 'font-size: {{SIZE}}px; width: {{SIZE}}px; height: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'float_radius', [
            'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%'],
            'default' => ['top'=>50,'right'=>50,'bottom'=>50,'left'=>50,'unit'=>'%','isLinked'=>true],
            'selectors' => [ '{{WRAPPER}} .mh-floating-cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name' => 'float_shadow',
            'selector' => '{{WRAPPER}} .mh-floating-cart',
            'fields_options' => ['box_shadow' => ['default' => ['horizontal' => 0, 'vertical' => 4, 'blur' => 16, 'spread' => 0, 'color' => 'rgba(0,0,0,0.3)']]],
        ] );

        $this->add_responsive_control( 'float_bottom', [
            'label' => __( 'Bottom Position', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 120]],
            'default' => ['size' => 24],
            'selectors' => [ '{{WRAPPER}} .mh-floating-cart' => 'bottom: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'float_right', [
            'label' => __( 'Right Position', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 120]],
            'default' => ['size' => 24],
            'selectors' => [ '{{WRAPPER}} .mh-floating-cart' => 'right: {{SIZE}}px;' ],
        ] );

        $this->add_control( 'float_badge_bg', [
            'label' => __( 'Badge Background', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-floating-cart .mh-float-badge' => 'background-color: {{VALUE}};' ],
            'separator' => 'before',
        ] );

        $this->add_control( 'float_badge_color', [
            'label' => __( 'Badge Text', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-floating-cart .mh-float-badge' => 'color: {{VALUE}};' ],
        ] );

        $this->end_controls_section();

        // ── STYLE: OFF-CANVAS PANEL ──
        $this->start_controls_section( 'section_style_offcanvas', [
            'label' => __( 'Off-Canvas Panel', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => ['cart_type' => 'off-canvas'],
        ] );

        $this->add_responsive_control( 'oc_width', [
            'label' => __( 'Panel Width', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','%','vw'],
            'range' => ['px' => ['min' => 250, 'max' => 600], '%' => ['min' => 20, 'max' => 100], 'vw' => ['min' => 20, 'max' => 100]],
            'default' => ['size' => 380, 'unit' => 'px'],
            'selectors' => [ '{{WRAPPER}} .mh-offcanvas-cart' => 'width: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_control( 'oc_bg', [
            'label' => __( 'Panel Background', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-offcanvas-cart' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'oc_overlay_color', [
            'label' => __( 'Overlay Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'default' => 'rgba(0,0,0,0.5)',
            'selectors' => [ '{{WRAPPER}} .mh-cart-overlay' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'heading_oc_header', ['label' => __( 'Header', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before'] );

        $this->add_control( 'oc_header_bg', [
            'label' => __( 'Header Background', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-offcanvas-header' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'oc_header_color', [
            'label' => __( 'Header Text Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-offcanvas-header, {{WRAPPER}} .mh-offcanvas-header h3' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'oc_header_typo',
            'selector' => '{{WRAPPER}} .mh-offcanvas-header h3',
        ] );

        $this->add_responsive_control( 'oc_header_padding', [
            'label' => __( 'Header Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em'],
            'selectors' => [ '{{WRAPPER}} .mh-offcanvas-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_control( 'oc_close_color', [
            'label' => __( 'Close Icon Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-offcanvas-close' => 'color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'oc_close_size', [
            'label' => __( 'Close Icon Size', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 12, 'max' => 36]],
            'selectors' => [ '{{WRAPPER}} .mh-offcanvas-close i' => 'font-size: {{SIZE}}px;' ],
        ] );

        $this->add_control( 'heading_oc_content', ['label' => __( 'Content', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before'] );

        $this->add_responsive_control( 'oc_content_padding', [
            'label' => __( 'Content Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em'],
            'selectors' => [ '{{WRAPPER}} .mh-offcanvas-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name' => 'oc_shadow',
            'selector' => '{{WRAPPER}} .mh-offcanvas-cart',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) return;

        $settings = $this->get_settings_for_display();
        $cart_count = WC()->cart->get_cart_contents_count();
        $cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
        $is_offcanvas = $settings['cart_type'] === 'off-canvas';
        $show_badge = $settings['show_badge'] === 'yes';
        $show_floating = $settings['show_floating'] === 'yes';
        $cart_icon = !empty($settings['cart_icon']['value']) ? $settings['cart_icon']['value'] : 'fas fa-shopping-bag';
        $wid = $this->get_id();

        $css = "
        .mh-floating-cart{position:fixed;bottom:24px;right:24px;width:56px;height:56px;border-radius:50%;border:none;background:#333;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:99998;box-shadow:0 4px 16px rgba(0,0,0,0.3);transition:all .3s ease;outline:none;text-decoration:none}
        .mh-floating-cart:hover{transform:scale(1.08)}
        .mh-floating-cart .mh-float-badge{position:absolute;top:-4px;right:-4px;width:20px;height:20px;border-radius:50%;background:#d63638;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;line-height:1}
        .mh-cart-action{position:relative;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;transition:all .3s ease;line-height:1}
        .mh-action-badge{position:absolute;top:-8px;right:-8px;width:18px;height:18px;border-radius:50%;background:#d63638;color:#fff;font-size:10px;font-weight:700;text-align:center;line-height:18px;pointer-events:none}
        ";
        wp_register_style( 'mh-header-cart-style', false );
        wp_enqueue_style( 'mh-header-cart-style' );
        wp_add_inline_style( 'mh-header-cart-style', $css );
        ?>

        <div class="mh-header-cart-wrapper">
            <a href="<?php echo esc_url( $cart_url ); ?>" class="mh-cart-action <?php echo $is_offcanvas ? 'mh-open-mini-cart' : ''; ?>">
                <i class="<?php echo esc_attr($cart_icon); ?>"></i>
                <?php if ($show_badge): ?>
                    <span class="mh-action-badge mh-cart-count"><?php echo esc_html( $cart_count ); ?></span>
                <?php endif; ?>
            </a>
        </div>

        <?php if ( $show_floating ) : ?>
            <a href="<?php echo esc_url( $cart_url ); ?>" class="mh-floating-cart <?php echo $is_offcanvas ? 'mh-open-mini-cart' : ''; ?>" id="mh-float-cart-<?php echo $wid; ?>">
                <i class="<?php echo esc_attr($cart_icon); ?>"></i>
                <?php if ($show_badge): ?>
                    <span class="mh-float-badge mh-cart-count"><?php echo esc_html( $cart_count ); ?></span>
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <?php if ( $is_offcanvas ) : ?>
            <div class="mh-offcanvas-overlay mh-cart-overlay"></div>
            <div class="mh-offcanvas-cart mh-mini-cart-panel">
                <div class="mh-offcanvas-header">
                    <h3><?php esc_html_e( 'Shopping Cart', 'mh-plug-ecommerce-builder-widgets' ); ?></h3>
                    <div class="mh-offcanvas-close mh-cart-close"><i class="fas fa-times"></i></div>
                </div>
                <div class="mh-offcanvas-content">
                    <div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
                </div>
            </div>
        <?php endif; ?>
        <?php
    }
}