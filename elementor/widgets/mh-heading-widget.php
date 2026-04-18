<?php
// Exit if this file is called directly to prevent security vulnerabilities.
if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;

/**
 * MH Advanced Heading Widget Class
 * Final Version with Repeating Tileable SVGs to prevent stretching!
 */
class MH_Heading_Widget extends Widget_Base {

    public function get_name() { return 'mh-heading'; }
    public function get_title() { return esc_html__('MH Heading', 'mh-plug'); }
    public function get_icon() { return 'eicon-t-letter'; }
    public function get_categories() { return ['mh-plug-widgets']; }
    public function get_keywords() { return ['heading', 'title', 'text', 'offset', 'z-index', 'wrap', 'underline']; }

    protected function register_controls() {

        /* ==========================================
         * CONTENT TAB: HEADING PARTS (REPEATER)
         * ========================================== */
        $this->start_controls_section(
            'section_heading_parts',
            [ 'label' => esc_html__('Heading Parts', 'mh-plug'), 'tab' => Controls_Manager::TAB_CONTENT ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'part_text',
            [
                'label'   => esc_html__('Text', 'mh-plug'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Heading', 'mh-plug'),
                'label_block' => true,
                'dynamic' => ['active' => true],
            ]
        );

        // --- REPEATER: STYLING (Applies to INNER WRAPPER) ---
        $repeater->add_control( 'part_styles_heading', [ 'label' => esc_html__('Styling & Colors', 'mh-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

        $repeater->add_control(
            'part_use_gradient',
            [ 'label' => esc_html__('Use Gradient Text?', 'mh-plug'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'no' ]
        );

        $repeater->add_control(
            'part_color',
            [
                'label'     => esc_html__('Text Color', 'mh-plug'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [ 'part_use_gradient' => '' ],
                'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .mh-heading-part-inner' => 'color: {{VALUE}};' ],
            ]
        );

        $repeater->add_control( 'part_grad_color_1', [ 'label' => esc_html__('Gradient Color 1', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'condition' => [ 'part_use_gradient' => 'yes' ] ] );
        $repeater->add_control( 'part_grad_color_2', [ 'label' => esc_html__('Gradient Color 2', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#8e44ad', 'condition' => [ 'part_use_gradient' => 'yes' ] ] );
        $repeater->add_control(
            'part_grad_angle',
            [
                'label' => esc_html__('Gradient Angle', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 360]], 'default' => ['size' => 90], 'condition' => [ 'part_use_gradient' => 'yes' ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .mh-heading-part-inner' => 'background: linear-gradient({{SIZE}}deg, {{part_grad_color_1.VALUE}} 0%, {{part_grad_color_2.VALUE}} 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;',
                ],
            ]
        );

        $repeater->add_control(
            'part_use_stroke',
            [ 'label' => esc_html__('Add Text Stroke (Outline)?', 'mh-plug'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'no', 'separator' => 'before' ]
        );
        $repeater->add_control(
            'part_stroke_width',
            [ 'label' => esc_html__('Stroke Width', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 1, 'max' => 10]], 'default' => ['size' => 1], 'condition' => [ 'part_use_stroke' => 'yes' ] ]
        );
        $repeater->add_control(
            'part_stroke_color',
            [
                'label' => esc_html__('Stroke Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#000000', 'condition' => [ 'part_use_stroke' => 'yes' ],
                'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .mh-heading-part-inner' => '-webkit-text-stroke: {{part_stroke_width.SIZE}}px {{VALUE}};' ],
            ]
        );

        $repeater->add_control( 'part_background_color', [ 'label' => esc_html__('Background Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .mh-heading-part-inner' => 'background-color: {{VALUE}};' ], 'separator' => 'before' ] );
        
        $repeater->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'part_typography', 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .mh-heading-part-inner' ] );
        $repeater->add_group_control( Group_Control_Text_Shadow::get_type(), [ 'name' => 'part_text_shadow', 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .mh-heading-part-inner' ] );


        // --- REPEATER: POSITIONING & OFFSETS (Applies to OUTER WRAPPER) ---
        $repeater->add_control( 'part_layout_heading', [ 'label' => esc_html__('Layout & Positioning', 'mh-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        
        $repeater->add_responsive_control(
            'part_y_offset',
            [
                'label' => esc_html__('Y Offset (Vertical Shift)', 'mh-plug'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => ['px' => ['min' => -200, 'max' => 200]],
                'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}{{UNIT}};' ],
            ]
        );

        $repeater->add_responsive_control(
            'part_x_offset',
            [
                'label' => esc_html__('X Offset (Horizontal Shift)', 'mh-plug'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => ['px' => ['min' => -200, 'max' => 200]],
                'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}{{UNIT}};' ],
            ]
        );

        $repeater->add_control(
            'part_z_index',
            [
                'label' => esc_html__('Z-Index (Overlap Order)', 'mh-plug'),
                'type' => Controls_Manager::NUMBER,
                'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}}' => 'z-index: {{VALUE}};' ],
            ]
        );

        $repeater->add_control(
            'part_display_mode',
            [
                'label' => esc_html__('Text Layout', 'mh-plug'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'inline-block' => esc_html__('Inline (Next to each other)', 'mh-plug'),
                    'block' => esc_html__('Block (Force New Line)', 'mh-plug'),
                    'vertical' => esc_html__('Vertical Text (Rotated)', 'mh-plug'),
                ],
                'default' => 'inline-block',
            ]
        );

        $repeater->add_responsive_control( 'part_margin', [ 'label' => esc_html__('Margin', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );


        // --- REPEATER: ANIMATION ---
        $repeater->add_control( 'part_animation_heading', [ 'label' => esc_html__('Animation', 'mh-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $repeater->add_control( 'part_typing_effect', [ 'label' => esc_html__('Typewriter Effect', 'mh-plug'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'no' ] );

        $this->add_control(
            'heading_parts',
            [
                'label'   => esc_html__('Text Parts', 'mh-plug'),
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [ ['part_text' => 'Advanced'], ['part_text' => 'Heading'] ],
                'title_field' => '{{{ part_text }}}',
            ]
        );
        
        $this->end_controls_section();

        /* ==========================================
         * CONTENT TAB: GENERAL SETTINGS
         * ========================================== */
        $this->start_controls_section( 'section_general_settings', [ 'label' => esc_html__('General Settings', 'mh-plug'), 'tab' => Controls_Manager::TAB_CONTENT ] );

        $this->add_responsive_control(
            'heading_alignment',
            [
                'label'   => esc_html__('Alignment', 'mh-plug'), 'type' => Controls_Manager::CHOOSE,
                'options' => [ 'left' => ['title' => 'Left', 'icon' => 'eicon-text-align-left'], 'center' => ['title' => 'Center', 'icon' => 'eicon-text-align-center'], 'right' => ['title' => 'Right', 'icon' => 'eicon-text-align-right'], ],
                'default'   => 'left',
                'selectors' => [ '{{WRAPPER}}' => 'text-align: {{VALUE}};' ],
            ]
        );
        
        $this->add_control( 'heading_html_tag', [ 'label' => esc_html__('HTML Tag', 'mh-plug'), 'type' => Controls_Manager::SELECT, 'options' => [ 'h1'=>'H1', 'h2'=>'H2', 'h3'=>'H3', 'h4'=>'H4', 'h5'=>'H5', 'h6'=>'H6', 'p'=>'P', 'div'=>'DIV' ], 'default' => 'h2' ] );

        $this->end_controls_section();

        /* ==========================================
         * STYLE TAB: UNDERLINE
         * ========================================== */
        $this->start_controls_section( 'section_underline_style', [ 'label' => esc_html__('Underline', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE ] );

        $this->add_control(
            'underline_apply_to',
            [
                'label' => esc_html__('Apply Underline To', 'mh-plug'), 'type' => Controls_Manager::SELECT,
                'options' => [ 
                    'each' => esc_html__('Every Part Individually (Wraps perfectly)', 'mh-plug'),
                    'all'  => esc_html__('Entire Heading Box (1 Continuous line)', 'mh-plug'), 
                    'last' => esc_html__('Last Part Only', 'mh-plug') 
                ],
                'default' => 'each',
            ]
        );

        $this->add_control(
            'underline_style',
            [
                'label' => esc_html__('Style', 'mh-plug'), 'type' => Controls_Manager::SELECT,
                'options' => [ 'none' => 'None', 'solid' => 'Solid', 'dotted' => 'Dotted', 'dashed' => 'Dashed', 'double' => 'Double', 'wavy' => 'Wavy (SVG)', 'jagged' => 'Jagged (SVG)' ],
                'default' => 'none',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'underline_background', 'label' => esc_html__( 'Solid Background Color', 'mh-plug' ), 'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .mh-underline-wrapper.mh-underline--solid::after, {{WRAPPER}} .mh-has-underline.mh-underline--solid::after',
                'condition' => [ 'underline_style' => 'solid' ],
            ]
        );

        $this->add_control( 'underline_simple_color', [ 'label' => esc_html__('Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'condition' => [ 'underline_style' => ['dotted', 'dashed', 'double', 'wavy', 'jagged'] ] ] );

        $this->add_responsive_control( 'underline_width', [ 'label' => esc_html__('Width', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => 0, 'max' => 200], 'px' => ['min' => 0, 'max' => 1000]], 'default' => ['unit' => '%', 'size' => 100], 'condition' => [ 'underline_style!' => 'none' ], 'selectors' => [ '{{WRAPPER}} .mh-underline-wrapper::after' => 'width: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-has-underline::after' => 'width: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'underline_height', [ 'label' => esc_html__('Height (Thickness)', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 50]], 'default' => ['unit' => 'px', 'size' => 3], 'condition' => [ 'underline_style!' => 'none' ], 'selectors' => [ '{{WRAPPER}} .mh-underline-wrapper::after' => '--underline-height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-has-underline' => 'text-decoration-thickness: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-has-underline::after' => '--underline-height: {{SIZE}}{{UNIT}};' ] ] );
        
        $this->add_control( 'underline_position', [ 'label' => esc_html__('Position', 'mh-plug'), 'type' => Controls_Manager::CHOOSE, 'options' => [ 'top' => [ 'title' => 'Top', 'icon' => 'eicon-v-align-top' ], 'bottom' => [ 'title' => 'Bottom', 'icon' => 'eicon-v-align-bottom' ] ], 'default' => 'bottom', 'toggle' => false, 'condition' => [ 'underline_style!' => 'none' ] ] );

        $this->add_responsive_control( 'underline_y_offset', [ 'label' => esc_html__('Y Offset', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => -100, 'max' => 100]], 'default' => ['unit' => 'px', 'size' => 0], 'condition' => [ 'underline_style' => ['solid', 'wavy', 'jagged'] ], 'selectors' => [ '{{WRAPPER}} .mh-underline-wrapper::after' => '--underline-offset: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-has-underline::after' => '--underline-offset: {{SIZE}}{{UNIT}};' ] ] );
        
        $this->add_responsive_control( 'underline_native_y_offset', [ 'label' => esc_html__('Y Offset', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => -50, 'max' => 50]], 'default' => ['unit' => 'px', 'size' => 0], 'condition' => [ 'underline_style' => ['dotted', 'dashed', 'double'] ], 'selectors' => [ '{{WRAPPER}} .mh-has-underline' => 'text-underline-offset: {{SIZE}}{{UNIT}};' ] ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $tag      = esc_attr($settings['heading_html_tag']);
        $apply_to = $settings['underline_apply_to'];
        $u_style  = $settings['underline_style'];

        $is_native_underline = in_array($u_style, ['dotted', 'dashed', 'double']);
        $is_pseudo_underline = in_array($u_style, ['solid', 'wavy', 'jagged']);

        $wrapper_classes = ['mh-advanced-heading-wrapper'];
        
        if ($apply_to === 'all') {
            $wrapper_classes[] = 'mh-underline-wrapper';
            if ($is_pseudo_underline) {
                $wrapper_classes[] = 'mh-underline--' . $u_style;
                $wrapper_classes[] = 'mh-underline-pos--' . $settings['underline_position'];
            }
            if ($is_native_underline) {
                $wrapper_classes[] = 'mh-has-underline';
            }
        }

        echo '<' . $tag . ' class="' . implode(' ', $wrapper_classes) . '">';

        $parts_count = count($settings['heading_parts']);

        foreach ($settings['heading_parts'] as $index => $item) {
            
            $outer_classes = ['elementor-repeater-item-' . esc_attr($item['_id']), 'mh-heading-part-outer'];
            $inner_classes = ['mh-heading-part-inner'];
            
            if ( isset($item['part_display_mode']) ) {
                if ($item['part_display_mode'] === 'block') {
                    $outer_classes[] = 'mh-layout-block';
                } elseif ($item['part_display_mode'] === 'vertical') {
                    $outer_classes[] = 'mh-layout-vertical';
                } else {
                    $outer_classes[] = 'mh-layout-inline';
                }
            } else {
                $outer_classes[] = 'mh-layout-inline';
            }

            if ($apply_to === 'each' || ($apply_to === 'last' && $index === ($parts_count - 1))) {
                if ($is_pseudo_underline) {
                    $inner_classes[] = 'mh-has-underline'; 
                    $inner_classes[] = 'mh-underline--' . $u_style;
                    $inner_classes[] = 'mh-underline-pos--' . $settings['underline_position'];
                }
                if ($is_native_underline) {
                    $inner_classes[] = 'mh-has-underline';
                }
            }

            if ( isset($item['part_typing_effect']) && $item['part_typing_effect'] === 'yes' ) {
                $inner_classes[] = 'mh-typing-effect';
            }

            echo '<span class="' . implode(' ', $outer_classes) . '">';
            echo '<span class="' . implode(' ', $inner_classes) . '">' . wp_kses_post($item['part_text']) . '</span>';
            echo '</span> ';
        }

        echo '</' . $tag . '>';

        ?>
        <style>
            .elementor-element-<?php echo $this->get_id(); ?> .mh-advanced-heading-wrapper {
                display: flex; flex-wrap: wrap; align-items: baseline; 
                justify-content: <?php echo esc_attr($settings['heading_alignment']); ?>;
                text-align: <?php echo esc_attr($settings['heading_alignment']); ?>;
            }
            
            .elementor-element-<?php echo $this->get_id(); ?> .mh-heading-part-outer { position: relative; }
            .elementor-element-<?php echo $this->get_id(); ?> .mh-layout-inline { display: inline-block; }
            .elementor-element-<?php echo $this->get_id(); ?> .mh-layout-block { display: block; flex-basis: 100%; width: 100%; }
            .elementor-element-<?php echo $this->get_id(); ?> .mh-layout-vertical { display: inline-block; writing-mode: vertical-rl; transform: rotate(180deg); }

            .elementor-element-<?php echo $this->get_id(); ?> .mh-heading-part-inner {
                position: relative; display: inline-block; line-height: 1; 
            }

            .elementor-element-<?php echo $this->get_id(); ?> .mh-underline-wrapper { position: relative; line-height: 1; width: 100%; }
            
            .elementor-element-<?php echo $this->get_id(); ?> .mh-typing-effect {
                display: inline-block; overflow: hidden; white-space: nowrap; border-right: 2px solid; width: 0;
                animation: mh-typing-<?php echo $this->get_id(); ?> 2s forwards steps(30, end), mh-blink-<?php echo $this->get_id(); ?> .75s step-end infinite;
            }
            @keyframes mh-typing-<?php echo $this->get_id(); ?> { from { width: 0; } to { width: 100%; } }
            @keyframes mh-blink-<?php echo $this->get_id(); ?> { from, to { border-color: transparent; } 50% { border-color: inherit; } }

            <?php if ($is_native_underline): ?>
                .elementor-element-<?php echo $this->get_id(); ?> .mh-has-underline {
                    text-decoration-line: <?php echo $settings['underline_position'] === 'top' ? 'overline' : 'underline'; ?>;
                    text-decoration-style: <?php echo esc_attr($u_style); ?>;
                    text-decoration-color: <?php echo esc_attr($settings['underline_simple_color']); ?>;
                }
            <?php endif; ?>

            <?php if ($is_pseudo_underline): ?>
                .elementor-element-<?php echo $this->get_id(); ?> .mh-underline-wrapper::after,
                .elementor-element-<?php echo $this->get_id(); ?> .mh-has-underline::after {
                    content: ''; position: absolute; left: 50%; transform: translateX(-50%);
                    height: var(--underline-height, 3px); z-index: -1;
                    /* 🚀 THE FIX: Tell the background to Repeat instead of Stretch */
                    background-repeat: repeat-x;
                }
                .elementor-element-<?php echo $this->get_id(); ?> .mh-underline-pos--bottom::after {
                    bottom: var(--underline-offset, -5px);
                }
                .elementor-element-<?php echo $this->get_id(); ?> .mh-underline-pos--top::after {
                    top: var(--underline-offset, -5px);
                }

                <?php 
                    $svg_color = !empty($settings['underline_simple_color']) ? $settings['underline_simple_color'] : '#000000';
                    $svg_color_safe = str_replace('#', '%23', $svg_color);

                    // 🚀 THE FIX: Updated the SVG code to be a perfectly repeating, tileable pattern!
                    if ($u_style === 'wavy') {
                        $svg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 10'%3E%3Cpath d='M0,5 Q5,0 10,5 T20,5' stroke='" . $svg_color_safe . "' stroke-width='2' fill='none'/%3E%3C/svg%3E";
                        echo '.elementor-element-' . $this->get_id() . ' .mh-underline--wavy::after { background-image: url("' . $svg . '"); background-size: 20px 100%; }';
                    }
                    if ($u_style === 'jagged') {
                         $svg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 10'%3E%3Cpolyline points='0,5 5,0 10,5 15,0 20,5' stroke='" . $svg_color_safe . "' stroke-width='2' fill='none'/%3E%3C/svg%3E";
                         echo '.elementor-element-' . $this->get_id() . ' .mh-underline--jagged::after { background-image: url("' . $svg . '"); background-size: 20px 100%; }';
                    }
                    if ($u_style === 'solid') {
                        echo '.elementor-element-' . $this->get_id() . ' .mh-underline--solid::after { background-size: cover; }';
                    }
                ?>
            <?php endif; ?>
        </style>
        <?php
    }
}