<?php
/**
 * MH FAQ / Accordion Widget
 * Fully Responsive, CSS-Grid Animated, Pixel-Perfect Styling.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Icons_Manager;

class MH_Plug_FAQ_Widget extends \Elementor\Widget_Base {

    public function get_name()        { return 'mh_faq'; }
    public function get_title()       { return __( 'MH Animated FAQ', 'mh-plug-ecommerce-builder-widgets' ); }
    public function get_icon()        { return 'eicon-accordion'; }
    public function get_categories()  { return [ 'mh-plug-widgets' ]; }

    protected function register_controls() {

        /* ==========================================
           📑 CONTENT: FAQ ITEMS
           ========================================== */
        $this->start_controls_section( 'section_items', [
            'label' => __( 'FAQ Items', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $repeater = new Repeater();

        $repeater->add_control( 'title', [
            'label'       => __( 'Question / Title', 'mh-plug-ecommerce-builder-widgets' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'What is your return policy?', 'mh-plug-ecommerce-builder-widgets' ),
            'label_block' => true,
        ] );

        $repeater->add_control( 'content', [
            'label'   => __( 'Answer / Content', 'mh-plug-ecommerce-builder-widgets' ),
            'type'    => Controls_Manager::WYSIWYG,
            'default' => __( 'We offer a 30-day money-back guarantee. If you are not satisfied with your purchase, please contact our support team.', 'mh-plug-ecommerce-builder-widgets' ),
        ] );

        $this->add_control( 'faq_items', [
            'label'       => __( 'Questions', 'mh-plug-ecommerce-builder-widgets' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'title' => __( 'What is your return policy?', 'mh-plug-ecommerce-builder-widgets' ) ],
                [ 'title' => __( 'How long does shipping take?', 'mh-plug-ecommerce-builder-widgets' ), 'content' => __( 'Standard shipping usually takes 3-5 business days. Express shipping options are available at checkout.', 'mh-plug-ecommerce-builder-widgets' ) ],
                [ 'title' => __( 'Do you ship internationally?', 'mh-plug-ecommerce-builder-widgets' ), 'content' => __( 'Yes, we ship to over 50 countries worldwide. International shipping rates apply.', 'mh-plug-ecommerce-builder-widgets' ) ],
            ],
            'title_field' => '{{{ title }}}',
        ] );

        $this->end_controls_section();

        /* ==========================================
           ⚙️ CONTENT: SETTINGS
           ========================================== */
        $this->start_controls_section( 'section_settings', [
            'label' => __( 'Behavior & Icons', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'behavior', [
            'label'   => __( 'Accordion Behavior', 'mh-plug-ecommerce-builder-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'accordion',
            'options' => [
                'accordion' => __( 'Accordion (Close others)', 'mh-plug-ecommerce-builder-widgets' ),
                'toggle'    => __( 'Toggle (Keep multiple open)', 'mh-plug-ecommerce-builder-widgets' ),
            ],
        ] );

        $this->add_control( 'first_open', [
            'label'        => __( 'First Item Open by Default', 'mh-plug-ecommerce-builder-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'icon_align', [
            'label'   => __( 'Icon Alignment', 'mh-plug-ecommerce-builder-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'right',
            'options' => [
                'left'  => __( 'Left', 'mh-plug-ecommerce-builder-widgets' ),
                'right' => __( 'Right', 'mh-plug-ecommerce-builder-widgets' ),
            ],
            'separator' => 'before',
        ] );

        $this->add_control( 'icon_closed', [
            'label'   => __( 'Icon (Closed)', 'mh-plug-ecommerce-builder-widgets' ),
            'type'    => Controls_Manager::ICONS,
            'default' => [ 'value' => 'fas fa-plus', 'library' => 'fa-solid' ],
        ] );

        $this->add_control( 'icon_opened', [
            'label'   => __( 'Icon (Opened)', 'mh-plug-ecommerce-builder-widgets' ),
            'type'    => Controls_Manager::ICONS,
            'default' => [ 'value' => 'fas fa-minus', 'library' => 'fa-solid' ],
        ] );

        $this->end_controls_section();

        /* ==========================================
           🎨 STYLE: GENERAL BOX
           ========================================== */
        $this->start_controls_section( 'style_general', [
            'label' => __( 'General Container', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'item_gap', [
            'label'      => __( 'Space Between Items', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
            'default'    => [ 'size' => 15 ],
            'selectors'  => [ '{{WRAPPER}} .mh-faq-container' => 'gap: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'item_border',
            'selector'  => '{{WRAPPER}} .mh-faq-item',
        ] );

        $this->add_responsive_control( 'item_radius', [
            'label'      => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-faq-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;' ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'      => 'item_shadow',
            'selector'  => '{{WRAPPER}} .mh-faq-item',
        ] );

        $this->end_controls_section();

        /* ==========================================
           🎨 STYLE: TITLE / QUESTION
           ========================================== */
        $this->start_controls_section( 'style_title', [
            'label' => __( 'Question / Title', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .mh-faq-title',
        ] );

        $this->start_controls_tabs( 'tabs_title_style' );

        // Normal State
        $this->start_controls_tab( 'tab_title_normal', [ 'label' => __( 'Normal', 'mh-plug-ecommerce-builder-widgets' ) ] );
        $this->add_control( 'title_color', [
            'label'     => __( 'Text Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#334155',
            'selectors' => [ '{{WRAPPER}} .mh-faq-title' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'title_bg', [
            'label'     => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f8fafc',
            'selectors' => [ '{{WRAPPER}} .mh-faq-header' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_control( 'icon_color', [
            'label'     => __( 'Icon Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2293e9',
            'selectors' => [ '{{WRAPPER}} .mh-faq-icon i' => 'color: {{VALUE}};', '{{WRAPPER}} .mh-faq-icon svg' => 'fill: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        // Active State
        $this->start_controls_tab( 'tab_title_active', [ 'label' => __( 'Active', 'mh-plug-ecommerce-builder-widgets' ) ] );
        $this->add_control( 'title_color_active', [
            'label'     => __( 'Text Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2293e9',
            'selectors' => [ '{{WRAPPER}} .mh-faq-item.active .mh-faq-title' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'title_bg_active', [
            'label'     => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f0f9ff',
            'selectors' => [ '{{WRAPPER}} .mh-faq-item.active .mh-faq-header' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_control( 'icon_color_active', [
            'label'     => __( 'Icon Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2293e9',
            'selectors' => [ '{{WRAPPER}} .mh-faq-item.active .mh-faq-icon i' => 'color: {{VALUE}};', '{{WRAPPER}} .mh-faq-item.active .mh-faq-icon svg' => 'fill: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control( 'title_padding', [
            'label'      => __( 'Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => 20, 'right' => 25, 'bottom' => 20, 'left' => 25, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-faq-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
            'separator'  => 'before',
        ] );

        $this->add_responsive_control( 'icon_size', [
            'label'      => __( 'Icon Size', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 50 ] ],
            'default'    => [ 'size' => 16, 'unit' => 'px' ],
            'selectors'  => [ 
                '{{WRAPPER}} .mh-faq-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-faq-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' 
            ],
        ] );

        $this->add_responsive_control( 'icon_spacing', [
            'label'      => __( 'Icon Spacing', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'    => [ 'size' => 15, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-faq-header' => 'gap: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        /* ==========================================
           🎨 STYLE: CONTENT / ANSWER
           ========================================== */
        $this->start_controls_section( 'style_content', [
            'label' => __( 'Answer / Content', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'content_typography',
            'selector' => '{{WRAPPER}} .mh-faq-body',
        ] );

        $this->add_control( 'content_color', [
            'label'     => __( 'Text Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#64748b',
            'selectors' => [ '{{WRAPPER}} .mh-faq-body' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'content_bg', [
            'label'     => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-faq-body' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'content_padding', [
            'label'      => __( 'Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => 20, 'right' => 25, 'bottom' => 25, 'left' => 25, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-faq-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'content_border',
            'label'     => __( 'Inner Border (Top)', 'mh-plug-ecommerce-builder-widgets' ),
            'selector'  => '{{WRAPPER}} .mh-faq-body',
            'separator' => 'before',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( empty( $settings['faq_items'] ) ) {
            return;
        }

        $icon_align = $settings['icon_align'];
        $behavior   = $settings['behavior'];
        $first_open = $settings['first_open'];
        
        $flex_dir = ( $icon_align === 'left' ) ? 'row-reverse' : 'row';
        ?>


        <div class="mh-faq-container" data-behavior="<?php echo esc_attr( $behavior ); ?>">
            <?php foreach ( $settings['faq_items'] as $index => $item ) : 
                $is_active = ( $first_open === 'yes' && $index === 0 ) ? 'active' : '';
                $item_key = $this->get_repeater_setting_key( 'title', 'faq_items', $index );
                $this->add_render_attribute( $item_key, 'class', 'mh-faq-title' );
            ?>
                <div class="mh-faq-item <?php echo esc_attr( $is_active ); ?>">
                    <div class="mh-faq-header" style="flex-direction: <?php echo esc_attr($flex_dir); ?>;" aria-expanded="<?php echo $is_active ? 'true' : 'false'; ?>">
                        <div <?php $this->print_render_attribute_string( $item_key ); ?>>
                            <?php echo wp_kses_post( $item['title'] ); ?>
                        </div>
                        <div class="mh-faq-icon">
                            <span class="mh-icon-closed">
                                <?php Icons_Manager::render_icon( $settings['icon_closed'], [ 'aria-hidden' => 'true' ] ); ?>
                            </span>
                            <span class="mh-icon-opened">
                                <?php Icons_Manager::render_icon( $settings['icon_opened'], [ 'aria-hidden' => 'true' ] ); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mh-faq-content-wrapper">
                        <div class="mh-faq-content-inner">
                            <div class="mh-faq-body">
                                <?php echo wp_kses_post( $item['content'] ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


        <?php
    }
}