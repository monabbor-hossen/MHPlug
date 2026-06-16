<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class MH_Product_Compare_Btn_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'mh_product_compare_btn'; }
    public function get_title() { return __( 'MH Add to Compare Button', 'mh-plug' ); }
    public function get_icon() { return 'eicon-button'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_script_depends() { return [ 'mh-widgets-js' ]; }

    protected function register_controls() {
        // ==========================================
        // CONTENT TAB
        // ==========================================
        $this->start_controls_section('section_content', ['label' => __('Content', 'mh-plug')]);
        
        $this->add_control('text_normal', [
            'label' => __('Default Text', 'mh-plug'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Add to Compare', 'mh-plug'),
        ]);
        
        $this->add_control('icon', [
            'label' => __('Compare Icon', 'mh-plug'),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-exchange-alt', 'library' => 'fa-solid'],
        ]);

        $this->add_responsive_control('align', [
            'label' => __('Alignment', 'mh-plug'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => ['title' => 'Left', 'icon' => 'eicon-text-align-left'],
                'center' => ['title' => 'Center', 'icon' => 'eicon-text-align-center'],
                'right' => ['title' => 'Right', 'icon' => 'eicon-text-align-right'],
            ],
            'selectors' => ['{{WRAPPER}} .mh-single-compare-wrap' => 'text-align: {{VALUE}};'],
        ]);
        
        $this->end_controls_section();

        // ==========================================
        // STYLE TAB
        // ==========================================
        $this->start_controls_section('section_style', ['label' => __('Button Style', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE]);
        
        // --- TYPOGRAPHY (TEXT SIZE & FONT) ---
        $this->add_control('heading_text_style', ['label' => __('Text Settings', 'mh-plug'), 'type' => Controls_Manager::HEADING]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'typography', 
            'label' => __('Text Typography & Size', 'mh-plug'),
            'selector' => '{{WRAPPER}} .mh-compare-btn .mh-compare-text'
        ]);

        // --- ICON SETTINGS ---
        $this->add_control('heading_icon_style', ['label' => __('Icon Settings', 'mh-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('icon_size', [
            'label' => __('Icon Size', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 100]],
            'selectors' => [
                '{{WRAPPER}} .mh-compare-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-compare-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);
        $this->add_responsive_control('icon_spacing', [
            'label' => __('Icon Spacing (Gap)', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn' => 'gap: {{SIZE}}{{UNIT}};'],
        ]);

        // --- BUTTON SPACING ---
        $this->add_control('heading_box_style', ['label' => __('Box Settings', 'mh-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('padding', [
            'label' => __('Padding', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => ['{{WRAPPER}} .mh-compare-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('border_radius', [
            'label' => __('Border Radius', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => ['{{WRAPPER}} .mh-compare-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        // ==========================================
        // NORMAL / HOVER / ADDED TABS
        // ==========================================
        $this->start_controls_tabs('tabs_btn_style');

        // --- NORMAL ---
        $this->start_controls_tab('tab_btn_normal', ['label' => __('Normal', 'mh-plug')]);
        $this->add_control('text_color', [
            'label' => __('Text Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn .mh-compare-text' => 'color: {{VALUE}};'],
        ]);
        $this->add_control('icon_color', [
            'label' => __('Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn i' => 'color: {{VALUE}};', '{{WRAPPER}} .mh-compare-btn svg' => 'fill: {{VALUE}};'],
        ]);
        $this->add_control('bg_color', [
            'label' => __('Background Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_normal', 'selector' => '{{WRAPPER}} .mh-compare-btn']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'shadow_normal', 'selector' => '{{WRAPPER}} .mh-compare-btn']);
        $this->end_controls_tab();

        // --- HOVER ---
        $this->start_controls_tab('tab_btn_hover', ['label' => __('Hover', 'mh-plug')]);
        $this->add_control('text_color_hover', [
            'label' => __('Text Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn:hover .mh-compare-text' => 'color: {{VALUE}};'],
        ]);
        $this->add_control('icon_color_hover', [
            'label' => __('Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn:hover i' => 'color: {{VALUE}};', '{{WRAPPER}} .mh-compare-btn:hover svg' => 'fill: {{VALUE}};'],
        ]);
        $this->add_control('bg_color_hover', [
            'label' => __('Background Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn:hover' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_hover', 'selector' => '{{WRAPPER}} .mh-compare-btn:hover']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'shadow_hover', 'selector' => '{{WRAPPER}} .mh-compare-btn:hover']);
        $this->add_control('hover_animation', [
            'label' => __('Hover Animation', 'mh-plug'),
            'type' => Controls_Manager::HOVER_ANIMATION,
        ]);
        $this->end_controls_tab();

        // --- ADDED (ACTIVE) ---
        $this->start_controls_tab('tab_btn_added', ['label' => __('Added', 'mh-plug')]);
        $this->add_control('text_color_added', [
            'label' => __('Text Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn.added .mh-compare-text' => 'color: {{VALUE}};'],
        ]);
        $this->add_control('icon_color_added', [
            'label' => __('Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn.added i' => 'color: {{VALUE}};', '{{WRAPPER}} .mh-compare-btn.added svg' => 'fill: {{VALUE}};'],
        ]);
        $this->add_control('bg_color_added', [
            'label' => __('Background Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn.added' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border_added', 'selector' => '{{WRAPPER}} .mh-compare-btn.added']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'shadow_added', 'selector' => '{{WRAPPER}} .mh-compare-btn.added']);
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control('transition_duration', [
            'label' => __('Transition Duration (s)', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 0.3],
            'range' => ['px' => ['max' => 3, 'step' => 0.1]],
            'selectors' => ['{{WRAPPER}} .mh-compare-btn, {{WRAPPER}} .mh-compare-btn i, {{WRAPPER}} .mh-compare-btn svg, {{WRAPPER}} .mh-compare-btn .mh-compare-text' => 'transition: all {{SIZE}}s ease;'],
            'separator' => 'before',
        ]);
        
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: SNACK ALERT (TOAST)
        // ----------------------------------------------------
        $this->start_controls_section('section_style_toast', ['label' => __('Snack Alert (Toast)', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('toast_position', [
            'label' => __('Position', 'mh-plug'),
            'type' => Controls_Manager::SELECT,
            'default' => 'bottom-left',
            'options' => [
                'bottom-left'   => __('Bottom Left', 'mh-plug'),
                'bottom-center' => __('Bottom Center', 'mh-plug'),
                'bottom-right'  => __('Bottom Right', 'mh-plug'),
                'top-left'      => __('Top Left', 'mh-plug'),
                'top-center'    => __('Top Center', 'mh-plug'),
                'top-right'     => __('Top Right', 'mh-plug'),
            ],
            'selectors_dictionary' => [
                'bottom-left'   => 'bottom: 20px !important; left: 20px !important; top: auto !important; right: auto !important; align-items: flex-start !important; transform: none !important;',
                'bottom-center' => 'bottom: 20px !important; left: 50% !important; top: auto !important; right: auto !important; align-items: center !important; transform: translateX(-50%) !important;',
                'bottom-right'  => 'bottom: 20px !important; right: 20px !important; left: auto !important; top: auto !important; align-items: flex-end !important; transform: none !important;',
                'top-left'      => 'top: 20px !important; left: 20px !important; bottom: auto !important; right: auto !important; align-items: flex-start !important; transform: none !important;',
                'top-center'    => 'top: 20px !important; left: 50% !important; bottom: auto !important; right: auto !important; align-items: center !important; transform: translateX(-50%) !important;',
                'top-right'     => 'top: 20px !important; right: 20px !important; bottom: auto !important; left: auto !important; align-items: flex-end !important; transform: none !important;',
            ],
            'selectors' => [
                'body .mh-toast-container' => '{{VALUE}}',
            ],
        ]);
        $this->add_control('toast_bg', ['label' => __('Background Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => ['body .mh-toast' => 'background-color: {{VALUE}} !important;']]);
        $this->add_control('toast_color', ['label' => __('Text Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['body .mh-toast' => 'color: {{VALUE}} !important;']]);
        $this->add_control('toast_success_border', ['label' => __('Success Border Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#4caf50', 'selectors' => ['body .mh-toast-success' => 'border-left-color: {{VALUE}} !important;']]);
        $this->add_control('toast_error_border', ['label' => __('Error Border Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#f44336', 'selectors' => ['body .mh-toast-error' => 'border-left-color: {{VALUE}} !important;']]);
        $this->add_control('toast_info_border', ['label' => __('Info Border Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#2196f3', 'selectors' => ['body .mh-toast-info' => 'border-left-color: {{VALUE}} !important;']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'toast_typo', 'selector' => 'body .mh-toast']);
        $this->add_responsive_control('toast_radius', ['label' => __('Border Radius', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['body .mh-toast' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->add_responsive_control('toast_padding', ['label' => __('Padding', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'selectors' => ['body .mh-toast' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->end_controls_section();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) ) return;
        $product_id = get_the_ID();
        if ( ! $product_id ) return;
        $settings = $this->get_settings_for_display();
        $animation_class = !empty($settings['hover_animation']) ? ' elementor-animation-' . $settings['hover_animation'] : '';
        ?>
        <div class="mh-single-compare-wrap">
            <a href="#" class="mh-compare-btn<?php echo esc_attr($animation_class); ?>" data-product-id="<?php echo esc_attr($product_id); ?>" style="display:inline-flex; align-items:center; text-decoration:none; cursor:pointer;">
                <?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?>
                <span class="mh-compare-text" data-default="<?php echo esc_attr($settings['text_normal']); ?>"><?php echo esc_html($settings['text_normal']); ?></span>
            </a>
        </div>
        <?php
    }
}