<?php
/**
 * MH Header Cart Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Header_Cart_Widget extends Widget_Base {

    public function get_name() { return 'mh_header_cart'; }
    public function get_title() { return __( 'MH Header Cart', 'mh-plug' ); }
    public function get_icon() { return 'eicon-cart-medium'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    public function get_style_depends() { return [ 'mh-widgets-css' ]; }
    public function get_script_depends() { return [ 'mh-widgets-js' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'section_layout', [ 'label' => __( 'Cart Behavior', 'mh-plug' ) ] );

        $this->add_control( 'cart_type', [
            'label' => __( 'Cart Click Action', 'mh-plug' ), 'type' => Controls_Manager::SELECT,
            'options' => [ 'redirect' => __( 'Go to Cart Page', 'mh-plug' ), 'off-canvas' => __( 'Open Off-Canvas Mini Cart', 'mh-plug' ) ],
            'default' => 'off-canvas',
        ] );
        
        $this->add_responsive_control( 'align', [
            'label' => __( 'Alignment', 'mh-plug' ), 'type' => Controls_Manager::CHOOSE,
            'options' => [ 'left' => ['title'=>'Left','icon'=>'eicon-text-align-left'], 'center' => ['title'=>'Center','icon'=>'eicon-text-align-center'], 'right' => ['title'=>'Right','icon'=>'eicon-text-align-right'] ],
            'selectors' => [ '{{WRAPPER}} .mh-header-cart-wrapper' => 'text-align: {{VALUE}};' ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_icon', [ 'label' => __( 'Icon Style', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );

        $this->add_responsive_control( 'icon_size', [
            'label' => __( 'Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 24 ],
            'selectors' => [ '{{WRAPPER}} .mh-cart-action i' => 'font-size: {{SIZE}}px;' ],
        ] );

        $this->add_control( 'icon_color', [
            'label' => __( 'Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-cart-action i' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'icon_hover_color', [
            'label' => __( 'Hover Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-cart-action:hover i' => 'color: {{VALUE}};' ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_badge', [ 'label' => __( 'Notification Badge', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );

        $this->add_control( 'badge_bg_color', [
            'label' => __( 'Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'badge_text_color', [
            'label' => __( 'Text', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'badge_size', [
            'label' => __( 'Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 18 ],
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'width: {{SIZE}}px; height: {{SIZE}}px; line-height: {{SIZE}}px;' ],
        ] );
        
        $this->add_responsive_control( 'badge_offset_x', [ 'label' => __( 'Horizontal Offset', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => -20, 'max' => 20 ] ], 'default' => [ 'size' => -8 ], 'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'right: {{SIZE}}px;' ] ] );
        $this->add_responsive_control( 'badge_offset_y', [ 'label' => __( 'Vertical Offset', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => -20, 'max' => 20 ] ], 'default' => [ 'size' => -8 ], 'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'top: {{SIZE}}px;' ] ] );

        $this->end_controls_section();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) return;

        $settings = $this->get_settings_for_display();
        $cart_count = WC()->cart->get_cart_contents_count();
        $cart_url = wc_get_cart_url();
        $is_offcanvas = $settings['cart_type'] === 'off-canvas';

        ?>
        <div class="mh-header-cart-wrapper">
            <a href="<?php echo esc_url( $cart_url ); ?>" class="mh-cart-action <?php echo $is_offcanvas ? 'mh-open-mini-cart' : ''; ?>">
                <i class="fas fa-shopping-bag"></i>
                <span class="mh-action-badge mh-cart-count"><?php echo esc_html( $cart_count ); ?></span>
            </a>
        </div>

        <?php if ( $is_offcanvas ) : ?>
            <div class="mh-offcanvas-overlay mh-cart-overlay"></div>
            <div class="mh-offcanvas-cart mh-mini-cart-panel">
                <div class="mh-offcanvas-header">
                    <h3><?php esc_html_e( 'Shopping Cart', 'mh-plug' ); ?></h3>
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