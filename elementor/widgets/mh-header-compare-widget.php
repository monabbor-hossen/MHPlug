<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class MH_Header_Compare_Widget extends Widget_Base {

    public function get_name() { return 'mh_header_compare'; }
    public function get_title() { return __( 'MH Header Compare', 'mh-plug' ); }
    public function get_icon() { return 'eicon-exchange'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_script_depends() { return [ 'mh-widgets-js' ]; }

    protected function register_controls() {
        // CONTENT
        $this->start_controls_section('section_content', ['label' => __('Compare Settings', 'mh-plug')]);
        
        $this->add_control('icon', [
            'label' => __('Compare Icon', 'mh-plug'),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-exchange-alt', 'library' => 'fa-solid'],
        ]);

        $this->add_control('compare_page_url', [
            'label' => __('Compare Page URL', 'mh-plug'),
            'type' => Controls_Manager::URL,
            'placeholder' => __('https://your-site.com/compare', 'mh-plug'),
        ]);
        
        $this->end_controls_section();

        // STYLE - ICON
        $this->start_controls_section('section_style_icon', ['label' => __('Icon Style', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE]);
        
        $this->add_responsive_control('icon_size', [
            'label' => __('Icon Size', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'selectors' => ['{{WRAPPER}} .mh-header-compare-icon i, {{WRAPPER}} .mh-header-compare-icon svg' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_responsive_control('box_size', [
            'label' => __('Box Size', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'selectors' => ['{{WRAPPER}} .mh-header-compare-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; display: flex; align-items: center; justify-content: center;'],
        ]);

        $this->add_responsive_control('icon_radius', [
            'label' => __('Border Radius', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => ['{{WRAPPER}} .mh-header-compare-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->start_controls_tabs('tabs_icon_style');

        // Normal State
        $this->start_controls_tab('tab_icon_normal', ['label' => __('Normal', 'mh-plug')]);
        $this->add_control('icon_color', [
            'label' => __('Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-header-compare-icon' => 'color: {{VALUE}}; fill: {{VALUE}};'],
        ]);
        $this->add_control('icon_bg', [
            'label' => __('Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-header-compare-icon' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'icon_border', 'selector' => '{{WRAPPER}} .mh-header-compare-icon']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'icon_shadow', 'selector' => '{{WRAPPER}} .mh-header-compare-icon']);
        $this->end_controls_tab();

        // Hover State
        $this->start_controls_tab('tab_icon_hover', ['label' => __('Hover', 'mh-plug')]);
        $this->add_control('icon_hover_color', [
            'label' => __('Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-header-compare-icon:hover' => 'color: {{VALUE}}; fill: {{VALUE}};'],
        ]);
        $this->add_control('icon_hover_bg', [
            'label' => __('Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-header-compare-icon:hover' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'icon_hover_border', 'selector' => '{{WRAPPER}} .mh-header-compare-icon:hover']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'icon_hover_shadow', 'selector' => '{{WRAPPER}} .mh-header-compare-icon:hover']);
        $this->add_control('hover_animation', [
            'label' => __('Hover Animation', 'mh-plug'),
            'type' => Controls_Manager::HOVER_ANIMATION,
        ]);
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control('transition_duration', [
            'label' => __('Transition Duration (s)', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 0.3],
            'range' => ['px' => ['max' => 3, 'step' => 0.1]],
            'selectors' => ['{{WRAPPER}} .mh-header-compare-icon' => 'transition: all {{SIZE}}s ease;'],
            'separator' => 'before',
        ]);

        $this->end_controls_section();

        // STYLE - BADGE
        $this->start_controls_section('section_style_badge', ['label' => __('Badge Style', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('badge_bg', [
            'label' => __('Badge Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'default' => '#d63638',
            'selectors' => ['{{WRAPPER}} .mh-compare-count' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_control('badge_color', [
            'label' => __('Badge Text Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => ['{{WRAPPER}} .mh-compare-count' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'badge_typo', 'selector' => '{{WRAPPER}} .mh-compare-count']);
        
        $this->add_responsive_control('badge_offset_x', [
            'label' => __('Horizontal Offset', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => -50, 'max' => 50]],
            'selectors' => ['{{WRAPPER}} .mh-compare-count' => 'right: {{SIZE}}px;'],
        ]);
        $this->add_responsive_control('badge_offset_y', [
            'label' => __('Vertical Offset', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => -50, 'max' => 50]],
            'selectors' => ['{{WRAPPER}} .mh-compare-count' => 'top: {{SIZE}}px;'],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $target = $settings['compare_page_url']['is_external'] ? ' target="_blank"' : '';
        $url = !empty($settings['compare_page_url']['url']) ? $settings['compare_page_url']['url'] : '#';
        $animation_class = !empty($settings['hover_animation']) ? ' elementor-animation-' . $settings['hover_animation'] : '';
        ?>
        <div class="mh-header-compare-wrapper" style="position: relative; display: inline-block;">
            <a href="<?php echo esc_url($url); ?>" <?php echo $target; ?> class="mh-header-compare-icon<?php echo esc_attr($animation_class); ?>" style="text-decoration:none; position:relative;">
                <?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?>
                <span class="mh-compare-count" style="position: absolute; top: -8px; right: -10px; font-size: 11px; padding: 2px 5px; border-radius: 50%; min-width: 18px; text-align: center; line-height: 1;">0</span>
            </a>
        </div>
        <?php
    }
}