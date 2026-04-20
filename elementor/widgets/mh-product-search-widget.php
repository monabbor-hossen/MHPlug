<?php
/**
 * MH Product Search Widget (Live AJAX Search)
 * Fully Responsive with Isolated CSS Variables and Pixel-Perfect Alignment Controls.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

class MH_Plug_Product_Search_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_search'; }
    public function get_title() { return __( 'MH Product Search', 'mh-plug' ); }
    public function get_icon() { return 'eicon-search'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    protected function register_controls() {
        
        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Search Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'search_layout', [
            'label'        => __( 'Layout', 'mh-plug' ),
            'type'         => Controls_Manager::SELECT,
            'default'      => 'standard',
            'options'      => [
                'standard'   => __( 'Standard (Always Open)', 'mh-plug' ),
                'expandable' => __( 'Dropdown Box (Icon Click)', 'mh-plug' ),
                'slide_out'  => __( 'Morphing (Sliding Animation)', 'mh-plug' ),
            ],
        ] );

        $this->add_control( 'design_style', [
            'label'   => __( 'Input Design Style', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'modern',
            'options' => [
                'classic' => __( 'Classic (Standard Box)', 'mh-plug' ),
                'modern'  => __( 'Modern (Icon Inside Input)', 'mh-plug' ),
            ],
        ] );

        $this->add_control( 'search_icon', [
            'label'     => __( 'Search Icon', 'mh-plug' ),
            'type'      => Controls_Manager::ICONS,
            'default'   => [ 'value' => 'fas fa-search', 'library' => 'fa-solid' ],
        ] );

        $this->add_control( 'placeholder', [
            'label'   => __( 'Placeholder Text', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Search for products...', 'mh-plug' ),
        ] );

        $this->add_control( 'not_found_text', [
            'label'   => __( 'Not Found Message', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'No products found.', 'mh-plug' ),
        ] );

        $this->end_controls_section();

        /* ==========================================
           🎨 STYLE: STANDARD LAYOUT
           ========================================== */
        $this->start_controls_section( 'style_standard_section', [
            'label' => __( '🎨 Standard Layout Styles', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'std_input_bg', [
            'label'     => __( 'Input Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}}' => '--std-in-bg: {{VALUE}};' ],
        ] );

        $this->add_control( 'std_input_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}}' => '--std-in-c: {{VALUE}};' ],
        ] );

        $this->add_control( 'std_ph_color', [
            'label'     => __( 'Placeholder Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}}' => '--std-ph-c: {{VALUE}};' ],
        ] );

        $this->add_control( 'std_icon_color', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}}' => '--std-ic-c: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'std_icon_size', [
            'label'      => __( 'Icon Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'selectors'  => [ '{{WRAPPER}}' => '--std-ic-s: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'std_padding', [
            'label'      => __( 'Input Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [ '{{WRAPPER}}' => '--std-in-p: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'std_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}}' => '--std-in-r: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_control( 'std_border_color', [
            'label'     => __( 'Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'separator' => 'before',
            'selectors' => [ '{{WRAPPER}}' => '--std-bd-c: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'std_border_width', [
            'label'      => __( 'Border Width', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'selectors'  => [ '{{WRAPPER}}' => '--std-bd-w: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        /* ==========================================
           🎨 STYLE: EXPANDABLE DROPDOWN
           ========================================== */
        $this->start_controls_section( 'style_expandable_section', [
            'label' => __( '🎨 Expandable Layout Styles', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'exp_box_width', [
            'label'      => __( 'Dropdown Box Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [ 'px' => [ 'min' => 200, 'max' => 600 ] ],
            'selectors'  => [ '{{WRAPPER}}' => '--exp-box-w: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'exp_box_pad', [
            'label'      => __( 'Dropdown Box Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [ '{{WRAPPER}}' => '--exp-box-p: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_control( 'heading_exp_trigger', [ 'label' => __( 'Trigger Button', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

        $this->add_control( 'exp_trig_color', [ 'label' => __( 'Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--exp-tr-c: {{VALUE}};' ] ] );
        $this->add_control( 'exp_trig_bg', [ 'label' => __( 'Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--exp-tr-bg: {{VALUE}};' ] ] );
        $this->add_control( 'exp_trig_color_h', [ 'label' => __( 'Hover Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--exp-tr-hc: {{VALUE}};' ] ] );
        $this->add_control( 'exp_trig_bg_h', [ 'label' => __( 'Hover Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--exp-tr-hbg: {{VALUE}};' ] ] );
        
        $this->add_responsive_control( 'exp_trig_size', [ 'label' => __( 'Icon Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}}' => '--exp-tr-s: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'exp_trig_pad', [ 'label' => __( 'Button Padding', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}}' => '--exp-tr-p: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'exp_trig_rad', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}}' => '--exp-tr-r: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

        $this->add_control( 'heading_exp_input', [ 'label' => __( 'Inside Search Input', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        
        $this->add_control( 'exp_in_bg', [ 'label' => __( 'Input Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--exp-in-bg: {{VALUE}};' ] ] );
        $this->add_control( 'exp_in_c', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--exp-in-c: {{VALUE}};' ] ] );
        $this->add_control( 'exp_ph_color', [ 'label' => __( 'Placeholder Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--exp-ph-c: {{VALUE}};' ] ] );
        $this->add_control( 'exp_ic_c', [ 'label' => __( 'Inside Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--exp-ic-c: {{VALUE}};' ] ] );
        
        $this->add_responsive_control( 'exp_in_pad', [
            'label'      => __( 'Input Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [ '{{WRAPPER}}' => '--exp-in-p: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        /* ==========================================
           🎨 STYLE: MORPHING SLIDER
           ========================================== */
        $this->start_controls_section( 'style_morphing_section', [
            'label' => __( '🎨 Morphing Layout Styles', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'mor_expanded_width', [
            'label'      => __( 'Expanded Search Max Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [ 'px' => [ 'min' => 150, 'max' => 600 ] ],
            'selectors'  => [ '{{WRAPPER}}' => '--mor-box-w: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_control( 'heading_mor_closed', [ 'label' => __( 'Closed State (Button)', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

        $this->add_control( 'mor_trig_color', [ 'label' => __( 'Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--mor-tr-c: {{VALUE}};' ] ] );
        $this->add_control( 'mor_trig_bg', [ 'label' => __( 'Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--mor-tr-bg: {{VALUE}};' ] ] );
        $this->add_control( 'mor_trig_color_h', [ 'label' => __( 'Hover Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--mor-tr-hc: {{VALUE}};' ] ] );
        $this->add_control( 'mor_trig_bg_h', [ 'label' => __( 'Hover Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--mor-tr-hbg: {{VALUE}};' ] ] );
        $this->add_responsive_control( 'mor_trig_s', [ 'label' => __( 'Icon Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}}' => '--mor-tr-s: {{SIZE}}{{UNIT}};' ] ] );
        
        $this->add_responsive_control( 'mor_trig_pad', [ 
            'label'      => __( 'Closed Button Padding', 'mh-plug' ), 
            'type'       => Controls_Manager::DIMENSIONS, 
            'size_units' => [ 'px' ],
            'selectors'  => [ 
                '{{WRAPPER}}' => '--mor-tr-pt: {{TOP}}{{UNIT}}; --mor-tr-pr: {{RIGHT}}{{UNIT}}; --mor-tr-pb: {{BOTTOM}}{{UNIT}}; --mor-tr-pl: {{LEFT}}{{UNIT}};' 
            ] 
        ] );

        $this->add_responsive_control( 'mor_trig_r', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}}' => '--mor-tr-r: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

        $this->add_control( 'heading_mor_open', [ 'label' => __( 'Open State (Expanded)', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

        $this->add_control( 'mor_in_bg', [ 'label' => __( 'Input Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--mor-in-bg: {{VALUE}};' ] ] );
        $this->add_control( 'mor_in_c', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--mor-in-c: {{VALUE}};' ] ] );
        $this->add_control( 'mor_ph_color', [ 'label' => __( 'Placeholder Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--mor-ph-c: {{VALUE}};' ] ] );
        $this->add_control( 'mor_ic_c', [ 'label' => __( 'Inside Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--mor-ic-c: {{VALUE}};' ] ] );
        
        $this->add_responsive_control( 'mor_in_pad', [
            'label'      => __( 'Expanded Input Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [ '{{WRAPPER}}' => '--mor-in-p: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'mor_in_r', [ 'label' => __( 'Expanded Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}}' => '--mor-in-r: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        
        $this->add_control( 'mor_bd_c', [ 'label' => __( 'Expanded Border Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}}' => '--mor-bd-c: {{VALUE}};' ] ] );
        $this->add_responsive_control( 'mor_bd_w', [ 'label' => __( 'Expanded Border Width', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}}' => '--mor-bd-w: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

        $this->end_controls_section();

        /* ==========================================
           🎨 STYLE: INSIDE ICON ALIGNMENT
           ========================================== */
        $this->start_controls_section( 'style_icon_align_section', [
            'label'     => __( '🎨 Inside Icon Alignment', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'description' => __( 'Use these sliders to perfectly align the search icon with your placeholder text.', 'mh-plug' ),
        ] );

        // 🚀 THE FIX: New dedicated controls for pixel-perfect X/Y alignment!
        $this->add_responsive_control( 'icon_x_pos', [
            'label'      => __( 'Horizontal Position (X)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'selectors'  => [ '{{WRAPPER}}' => '--ic-x: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'icon_y_pos', [
            'label'      => __( 'Vertical Fine-Tune (Y)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => -30, 'max' => 30 ] ],
            'selectors'  => [ '{{WRAPPER}}' => '--ic-y: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        /* ==========================================
           🌍 STYLE: GLOBAL (Applies to all)
           ========================================== */
        $this->start_controls_section( 'style_global_section', [
            'label' => __( '🌍 Global Styles (All Layouts)', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'input_typography',
            'label'    => __( 'Search Text Typography', 'mh-plug' ),
            'selector' => '{{WRAPPER}} .mh-search-input',
        ] );

        $this->add_control( 'results_bg', [
            'label'     => __( 'Results Box Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'separator' => 'before',
            'selectors' => [ '{{WRAPPER}} .mh-search-results' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'results_shadow',
            'selector' => '{{WRAPPER}} .mh-search-results',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $design    = $settings['design_style'];
        $icon      = $settings['search_icon'];
        $not_found = esc_attr( $settings['not_found_text'] );

        $layout_desktop = isset($settings['search_layout']) ? $settings['search_layout'] : 'standard';
        $layout_tablet  = !empty($settings['search_layout_tablet']) ? $settings['search_layout_tablet'] : $layout_desktop;
        $layout_mobile  = !empty($settings['search_layout_mobile']) ? $settings['search_layout_mobile'] : $layout_tablet;
        
        $wrapper_classes = "mh-desk-{$layout_desktop} mh-tab-{$layout_tablet} mh-mob-{$layout_mobile} mh-design-{$design}";
        ?>

        <style>
            .mh-live-search-wrapper { position: relative; display: flex; align-items: center; width: 100%; }
            
            /* Base Reset Styles */
            .mh-search-trigger { background: transparent; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; padding: 10px; z-index: 10; }
            .mh-search-expandable-container { transition: all 0.3s ease; }
            .mh-search-form { position: relative; display: flex; align-items: center; margin: 0 !important; padding: 0 !important; width: 100%; box-sizing: border-box; }
            .mh-search-input { width: 100%; outline: none; transition: 0.3s; margin: 0 !important; box-sizing: border-box; display: block; line-height: normal !important; }
            .mh-search-input::-webkit-search-cancel-button { cursor: pointer; }
            
            /* 🚀 THE FIX: Icons now use dynamic X and Y CSS variables for pixel-perfect centering! */
            .mh-search-icon { 
                position: absolute; 
                left: var(--ic-x, 15px); 
                top: calc(50% + var(--ic-y, 0px)); 
                transform: translateY(-50%); 
                display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 2; line-height: 1; 
            }
            .mh-design-classic .mh-search-icon { display: none !important; }
            
            .mh-search-spinner { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); display: none; z-index: 2; line-height: 1; }
            .mh-search-results a { transition: background 0.2s; }
            .mh-search-results a:hover { background: #f9f9f9; }

            /* =======================================
               DYNAMIC CSS VARIABLE ENGINE
               ======================================= */
            <?php
            $breakpoints = [
                'desktop' => [ 'class' => '.mh-desk', 'media' => '@media (min-width: 1025px)' ],
                'tablet'  => [ 'class' => '.mh-tab',  'media' => '@media (min-width: 768px) and (max-width: 1024px)' ],
                'mobile'  => [ 'class' => '.mh-mob',  'media' => '@media (max-width: 767px)' ],
            ];

            foreach ( $breakpoints as $bp ) {
                echo $bp['media'] . " {\n";
                $prefix = $bp['class'];

                // 1. STANDARD LAYOUT
                echo "{$prefix}-standard .mh-search-trigger { display: none !important; }\n";
                echo "{$prefix}-standard .mh-search-expandable-container { position: relative !important; top: auto !important; right: auto !important; opacity: 1 !important; visibility: visible !important; transform: none !important; background: transparent !important; padding: 0 !important; border-radius: 0 !important; box-shadow: none !important; max-width: 100% !important; width: 100% !important; z-index: 1 !important; display: flex; }\n";
                echo "{$prefix}-standard.mh-live-search-wrapper { justify-content: center !important; }\n";
                echo "{$prefix}-standard .mh-search-form { width: 100% !important; max-width: 100% !important; transition: none !important; background: transparent !important; border: none !important; }\n";
                echo "{$prefix}-standard .mh-search-input { cursor: text !important; background-color: var(--std-in-bg, #f1f1f1) !important; color: var(--std-in-c, #333333) !important; padding: var(--std-in-p, 12px 15px 12px 45px) !important; border-radius: var(--std-in-r, 4px) !important; border: var(--std-bd-w, 0px) solid var(--std-bd-c, transparent) !important; }\n";
                echo "{$prefix}-standard .mh-search-input::placeholder { color: var(--std-ph-c, #888888) !important; opacity: 1 !important; }\n";
                echo "{$prefix}-standard .mh-search-icon i, {$prefix}-standard .mh-search-icon svg, {$prefix}-standard .mh-search-spinner i { color: var(--std-ic-c, #888888) !important; fill: var(--std-ic-c, #888888) !important; font-size: var(--std-ic-s, 16px) !important; width: var(--std-ic-s, 16px) !important; height: var(--std-ic-s, 16px) !important; }\n";
                echo "{$prefix}-standard .mh-search-results { position: absolute !important; top: calc(100% + 5px) !important; left: 0 !important; width: 100% !important; z-index: 99999 !important; border-radius: 4px !important; box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; border: 1px solid #eee !important; margin: 0 !important; padding: 0 !important; max-height: 400px !important; overflow-y: auto !important; display: none; }\n";

                // 2. EXPANDABLE DROPDOWN
                echo "{$prefix}-expandable .mh-search-trigger { display: flex !important; color: var(--exp-tr-c, #333333) !important; background-color: var(--exp-tr-bg, transparent) !important; padding: var(--exp-tr-p, 10px) !important; border-radius: var(--exp-tr-r, 4px) !important; }\n";
                echo "{$prefix}-expandable .mh-search-trigger:hover { color: var(--exp-tr-hc, #d63638) !important; background-color: var(--exp-tr-hbg, transparent) !important; }\n";
                echo "{$prefix}-expandable .mh-search-trigger i, {$prefix}-expandable .mh-search-trigger svg { font-size: var(--exp-tr-s, 20px) !important; width: var(--exp-tr-s, 20px) !important; height: var(--exp-tr-s, 20px) !important; }\n";
                echo "{$prefix}-expandable .mh-search-expandable-container { position: absolute !important; top: calc(100% + 15px) !important; right: 0 !important; opacity: 0 !important; visibility: hidden !important; transform: translateY(10px) !important; background: #fff !important; padding: var(--exp-box-p, 15px) !important; border-radius: 8px !important; box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important; max-width: 90vw !important; width: var(--exp-box-w, 320px) !important; z-index: 9999 !important; display: block; }\n";
                echo "{$prefix}-expandable.mh-search-is-open .mh-search-expandable-container { opacity: 1 !important; visibility: visible !important; transform: translateY(0) !important; }\n";
                echo "{$prefix}-expandable.mh-live-search-wrapper { justify-content: flex-end !important; }\n";
                echo "{$prefix}-expandable .mh-search-form { width: 100% !important; max-width: 100% !important; transition: none !important; background: transparent !important; border: none !important; }\n";
                echo "{$prefix}-expandable .mh-search-input { cursor: text !important; background-color: var(--exp-in-bg, #f1f1f1) !important; color: var(--exp-in-c, #333333) !important; padding: var(--exp-in-p, 12px 15px 12px 45px) !important; border-radius: 4px !important; }\n";
                echo "{$prefix}-expandable .mh-search-input::placeholder { color: var(--exp-ph-c, #888888) !important; opacity: 1 !important; }\n";
                echo "{$prefix}-expandable .mh-search-icon i, {$prefix}-expandable .mh-search-icon svg { color: var(--exp-ic-c, #888888) !important; fill: var(--exp-ic-c, #888888) !important; }\n";
                echo "{$prefix}-expandable .mh-search-results { position: static !important; box-shadow: none !important; border-top: 1px solid #eee !important; margin-top: 10px !important; padding-top: 10px !important; border: none !important; max-height: 400px !important; overflow-y: auto !important; display: none; }\n";

                // 3. MORPHING SLIDER
                echo "{$prefix}-slide_out .mh-search-trigger { display: none !important; }\n";
                echo "{$prefix}-slide_out .mh-search-expandable-container { position: relative !important; top: auto !important; right: auto !important; opacity: 1 !important; visibility: visible !important; transform: none !important; background: transparent !important; padding: 0 !important; border-radius: 0 !important; box-shadow: none !important; max-width: 100% !important; width: 100% !important; z-index: 1 !important; display: flex; justify-content: flex-end; align-items: center !important; }\n";
                
                echo "{$prefix}-slide_out .mh-search-form { display: flex !important; align-items: center !important; position: relative !important; width: 100% !important; max-width: calc(var(--mor-tr-s, 20px) + var(--mor-tr-pl, 15px) + var(--mor-tr-pr, 15px)) !important; transition: max-width 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), background-color 0.4s ease, border-radius 0.4s ease !important; margin-left: auto !important; background-color: var(--mor-tr-bg, #f1f1f1) !important; border-radius: var(--mor-tr-r, 50px) !important; border: var(--mor-bd-w, 0px) solid transparent !important; overflow: hidden !important; }\n";
                
                echo "{$prefix}-slide_out .mh-search-icon { position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; display: flex !important; align-items: center !important; justify-content: center !important; color: var(--mor-tr-c, #333333) !important; fill: var(--mor-tr-c, #333333) !important; pointer-events: none !important; transition: all 0.4s ease !important; z-index: 2 !important; }\n";
                
                echo "{$prefix}-slide_out .mh-search-icon i, {$prefix}-slide_out .mh-search-icon svg { font-size: var(--mor-tr-s, 20px) !important; width: var(--mor-tr-s, 20px) !important; height: var(--mor-tr-s, 20px) !important; display: block !important; }\n";
                
                echo "{$prefix}-slide_out .mh-search-input { width: 100% !important; cursor: pointer !important; color: transparent !important; background: transparent !important; border: none !important; padding: var(--mor-tr-pt, 15px) var(--mor-tr-pr, 15px) var(--mor-tr-pb, 15px) var(--mor-tr-pl, 15px) !important; line-height: var(--mor-tr-s, 20px) !important; transition: all 0.4s ease !important; z-index: 1 !important; }\n";
                
                echo "{$prefix}-slide_out .mh-search-input::placeholder { color: transparent !important; }\n";
                
                echo "{$prefix}-slide_out.mh-live-search-wrapper { justify-content: flex-end !important; }\n";
                
                echo "{$prefix}-slide_out.mh-live-search-wrapper:not(.mh-search-is-open) .mh-search-form:hover { background-color: var(--mor-tr-hbg, #e1e1e1) !important; }\n";
                echo "{$prefix}-slide_out.mh-live-search-wrapper:not(.mh-search-is-open) .mh-search-form:hover .mh-search-icon { color: var(--mor-tr-hc, #d63638) !important; fill: var(--mor-tr-hc, #d63638) !important; }\n";
                
                // Active Open State (Morphing)
                echo "{$prefix}-slide_out.mh-search-is-open .mh-search-form { max-width: var(--mor-box-w, 320px) !important; background-color: var(--mor-in-bg, #ffffff) !important; border-radius: var(--mor-in-r, 50px) !important; border-color: var(--mor-bd-c, transparent) !important; }\n";
                
                // 🚀 THE FIX: Use User-Controlled X/Y Variables for the Morphing Open Icon!
                echo "{$prefix}-slide_out.mh-search-is-open .mh-search-icon { left: var(--ic-x, 15px) !important; top: calc(50% + var(--ic-y, 0px)) !important; transform: translateY(-50%) !important; color: var(--mor-ic-c, #888888) !important; fill: var(--mor-ic-c, #888888) !important; }\n";
                
                echo "{$prefix}-slide_out.mh-search-is-open .mh-search-input { cursor: text !important; color: var(--mor-in-c, #333333) !important; padding: var(--mor-in-p, 12px 15px 12px 45px) !important; line-height: normal !important; }\n";
                
                echo "{$prefix}-slide_out.mh-search-is-open .mh-search-input::placeholder { color: var(--mor-ph-c, #888888) !important; opacity: 1 !important; }\n";
                
                echo "{$prefix}-slide_out .mh-search-results { position: absolute !important; top: calc(100% + 5px) !important; right: 0 !important; left: auto !important; width: var(--mor-box-w, 320px) !important; z-index: 99999 !important; border-radius: 4px !important; box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; border: 1px solid #eee !important; margin: 0 !important; padding: 0 !important; max-height: 400px !important; overflow-y: auto !important; display: none; }\n";
                
                echo "}\n";
            }
            ?>
        </style>

        <div class="mh-live-search-wrapper <?php echo esc_attr($wrapper_classes); ?>">
            
            <button class="mh-search-trigger" aria-label="<?php esc_attr_e('Open Search', 'mh-plug'); ?>">
                <?php Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?>
            </button>
            
            <div class="mh-search-expandable-container">
                <form role="search" method="get" class="mh-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="hidden" name="post_type" value="product">
                    
                    <span class="mh-search-icon">
                        <?php Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?>
                    </span>
                    
                    <input 
                        type="search" 
                        name="s"
                        class="mh-search-input" 
                        placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>" 
                        autocomplete="off"
                    >
                    
                    <span class="mh-search-spinner"><i class="fas fa-spinner fa-spin"></i></span>
                </form>
                
                <div class="mh-search-results"></div>
            </div>
            
        </div>

        <script>
            jQuery(document).ready(function($) {
                var ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
                var searchTimer;
                var notFoundText = '<?php echo $not_found; ?>';

                $('.mh-search-trigger, .mh-search-icon, .mh-search-input').on('click focus', function(e) {
                    var $wrapper = $(this).closest('.mh-live-search-wrapper');
                    
                    if (!$wrapper.hasClass('mh-search-is-open')) {
                        e.preventDefault();
                        e.stopPropagation();
                        $wrapper.addClass('mh-search-is-open');
                        
                        setTimeout(function() { $wrapper.find('.mh-search-input').focus(); }, 150);
                    }
                });

                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.mh-live-search-wrapper').length) {
                        $('.mh-live-search-wrapper').removeClass('mh-search-is-open');
                        $('.mh-search-results').hide(); 
                    }
                });

                $('.mh-search-expandable-container').on('click', function(e) {
                    e.stopPropagation();
                });

                $('.mh-search-input').on('keyup', function() {
                    var keyword = $(this).val().trim();
                    var $wrapper = $(this).closest('.mh-live-search-wrapper');
                    var $results = $wrapper.find('.mh-search-results');
                    var $spinner = $wrapper.find('.mh-search-spinner');

                    clearTimeout(searchTimer);

                    if (keyword.length < 3) {
                        $results.hide().empty();
                        $spinner.hide();
                        return;
                    }

                    $spinner.show();

                    searchTimer = setTimeout(function() {
                        $.post(ajaxUrl, { action: 'mh_live_product_search', keyword: keyword }, function(response) {
                            $spinner.hide();
                            if (response.success && response.data !== '') {
                                $results.html(response.data).slideDown(200);
                            } else {
                                $results.html('<div style="padding: 15px; color: #888; text-align: center;">' + notFoundText + '</div>').slideDown(200);
                            }
                        });
                    }, 500); 
                });
            });
        </script>
        <?php
    }
}