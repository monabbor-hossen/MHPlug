<?php
/**
 * MH Wishlist Button Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

class MH_Wishlist_Button_Widget extends Widget_Base {

    public function get_name()  { return 'mh_wishlist_button'; }
    public function get_title() { return __( 'MH Wishlist Button', 'mh-plug' ); }
    public function get_icon()  { return 'eicon-heart'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords()   { return [ 'wishlist', 'heart', 'woocommerce', 'mh' ]; }

    protected function register_controls() {

        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Button Content', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        // 🚀 NEW: Toggle to hide the text label
        $this->add_control( 'show_label', [
            'label'        => __( 'Show Text Label', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'btn_text_normal', [
            'label'     => __( 'Button Label (Normal)', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Add to Wishlist', 'mh-plug' ),
            'condition' => [ 'show_label' => 'yes' ], // Only show if label is enabled
        ] );

        $this->add_control( 'btn_text_added', [
            'label'     => __( 'Button Label (Added)', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Remove from Wishlist', 'mh-plug' ),
            'condition' => [ 'show_label' => 'yes' ], // Only show if label is enabled
        ] );

        $this->add_control( 'btn_icon', [
            'label'   => __( 'Icon', 'mh-plug' ),
            'type'    => Controls_Manager::ICONS,
            'default' => [
                'value'   => 'far fa-heart',
                'library' => 'fa-regular',
            ],
        ] );

        $this->add_control( 'icon_position', [
            'label'     => __( 'Icon Position', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'left'  => [ 'title' => __( 'Left', 'mh-plug' ),  'icon' => 'eicon-h-align-left' ],
                'right' => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-h-align-right' ],
            ],
            'default'   => 'left',
            'condition' => [ 'show_label' => 'yes' ], // Position only matters if text exists
        ] );

        $this->add_responsive_control( 'align', [
            'label'        => __( 'Alignment', 'mh-plug' ),
            'type'         => Controls_Manager::CHOOSE,
            'options'      => [
                'left'   => [ 'title' => __( 'Left', 'mh-plug' ),   'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'mh-plug' ),  'icon' => 'eicon-text-align-right' ],
            ],
            'default'      => 'left',
            'selectors'    => [
                '{{WRAPPER}} .mh-wishlist-widget-wrap' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE ── */
        $this->start_controls_section( 'style_section', [
            'label' => __( 'Button Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'      => 'btn_typography',
            'selector'  => '{{WRAPPER}} .mh-wishlist-btn',
            'condition' => [ 'show_label' => 'yes' ],
        ] );

        // 🚀 UPGRADED: Icon Size control now affects SVGs properly
        $this->add_responsive_control( 'icon_size', [
            'label'      => __( 'Icon Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 80 ] ],
            'default'    => [ 'size' => 16, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-wishlist-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-wishlist-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; fill: currentColor;',
            ],
        ] );

        $this->start_controls_tabs( 'btn_color_tabs' );

        /* Normal */
        $this->start_controls_tab( 'btn_tab_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'btn_color_normal', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-wishlist-btn' => 'color: {{VALUE}}; border-color: {{VALUE}};' ],
        ] );
        $this->add_control( 'btn_bg_normal', [
            'label'     => __( 'Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'transparent',
            'selectors' => [ '{{WRAPPER}} .mh-wishlist-btn' => 'background: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        /* Hover */
        $this->start_controls_tab( 'btn_tab_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'btn_color_hover', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#fff',
            'selectors' => [ '{{WRAPPER}} .mh-wishlist-btn:hover' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'btn_bg_hover', [
            'label'     => __( 'Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-wishlist-btn:hover' => 'background: {{VALUE}}; border-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        /* Added state */
        $this->start_controls_tab( 'btn_tab_added', [ 'label' => __( 'Added', 'mh-plug' ) ] );
        $this->add_control( 'btn_color_added', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#fff',
            'selectors' => [ '{{WRAPPER}} .mh-wishlist-btn.mh-in-wishlist' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'btn_bg_added', [
            'label'     => __( 'Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-wishlist-btn.mh-in-wishlist' => 'background: {{VALUE}}; border-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'btn_border',
            'selector'  => '{{WRAPPER}} .mh-wishlist-btn',
            'separator' => 'before',
        ] );

        $this->add_control( 'btn_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-wishlist-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
        ] );

        $this->add_responsive_control( 'btn_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [ '{{WRAPPER}} .mh-wishlist-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
        ] );

        $this->add_control( 'icon_gap', [
            'label'      => __( 'Icon Gap', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'size' => 6, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-wishlist-btn' => 'gap: {{SIZE}}{{UNIT}};', ],
            'condition'  => [ 'show_label' => 'yes' ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        // Elementor Editor Mock Product Context
        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                $mock_products = wc_get_products( [ 'limit' => 1, 'status' => 'publish' ] );
                if ( ! empty( $mock_products ) ) {
                    $product = $mock_products[0];
                }
            }
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 15px; border: 1px dashed #d63638; color: #d63638; text-align: center;"><strong>MH Plug:</strong> Please create a product first.</div>';
            }
            return;
        }

        $product_id   = $product->get_id();
        
        // 🚀 User Account Context Data
        $is_logged_in = is_user_logged_in() ? 'true' : 'false'; 
        
        $in_wishlist  = function_exists( 'mh_wishlist_has_product' ) && mh_wishlist_has_product( $product_id );
        $btn_class    = 'mh-wishlist-btn' . ( $in_wishlist ? ' mh-in-wishlist' : '' );
        
        // Use labels if enabled, otherwise fallback to standard text for accessibility
        $text_normal  = !empty($settings['btn_text_normal']) ? $settings['btn_text_normal'] : 'Add to Wishlist';
        $text_added   = !empty($settings['btn_text_added']) ? $settings['btn_text_added'] : 'Remove from Wishlist';
        $label        = $in_wishlist ? $text_added : $text_normal;
        
        $nonce        = wp_create_nonce( 'mh_wishlist_nonce' );
        $show_label   = $settings['show_label'] === 'yes';
        $icon_pos     = $settings['icon_position'] ?? 'left';

        ?>
        <div class="mh-wishlist-widget-wrap">
            <span
                class="<?php echo esc_attr( $btn_class ); ?>"
                data-product-id="<?php echo esc_attr( $product_id ); ?>"
                data-nonce="<?php echo esc_attr( $nonce ); ?>"
                data-text-normal="<?php echo esc_attr( $text_normal ); ?>"
                data-text-added="<?php echo esc_attr( $text_added ); ?>"
                data-logged-in="<?php echo esc_attr( $is_logged_in ); ?>"
                role="button"
                tabindex="0"
                aria-label="<?php echo esc_attr( $label ); ?>"
                title="<?php echo esc_attr( $label ); ?>"
                style="display: inline-flex; align-items: center; cursor: pointer; transition: all 0.3s ease; justify-content: center;"
            >
                <?php if ( $icon_pos === 'left' && ! empty( $settings['btn_icon']['value'] ) ) : ?>
                    <?php Icons_Manager::render_icon( $settings['btn_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                <?php endif; ?>

                <?php if ( $show_label ) : ?>
                    <span class="mh-wishlist-btn-text"><?php echo esc_html( $label ); ?></span>
                <?php endif; ?>

                <?php if ( $icon_pos === 'right' && ! empty( $settings['btn_icon']['value'] ) ) : ?>
                    <?php Icons_Manager::render_icon( $settings['btn_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                <?php endif; ?>
            </span>
        </div>
        <?php
    }
}