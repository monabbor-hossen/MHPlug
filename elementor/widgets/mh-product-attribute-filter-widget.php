<?php
/**
 * MH Product Attribute Filter Widget
 * Advanced WooCommerce sidebar filters with AJAX grid updates
 */
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

class MH_Product_Attribute_Filter_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'mh_product_attribute_filter'; }
    public function get_title() { return __( 'MH Product Attribute Filter', 'mh-plug' ); }
    public function get_icon() { return 'eicon-filter'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_script_depends() { return [ 'jquery-ui-slider' ]; }
    public function get_style_depends() { return []; }

    protected function register_controls() {

        // ── CONTENT: FILTER TOGGLES ──
        $this->start_controls_section( 'section_filter_settings', [
            'label' => __( 'Filter Sections', 'mh-plug' ),
        ]);

        $this->add_control('show_search', [ 'label' => __('Search', 'mh-plug'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ]);
        $this->add_control('show_categories', [ 'label' => __('Categories', 'mh-plug'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ]);
        $this->add_control('show_price', [ 'label' => __('Price Range', 'mh-plug'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ]);
        $this->add_control('show_brands', [ 'label' => __('Brands', 'mh-plug'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ]);
        $this->add_control('show_status', [ 'label' => __('Status (Sale / Featured)', 'mh-plug'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ]);
        $this->add_control('show_rating', [ 'label' => __('Rating', 'mh-plug'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ]);

        $attributes = [];
        if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
            foreach ( wc_get_attribute_taxonomies() as $tax ) {
                $attributes['pa_' . $tax->attribute_name] = $tax->attribute_label;
            }
        }
        $this->add_control('selected_attributes', [
            'label' => __('Attributes to Show', 'mh-plug'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $attributes,
            'default' => array_keys($attributes),
        ]);

        $this->add_control('target_grid_id', [
            'label' => __('Target Grid CSS ID', 'mh-plug'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'description' => __('Optional. CSS ID of the MH Product Grid to update. Leave empty to target the first grid on the page.', 'mh-plug'),
        ]);

        $this->add_control('show_reset', [ 'label' => __('Reset Button', 'mh-plug'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ]);

        $this->add_control('reset_text', [
            'label' => __('Button Text', 'mh-plug'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Reset All Filters', 'mh-plug'),
            'condition' => ['show_reset' => 'yes'],
        ]);

        $this->add_control('reset_icon', [
            'label' => __('Button Icon', 'mh-plug'),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-undo', 'library' => 'fa-solid'],
            'condition' => ['show_reset' => 'yes'],
        ]);

        $this->add_control('reset_icon_position', [
            'label' => __('Icon Position', 'mh-plug'),
            'type' => Controls_Manager::SELECT,
            'default' => 'before',
            'options' => [
                'before' => __('Before Text', 'mh-plug'),
                'after'  => __('After Text', 'mh-plug'),
            ],
            'condition' => ['show_reset' => 'yes'],
        ]);

        $this->end_controls_section();

        // ── STYLE: GENERAL ──
        $this->start_controls_section( 'section_style_general', [ 'label' => __( 'General', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ]);
        $this->add_control('widget_bg', [ 'label' => __('Background', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-widget' => 'background: {{VALUE}};'] ]);
        $this->add_responsive_control('widget_padding', [ 'label' => __('Padding', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px','em'], 'selectors' => ['{{WRAPPER}} .mhaf-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ]);
        $this->add_responsive_control('widget_radius', [ 'label' => __('Border Radius', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px','%'], 'selectors' => ['{{WRAPPER}} .mhaf-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ]);
        $this->add_group_control(Group_Control_Border::get_type(), [ 'name' => 'widget_border', 'selector' => '{{WRAPPER}} .mhaf-widget' ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [ 'name' => 'widget_shadow', 'selector' => '{{WRAPPER}} .mhaf-widget' ]);
        $this->add_responsive_control('section_spacing', [ 'label' => __('Section Spacing', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 60]], 'default' => ['size' => 20], 'selectors' => ['{{WRAPPER}} .mhaf-section' => 'margin-bottom: {{SIZE}}px; padding-bottom: {{SIZE}}px;'] ]);
        $this->add_control('divider_color', [ 'label' => __('Section Divider Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#eee', 'selectors' => ['{{WRAPPER}} .mhaf-section' => 'border-bottom-color: {{VALUE}};'] ]);
        $this->end_controls_section();

        // ── STYLE: HEADINGS ──
        $this->start_controls_section( 'section_style_headings', [ 'label' => __( 'Section Headings', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [ 'name' => 'heading_typo', 'selector' => '{{WRAPPER}} .mhaf-heading' ]);
        $this->add_control('heading_color', [ 'label' => __('Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-heading' => 'color: {{VALUE}};'] ]);
        $this->add_responsive_control('heading_margin', [ 'label' => __('Bottom Spacing', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 30]], 'selectors' => ['{{WRAPPER}} .mhaf-heading' => 'margin-bottom: {{SIZE}}px;'] ]);
        $this->end_controls_section();

        // ── STYLE: SEARCH INPUT ──
        $this->start_controls_section( 'section_style_search', [ 'label' => __( 'Search Input', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [ 'name' => 'search_typo', 'selector' => '{{WRAPPER}} .mhaf-search' ]);
        $this->add_control('search_color', [ 'label' => __('Text Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-search' => 'color: {{VALUE}};'] ]);
        $this->add_control('search_bg', [ 'label' => __('Background', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-search' => 'background: {{VALUE}};'] ]);
        $this->add_control('search_border_color', [ 'label' => __('Border Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-search' => 'border-color: {{VALUE}};'] ]);
        $this->add_control('search_focus_color', [ 'label' => __('Focus Border Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-search:focus' => 'border-color: {{VALUE}};'] ]);
        $this->add_responsive_control('search_padding', [ 'label' => __('Padding', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .mhaf-search' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ]);
        $this->add_responsive_control('search_radius', [ 'label' => __('Border Radius', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .mhaf-search' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ]);
        $this->end_controls_section();

        // ── STYLE: CHECKBOXES & LABELS ──
        $this->start_controls_section( 'section_style_checkbox', [ 'label' => __( 'Checkbox & Labels', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [ 'name' => 'label_typo', 'selector' => '{{WRAPPER}} .mhaf-label' ]);
        $this->add_control('label_color', [ 'label' => __('Label Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-label' => 'color: {{VALUE}};'] ]);
        $this->add_control('count_color', [ 'label' => __('Count Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-count' => 'color: {{VALUE}};'] ]);
        $this->add_control('checkbox_border_color', [ 'label' => __('Checkbox Border', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-mark' => 'border-color: {{VALUE}};'] ]);
        $this->add_control('accent_color', [ 'label' => __('Active/Checked Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#004265', 'selectors' => [
            '{{WRAPPER}} .mhaf-cb:checked + .mhaf-mark' => 'background: {{VALUE}}; border-color: {{VALUE}};',
            '{{WRAPPER}} .mhaf-range-track' => 'background: {{VALUE}};',
            '{{WRAPPER}} .mhaf-range-handle' => 'border-color: {{VALUE}};',
            '{{WRAPPER}} .mhaf-swatch.active' => 'outline-color: {{VALUE}}; border-color: {{VALUE}};',
            '{{WRAPPER}} .mhaf-tag.active' => 'background: {{VALUE}}; border-color: {{VALUE}};',
        ]]);
        $this->add_responsive_control('checkbox_size', [ 'label' => __('Checkbox Size', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 12, 'max' => 28]], 'selectors' => ['{{WRAPPER}} .mhaf-mark' => 'width: {{SIZE}}px; height: {{SIZE}}px;'] ]);
        $this->add_responsive_control('item_spacing', [ 'label' => __('Item Spacing', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 20]], 'selectors' => ['{{WRAPPER}} .mhaf-item' => 'margin-bottom: {{SIZE}}px;'] ]);
        $this->end_controls_section();

        // ── STYLE: PRICE RANGE ──
        $this->start_controls_section( 'section_style_price', [ 'label' => __( 'Price Range', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ]);
        $this->add_control('price_track_bg', [ 'label' => __('Track Background', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#e0e0e0', 'selectors' => ['{{WRAPPER}} .mhaf-range-bar' => 'background: {{VALUE}};'] ]);
        $this->add_control('price_track_active', [ 'label' => __('Active Track Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-range-track' => 'background: {{VALUE}};'] ]);
        $this->add_control('price_handle_bg', [ 'label' => __('Handle Background', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-range-handle' => 'background: {{VALUE}};'] ]);
        $this->add_responsive_control('price_handle_size', [ 'label' => __('Handle Size', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 12, 'max' => 28]], 'selectors' => ['{{WRAPPER}} .mhaf-range-handle' => 'width: {{SIZE}}px; height: {{SIZE}}px;'] ]);
        $this->add_responsive_control('price_track_height', [ 'label' => __('Track Height', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 2, 'max' => 12]], 'selectors' => ['{{WRAPPER}} .mhaf-range-bar' => 'height: {{SIZE}}px;'] ]);
        $this->add_control('price_input_border', [ 'label' => __('Input Border Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-price-inputs input' => 'border-color: {{VALUE}};'] ]);
        $this->end_controls_section();

        // ── STYLE: ATTRIBUTE SWATCHES ──
        $this->start_controls_section( 'section_style_swatches', [ 'label' => __( 'Attribute Swatches', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ]);
        $this->add_control('swatch_bg', [ 'label' => __('Background', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-swatch:not(.color-type)' => 'background: {{VALUE}};'] ]);
        $this->add_control('swatch_color', [ 'label' => __('Text Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-swatch:not(.color-type)' => 'color: {{VALUE}};'] ]);
        $this->add_control('swatch_border_color', [ 'label' => __('Border Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-swatch' => 'border-color: {{VALUE}};'] ]);
        $this->add_control('swatch_active_bg', [ 'label' => __('Active Background', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-swatch.active:not(.color-type)' => 'background: {{VALUE}};'] ]);
        $this->add_responsive_control('swatch_padding', [ 'label' => __('Padding', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .mhaf-swatch:not(.color-type)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ]);
        $this->add_responsive_control('swatch_radius', [ 'label' => __('Border Radius', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px','%'], 'selectors' => ['{{WRAPPER}} .mhaf-swatch:not(.color-type)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ]);
        $this->add_responsive_control('color_swatch_size', [ 'label' => __('Color Swatch Size', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 18, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .mhaf-swatch.color-type' => 'width: {{SIZE}}px; height: {{SIZE}}px;'] ]);
        $this->add_responsive_control('swatch_gap', [ 'label' => __('Gap', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 2, 'max' => 20]], 'selectors' => ['{{WRAPPER}} .mhaf-swatches' => 'gap: {{SIZE}}px;'] ]);
        $this->end_controls_section();

        // ── STYLE: STATUS TAGS ──
        $this->start_controls_section( 'section_style_tags', [ 'label' => __( 'Status Tags', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [ 'name' => 'tag_typo', 'selector' => '{{WRAPPER}} .mhaf-tag' ]);
        $this->add_control('tag_color', [ 'label' => __('Text Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-tag' => 'color: {{VALUE}};'] ]);
        $this->add_control('tag_bg', [ 'label' => __('Background', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-tag' => 'background: {{VALUE}};'] ]);
        $this->add_control('tag_border_color', [ 'label' => __('Border Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-tag' => 'border-color: {{VALUE}};'] ]);
        $this->add_control('tag_active_color', [ 'label' => __('Active Text', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mhaf-tag.active' => 'color: {{VALUE}};'] ]);
        $this->add_responsive_control('tag_padding', [ 'label' => __('Padding', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .mhaf-tag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ]);
        $this->add_responsive_control('tag_radius', [ 'label' => __('Border Radius', 'mh-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .mhaf-tag' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ]);
        $this->end_controls_section();

        // ── STYLE: RATING STARS ──
        $this->start_controls_section( 'section_style_stars', [ 'label' => __( 'Rating Stars', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ]);
        $this->add_control('star_color', [ 'label' => __('Star Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#ffb800', 'selectors' => ['{{WRAPPER}} .mhaf-stars' => 'color: {{VALUE}};'] ]);
        $this->add_control('star_empty_color', [ 'label' => __('Empty Star Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#ddd', 'selectors' => ['{{WRAPPER}} .mhaf-stars .empty' => 'color: {{VALUE}};'] ]);
        $this->add_responsive_control('star_size', [ 'label' => __('Star Size', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 10, 'max' => 24]], 'selectors' => ['{{WRAPPER}} .mhaf-stars' => 'font-size: {{SIZE}}px;'] ]);
        $this->end_controls_section();

        // ── STYLE: RESET BUTTON ──
        $this->start_controls_section( 'section_style_reset', [
            'label' => __( 'Reset Button', 'mh-plug' ),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => ['show_reset' => 'yes'],
        ]);

        $this->add_responsive_control('reset_width', [
            'label' => __('Button Width', 'mh-plug'),
            'type' => Controls_Manager::SELECT,
            'default' => 'full',
            'options' => [
                'auto' => __('Auto (Fit Content)', 'mh-plug'),
                'full' => __('Full Width', 'mh-plug'),
            ],
            'selectors_dictionary' => [
                'auto' => 'width: auto;',
                'full' => 'width: 100%;',
            ],
            'selectors' => ['{{WRAPPER}} .mhaf-reset' => '{{VALUE}}'],
        ]);

        $this->add_responsive_control('reset_alignment', [
            'label' => __('Alignment', 'mh-plug'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => ['title' => __('Left', 'mh-plug'), 'icon' => 'eicon-text-align-left'],
                'center'     => ['title' => __('Center', 'mh-plug'), 'icon' => 'eicon-text-align-center'],
                'flex-end'   => ['title' => __('Right', 'mh-plug'), 'icon' => 'eicon-text-align-right'],
            ],
            'default' => 'center',
            'selectors' => ['{{WRAPPER}} .mhaf-reset-wrap' => 'display:flex; justify-content:{{VALUE}};'],
            'condition' => ['reset_width' => 'auto'],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'reset_typo',
            'selector' => '{{WRAPPER}} .mhaf-reset',
        ]);

        $this->add_responsive_control('reset_padding', [
            'label' => __('Padding', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em'],
            'default' => ['top'=>12,'right'=>24,'bottom'=>12,'left'=>24,'unit'=>'px','isLinked'=>false],
            'selectors' => ['{{WRAPPER}} .mhaf-reset' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->add_responsive_control('reset_margin', [
            'label' => __('Margin', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em'],
            'default' => ['top'=>10,'right'=>0,'bottom'=>0,'left'=>0,'unit'=>'px','isLinked'=>false],
            'selectors' => ['{{WRAPPER}} .mhaf-reset-wrap' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->add_responsive_control('reset_radius', [
            'label' => __('Border Radius', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%'],
            'default' => ['top'=>6,'right'=>6,'bottom'=>6,'left'=>6,'isLinked'=>true],
            'selectors' => ['{{WRAPPER}} .mhaf-reset' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        // ─ Normal / Hover Tabs ─
        $this->start_controls_tabs('tabs_reset_style');

        $this->start_controls_tab('tab_reset_normal', ['label' => __('Normal','mh-plug')]);
        $this->add_control('reset_color', ['label'=>__('Text Color','mh-plug'),'type'=>Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>['{{WRAPPER}} .mhaf-reset'=>'color:{{VALUE}};']]);
        $this->add_control('reset_bg', ['label'=>__('Background','mh-plug'),'type'=>Controls_Manager::COLOR,'default'=>'#e74c3c','selectors'=>['{{WRAPPER}} .mhaf-reset'=>'background-color:{{VALUE}};']]);
        $this->add_control('reset_icon_color', ['label'=>__('Icon Color','mh-plug'),'type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .mhaf-reset .mhaf-reset-icon'=>'color:{{VALUE}};fill:{{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name'=>'reset_border','selector'=>'{{WRAPPER}} .mhaf-reset']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name'=>'reset_shadow','selector'=>'{{WRAPPER}} .mhaf-reset']);
        $this->end_controls_tab();

        $this->start_controls_tab('tab_reset_hover', ['label' => __('Hover','mh-plug')]);
        $this->add_control('reset_hover_color', ['label'=>__('Text Color','mh-plug'),'type'=>Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>['{{WRAPPER}} .mhaf-reset:hover'=>'color:{{VALUE}};']]);
        $this->add_control('reset_hover_bg', ['label'=>__('Background','mh-plug'),'type'=>Controls_Manager::COLOR,'default'=>'#c0392b','selectors'=>['{{WRAPPER}} .mhaf-reset:hover'=>'background-color:{{VALUE}};']]);
        $this->add_control('reset_hover_icon_color', ['label'=>__('Icon Color','mh-plug'),'type'=>Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .mhaf-reset:hover .mhaf-reset-icon'=>'color:{{VALUE}};fill:{{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name'=>'reset_hover_border','selector'=>'{{WRAPPER}} .mhaf-reset:hover']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name'=>'reset_hover_shadow','selector'=>'{{WRAPPER}} .mhaf-reset:hover']);
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control('reset_divider_icon', ['type' => Controls_Manager::DIVIDER]);

        $this->add_responsive_control('reset_icon_size', [
            'label' => __('Icon Size', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px'=>['min'=>8,'max'=>40]],
            'default' => ['size'=>14],
            'selectors' => ['{{WRAPPER}} .mhaf-reset .mhaf-reset-icon'=>'font-size:{{SIZE}}px;'],
        ]);

        $this->add_responsive_control('reset_icon_spacing', [
            'label' => __('Icon Spacing', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px'=>['min'=>0,'max'=>30]],
            'default' => ['size'=>8],
            'selectors' => ['{{WRAPPER}} .mhaf-reset'=>'gap:{{SIZE}}px;'],
        ]);

        $this->add_control('reset_transition', [
            'label' => __('Hover Transition (s)', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px'=>['min'=>0.1,'max'=>1,'step'=>0.1]],
            'default' => ['size'=>0.3],
            'selectors' => ['{{WRAPPER}} .mhaf-reset'=>'transition:all {{SIZE}}s ease;'],
        ]);

        $this->end_controls_section();

        // ── STYLE: MOBILE FLOATING BUTTON ──
        $this->start_controls_section( 'section_style_mobile', [
            'label' => __( 'Mobile Floating Button', 'mh-plug' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('mobile_breakpoint', [
            'label' => __('Mobile Breakpoint (px)', 'mh-plug'),
            'type' => Controls_Manager::NUMBER,
            'default' => 767,
            'description' => __('Below this width, filter hides and floating button appears.', 'mh-plug'),
        ]);

        $this->add_control('fab_icon', [
            'label' => __('Button Icon', 'mh-plug'),
            'type' => Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-sliders-h', 'library' => 'fa-solid'],
        ]);

        $this->add_control('fab_text', [
            'label' => __('Button Text', 'mh-plug'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Filters', 'mh-plug'),
        ]);

        $this->start_controls_tabs('tabs_fab_style');

        $this->start_controls_tab('tab_fab_normal', ['label' => __('Normal', 'mh-plug')]);
        $this->add_control('fab_color', [
            'label' => __('Text/Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => ['{{WRAPPER}} .mhaf-fab' => 'color:{{VALUE}};'],
        ]);
        $this->add_control('fab_bg', [
            'label' => __('Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'default' => '#004265',
            'selectors' => ['{{WRAPPER}} .mhaf-fab' => 'background-color:{{VALUE}};'],
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab('tab_fab_hover', ['label' => __('Hover', 'mh-plug')]);
        $this->add_control('fab_hover_color', [
            'label' => __('Text/Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mhaf-fab:hover' => 'color:{{VALUE}};'],
        ]);
        $this->add_control('fab_hover_bg', [
            'label' => __('Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'default' => '#002d47',
            'selectors' => ['{{WRAPPER}} .mhaf-fab:hover' => 'background-color:{{VALUE}};'],
        ]);
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control('fab_size', [
            'label' => __('Button Size', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 40, 'max' => 80]],
            'default' => ['size' => 54],
            'selectors' => ['{{WRAPPER}} .mhaf-fab' => 'width:{{SIZE}}px; height:{{SIZE}}px;'],
        ]);

        $this->add_responsive_control('fab_icon_size', [
            'label' => __('Icon Size', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 14, 'max' => 36]],
            'default' => ['size' => 20],
            'selectors' => ['{{WRAPPER}} .mhaf-fab i' => 'font-size:{{SIZE}}px;'],
        ]);

        $this->add_responsive_control('fab_radius', [
            'label' => __('Border Radius', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%'],
            'default' => ['top'=>50,'right'=>50,'bottom'=>50,'left'=>50,'unit'=>'%','isLinked'=>true],
            'selectors' => ['{{WRAPPER}} .mhaf-fab' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'fab_shadow',
            'selector' => '{{WRAPPER}} .mhaf-fab',
            'fields_options' => ['box_shadow' => ['default' => ['horizontal' => 0, 'vertical' => 4, 'blur' => 16, 'spread' => 0, 'color' => 'rgba(0,0,0,0.25)']]],
        ]);

        $this->add_responsive_control('fab_bottom', [
            'label' => __('Bottom Position', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 100]],
            'default' => ['size' => 24],
            'selectors' => ['{{WRAPPER}} .mhaf-fab' => 'bottom:{{SIZE}}px;'],
        ]);

        $this->add_responsive_control('fab_right', [
            'label' => __('Right Position', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 10, 'max' => 100]],
            'default' => ['size' => 24],
            'selectors' => ['{{WRAPPER}} .mhaf-fab' => 'right:{{SIZE}}px;'],
        ]);

        $this->add_control('heading_sidebar_style', ['label' => __('Sidebar Panel', 'mh-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);

        $this->add_control('sidebar_bg', [
            'label' => __('Sidebar Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => ['{{WRAPPER}} .mhaf-sidebar-panel' => 'background-color:{{VALUE}};'],
        ]);

        $this->add_control('heading_sidebar_header_style', ['label' => __('Sidebar Header & Close', 'mh-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);

        $this->add_control('sidebar_header_bg', [
            'label' => __('Header Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'default' => '#004265',
            'selectors' => ['{{WRAPPER}} .mhaf-sidebar-header' => 'background-color:{{VALUE}};'],
        ]);

        $this->add_control('sidebar_header_color', [
            'label' => __('Header Title Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => ['{{WRAPPER}} .mhaf-sidebar-header' => 'color:{{VALUE}};'],
        ]);

        $this->start_controls_tabs('tabs_sidebar_close');

        $this->start_controls_tab('tab_close_normal', ['label' => __('Normal', 'mh-plug')]);
        $this->add_control('close_color', [
            'label' => __('Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mhaf-sidebar-close' => 'color:{{VALUE}};'],
        ]);
        $this->add_control('close_bg', [
            'label' => __('Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mhaf-sidebar-close' => 'background-color:{{VALUE}};'],
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab('tab_close_hover', ['label' => __('Hover', 'mh-plug')]);
        $this->add_control('close_hover_color', [
            'label' => __('Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mhaf-sidebar-close:hover' => 'color:{{VALUE}};'],
        ]);
        $this->add_control('close_hover_bg', [
            'label' => __('Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mhaf-sidebar-close:hover' => 'background-color:{{VALUE}};'],
        ]);
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control('close_size', [
            'label' => __('Close Icon Size', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 14, 'max' => 40]],
            'selectors' => ['{{WRAPPER}} .mhaf-sidebar-close' => 'font-size:{{SIZE}}px;'],
        ]);

        $this->add_responsive_control('close_radius', [
            'label' => __('Close Border Radius', 'mh-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%'],
            'selectors' => ['{{WRAPPER}} .mhaf-sidebar-close' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);

        $this->add_control('heading_sidebar_layout', ['label' => __('Sidebar Layout', 'mh-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);

        $this->add_responsive_control('sidebar_width', [
            'label' => __('Sidebar Width', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px','%','vw'],
            'range' => ['px' => ['min' => 250, 'max' => 500], '%' => ['min' => 50, 'max' => 100], 'vw' => ['min' => 50, 'max' => 100]],
            'default' => ['size' => 320, 'unit' => 'px'],
            'selectors' => ['{{WRAPPER}} .mhaf-sidebar-panel' => 'width:{{SIZE}}{{UNIT}};'],
        ]);

        $this->add_control('overlay_color', [
            'label' => __('Overlay Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'default' => 'rgba(0,0,0,0.5)',
            'selectors' => ['{{WRAPPER}} .mhaf-overlay' => 'background-color:{{VALUE}};'],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        if (!class_exists('WooCommerce')) return;
        $s = $this->get_settings_for_display();
        $target = !empty($s['target_grid_id']) ? esc_attr($s['target_grid_id']) : '';
        $wid = $this->get_id();

        // Price bounds
        global $wpdb;
        $min_p = floor((float)$wpdb->get_var("SELECT MIN(meta_value+0) FROM {$wpdb->postmeta} WHERE meta_key='_price' AND meta_value+0 > 0"));
        $max_p = ceil((float)$wpdb->get_var("SELECT MAX(meta_value+0) FROM {$wpdb->postmeta} WHERE meta_key='_price'"));
        if ($min_p >= $max_p) { $min_p = 0; $max_p = 1000; }

        // Currency
        $currency = get_woocommerce_currency_symbol();
        
        $css = "";
        ob_start();
        ?>
        .mhaf-widget{font-family:inherit}
        .mhaf-section{margin-bottom:20px;padding-bottom:18px;border-bottom:1px solid #eee}
        .mhaf-section:last-child{border:0;margin:0;padding:0}
        .mhaf-heading{font-size:15px;font-weight:700;margin:0 0 12px;color:#222}
        /* Search */
        .mhaf-search{width:100%;padding:9px 14px;border:1px solid #ddd;border-radius:6px;font-size:14px;outline:none;transition:.2s}
        .mhaf-search:focus{border-color:#004265}
        /* Checkbox Items */
        .mhaf-item{display:flex;align-items:center;gap:10px;margin-bottom:7px;cursor:pointer;font-size:14px}
        .mhaf-cb{display:none}
        .mhaf-mark{width:17px;height:17px;border:1.5px solid #bbb;border-radius:3px;display:flex;align-items:center;justify-content:center;transition:.15s;flex-shrink:0}
        .mhaf-cb:checked+.mhaf-mark{background:#004265;border-color:#004265}
        .mhaf-cb:checked+.mhaf-mark::after{content:'\2713';color:#fff;font-size:11px;line-height:1}
        .mhaf-label{flex:1;color:#555}
        .mhaf-count{font-size:12px;color:#aaa}
        /* Price Range */
        .mhaf-price-wrap{padding:8px 4px 0}
        .mhaf-range-bar{position:relative;height:6px;background:#e0e0e0;border-radius:3px;margin:18px 0}
        .mhaf-range-track{position:absolute;height:100%;background:#004265;border-radius:3px}
        .mhaf-range-handle{position:absolute;top:50%;width:18px;height:18px;background:#fff;border:2px solid #004265;border-radius:50%;transform:translate(-50%,-50%);cursor:pointer;z-index:2;touch-action:none}
        .mhaf-price-inputs{display:flex;gap:10px;align-items:center;margin-top:12px}
        .mhaf-price-inputs input{flex:1;padding:7px 10px;border:1px solid #ddd;border-radius:5px;text-align:center;font-size:13px}
        .mhaf-price-inputs span{color:#999}
        /* Swatches */
        .mhaf-swatches{display:flex;flex-wrap:wrap;gap:8px}
        .mhaf-swatch{padding:5px 14px;border:1px solid #ddd;border-radius:5px;font-size:13px;cursor:pointer;transition:.15s;background:#fff;user-select:none}
        .mhaf-swatch.active{border-color:#004265;background:#f0f7ff}
        .mhaf-swatch.color-type{width:30px;height:30px;padding:0;border-radius:50%;outline:2px solid transparent;outline-offset:2px}
        .mhaf-swatch.color-type.active{outline-color:#004265}
        /* Tags */
        .mhaf-tags{display:flex;flex-wrap:wrap;gap:8px}
        .mhaf-tag{padding:6px 16px;border:1px solid #ddd;border-radius:20px;font-size:13px;cursor:pointer;transition:.15s;background:#fff;user-select:none}
        .mhaf-tag.active{background:#004265;color:#fff;border-color:#004265}
        /* Stars */
        .mhaf-stars{color:#ffb800;font-size:13px;display:flex;align-items:center;gap:2px}
        .mhaf-stars .empty{color:#ddd}
        /* Reset Button */
        .mhaf-reset-wrap{margin-top:10px}
        .mhaf-reset{display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px 24px;border:none;border-radius:6px;background:#e74c3c;color:#fff;font-size:14px;font-weight:600;cursor:pointer;transition:all .3s ease;outline:none;line-height:1.4}
        .mhaf-reset:hover{background:#c0392b;transform:translateY(-1px)}
        .mhaf-reset:active{transform:translateY(0)}
        .mhaf-reset-icon{display:inline-flex;align-items:center;line-height:1}
        /* ── Mobile Floating Button & Sidebar ── */
        .mhaf-fab{display:none;position:fixed;bottom:24px;right:24px;width:54px;height:54px;border-radius:50%;border:none;background:#004265;color:#fff;font-size:20px;cursor:pointer;z-index:99999;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,0.25);transition:all .3s ease;outline:none}
        .mhaf-fab:hover{transform:scale(1.08)}
        .mhaf-fab i{line-height:1}
        .mhaf-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999990;opacity:0;transition:opacity .3s ease}
        .mhaf-overlay.mhaf-open{display:block;opacity:1}
        .mhaf-sidebar-panel{position:fixed;top:0;right:0;height:100%;width:320px;max-width:90vw;background:#fff;z-index:999991;transform:translateX(100%);transition:transform .35s cubic-bezier(.4,0,.2,1);overflow-y:auto;display:flex;flex-direction:column}
        .mhaf-sidebar-panel.mhaf-open{transform:translateX(0)}
        .mhaf-sidebar-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#004265;color:#fff;flex-shrink:0}
        .mhaf-sidebar-title{font-size:17px;font-weight:700;margin:0}
        .mhaf-sidebar-close{background:none;border:none;color:inherit;font-size:22px;cursor:pointer;padding:4px;line-height:1;opacity:.85;transition:opacity .2s}
        .mhaf-sidebar-close:hover{opacity:1}
        .mhaf-sidebar-body{padding:20px;flex:1;overflow-y:auto}
        .mhaf-sidebar-body .mhaf-widget{background:none !important;border:none !important;box-shadow:none !important;padding:0 !important;border-radius:0 !important}
        @media(max-width:767px){
            .mhaf-desktop-filter:not(.mhaf-sidebar-clone){display:none !important}
            .mhaf-fab{display:flex !important}
        }

        <?php
            $css .= ob_get_clean();
            $bp = !empty($s['mobile_breakpoint']) ? intval($s['mobile_breakpoint']) : 767;
            $fab_icon = !empty($s['fab_icon']['value']) ? $s['fab_icon']['value'] : 'fas fa-sliders-h';
            $fab_text = !empty($s['fab_text']) ? $s['fab_text'] : '';
        ?>
        <?php
        $css .= '@media(max-width:' . $bp . 'px){.mhaf-desktop-filter-' . $wid . ':not(.mhaf-sidebar-clone){display:none !important}.mhaf-fab-' . $wid . '{display:flex !important}}';
        
        wp_register_style( 'mh-product-filter-style', false );
        wp_enqueue_style( 'mh-product-filter-style' );
        wp_add_inline_style( 'mh-product-filter-style', $css );
        ?>

        <!-- Floating Button (mobile only) -->
        <button type="button" class="mhaf-fab mhaf-fab-<?php echo $wid; ?>" id="mhaf-fab-<?php echo $wid; ?>" aria-label="<?php esc_attr_e('Open Filters', 'mh-plug'); ?>">
            <i class="<?php echo esc_attr($fab_icon); ?>"></i>
        </button>

        <!-- Sidebar Overlay -->
        <div class="mhaf-overlay" id="mhaf-overlay-<?php echo $wid; ?>"></div>
        <div class="mhaf-sidebar-panel" id="mhaf-sidebar-<?php echo $wid; ?>">
            <div class="mhaf-sidebar-header">
                <span class="mhaf-sidebar-title"><?php esc_html_e('Filters', 'mh-plug'); ?></span>
                <button type="button" class="mhaf-sidebar-close" id="mhaf-close-<?php echo $wid; ?>" aria-label="<?php esc_attr_e('Close', 'mh-plug'); ?>">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mhaf-sidebar-body" id="mhaf-sidebar-body-<?php echo $wid; ?>"></div>
        </div>

        <!-- Desktop Filter (hidden on mobile) -->
        <div class="mhaf-widget mhaf-desktop-filter mhaf-desktop-filter-<?php echo $wid; ?>" id="mhaf-<?php echo $wid; ?>" data-target="<?php echo $target; ?>">

            <?php if ($s['show_search'] === 'yes'): ?>
            <div class="mhaf-section">
                <div class="mhaf-heading"><?php esc_html_e('Search', 'mh-plug'); ?></div>
                <input type="text" class="mhaf-search" data-filter="mh_search" placeholder="<?php esc_attr_e('Search products...', 'mh-plug'); ?>">
            </div>
            <?php endif; ?>

            <?php if ($s['show_categories'] === 'yes'):
                $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
                if (!empty($cats) && !is_wp_error($cats)): ?>
            <div class="mhaf-section">
                <div class="mhaf-heading"><?php esc_html_e('Category', 'mh-plug'); ?></div>
                <?php foreach($cats as $cat): ?>
                <label class="mhaf-item">
                    <input type="checkbox" class="mhaf-cb" data-filter="product_cat" value="<?php echo esc_attr($cat->slug); ?>">
                    <span class="mhaf-mark"></span>
                    <span class="mhaf-label"><?php echo esc_html($cat->name); ?></span>
                    <span class="mhaf-count">(<?php echo $cat->count; ?>)</span>
                </label>
                <?php endforeach; ?>
            </div>
            <?php endif; endif; ?>

            <?php if ($s['show_price'] === 'yes'): ?>
            <div class="mhaf-section">
                <div class="mhaf-heading"><?php esc_html_e('Price', 'mh-plug'); ?></div>
                <div class="mhaf-price-wrap">
                    <div class="mhaf-range-bar" data-min="<?php echo $min_p; ?>" data-max="<?php echo $max_p; ?>">
                        <div class="mhaf-range-track"></div>
                        <div class="mhaf-range-handle mhaf-handle-min" data-type="min"></div>
                        <div class="mhaf-range-handle mhaf-handle-max" data-type="max"></div>
                    </div>
                    <div class="mhaf-price-inputs">
                        <input type="number" class="mhaf-price-min" value="<?php echo $min_p; ?>" min="<?php echo $min_p; ?>" max="<?php echo $max_p; ?>">
                        <span>–</span>
                        <input type="number" class="mhaf-price-max" value="<?php echo $max_p; ?>" min="<?php echo $min_p; ?>" max="<?php echo $max_p; ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php
            // Dynamic Attributes (Color, Size, etc.)
            if (!empty($s['selected_attributes'])):
                foreach ($s['selected_attributes'] as $attr_tax):
                    $terms = get_terms(['taxonomy' => $attr_tax, 'hide_empty' => true]);
                    if (empty($terms) || is_wp_error($terms)) continue;
                    $is_color = (stripos($attr_tax, 'color') !== false || stripos($attr_tax, 'colour') !== false);
                    $filter_key = 'filter_' . str_replace('pa_', '', $attr_tax);
            ?>
            <div class="mhaf-section">
                <div class="mhaf-heading"><?php echo esc_html(wc_attribute_label($attr_tax)); ?></div>
                <div class="mhaf-swatches">
                    <?php foreach($terms as $term):
                        $color_val = get_term_meta($term->term_id, 'color', true);
                        if (!$color_val) $color_val = get_term_meta($term->term_id, 'product_attribute_color', true);
                        if ($is_color && !$color_val) $color_val = strtolower($term->name);
                    ?>
                    <span class="mhaf-swatch <?php echo $is_color ? 'color-type' : ''; ?>"
                          data-filter="<?php echo esc_attr($filter_key); ?>"
                          data-value="<?php echo esc_attr($term->slug); ?>"
                          <?php if ($is_color && $color_val): ?>style="background-color:<?php echo esc_attr($color_val); ?>"<?php endif; ?>
                          title="<?php echo esc_attr($term->name); ?>">
                        <?php if (!$is_color) echo esc_html($term->name); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; endif; ?>

            <?php if ($s['show_brands'] === 'yes'):
                // Try common brand taxonomies
                $brand_tax = null;
                foreach (['product_brand', 'pwb-brand', 'yith_product_brand', 'pa_brand'] as $bt) {
                    if (taxonomy_exists($bt)) { $brand_tax = $bt; break; }
                }
                if ($brand_tax):
                    $brands = get_terms(['taxonomy' => $brand_tax, 'hide_empty' => true]);
                    if (!empty($brands) && !is_wp_error($brands)): ?>
            <div class="mhaf-section">
                <div class="mhaf-heading"><?php esc_html_e('Brand', 'mh-plug'); ?></div>
                <?php foreach($brands as $brand): ?>
                <label class="mhaf-item">
                    <input type="checkbox" class="mhaf-cb" data-filter="mh_brand" value="<?php echo esc_attr($brand->slug); ?>">
                    <span class="mhaf-mark"></span>
                    <span class="mhaf-label"><?php echo esc_html($brand->name); ?></span>
                    <span class="mhaf-count">(<?php echo $brand->count; ?>)</span>
                </label>
                <?php endforeach; ?>
            </div>
            <?php endif; endif; endif; ?>

            <?php if ($s['show_status'] === 'yes'): ?>
            <div class="mhaf-section">
                <div class="mhaf-heading"><?php esc_html_e('Status', 'mh-plug'); ?></div>
                <div class="mhaf-tags">
                    <span class="mhaf-tag" data-filter="mh_status" data-value="featured"><?php esc_html_e('Featured', 'mh-plug'); ?></span>
                    <span class="mhaf-tag" data-filter="mh_status" data-value="onsale"><?php esc_html_e('On Sale', 'mh-plug'); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($s['show_rating'] === 'yes'): ?>
            <div class="mhaf-section">
                <div class="mhaf-heading"><?php esc_html_e('Rating', 'mh-plug'); ?></div>
                <?php for($i = 5; $i >= 1; $i--): ?>
                <label class="mhaf-item">
                    <input type="radio" name="mhaf_rating_<?php echo $wid; ?>" class="mhaf-cb" data-filter="mh_rating" value="<?php echo $i; ?>">
                    <span class="mhaf-mark"></span>
                    <span class="mhaf-stars">
                        <?php for($j=1;$j<=5;$j++) echo $j<=$i ? '<i class="fas fa-star"></i>' : '<i class="fas fa-star empty"></i>'; ?>
                        <span style="color:#999;margin-left:4px">&amp; up</span>
                    </span>
                </label>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <?php if ($s['show_reset'] === 'yes'):
                $reset_text = !empty($s['reset_text']) ? $s['reset_text'] : __('Reset All Filters', 'mh-plug');
                $icon_pos = !empty($s['reset_icon_position']) ? $s['reset_icon_position'] : 'before';
            ?>
            <div class="mhaf-reset-wrap">
                <button type="button" class="mhaf-reset">
                    <?php if ($icon_pos === 'before' && !empty($s['reset_icon']['value'])): ?>
                        <i class="mhaf-reset-icon <?php echo esc_attr($s['reset_icon']['value']); ?>"></i>
                    <?php endif; ?>
                    <span class="mhaf-reset-text"><?php echo esc_html($reset_text); ?></span>
                    <?php if ($icon_pos === 'after' && !empty($s['reset_icon']['value'])): ?>
                        <i class="mhaf-reset-icon <?php echo esc_attr($s['reset_icon']['value']); ?>"></i>
                    <?php endif; ?>
                </button>
            </div>
            <?php endif; ?>

        </div>

        <?php
        ob_start();
        ?>
        (function($){
            var wid = '<?php echo $wid; ?>';
            var $w = $('#mhaf-' + wid);
            var timer, debounce = 500;

            // ── MOBILE SIDEBAR ──
            var $fab = $('#mhaf-fab-' + wid);
            var $overlay = $('#mhaf-overlay-' + wid);
            var $sidebar = $('#mhaf-sidebar-' + wid);
            var $sidebarBody = $('#mhaf-sidebar-body-' + wid);
            var $closeBtn = $('#mhaf-close-' + wid);
            var sidebarReady = false;

            function openSidebar() {
                if (!sidebarReady) {
                    // Clone the desktop filter into the sidebar body
                    var $clone = $w.clone(true);
                    $clone.removeAttr('id').addClass('mhaf-sidebar-clone');
                    $sidebarBody.empty().append($clone);
                    // Rebind events on clone
                    bindFilterEvents($clone);
                    sidebarReady = true;
                }
                $overlay.addClass('mhaf-open');
                $sidebar.addClass('mhaf-open');
                $('body').css('overflow', 'hidden');
            }

            function closeSidebar() {
                $overlay.removeClass('mhaf-open');
                $sidebar.removeClass('mhaf-open');
                $('body').css('overflow', '');
                // Sync sidebar state back to desktop
                syncFilters($sidebar.find('.mhaf-widget'), $w);
                sidebarReady = false;
            }

            $fab.on('click', openSidebar);
            $overlay.on('click', closeSidebar);
            $closeBtn.on('click', closeSidebar);

            // Sync filter state from source to target
            function syncFilters($src, $dst) {
                // Checkboxes
                $src.find('.mhaf-cb').each(function(i) {
                    var $d = $dst.find('.mhaf-cb').eq(i);
                    if ($d.length) $d.prop('checked', $(this).prop('checked'));
                });
                // Swatches
                $src.find('.mhaf-swatch').each(function(i) {
                    var $d = $dst.find('.mhaf-swatch').eq(i);
                    if ($d.length) $d.toggleClass('active', $(this).hasClass('active'));
                });
                // Tags
                $src.find('.mhaf-tag').each(function(i) {
                    var $d = $dst.find('.mhaf-tag').eq(i);
                    if ($d.length) $d.toggleClass('active', $(this).hasClass('active'));
                });
                // Search
                var sv = $src.find('.mhaf-search').val() || '';
                $dst.find('.mhaf-search').val(sv);
            }

            // Bind filter events on a given widget root
            function bindFilterEvents($root) {
                $root.on('change', '.mhaf-cb', function(){ doFilterFrom($root); });
                $root.on('click', '.mhaf-swatch', function(){ $(this).toggleClass('active'); doFilterFrom($root); });
                $root.on('click', '.mhaf-tag', function(){ $(this).toggleClass('active'); doFilterFrom($root); });
                $root.on('input', '.mhaf-search', function(){ clearTimeout(timer); timer = setTimeout(function(){ doFilterFrom($root); }, debounce); });
                $root.on('click', '.mhaf-reset', function(){
                    $root.find('.mhaf-cb').prop('checked', false);
                    $root.find('.mhaf-swatch.active').removeClass('active');
                    $root.find('.mhaf-tag.active').removeClass('active');
                    $root.find('.mhaf-search').val('');
                    // Reset price range for clone
                    var $cBar = $root.find('.mhaf-range-bar');
                    if ($cBar.length) {
                        var cMinP = parseInt($cBar.data('min')), cMaxP = parseInt($cBar.data('max'));
                        $root.find('.mhaf-price-min').val(cMinP);
                        $root.find('.mhaf-price-max').val(cMaxP);
                    }
                    doFilterFrom($root);
                });
            }

            function doFilterFrom($root) {
                // Copy state to desktop widget for doFilter
                syncFilters($root, $w);
                if ($bar.length) {
                    var mVal = parseInt($root.find('.mhaf-price-min').val()) || minP;
                    var xVal = parseInt($root.find('.mhaf-price-max').val()) || maxP;
                    curMin = Math.max(minP, Math.min(mVal, maxP));
                    curMax = Math.max(minP, Math.min(xVal, maxP));
                    updateSliderUI();
                }
                doFilter();
            }

            // ── RANGE SLIDER ──
            var $bar = $w.find('.mhaf-range-bar');
            if ($bar.length) {
                var minP = parseInt($bar.data('min')), maxP = parseInt($bar.data('max'));
                var $track = $bar.find('.mhaf-range-track');
                var $hMin = $bar.find('.mhaf-handle-min'), $hMax = $bar.find('.mhaf-handle-max');
                var $iMin = $w.find('.mhaf-price-min'), $iMax = $w.find('.mhaf-price-max');
                var curMin = minP, curMax = maxP;

                function updateSliderUI() {
                    var range = maxP - minP || 1;
                    var lp = ((curMin - minP) / range) * 100;
                    var rp = ((curMax - minP) / range) * 100;
                    $hMin.css('left', lp + '%');
                    $hMax.css('left', rp + '%');
                    $track.css({ left: lp + '%', width: (rp - lp) + '%' });
                    $iMin.val(curMin);
                    $iMax.val(curMax);
                }

                function startDrag(e, $handle) {
                    e.preventDefault();
                    var barW = $bar.width(), barL = $bar.offset().left;
                    var isMin = $handle.data('type') === 'min';

                    function onMove(ev) {
                        var cx = (ev.touches ? ev.touches[0].clientX : ev.clientX);
                        var pct = Math.max(0, Math.min(1, (cx - barL) / barW));
                        var val = Math.round(minP + pct * (maxP - minP));
                        if (isMin) { curMin = Math.min(val, curMax - 1); }
                        else       { curMax = Math.max(val, curMin + 1); }
                        updateSliderUI();
                    }
                    function onUp() {
                        $(document).off('mousemove touchmove', onMove).off('mouseup touchend', onUp);
                        doFilter();
                    }
                    $(document).on('mousemove touchmove', onMove).on('mouseup touchend', onUp);
                }

                $hMin.on('mousedown touchstart', function(e){ startDrag(e, $(this)); });
                $hMax.on('mousedown touchstart', function(e){ startDrag(e, $(this)); });
                $iMin.on('change', function(){ curMin = Math.max(minP, Math.min(parseInt($(this).val())||minP, curMax-1)); updateSliderUI(); doFilter(); });
                $iMax.on('change', function(){ curMax = Math.min(maxP, Math.max(parseInt($(this).val())||maxP, curMin+1)); updateSliderUI(); doFilter(); });
                updateSliderUI();
            }

            // ── DESKTOP EVENT HANDLERS ──
            $w.on('change', '.mhaf-cb', function(){ doFilter(); });
            $w.on('click', '.mhaf-swatch', function(){ $(this).toggleClass('active'); doFilter(); });
            $w.on('click', '.mhaf-tag', function(){ $(this).toggleClass('active'); doFilter(); });
            $w.on('input', '.mhaf-search', function(){ clearTimeout(timer); timer = setTimeout(doFilter, debounce); });
            $w.on('click', '.mhaf-reset', function(){
                $w.find('.mhaf-cb').prop('checked', false);
                $w.find('.mhaf-swatch.active').removeClass('active');
                $w.find('.mhaf-tag.active').removeClass('active');
                $w.find('.mhaf-search').val('');
                if ($bar.length) { curMin = minP; curMax = maxP; updateSliderUI(); }
                doFilter();
            });

            // ── COLLECT + FETCH ──
            function doFilter() {
                var params = new URLSearchParams();
                var groups = {};

                // Checkboxes
                $w.find('.mhaf-cb:checked').each(function(){
                    var key = $(this).data('filter'), val = $(this).val();
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(val);
                });

                // Swatches
                $w.find('.mhaf-swatch.active').each(function(){
                    var key = $(this).data('filter'), val = $(this).data('value');
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(val);
                });

                // Tags
                $w.find('.mhaf-tag.active').each(function(){
                    var key = $(this).data('filter'), val = $(this).data('value');
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(val);
                });

                // Serialize groups
                for (var key in groups) {
                    groups[key].forEach(function(v){ params.append(key, v); });
                }

                // Price
                if ($bar.length) {
                    params.set('min_price', curMin);
                    params.set('max_price', curMax);
                }

                // Search
                var sq = $w.find('.mhaf-search').val();
                if (sq) params.set('mh_search', sq);

                var qs = params.toString();
                var targetId = $w.data('target');
                var $grid = targetId ? $('#' + targetId) : $('.mh-product-grid').first();
                if (!$grid.length) { console.warn('MH Filter: grid not found'); return; }

                var base = window.location.href.split('?')[0];
                var url = qs ? base + '?' + qs : base;
                window.history.replaceState({}, '', url);
                $grid.css({ opacity: 0.4, pointerEvents: 'none' });

                $.get(url, function(html){
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var sel = targetId ? '#' + targetId : '.mh-product-grid';
                    var el = doc.querySelector(sel);
                    if (el) {
                        $grid.html(el.innerHTML);
                    } else {
                        $grid.html('<p style="text-align:center;padding:30px;color:#999">No products found.</p>');
                    }
                    $grid.css({ opacity: 1, pointerEvents: 'auto' });
                }).fail(function(){
                    $grid.css({ opacity: 1, pointerEvents: 'auto' });
                    if (window.mhShowToast) window.mhShowToast('Filter failed. Please refresh.', 'error');
                });
            }
        })(jQuery);
        <?php
        $js = ob_get_clean();
        wp_add_inline_script( 'jquery-core', $js );
        ?>
        <?php
    }
}
