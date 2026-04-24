<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class MH_Compare_Table_Widget extends Widget_Base {

    public function get_name() { return 'mh_compare_table'; }
    public function get_title() { return __( 'MH Compare Table', 'mh-plug' ); }
    public function get_icon() { return 'eicon-table'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_script_depends() { return [ 'mh-widgets-js' ]; }

    protected function register_controls() {
        
        // TABLE STRUCTURE
        $this->start_controls_section('section_style', ['label' => __('Table Structure', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('header_bg', [
            'label' => __('Left Heading Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-table th' => 'background-color: {{VALUE}};']
        ]);
        $this->add_control('content_bg', [
            'label' => __('Content Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-table td' => 'background-color: {{VALUE}};']
        ]);
        $this->add_control('border_color', [
            'label' => __('Border Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-table th, {{WRAPPER}} .mh-compare-table td' => 'border-color: {{VALUE}};']
        ]);
        $this->add_responsive_control('cell_padding', [
            'label' => __('Cell Padding', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => ['{{WRAPPER}} .mh-compare-table th, {{WRAPPER}} .mh-compare-table td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->end_controls_section();

        // TYPOGRAPHY
        $this->start_controls_section('section_typo', ['label' => __('Typography & Colors', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE]);
        
        $this->add_control('heading_th', ['label' => __('Headings (Left Column)', 'mh-plug'), 'type' => Controls_Manager::HEADING]);
        $this->add_control('th_color', [
            'label' => __('Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-table th' => 'color: {{VALUE}};']
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'th_typo', 'selector' => '{{WRAPPER}} .mh-compare-table th']);

        $this->add_control('heading_title', ['label' => __('Product Title', 'mh-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('title_color', [
            'label' => __('Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-title a' => 'color: {{VALUE}};']
        ]);
        $this->add_control('title_hover_color', [
            'label' => __('Hover Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-title a:hover' => 'color: {{VALUE}};']
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'title_typo', 'selector' => '{{WRAPPER}} .mh-compare-title']);

        $this->add_control('heading_price', ['label' => __('Price', 'mh-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('price_color', [
            'label' => __('Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-price' => 'color: {{VALUE}};']
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'price_typo', 'selector' => '{{WRAPPER}} .mh-compare-price']);
        
        $this->end_controls_section();

        // ADD TO CART BUTTON
        $this->start_controls_section('section_cart_btn', ['label' => __('Add to Cart Button', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE]);
        
        $this->add_control('show_cart_btn', [
            'label' => __('Show Button', 'mh-plug'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'btn_typo', 
            'selector' => '{{WRAPPER}} .mh-compare-add-to-cart .button',
            'condition' => ['show_cart_btn' => 'yes'],
        ]);

        $this->add_responsive_control('btn_padding', [
            'label' => __('Padding', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => ['{{WRAPPER}} .mh-compare-add-to-cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            'condition' => ['show_cart_btn' => 'yes'],
        ]);

        $this->add_responsive_control('btn_radius', [
            'label' => __('Border Radius', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => ['{{WRAPPER}} .mh-compare-add-to-cart .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            'condition' => ['show_cart_btn' => 'yes'],
        ]);

        $this->start_controls_tabs('tabs_cart_btn', ['condition' => ['show_cart_btn' => 'yes']]);
        
        // Button Normal
        $this->start_controls_tab('tab_cart_normal', ['label' => __('Normal', 'mh-plug')]);
        $this->add_control('btn_color', [
            'label' => __('Text Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-add-to-cart .button' => 'color: {{VALUE}};']
        ]);
        $this->add_control('btn_bg', [
            'label' => __('Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-add-to-cart .button' => 'background-color: {{VALUE}};']
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'btn_border', 'selector' => '{{WRAPPER}} .mh-compare-add-to-cart .button']);
        $this->end_controls_tab();

        // Button Hover
        $this->start_controls_tab('tab_cart_hover', ['label' => __('Hover', 'mh-plug')]);
        $this->add_control('btn_hover_color', [
            'label' => __('Text Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-add-to-cart .button:hover' => 'color: {{VALUE}};']
        ]);
        $this->add_control('btn_hover_bg', [
            'label' => __('Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-add-to-cart .button:hover' => 'background-color: {{VALUE}};']
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'btn_hover_border', 'selector' => '{{WRAPPER}} .mh-compare-add-to-cart .button:hover']);
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        $this->end_controls_section();

        // REMOVE ICON
        $this->start_controls_section('section_remove_icon', ['label' => __('Remove Icon (X)', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('remove_color', [
            'label' => __('Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-remove-compare' => 'color: {{VALUE}};']
        ]);
        $this->add_control('remove_bg', [
            'label' => __('Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-remove-compare' => 'background-color: {{VALUE}};']
        ]);
        $this->add_control('remove_hover_color', [
            'label' => __('Hover Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-remove-compare:hover' => 'color: {{VALUE}};']
        ]);
        $this->add_control('remove_hover_bg', [
            'label' => __('Hover Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-remove-compare:hover' => 'background-color: {{VALUE}};']
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $hide_cart_class = ($settings['show_cart_btn'] !== 'yes') ? ' mh-hide-cart-btn' : '';
        ?>
        <div class="mh-compare-table-wrapper<?php echo esc_attr($hide_cart_class); ?>">
            <div class="mh-compare-loading" style="text-align:center; padding:50px;">
                <i class="fas fa-spinner fa-spin fa-2x" style="color:#004265;"></i>
                <p style="margin-top:15px; font-weight:600;"><?php esc_html_e('Loading Compare Data...', 'mh-plug'); ?></p>
            </div>
        </div>
        
        <style>
            .mh-compare-table-wrapper { overflow-x: auto; width: 100%; }
            .mh-compare-table { width: 100%; border-collapse: collapse; min-width: 900px; background: #fff; }
            .mh-compare-table th, .mh-compare-table td { border: 1px solid #eaeaea; padding: 25px; text-align: center; vertical-align: top; transition: 0.3s; }
            .mh-compare-table th { background: #f8f9fa; font-weight: 600; width: 20%; text-align: left; vertical-align: middle; color: #222; text-transform: uppercase; font-size: 14px; }
            .mh-compare-image { display: block; margin-bottom: 15px; position: relative; }
            .mh-compare-image img { max-width: 100%; height: auto; border-radius: 8px; }
            .mh-compare-title { font-size: 16px; margin: 0 0 10px; font-weight: 600; line-height: 1.4; }
            .mh-compare-title a { color: #111; text-decoration: none; transition: 0.3s; }
            .mh-compare-title a:hover { color: #d63638; }
            .mh-compare-price { font-size: 18px; font-weight: bold; color: #d63638; margin-bottom: 15px; }
            .mh-remove-compare { color: #999; position: absolute; top: 10px; right: 10px; font-size: 18px; background: #fff; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.3s ease; z-index: 10; text-decoration: none; }
            .mh-remove-compare:hover { color: #fff; background: #d63638; transform: scale(1.1); }
            
            /* Button Base Styles */
            .mh-compare-add-to-cart .button { background: #004265; color: #fff; border-radius: 4px; padding: 12px 24px; border: none; text-decoration: none; display: inline-block; font-weight: 600; transition: 0.3s ease; cursor: pointer; }
            .mh-compare-add-to-cart .button:hover { background: #002b42; transform: translateY(-2px); }
            
            /* Hide Button Logic */
            .mh-compare-table-wrapper.mh-hide-cart-btn .mh-compare-add-to-cart { display: none !important; }
            
            .mh-compare-empty { padding: 60px; text-align: center; border: 2px dashed #ccc; border-radius: 8px; }
            .mh-compare-empty h3 { color: #333; margin-bottom: 10px; }
        </style>
        <?php
    }
}