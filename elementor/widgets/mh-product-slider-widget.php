<?php
/**
 * MH Product Slider Widget
 * Inherits all powerful features of the Product Grid but displays them in a highly customizable slider.
 * Fixed: CSS Comma Selector bug breaking the Wishlist button size & added SVG fill colors.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 🚀 Require the modular Quick View Trait
require_once __DIR__ . '/../modules/quick-view/mh-quick-view-trait.php';

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

class MH_Product_Slider_Widget extends Widget_Base {

    use MH_Quick_View_Trait;

    public function get_name() { return 'mh_product_slider'; }
    public function get_title() { return __( 'MH Product Slider', 'mh-plug' ); }
    public function get_icon() { return 'eicon-slider-push'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    public function get_style_depends() { return [ 'mh-widgets-css' ]; }
    public function get_script_depends() { return [ 'mh-widgets-js', 'slick-js' ]; }

    protected function register_controls() {
        
        // ----------------------------------------------------
        // QUERY SETTINGS
        // ----------------------------------------------------
        $this->start_controls_section( 'section_query', [ 'label' => __( 'Query Settings', 'mh-plug' ), 'tab' => Controls_Manager::TAB_CONTENT ] );
        
        $this->add_control( 'query_type', [ 
            'label' => __( 'Filter By', 'mh-plug' ), 
            'type' => Controls_Manager::SELECT, 
            'default' => 'latest', 
            'options' => [ 
                'current_query' => __( 'Current Archive (Category/Tag/Brand)', 'mh-plug' ), 
                'latest' => __( 'Latest', 'mh-plug' ), 
                'best_sellers' => __( 'Best Sellers', 'mh-plug' ), 
                'top_rated' => __( 'Top Rated', 'mh-plug' ), 
                'sale' => __( 'On Sale', 'mh-plug' ), 
                'featured' => __( 'Featured', 'mh-plug' ), 
            ], 
        ] );
        
        $this->add_control( 'posts_per_page', [ 'label' => __( 'Total Products to Load', 'mh-plug' ), 'type' => Controls_Manager::NUMBER, 'default' => 8, 'min' => 1, 'max' => 50, ] );
        $this->end_controls_section();

        // ----------------------------------------------------
        // SLIDER SETTINGS
        // ----------------------------------------------------
        $this->start_controls_section( 'section_slider_settings', [ 'label' => __( 'Slider Settings', 'mh-plug' ), 'tab' => Controls_Manager::TAB_CONTENT ] );

        $this->add_responsive_control( 'slides_to_show', [
            'label'   => __( 'Slides to Show', 'mh-plug' ),
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 6,
            'default' => 4,
            'tablet_default' => 2,
            'mobile_default' => 1,
        ] );

        $this->add_responsive_control( 'slides_to_scroll', [
            'label'   => __( 'Slides to Scroll', 'mh-plug' ),
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 6,
            'default' => 1,
        ] );

        $this->add_control( 'autoplay', [ 'label' => __( 'Autoplay', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'separator' => 'before' ] );
        $this->add_control( 'autoplay_speed', [ 'label' => __( 'Autoplay Delay (ms)', 'mh-plug' ), 'type' => Controls_Manager::NUMBER, 'default' => 3000, 'condition' => [ 'autoplay' => 'yes' ] ] );
        $this->add_control( 'pause_on_hover', [ 'label' => __( 'Pause on Hover', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'condition' => [ 'autoplay' => 'yes' ] ] );
        $this->add_control( 'infinite', [ 'label' => __( 'Infinite Loop', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );
        $this->add_control( 'slider_speed', [ 'label' => __( 'Transition Speed (ms)', 'mh-plug' ), 'type' => Controls_Manager::NUMBER, 'default' => 500 ] );
        $this->add_control( 'show_arrows', [ 'label' => __( 'Show Arrows', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'separator' => 'before' ] );
        $this->add_control( 'show_dots', [ 'label' => __( 'Show Dots', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // CARD ELEMENTS
        // ----------------------------------------------------
        $this->start_controls_section( 'section_elements', [ 'label' => __( 'Card Elements', 'mh-plug' ), 'tab' => Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'show_category', [ 'label' => __( 'Show Category', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_rating', [ 'label' => __( 'Show Star Rating', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_badge', [ 'label' => __( 'Show Sale Badge', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_compare', [ 'label' => __( 'Show Compare Button', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->end_controls_section();

        $this->register_quick_view_controls();

        // ----------------------------------------------------
        // STYLE: SLIDER & CARD CONTAINER
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_card', [ 'label' => __( 'Slider Box & Card', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );
        
        $this->add_responsive_control( 'slide_gap', [ 
            'label' => __( 'Gap Between Slides', 'mh-plug' ), 
            'type' => Controls_Manager::SLIDER, 
            'default' => [ 'size' => 15 ], 
            'selectors' => [ 
                '{{WRAPPER}} .mh-product-slider .slick-slide' => 'margin: 0 {{SIZE}}px;',
                '{{WRAPPER}} .mh-product-slider .slick-list' => 'margin: 0 -{{SIZE}}px;'
            ], 
        ] );

        $this->add_control( 'card_bg', [ 'label' => __( 'Card Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-product-card' => 'background-color: {{VALUE}};' ], 'separator' => 'before' ] );
        $this->add_responsive_control( 'card_radius', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%' ], 'default' => [ 'top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10, 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .mh-product-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ], ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .mh-product-card' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .mh-product-card' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow_hover', 'label' => __( 'Hover Box Shadow', 'mh-plug' ), 'selector' => '{{WRAPPER}} .mh-product-card:hover' ] );
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: SLIDER NAVIGATION
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_nav', [ 'label' => __( 'Slider Navigation', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );

        $this->add_control( 'heading_arrows', [ 'label' => __( 'Arrows', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->start_controls_tabs( 'tabs_arrows_style', [ 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->start_controls_tab( 'tab_arrows_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'arrow_color', [ 'label' => __( 'Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => [ '{{WRAPPER}} .slick-arrow i' => 'color: {{VALUE}} !important;' ] ] );
        $this->add_control( 'arrow_bg', [ 'label' => __( 'Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .slick-arrow' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_arrows_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'arrow_color_hover', [ 'label' => __( 'Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .slick-arrow:hover i' => 'color: {{VALUE}} !important;' ] ] );
        $this->add_control( 'arrow_bg_hover', [ 'label' => __( 'Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ '{{WRAPPER}} .slick-arrow:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control( 'arrow_box_size', [ 'label' => __( 'Arrow Button Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 20, 'max' => 80 ] ], 'default' => [ 'size' => 40 ], 'selectors' => [ '{{WRAPPER}} .slick-arrow' => 'width: {{SIZE}}px; height: {{SIZE}}px;' ], 'condition' => [ 'show_arrows' => 'yes' ], 'separator' => 'before' ] );
        $this->add_responsive_control( 'arrow_icon_size', [ 'label' => __( 'Arrow Icon Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 10, 'max' => 50 ] ], 'default' => [ 'size' => 16 ], 'selectors' => [ '{{WRAPPER}} .slick-arrow i' => 'font-size: {{SIZE}}px !important;' ], 'condition' => [ 'show_arrows' => 'yes' ] ] );

        $this->add_control( 'heading_dots', [ 'label' => __( 'Dots', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [ 'show_dots' => 'yes' ] ] );
        $this->add_control( 'dot_color', [ 'label' => __( 'Dot Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#cccccc', 'selectors' => [ '{{WRAPPER}} .mh-product-slider' => '--mh-dot-color: {{VALUE}};' ], 'condition' => [ 'show_dots' => 'yes' ] ] );
        $this->add_control( 'dot_active_color', [ 'label' => __( 'Active Dot Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ '{{WRAPPER}} .mh-product-slider' => '--mh-dot-active-color: {{VALUE}};' ], 'condition' => [ 'show_dots' => 'yes' ] ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: IMAGE & SALE BADGE
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_image', [ 'label' => __( 'Image & Sale Badge', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'image_bg', [ 'label' => __( 'Image Wrapper Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#f7f7f7', 'selectors' => [ '{{WRAPPER}} .mh-product-image-wrap' => 'background-color: {{VALUE}};' ], ] );
        $this->add_responsive_control( 'image_padding', [ 'label' => __( 'Image Padding', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%' ], 'selectors' => [ '{{WRAPPER}} .mh-product-image-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ], ] );
        $this->add_control( 'heading_badge_style', [ 'label' => __( 'Sale Badge', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'badge_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => [ '{{WRAPPER}} .mh-badge' => 'background-color: {{VALUE}};' ] ] );
        $this->add_control( 'badge_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-badge' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'badge_typo', 'selector' => '{{WRAPPER}} .mh-badge' ] );
        $this->add_responsive_control( 'badge_padding', [ 'label' => __( 'Padding', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'selectors' => [ '{{WRAPPER}} .mh-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'badge_radius', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%' ], 'selectors' => [ '{{WRAPPER}} .mh-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: CONTENT AREA
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_content', [ 'label' => __( 'Content Area (Text)', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );
        $this->add_responsive_control( 'content_padding', [ 'label' => __( 'Content Padding', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em', '%' ], 'default' => [ 'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .mh-product-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ], ] );
        $this->add_responsive_control( 'content_align', [ 'label' => __( 'Alignment', 'mh-plug' ), 'type' => Controls_Manager::CHOOSE, 'options' => [ 'left' => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ], 'center' => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ], 'right' => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ] ], 'default' => 'left', 'selectors' => [ '{{WRAPPER}} .mh-product-info' => 'text-align: {{VALUE}};' ], ] );
        $this->add_control( 'heading_cat_style', [ 'label' => __( 'Category', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'cat_color', [ 'label' => __( 'Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#888888', 'selectors' => [ '{{WRAPPER}} .mh-product-cat, {{WRAPPER}} .mh-product-cat a' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'cat_hover_color', [ 'label' => __( 'Hover Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => [ '{{WRAPPER}} .mh-product-cat a:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'cat_typo', 'selector' => '{{WRAPPER}} .mh-product-cat' ] );
        $this->add_responsive_control( 'cat_margin', [ 'label' => __( 'Margin Bottom', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-product-cat' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_control( 'heading_title_style', [ 'label' => __( 'Title', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'title_color', [ 'label' => __( 'Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#111111', 'selectors' => [ '{{WRAPPER}} .mh-product-title a' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'title_hover_color', [ 'label' => __( 'Hover Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => [ '{{WRAPPER}} .mh-product-title a:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .mh-product-title' ] );
        $this->add_responsive_control( 'title_margin', [ 'label' => __( 'Margin Bottom', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-product-title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_control( 'heading_rating_style', [ 'label' => __( 'Rating Stars', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'star_color', [ 'label' => __( 'Star Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#f5b223', 'selectors' => [ '{{WRAPPER}} .mh-product-rating .star-rating' => 'color: {{VALUE}};' ] ] );
        $this->add_responsive_control( 'star_size', [ 'label' => __( 'Star Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-product-rating .star-rating' => 'font-size: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'rating_margin', [ 'label' => __( 'Margin Bottom', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-product-rating' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_control( 'heading_price_style', [ 'label' => __( 'Price / Date', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'price_color', [ 'label' => __( 'Regular/Sale Price Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => [ '{{WRAPPER}} .mh-product-price, {{WRAPPER}} .mh-post-date' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'old_price_color', [ 'label' => __( 'Old Price Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#aaaaaa', 'selectors' => [ '{{WRAPPER}} .mh-product-price del' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'price_typo', 'selector' => '{{WRAPPER}} .mh-product-price, {{WRAPPER}} .mh-post-date' ] );
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: GENERAL ACTION BUTTONS (Excluding Quick View)
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_buttons', [ 'label' => __( 'General Action Buttons', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );
        
        $btn_target = '{{WRAPPER}} .mh-compare-btn, {{WRAPPER}} .mh-advanced-wishlist-btn, {{WRAPPER}} .mh-action-btn[title="Read More"]';
        $btn_hover_target = '{{WRAPPER}} .mh-compare-btn:hover, {{WRAPPER}} .mh-advanced-wishlist-btn:hover, {{WRAPPER}} .mh-advanced-wishlist-btn.added, {{WRAPPER}} .mh-action-btn[title="Read More"]:hover';
        
        // 🚀 FIX: Apply the child selectors cleanly so they don't break string formatting!
        $icon_target = '{{WRAPPER}} .mh-compare-btn i, {{WRAPPER}} .mh-advanced-wishlist-btn i, {{WRAPPER}} .mh-action-btn[title="Read More"] i';
        $svg_target = '{{WRAPPER}} .mh-compare-btn svg, {{WRAPPER}} .mh-advanced-wishlist-btn svg, {{WRAPPER}} .mh-action-btn[title="Read More"] svg';

        $this->add_responsive_control( 'btn_width', [ 'label' => __( 'Button Width', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 40 ], 'selectors' => [ $btn_target => 'width: {{SIZE}}px !important; min-width: {{SIZE}}px !important;' ] ] );
        $this->add_responsive_control( 'btn_height', [ 'label' => __( 'Button Height', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 40 ], 'selectors' => [ $btn_target => 'height: {{SIZE}}px !important; min-height: {{SIZE}}px !important;' ] ] );
        
        // 🚀 FIX: Correctly mapped icon sizing
        $this->add_responsive_control( 'btn_icon_size', [ 
            'label' => __( 'Icon Size', 'mh-plug' ), 
            'type' => Controls_Manager::SLIDER, 
            'default' => [ 'size' => 16 ], 
            'selectors' => [ 
                $icon_target => 'font-size: {{SIZE}}px !important;', 
                $svg_target => 'width: {{SIZE}}px !important; height: {{SIZE}}px !important;' 
            ] 
        ] );
        
        $this->add_responsive_control( 'btn_gap', [ 'label' => __( 'Gap Between Buttons', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 8 ], 'selectors' => [ '{{WRAPPER}} .mh-product-actions' => 'gap: {{SIZE}}px;' ] ] );
        $this->add_responsive_control( 'btn_radius', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%' ], 'default' => [ 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'unit' => '%' ], 'selectors' => [ $btn_target => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;' ] ] );

        $this->start_controls_tabs( 'tabs_btn_style' );
        $this->start_controls_tab( 'tab_btn_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'btn_color', [ 'label' => __( 'Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => [ $btn_target => 'color: {{VALUE}} !important;' ] ] );
        $this->add_control( 'btn_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ $btn_target => 'background-color: {{VALUE}} !important;' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_border', 'selector' => $btn_target ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_shadow', 'selector' => $btn_target ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_btn_hover', [ 'label' => __( 'Hover & Active', 'mh-plug' ) ] );
        $this->add_control( 'btn_hover_color', [ 'label' => __( 'Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ $btn_hover_target => 'color: {{VALUE}} !important;' ] ] );
        $this->add_control( 'btn_hover_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => [ $btn_hover_target => 'background-color: {{VALUE}} !important;' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_hover_border', 'selector' => $btn_hover_target ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_hover_shadow', 'selector' => $btn_hover_target ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( class_exists( 'WooCommerce' ) ) {
            wp_enqueue_script( 'zoom' );
            wp_enqueue_script( 'flexslider' );
            wp_enqueue_script( 'photoswipe-ui-default' );
            wp_enqueue_script( 'wc-single-product' );
            wp_enqueue_style( 'photoswipe-default-skin' );
        }

        $post_type = 'product'; 
        $args = [ 
            'post_status'         => 'publish', 
            'ignore_sticky_posts' => 1, 
            'posts_per_page'      => $settings['posts_per_page'] 
        ];

        if ( $settings['query_type'] === 'current_query' ) {
            $queried_object = get_queried_object();
            if ( $queried_object instanceof \WP_Term ) {
                if ( $queried_object->taxonomy === 'category' || $queried_object->taxonomy === 'post_tag' ) {
                    $post_type = 'post';
                }
                $args['tax_query'] = [ [ 'taxonomy' => $queried_object->taxonomy, 'field' => 'term_id', 'terms' => $queried_object->term_id ] ];
            } elseif ( is_search() ) {
                $args['s'] = get_search_query();
            }
        } else {
            if ( $settings['query_type'] === 'best_sellers' ) {
                $args['meta_key'] = 'total_sales'; $args['orderby'] = 'meta_value_num';
            } elseif ( $settings['query_type'] === 'top_rated' ) {
                $args['meta_key'] = '_wc_average_rating'; $args['orderby'] = 'meta_value_num';
            } elseif ( $settings['query_type'] === 'sale' && class_exists('WooCommerce') ) {
                $product_ids_on_sale = wc_get_product_ids_on_sale(); 
                $args['post__in'] = !empty($product_ids_on_sale) ? $product_ids_on_sale : [0];
            } elseif ( $settings['query_type'] === 'featured' ) {
                $args['tax_query'] = [['taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'featured', 'operator' => 'IN']];
            } elseif ( $settings['query_type'] === 'latest' ) {
                $args['orderby'] = 'date'; $args['order'] = 'DESC';
            }
        }

        $args['post_type'] = $post_type;
        $loop = new \WP_Query( $args );
        
        if ( ! $loop->have_posts() ) { 
            echo '<p>' . esc_html__( 'No items found.', 'mh-plug' ) . '</p>'; 
            return; 
        }

        $slider_id = 'mh-prod-slider-' . $this->get_id();
        $slider_options = [
            'slidesToShow'   => $settings['slides_to_show']['size'] ?? 4,
            'slidesToScroll' => $settings['slides_to_scroll']['size'] ?? 1,
            'autoplay'       => $settings['autoplay'] === 'yes',
            'autoplaySpeed'  => $settings['autoplay_speed'] ? absint($settings['autoplay_speed']) : 3000,
            'pauseOnHover'   => $settings['pause_on_hover'] === 'yes',
            'infinite'       => $settings['infinite'] === 'yes',
            'speed'          => $settings['slider_speed'] ? absint($settings['slider_speed']) : 500,
            'arrows'         => $settings['show_arrows'] === 'yes',
            'dots'           => $settings['show_dots'] === 'yes',
            'prevArrow'      => '<button type="button" class="slick-prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>',
            'nextArrow'      => '<button type="button" class="slick-next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>',
            'responsive'     => [
                [ 'breakpoint' => 1025, 'settings' => [ 'slidesToShow' => $settings['slides_to_show_tablet']['size'] ?? 2, 'slidesToScroll' => 1 ] ],
                [ 'breakpoint' => 768,  'settings' => [ 'slidesToShow' => $settings['slides_to_show_mobile']['size'] ?? 1, 'slidesToScroll' => 1 ] ]
            ]
        ];
        ?>
        
        <style>
            .mh-product-slider-wrapper.mh-product-grid { display: block !important; width: 100%; position: relative; grid-template-columns: none !important; gap: 0 !important; }
            .mh-product-slider .slick-track { display: flex; }
            .mh-product-slider .slick-slide { height: auto; display: flex; outline: none; }
            .mh-product-slider .slick-slide > div { width: 100%; display: flex; }
            .mh-product-card { width: 100%; display: flex; flex-direction: column; }
            
            .mh-product-slider .slick-arrow { position: absolute; top: 50%; transform: translateY(-50%); z-index: 20; border-radius: 50%; border: none; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 0; outline: none; }
            .mh-product-slider .slick-arrow:before, .mh-product-slider .slick-arrow:after { content: none !important; display: none !important; }
            .mh-product-slider .slick-prev { left: -15px; }
            .mh-product-slider .slick-next { right: -15px; }
            .mh-product-slider .slick-arrow i { margin: 0; padding: 0; line-height: 1; }
            
            .mh-product-slider .slick-dots { display: flex !important; justify-content: center; list-style: none; padding: 0; margin: 20px 0 0 0; gap: 8px; position: relative; bottom: auto; }
            .mh-product-slider .slick-dots li { margin: 0; padding: 0; width: auto; height: auto; }
            .mh-product-slider .slick-dots button { font-size: 0; line-height: 0; display: block; width: 12px; height: 12px; padding: 0; cursor: pointer; color: transparent; border: 0; outline: none; background: transparent; }
            .mh-product-slider .slick-dots button:before { content: ''; display: block; width: 100%; height: 100%; border-radius: 50%; background-color: var(--mh-dot-color, #cccccc); transition: 0.3s; opacity: 1; position: static; }
            .mh-product-slider .slick-dots .slick-active button:before { background-color: var(--mh-dot-active-color, #2293e9); transform: scale(1.3); }

            /* Default fallback fill for SVG so it inherits the text color */
            .mh-advanced-wishlist-btn svg { fill: currentColor; }
        </style>

        <div class="mh-product-slider-wrapper mh-product-grid">
            <div id="<?php echo esc_attr( $slider_id ); ?>" class="mh-product-slider" data-slick='<?php echo wp_json_encode( $slider_options ); ?>'>
                <?php
                while ( $loop->have_posts() ) : $loop->the_post();
                    global $post;
                    $post_id = get_the_ID();
                    $is_product = ( get_post_type() === 'product' && class_exists( 'WooCommerce' ) && function_exists( 'wc_get_product' ) );
                    
                    if ( $is_product ) {
                        $product = wc_get_product( $post_id );
                        $in_wishlist = function_exists('mh_wishlist_has_product') ? mh_wishlist_has_product($post_id) : false;
                    }
                    ?>
                    <div class="mh-prod-slide">
                        <div class="mh-product-card">
                            <div class="mh-product-image-wrap">
                                <a href="<?php the_permalink(); ?>">
                                    <?php 
                                    if ( $is_product ) {
                                        echo $product->get_image('woocommerce_thumbnail');
                                    } else {
                                        echo get_the_post_thumbnail( $post_id, 'medium' );
                                    }
                                    ?>
                                </a>
                                
                                <?php if ( $is_product && $settings['show_badge'] === 'yes' && $product->is_on_sale() ) : ?>
                                    <div class="mh-product-badges"><span class="mh-badge"><?php esc_html_e('Sale', 'mh-plug'); ?></span></div>
                                <?php endif; ?>

                                <div class="mh-product-actions">
                                    <?php if ( $is_product ) : ?>
                                        
                                        <?php $this->render_quick_view_button( $post_id, $settings ); ?>

                                        <?php if ( $settings['show_compare'] === 'yes' ) : ?>
                                        <a href="#" class="mh-action-btn mh-compare-btn" 
                                           data-product-id="<?php echo esc_attr($post_id); ?>" 
                                           title="<?php esc_html_e( 'Compare', 'mh-plug' ); ?>"
                                           style="display:flex; align-items:center; justify-content:center;">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                        <?php endif; ?>

                                        <a href="#" class="mh-action-btn mh-advanced-wishlist-btn <?php echo $in_wishlist ? 'added' : ''; ?>" data-product-id="<?php echo esc_attr($post_id); ?>" data-behavior="toggle" title="<?php esc_html_e('Wishlist', 'mh-plug'); ?>" style="display:flex; align-items:center; justify-content:center;">
                                            <span class="mh-icon-normal" style="display:flex; align-items:center; justify-content:center;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"><path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 429.3l175.2-161.3c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/></svg></span>
                                            <span class="mh-icon-added" style="display:none; align-items:center; justify-content:center;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"><path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg></span>
                                        </a>
                                    <?php else : ?>
                                        <a href="<?php the_permalink(); ?>" class="mh-action-btn" title="<?php esc_html_e( 'Read More', 'mh-plug' ); ?>" style="display:flex; align-items:center; justify-content:center;">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mh-product-info">
                                <?php if ( $settings['show_category'] === 'yes' ) : ?>
                                    <div class="mh-product-cat">
                                        <?php 
                                        if ( $is_product ) {
                                            echo wc_get_product_category_list( $post_id, ', ' ); 
                                        } else {
                                            echo get_the_category_list( ', ', '', $post_id );
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                                
                                <h3 class="mh-product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                
                                <?php if ( $is_product ) : ?>
                                    <?php if ( $settings['show_rating'] === 'yes' ) : ?>
                                        <div class="mh-product-rating"><?php echo wc_get_rating_html( $product->get_average_rating() ); ?></div>
                                    <?php endif; ?>
                                    <div class="mh-product-price"><?php echo $product->get_price_html(); ?></div>
                                <?php else : ?>
                                    <div class="mh-post-date"><?php echo get_the_date(); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var initProductSlider = function() {
                var $slider = $('#<?php echo esc_js( $slider_id ); ?>');
                if ($slider.length && typeof $.fn.slick === 'function' && !$slider.hasClass('slick-initialized')) {
                    $slider.slick();
                }
            };
            initProductSlider();

            if (typeof elementorFrontend !== 'undefined') {
                elementorFrontend.hooks.addAction('frontend/element_ready/mh_product_slider.default', initProductSlider);
            }
        });
        </script>
        <?php
    }
}