<?php
/**
 * MH Header Wishlist Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Header_Wishlist_Widget extends Widget_Base {

    public function get_name() { return 'mh_header_wishlist'; }
    public function get_title() { return __( 'MH Header Wishlist', 'mh-plug' ); }
    public function get_icon() { return 'eicon-heart'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    
    public function get_style_depends() { return [ 'mh-widgets-css' ]; }
    public function get_script_depends() { return [ 'mh-widgets-js' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'section_style_icon', [ 'label' => __( 'Icon & Layout', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );

        $this->add_responsive_control( 'align', [
            'label' => __( 'Alignment', 'mh-plug' ), 'type' => Controls_Manager::CHOOSE,
            'options' => [ 'left' => ['title'=>'Left','icon'=>'eicon-text-align-left'], 'center' => ['title'=>'Center','icon'=>'eicon-text-align-center'], 'right' => ['title'=>'Right','icon'=>'eicon-text-align-right'] ],
            'selectors' => [ '{{WRAPPER}} .mh-header-wishlist-wrapper' => 'text-align: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'icon_size', [
            'label' => __( 'Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px' ], 'range' => [ 'px' => [ 'min' => 10, 'max' => 100 ] ], 'default' => [ 'size' => 24 ],
            'selectors' => [ '{{WRAPPER}} .mh-wishlist-action i' => 'font-size: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_control( 'icon_color', [
            'label' => __( 'Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-wishlist-action i' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'icon_hover_color', [
            'label' => __( 'Hover Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-wishlist-action:hover i' => 'color: {{VALUE}};' ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_badge', [ 'label' => __( 'Notification Badge', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );

        $this->add_control( 'badge_bg_color', [
            'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'badge_text_color', [
            'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'badge_typography', 'selector' => '{{WRAPPER}} .mh-action-badge' ] );

        $this->add_responsive_control( 'badge_size', [
            'label' => __( 'Badge Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 18 ],
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'width: {{SIZE}}px; height: {{SIZE}}px; line-height: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'badge_offset_x', [
            'label' => __( 'Horizontal Offset', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => -20, 'max' => 20 ] ], 'default' => [ 'size' => -8 ],
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'right: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'badge_offset_y', [
            'label' => __( 'Vertical Offset', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => -20, 'max' => 20 ] ], 'default' => [ 'size' => -8 ],
            'selectors' => [ '{{WRAPPER}} .mh-action-badge' => 'top: {{SIZE}}px;' ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) ) return;

        $wishlist_count = 0;
        if ( is_user_logged_in() ) {
            $wishlist = get_user_meta( get_current_user_id(), '_mh_wishlist', true );
            if ( is_array( $wishlist ) ) $wishlist_count = count( $wishlist );
        }

        ?>

        
        <div class="mh-header-wishlist-wrapper">
            <a href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>" class="mh-wishlist-action">
                <i class="far fa-heart"></i>
                <span class="mh-action-badge mh-wishlist-count"><?php echo esc_html( $wishlist_count ); ?></span>
            </a>
        </div>


        <?php
    }
}
