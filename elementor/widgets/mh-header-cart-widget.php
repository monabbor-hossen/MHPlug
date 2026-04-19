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
        <style>
            .mh-header-cart-wrapper .mh-cart-action { position: relative; display: inline-flex; cursor: pointer; text-decoration: none; transition: 0.3s; }
            .mh-header-cart-wrapper .mh-action-badge { position: absolute; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 11px; font-weight: 600; z-index: 2; transition: all 0.3s; }
            
            /* Off-Canvas Cart */
            .mh-offcanvas-cart { position: fixed; top: 0; right: -400px; width: 350px; max-width: 100%; height: 100vh; background: #fff; box-shadow: -5px 0 15px rgba(0,0,0,0.1); z-index: 999999; transition: right 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); display: flex; flex-direction: column; }
            .mh-offcanvas-cart.mh-open { right: 0; }
            .mh-offcanvas-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999998; opacity: 0; visibility: hidden; transition: 0.3s; }
            .mh-offcanvas-overlay.mh-open { opacity: 1; visibility: visible; }
            .mh-offcanvas-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
            .mh-offcanvas-header h3 { margin: 0; font-size: 18px; font-weight: 600; }
            .mh-offcanvas-close { cursor: pointer; font-size: 20px; color: #333; transition: 0.3s; }
            .mh-offcanvas-close:hover { color: #d63638; transform: rotate(90deg); }
            .mh-offcanvas-content { padding: 20px; overflow-y: auto; flex-grow: 1; }
        </style>

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

            <script>
                jQuery(document).ready(function($) {
                    var $panel = $('.mh-mini-cart-panel'), $overlay = $('.mh-cart-overlay');
                    $('.mh-open-mini-cart').on('click', function(e) { e.preventDefault(); $panel.addClass('mh-open'); $overlay.addClass('mh-open'); $('body').css('overflow', 'hidden'); });
                    $('.mh-cart-close, .mh-cart-overlay').on('click', function() { $panel.removeClass('mh-open'); $overlay.removeClass('mh-open'); $('body').css('overflow', 'auto'); });
                    
                    $('body').on('added_to_cart', function(event, fragments) {
                        if (fragments && fragments['div.widget_shopping_cart_content']) {
                            $('.mh-offcanvas-content .widget_shopping_cart_content').html(fragments['div.widget_shopping_cart_content']);
                        }
                        $panel.addClass('mh-open'); $overlay.addClass('mh-open'); $('body').css('overflow', 'hidden');
                    });
                });
            </script>
        <?php endif; ?>

        <script>
            jQuery(document).ready(function($) {
                $('body').on('added_to_cart removed_from_cart updated_cart_totals', function() {
                    $.post(mh_plug_ajax.ajax_url, { action: 'mh_get_cart_count' }, function(response) {
                        if(response.success) {
                            $('.mh-cart-count').text(response.data).css('transform', 'scale(1.3)');
                            setTimeout(function(){ $('.mh-cart-count').css('transform', 'scale(1)'); }, 200);
                        }
                    });
                });
            });
        </script>
        <?php
    }
}