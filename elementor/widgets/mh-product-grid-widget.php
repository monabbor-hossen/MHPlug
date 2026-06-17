<?php
/**
 * MH Product Grid Widget
 * Removed Add to Cart Button. Added powerful Quick View customization via Trait.
 * Fixed: Explicitly forced Hover colors onto nested SVGs and <i> icons.
 * Added: Responsive Number of Products & Price Margin Controls.
 * Fixed: Added URL Interceptor so the grid reacts to the MH Product Filter Widget.
 * Added: Load More button with full style, responsive, and animation controls.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 🚀 Require the modular Quick View Trait
require_once __DIR__ . '/../modules/quick-view/mh-quick-view-trait.php';


use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

class MH_Product_Grid_Widget extends \Elementor\Widget_Base {
    use MH_Quick_View_Trait;

    public function get_name()
    {
        return 'mh_product_grid';
    }
    public function get_title()
    {
        return __('MH Product Grid', 'mh-plug-ecommerce-builder-widgets');
    }
    public function get_icon()
    {
        return 'eicon-products';
    }
    public function get_categories()
    {
        return ['mh-plug-widgets'];
    }

    public function get_style_depends()
    {
        return ['mh-widgets-css'];
    }
    public function get_script_depends()
    {
        return ['mh-widgets-js'];
    }

    protected function register_controls()
    {

        // ----------------------------------------------------
        // LAYOUT
        // ----------------------------------------------------
        $this->start_controls_section('section_layout', ['label' => __('Layout', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('card_layout', [
            'label' => __('Card Layout', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::SELECT,
            'default' => 'classic',
            'options' => [
                'classic' => __('Classic (Hover Actions)', 'mh-plug-ecommerce-builder-widgets'),
                'shop_card' => __('Shop Card (Add to Cart)', 'mh-plug-ecommerce-builder-widgets'),
            ],
        ]);
        $this->end_controls_section();

        // ----------------------------------------------------
        // QUERY SETTINGS
        // ----------------------------------------------------
        $this->start_controls_section('section_query', ['label' => __('Query Settings', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_CONTENT]);

        $this->add_control('query_type', [
            'label' => __('Filter By', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::SELECT,
            'default' => 'latest',
            'options' => [
                'current_query' => __('Current Archive (Category/Tag/Brand)', 'mh-plug-ecommerce-builder-widgets'),
                'latest' => __('Latest', 'mh-plug-ecommerce-builder-widgets'),
                'best_sellers' => __('Best Sellers', 'mh-plug-ecommerce-builder-widgets'),
                'top_rated' => __('Top Rated', 'mh-plug-ecommerce-builder-widgets'),
                'sale' => __('On Sale', 'mh-plug-ecommerce-builder-widgets'),
                'featured' => __('Featured', 'mh-plug-ecommerce-builder-widgets'),
                'combo_offers' => __('Product Type: Combo Offers', 'mh-plug-ecommerce-builder-widgets'),
            ],
        ]);

        $this->add_control('exclude_combo', [
            'label' => __('Hide Combo Offers Here', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Hide', 'mh-plug-ecommerce-builder-widgets'),
            'label_off' => __('Show', 'mh-plug-ecommerce-builder-widgets'),
            'return_value' => 'yes',
            'default' => 'no',
            'description' => __('Turn this ON to automatically hide any Combo/Bundle products from this grid.', 'mh-plug-ecommerce-builder-widgets'),
            'condition' => [
                'query_type!' => 'combo_offers',
            ],
            'separator' => 'after',
        ]);

        $this->add_responsive_control('posts_per_page', [
            'label' => __('Number of Products', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::NUMBER,
            'default' => 8,
            'tablet_default' => 6,
            'mobile_default' => 4,
            'min' => 1,
            'max' => 50,
            'description' => __('You can set different product counts for Desktop, Tablet, and Mobile.', 'mh-plug-ecommerce-builder-widgets'),
        ]);

        // ── Load More Button ──────────────────────────────────────────────────
        $this->add_control('show_load_more', [
            'label'        => __('Show Load More Button', 'mh-plug-ecommerce-builder-widgets'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __('Show', 'mh-plug-ecommerce-builder-widgets'),
            'label_off'    => __('Hide', 'mh-plug-ecommerce-builder-widgets'),
            'return_value' => 'yes',
            'default'      => 'no',
            'separator'    => 'before',
        ]);
        $this->add_control('load_more_text', [
            'label'     => __('Button Text', 'mh-plug-ecommerce-builder-widgets'),
            'type'      => Controls_Manager::TEXT,
            'default'   => __('Load More', 'mh-plug-ecommerce-builder-widgets'),
            'condition' => ['show_load_more' => 'yes'],
        ]);
        $this->add_control('load_more_icon', [
            'label'     => __('Button Icon', 'mh-plug-ecommerce-builder-widgets'),
            'type'      => Controls_Manager::ICONS,
            'default'   => ['value' => 'fas fa-chevron-down', 'library' => 'fa-solid'],
            'condition' => ['show_load_more' => 'yes'],
        ]);
        $this->add_control('load_more_icon_position', [
            'label'   => __('Icon Position', 'mh-plug-ecommerce-builder-widgets'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'after',
            'options' => [
                'before' => __('Before Text', 'mh-plug-ecommerce-builder-widgets'),
                'after'  => __('After Text', 'mh-plug-ecommerce-builder-widgets'),
            ],
            'condition' => ['show_load_more' => 'yes'],
        ]);
        $this->add_control('load_more_posts', [
            'label'       => __('Products to Load Per Click', 'mh-plug-ecommerce-builder-widgets'),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 4,
            'min'         => 1,
            'max'         => 50,
            'description' => __('How many more products to reveal each time the button is clicked.', 'mh-plug-ecommerce-builder-widgets'),
            'condition'   => ['show_load_more' => 'yes'],
        ]);

        $this->add_responsive_control('columns', ['label' => __('Columns', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SELECT, 'default' => '4', 'tablet_default' => '2', 'mobile_default' => '1', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'], 'selectors' => ['{{WRAPPER}} .mh-product-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr); display: grid;'],]);
        $this->end_controls_section();

        // ----------------------------------------------------
        // CARD ELEMENTS
        // ----------------------------------------------------
        $this->start_controls_section('section_elements', ['label' => __('Card Elements', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('show_category', ['label' => __('Show Category', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_rating', ['label' => __('Show Star Rating', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_badge', ['label' => __('Show Sale Badge', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('show_compare', ['label' => __('Show Compare Button', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['card_layout' => 'classic']]);
        $this->add_control('show_add_to_cart', ['label' => __('Show Add to Cart', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['card_layout' => 'shop_card']]);
        $this->add_control('show_attributes', ['label' => __('Show Attribute Tags', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['card_layout' => 'shop_card']]);
        $this->add_control('attribute_taxonomy', [
            'label' => __('Attribute to Display', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'placeholder' => 'pa_flavor',
            'description' => __('Enter the attribute taxonomy slug (e.g. pa_flavor, pa_color). Leave empty to auto-detect.', 'mh-plug-ecommerce-builder-widgets'),
            'condition' => ['card_layout' => 'shop_card', 'show_attributes' => 'yes'],
        ]);
        $this->add_control('max_attributes', [
            'label' => __('Max Tags Shown', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::NUMBER,
            'default' => 3,
            'min' => 1,
            'max' => 10,
            'condition' => ['card_layout' => 'shop_card', 'show_attributes' => 'yes'],
        ]);
        $this->add_control('show_wishlist_btn', ['label' => __('Show Wishlist Icon', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['card_layout' => 'shop_card']]);
        $this->add_control('show_wishlist_text', [
            'label' => __('Show Wishlist Text', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'no',
            'condition' => ['card_layout' => 'shop_card', 'show_wishlist_btn' => 'yes']
        ]);
        $this->add_control('wishlist_text', [
            'label' => __('Wishlist Text', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Wishlist', 'mh-plug-ecommerce-builder-widgets'),
            'condition' => ['card_layout' => 'shop_card', 'show_wishlist_btn' => 'yes', 'show_wishlist_text' => 'yes']
        ]);
        $this->add_control('wishlist_added_text', [
            'label' => __('Added Text', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::TEXT,
            'default' => __('In Wishlist', 'mh-plug-ecommerce-builder-widgets'),
            'condition' => ['card_layout' => 'shop_card', 'show_wishlist_btn' => 'yes', 'show_wishlist_text' => 'yes']
        ]);
        $this->add_control('hide_text_when_active', [
            'label' => __('Hide Text When Active', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'no',
            'description' => __('If enabled, the text will disappear when the product is in the wishlist, showing only the icon.', 'mh-plug-ecommerce-builder-widgets'),
            'condition' => ['card_layout' => 'shop_card', 'show_wishlist_btn' => 'yes', 'show_wishlist_text' => 'yes']
        ]);
        $this->end_controls_section();

        $this->register_quick_view_controls();

        // ----------------------------------------------------
        // STYLE: GRID & CARD CONTAINER
        // ----------------------------------------------------
        $this->start_controls_section('section_style_card', ['label' => __('Grid & Card Container', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('grid_gap', ['label' => __('Grid Gap', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 20], 'selectors' => ['{{WRAPPER}} .mh-product-grid' => 'gap: {{SIZE}}px;'],]);
        $this->add_control('card_bg', ['label' => __('Card Background', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .mh-product-card' => 'background-color: {{VALUE}};'],]);
        $this->add_responsive_control('card_radius', ['label' => __('Border Radius', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'default' => ['top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10, 'isLinked' => true], 'selectors' => ['{{WRAPPER}} .mh-product-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'card_border', 'selector' => '{{WRAPPER}} .mh-product-card']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'card_shadow', 'selector' => '{{WRAPPER}} .mh-product-card']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'card_shadow_hover', 'label' => __('Hover Box Shadow', 'mh-plug-ecommerce-builder-widgets'), 'selector' => '{{WRAPPER}} .mh-product-card:hover']);
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: IMAGE & SALE BADGE
        // ----------------------------------------------------
        $this->start_controls_section('section_style_image', ['label' => __('Image & Sale Badge', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('image_bg', ['label' => __('Image Wrapper Background', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#f7f7f7', 'selectors' => ['{{WRAPPER}} .mh-product-image-wrap' => 'background-color: {{VALUE}};'],]);
        $this->add_responsive_control('image_padding', ['label' => __('Image Padding', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .mh-product-image-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],]);
        $this->add_control('heading_badge_style', ['label' => __('Sale Badge', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('badge_bg', ['label' => __('Background Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => ['{{WRAPPER}} .mh-badge' => 'background-color: {{VALUE}};']]);
        $this->add_control('badge_color', ['label' => __('Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .mh-badge' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'badge_typo', 'selector' => '{{WRAPPER}} .mh-badge']);
        $this->add_responsive_control('badge_padding', ['label' => __('Padding', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'selectors' => ['{{WRAPPER}} .mh-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('badge_radius', ['label' => __('Border Radius', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .mh-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: CONTENT AREA
        // ----------------------------------------------------
        $this->start_controls_section('section_style_content', ['label' => __('Content Area (Text)', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('content_padding', ['label' => __('Content Padding', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'default' => ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'isLinked' => true], 'selectors' => ['{{WRAPPER}} .mh-product-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],]);
        $this->add_responsive_control('content_align', ['label' => __('Alignment', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => 'Left', 'icon' => 'eicon-text-align-left'], 'center' => ['title' => 'Center', 'icon' => 'eicon-text-align-center'], 'right' => ['title' => 'Right', 'icon' => 'eicon-text-align-right']], 'default' => 'left', 'selectors' => ['{{WRAPPER}} .mh-product-info' => 'text-align: {{VALUE}};'],]);

        $this->add_control('heading_cat_style', ['label' => __('Category', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('cat_color', ['label' => __('Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#888888', 'selectors' => ['{{WRAPPER}} .mh-product-cat, {{WRAPPER}} .mh-product-cat a' => 'color: {{VALUE}};']]);
        $this->add_control('cat_hover_color', ['label' => __('Hover Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => ['{{WRAPPER}} .mh-product-cat a:hover' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'cat_typo', 'selector' => '{{WRAPPER}} .mh-product-cat']);
        $this->add_responsive_control('cat_margin', ['label' => __('Margin Bottom', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SLIDER, 'selectors' => ['{{WRAPPER}} .mh-product-cat' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);

        $this->add_control('heading_title_style', ['label' => __('Title', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('title_color', ['label' => __('Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#111111', 'selectors' => ['{{WRAPPER}} .mh-product-title a' => 'color: {{VALUE}};']]);
        $this->add_control('title_hover_color', ['label' => __('Hover Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => ['{{WRAPPER}} .mh-product-title a:hover' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'title_typo', 'selector' => '{{WRAPPER}} .mh-product-title']);
        $this->add_responsive_control('title_margin', ['label' => __('Margin Bottom', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SLIDER, 'selectors' => ['{{WRAPPER}} .mh-product-title' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);

        $this->add_control('heading_rating_style', ['label' => __('Rating Stars', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('star_color', ['label' => __('Star Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#f5b223', 'selectors' => ['{{WRAPPER}} .mh-product-rating .star-rating' => 'color: {{VALUE}};']]);
        $this->add_responsive_control('star_size', ['label' => __('Star Size', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SLIDER, 'selectors' => ['{{WRAPPER}} .mh-product-rating .star-rating' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('rating_margin', ['label' => __('Margin Bottom', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SLIDER, 'selectors' => ['{{WRAPPER}} .mh-product-rating' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);

        $this->add_control('heading_price_style', ['label' => __('Price / Date', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('price_color', ['label' => __('Regular/Sale Price Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => ['{{WRAPPER}} .mh-product-price, {{WRAPPER}} .mh-post-date' => 'color: {{VALUE}};']]);
        $this->add_control('old_price_color', ['label' => __('Old Price Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#aaaaaa', 'selectors' => ['{{WRAPPER}} .mh-product-price del' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'price_typo', 'selector' => '{{WRAPPER}} .mh-product-price, {{WRAPPER}} .mh-post-date']);
        $this->add_responsive_control('price_margin', ['label' => __('Margin', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .mh-product-price, {{WRAPPER}} .mh-post-date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: GENERAL ACTION BUTTONS 
        // ----------------------------------------------------
        $this->start_controls_section('section_style_buttons', ['label' => __('General Action Buttons', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_STYLE]);

        $btn_a = '{{WRAPPER}} .mh-compare-btn, {{WRAPPER}} .mh-wishlist-btn, {{WRAPPER}} .mh-action-btn[title="Read More"]';
        $btn_i = '{{WRAPPER}} .mh-compare-btn i, {{WRAPPER}} .mh-wishlist-btn i, {{WRAPPER}} .mh-action-btn[title="Read More"] i';
        $btn_svg = '{{WRAPPER}} .mh-compare-btn svg, {{WRAPPER}} .mh-wishlist-btn svg, {{WRAPPER}} .mh-action-btn[title="Read More"] svg';

        $btn_hover_a = '{{WRAPPER}} .mh-compare-btn:hover, {{WRAPPER}} .mh-wishlist-btn:hover, {{WRAPPER}} .mh-wishlist-btn.mh-added, {{WRAPPER}} .mh-action-btn[title="Read More"]:hover';
        $btn_hover_i = '{{WRAPPER}} .mh-compare-btn:hover i, {{WRAPPER}} .mh-wishlist-btn:hover i, {{WRAPPER}} .mh-wishlist-btn.mh-added i, {{WRAPPER}} .mh-action-btn[title="Read More"]:hover i';
        $btn_hover_svg = '{{WRAPPER}} .mh-compare-btn:hover svg, {{WRAPPER}} .mh-wishlist-btn:hover svg, {{WRAPPER}} .mh-wishlist-btn.mh-added svg, {{WRAPPER}} .mh-action-btn[title="Read More"]:hover svg';

        $this->add_responsive_control('btn_width', ['label' => __('Button Width', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 40], 'selectors' => [$btn_a => 'width: {{SIZE}}px !important; min-width: {{SIZE}}px !important;']]);
        $this->add_responsive_control('btn_height', ['label' => __('Button Height', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 40], 'selectors' => [$btn_a => 'height: {{SIZE}}px !important; min-height: {{SIZE}}px !important;']]);

        $this->add_responsive_control('btn_icon_size', [
            'label' => __('Icon Size', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 16],
            'selectors' => [
                $btn_i => 'font-size: {{SIZE}}px !important;',
                $btn_svg => 'width: {{SIZE}}px !important; height: {{SIZE}}px !important;'
            ]
        ]);

        $this->add_responsive_control('btn_gap', ['label' => __('Gap Between Buttons', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 8], 'selectors' => ['{{WRAPPER}} .mh-product-actions' => 'gap: {{SIZE}}px;']]);
        $this->add_responsive_control('btn_radius', ['label' => __('Border Radius', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'default' => ['top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'unit' => '%'], 'selectors' => [$btn_a => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);

        $this->start_controls_tabs('tabs_btn_style');

        $this->start_controls_tab('tab_btn_normal', ['label' => __('Normal', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control('btn_color', ['label' => __('Icon Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => [$btn_a => 'color: {{VALUE}} !important;', $btn_i => 'color: {{VALUE}} !important;', $btn_svg => 'fill: {{VALUE}} !important;']]);
        $this->add_control('btn_bg', ['label' => __('Background Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [$btn_a => 'background-color: {{VALUE}} !important;']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'btn_border', 'selector' => $btn_a]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'btn_shadow', 'selector' => $btn_a]);
        $this->end_controls_tab();

        $this->start_controls_tab('tab_btn_hover', ['label' => __('Hover & Active', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control('btn_hover_color', ['label' => __('Icon Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [$btn_hover_a => 'color: {{VALUE}} !important;', $btn_hover_i => 'color: {{VALUE}} !important;', $btn_hover_svg => 'fill: {{VALUE}} !important;']]);
        $this->add_control('btn_hover_bg', ['label' => __('Background Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => [$btn_hover_a => 'background-color: {{VALUE}} !important;']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'btn_hover_border', 'selector' => $btn_hover_a]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'btn_hover_shadow', 'selector' => $btn_hover_a]);
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: ADD TO CART BUTTON (Shop Card)
        // ----------------------------------------------------
        $this->start_controls_section('section_style_atc', ['label' => __('Add to Cart Button', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['card_layout' => 'shop_card']]);
        $this->start_controls_tabs('tabs_atc');
        $this->start_controls_tab('tab_atc_normal', ['label' => __('Normal', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control('atc_bg', ['label' => __('Background', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#c5a94e', 'selectors' => ['{{WRAPPER}} .mh-atc-btn' => 'background:{{VALUE}};']]);
        $this->add_control('atc_color', ['label' => __('Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .mh-atc-btn' => 'color:{{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_atc_hover', ['label' => __('Hover', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control('atc_bg_hover', ['label' => __('Background', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#b08d3e', 'selectors' => ['{{WRAPPER}} .mh-atc-btn:hover' => 'background:{{VALUE}};']]);
        $this->add_control('atc_color_hover', ['label' => __('Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .mh-atc-btn:hover' => 'color:{{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'atc_typo', 'selector' => '{{WRAPPER}} .mh-atc-btn', 'separator' => 'before']);
        $this->add_responsive_control('atc_radius', ['label' => __('Border Radius', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px'], 'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => true], 'selectors' => ['{{WRAPPER}} .mh-atc-btn' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->add_responsive_control('atc_padding', ['label' => __('Padding', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'selectors' => ['{{WRAPPER}} .mh-atc-btn' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: ATTRIBUTE TAGS (Shop Card)
        // ----------------------------------------------------
        $this->start_controls_section('section_style_attr', ['label' => __('Attribute Tags', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['card_layout' => 'shop_card', 'show_attributes' => 'yes']]);
        $this->add_control('attr_bg', ['label' => __('Background', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#f5f5f5', 'selectors' => ['{{WRAPPER}} .mh-attr-tag' => 'background:{{VALUE}};']]);
        $this->add_control('attr_color', ['label' => __('Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#333', 'selectors' => ['{{WRAPPER}} .mh-attr-tag' => 'color:{{VALUE}};']]);
        $this->add_control('attr_border_color', ['label' => __('Border Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#e0e0e0', 'selectors' => ['{{WRAPPER}} .mh-attr-tag' => 'border-color:{{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'attr_typo', 'selector' => '{{WRAPPER}} .mh-attr-tag']);
        $this->add_control('attr_more_color', ['label' => __('More Link Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#c5a94e', 'selectors' => ['{{WRAPPER}} .mh-attr-more' => 'color:{{VALUE}};']]);
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: WISHLIST BUTTON (Shop Card)
        // ----------------------------------------------------
        $this->start_controls_section('section_style_sc_wish', ['label' => __('Wishlist Icon (Shop Card)', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['card_layout' => 'shop_card', 'show_wishlist_btn' => 'yes']]);
        $this->start_controls_tabs('tabs_sc_wish');
        $this->start_controls_tab('tab_sc_wish_normal', ['label' => __('Normal', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control('sc_wish_icon_color', ['label' => __('Icon Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#aaaaaa', 'selectors' => ['{{WRAPPER}} .mh-sc-wish svg' => 'fill:{{VALUE}} !important;']]);
        $this->add_control('sc_wish_bg', ['label' => __('Background', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .mh-sc-wish' => 'background:{{VALUE}} !important;']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_sc_wish_hover', ['label' => __('Hover', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control('sc_wish_icon_hover', ['label' => __('Icon Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => ['{{WRAPPER}} .mh-sc-wish:hover svg' => 'fill:{{VALUE}} !important;']]);
        $this->add_control('sc_wish_bg_hover', ['label' => __('Background', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .mh-sc-wish:hover' => 'background:{{VALUE}} !important;']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_sc_wish_active', ['label' => __('Active (Added)', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control('sc_wish_icon_active', ['label' => __('Active Icon Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => ['{{WRAPPER}} .mh-sc-wish.mh-added svg' => 'fill:{{VALUE}} !important;']]);
        $this->add_control('sc_wish_bg_active', ['label' => __('Active Background', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .mh-sc-wish.mh-added' => 'background:{{VALUE}} !important;']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'sc_wish_shadow', 'selector' => '{{WRAPPER}} .mh-sc-wish', 'separator' => 'before']);
        $this->add_responsive_control('sc_wish_size', ['label' => __('Icon Size', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 20], 'selectors' => ['{{WRAPPER}} .mh-sc-wish svg' => 'width:{{SIZE}}px !important; height:{{SIZE}}px !important;'], 'separator' => 'before']);
        $this->add_responsive_control('sc_wish_radius', ['label' => __('Border Radius', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .mh-sc-wish' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->add_responsive_control('sc_wish_padding', ['label' => __('Padding', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'selectors' => ['{{WRAPPER}} .mh-sc-wish' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: SNACK ALERT (TOAST)
        // ----------------------------------------------------
        $this->start_controls_section('section_style_toast', ['label' => __('Snack Alert (Toast)', 'mh-plug-ecommerce-builder-widgets'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('toast_position', [
            'label' => __('Position', 'mh-plug-ecommerce-builder-widgets'),
            'type' => Controls_Manager::SELECT,
            'default' => 'bottom-left',
            'options' => [
                'bottom-left'   => __('Bottom Left', 'mh-plug-ecommerce-builder-widgets'),
                'bottom-center' => __('Bottom Center', 'mh-plug-ecommerce-builder-widgets'),
                'bottom-right'  => __('Bottom Right', 'mh-plug-ecommerce-builder-widgets'),
                'top-left'      => __('Top Left', 'mh-plug-ecommerce-builder-widgets'),
                'top-center'    => __('Top Center', 'mh-plug-ecommerce-builder-widgets'),
                'top-right'     => __('Top Right', 'mh-plug-ecommerce-builder-widgets'),
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
        $this->add_control('toast_bg', ['label' => __('Background Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => ['body .mh-toast' => 'background-color: {{VALUE}} !important;']]);
        $this->add_control('toast_color', ['label' => __('Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => ['body .mh-toast' => 'color: {{VALUE}} !important;']]);
        $this->add_control('toast_success_border', ['label' => __('Success Border Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#4caf50', 'selectors' => ['body .mh-toast-success' => 'border-left-color: {{VALUE}} !important;']]);
        $this->add_control('toast_error_border', ['label' => __('Error Border Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#f44336', 'selectors' => ['body .mh-toast-error' => 'border-left-color: {{VALUE}} !important;']]);
        $this->add_control('toast_info_border', ['label' => __('Info Border Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::COLOR, 'default' => '#2196f3', 'selectors' => ['body .mh-toast-info' => 'border-left-color: {{VALUE}} !important;']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'toast_typo', 'selector' => 'body .mh-toast']);
        $this->add_responsive_control('toast_radius', ['label' => __('Border Radius', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['body .mh-toast' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->add_responsive_control('toast_padding', ['label' => __('Padding', 'mh-plug-ecommerce-builder-widgets'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'selectors' => ['body .mh-toast' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: LOAD MORE BUTTON
        // ----------------------------------------------------
        $this->start_controls_section('section_style_load_more', [
            'label'     => __('Load More Button', 'mh-plug-ecommerce-builder-widgets'),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => ['show_load_more' => 'yes'],
        ]);

        // --- Alignment & Width ---
        $this->add_responsive_control('load_more_align', [
            'label'   => __('Alignment', 'mh-plug-ecommerce-builder-widgets'),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start'  => ['title' => __('Left', 'mh-plug-ecommerce-builder-widgets'),   'icon' => 'eicon-text-align-left'],
                'center'      => ['title' => __('Center', 'mh-plug-ecommerce-builder-widgets'), 'icon' => 'eicon-text-align-center'],
                'flex-end'    => ['title' => __('Right', 'mh-plug-ecommerce-builder-widgets'),  'icon' => 'eicon-text-align-right'],
            ],
            'default'   => 'center',
            'selectors' => ['{{WRAPPER}} .mh-load-more-wrap' => 'justify-content: {{VALUE}};'],
        ]);
        $this->add_responsive_control('load_more_width', [
            'label'      => __('Button Width', 'mh-plug-ecommerce-builder-widgets'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range'      => ['px' => ['min' => 80, 'max' => 800], '%' => ['min' => 10, 'max' => 100]],
            'default'    => ['unit' => 'px', 'size' => 180],
            'selectors'  => ['{{WRAPPER}} .mh-load-more-btn' => 'width: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('load_more_margin_top', [
            'label'      => __('Margin Top', 'mh-plug-ecommerce-builder-widgets'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em'],
            'default'    => ['size' => 30, 'unit' => 'px'],
            'selectors'  => ['{{WRAPPER}} .mh-load-more-wrap' => 'margin-top: {{SIZE}}{{UNIT}};'],
        ]);

        // --- Animation ---
        $this->add_control('load_more_animation', [
            'label'     => __('Entry Animation', 'mh-plug-ecommerce-builder-widgets'),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'none',
            'options'   => [
                'none'      => __('None', 'mh-plug-ecommerce-builder-widgets'),
                'fadeIn'    => __('Fade In', 'mh-plug-ecommerce-builder-widgets'),
                'slideUp'   => __('Slide Up', 'mh-plug-ecommerce-builder-widgets'),
                'slideDown' => __('Slide Down', 'mh-plug-ecommerce-builder-widgets'),
                'zoomIn'    => __('Zoom In', 'mh-plug-ecommerce-builder-widgets'),
                'bounce'    => __('Bounce', 'mh-plug-ecommerce-builder-widgets'),
                'pulse'     => __('Pulse', 'mh-plug-ecommerce-builder-widgets'),
            ],
            'separator' => 'before',
        ]);
        $this->add_control('load_more_animation_duration', [
            'label'      => __('Animation Duration (ms)', 'mh-plug-ecommerce-builder-widgets'),
            'type'       => Controls_Manager::SLIDER,
            'default'    => ['size' => 600],
            'range'      => ['px' => ['min' => 200, 'max' => 2000, 'step' => 50]],
            'condition'  => ['load_more_animation!' => 'none'],
            'selectors'  => ['{{WRAPPER}} .mh-load-more-btn' => 'animation-duration: {{SIZE}}ms;'],
        ]);

        // --- Typography ---
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'      => 'load_more_typo',
            'selector'  => '{{WRAPPER}} .mh-load-more-btn',
            'separator' => 'before',
        ]);

        // --- Normal / Hover tabs ---
        $this->start_controls_tabs('tabs_load_more');

        $this->start_controls_tab('tab_load_more_normal', ['label' => __('Normal', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control('load_more_bg', [
            'label'     => __('Background', 'mh-plug-ecommerce-builder-widgets'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#004265',
            'selectors' => ['{{WRAPPER}} .mh-load-more-btn' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_control('load_more_color', [
            'label'     => __('Text Color', 'mh-plug-ecommerce-builder-widgets'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => ['{{WRAPPER}} .mh-load-more-btn' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'     => 'load_more_border',
            'selector' => '{{WRAPPER}} .mh-load-more-btn',
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'load_more_shadow',
            'selector' => '{{WRAPPER}} .mh-load-more-btn',
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab('tab_load_more_hover', ['label' => __('Hover', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control('load_more_bg_hover', [
            'label'     => __('Background', 'mh-plug-ecommerce-builder-widgets'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#002b42',
            'selectors' => ['{{WRAPPER}} .mh-load-more-btn:hover' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_control('load_more_color_hover', [
            'label'     => __('Text Color', 'mh-plug-ecommerce-builder-widgets'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => ['{{WRAPPER}} .mh-load-more-btn:hover' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'     => 'load_more_border_hover',
            'selector' => '{{WRAPPER}} .mh-load-more-btn:hover',
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'load_more_shadow_hover',
            'selector' => '{{WRAPPER}} .mh-load-more-btn:hover',
        ]);
        $this->end_controls_tab();

        $this->end_controls_tabs();

        // --- Padding & Radius ---
        $this->add_responsive_control('load_more_padding', [
            'label'      => __('Padding', 'mh-plug-ecommerce-builder-widgets'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'default'    => ['top' => 14, 'right' => 28, 'bottom' => 14, 'left' => 28, 'isLinked' => false],
            'selectors'  => ['{{WRAPPER}} .mh-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            'separator'  => 'before',
        ]);
        $this->add_responsive_control('load_more_radius', [
            'label'      => __('Border Radius', 'mh-plug-ecommerce-builder-widgets'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'default'    => ['top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6, 'isLinked' => true],
            'selectors'  => ['{{WRAPPER}} .mh-load-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('load_more_icon_size', [
            'label'      => __('Icon Size', 'mh-plug-ecommerce-builder-widgets'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'default'    => ['size' => 14],
            'selectors'  => ['{{WRAPPER}} .mh-load-more-btn .mh-lm-icon' => 'font-size: {{SIZE}}px; width: {{SIZE}}px; height: {{SIZE}}px;'],
        ]);
        $this->add_responsive_control('load_more_icon_gap', [
            'label'      => __('Icon Gap', 'mh-plug-ecommerce-builder-widgets'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'default'    => ['size' => 8],
            'selectors'  => ['{{WRAPPER}} .mh-load-more-btn' => 'gap: {{SIZE}}px;'],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (class_exists('WooCommerce')) {
            wp_enqueue_script('zoom');
            wp_enqueue_script('flexslider');
            wp_enqueue_script('photoswipe-ui-default');
            wp_enqueue_script('wc-single-product');
            wp_enqueue_style('photoswipe-default-skin');
        }

        $desktop_count = !empty($settings['posts_per_page']) ? intval($settings['posts_per_page']) : 8;
        $tablet_count = !empty($settings['posts_per_page_tablet']) ? intval($settings['posts_per_page_tablet']) : $desktop_count;
        $mobile_count = !empty($settings['posts_per_page_mobile']) ? intval($settings['posts_per_page_mobile']) : $tablet_count;

        $max_posts = max($desktop_count, $tablet_count, $mobile_count);

        $post_type = 'product';
        $args = [
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'tax_query' => ['relation' => 'AND']
        ];

        $combo_slugs = ['wooco', 'combo', 'bundle', 'woosb', 'yith_bundle', 'woosg'];

        if ($settings['query_type'] === 'combo_offers' && class_exists('WooCommerce')) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_type',
                'field' => 'slug',
                'terms' => $combo_slugs,
            ];
        } else {
            if ($settings['query_type'] === 'current_query') {
                $queried_object = get_queried_object();
                if ($queried_object instanceof \WP_Term) {
                    if ($queried_object->taxonomy === 'category' || $queried_object->taxonomy === 'post_tag') {
                        $post_type = 'post';
                    }
                    $args['tax_query'][] = ['taxonomy' => $queried_object->taxonomy, 'field' => 'term_id', 'terms' => $queried_object->term_id];
                } elseif (is_search()) {
                    $args['s'] = get_search_query();
                }
            } elseif ($settings['query_type'] === 'best_sellers') {
                $args['meta_key'] = 'total_sales';
                $args['orderby'] = 'meta_value_num';
            } elseif ($settings['query_type'] === 'top_rated') {
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby'] = 'meta_value_num';
            } elseif ($settings['query_type'] === 'sale' && class_exists('WooCommerce')) {
                $product_ids_on_sale = wc_get_product_ids_on_sale();
                $args['post__in'] = !empty($product_ids_on_sale) ? $product_ids_on_sale : [0];
            } elseif ($settings['query_type'] === 'featured') {
                $args['tax_query'][] = [['taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'featured', 'operator' => 'IN']];
            } elseif ($settings['query_type'] === 'latest') {
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
            }

            if ($settings['exclude_combo'] === 'yes' && class_exists('WooCommerce')) {
                $args['tax_query'][] = [
                    'taxonomy' => 'product_type',
                    'field' => 'slug',
                    'terms' => $combo_slugs,
                    'operator' => 'NOT IN',
                ];
            }
        }

        // 🚀 THE FIX: Listen to the Filter Widget's URL parameters and instantly override the Elementor Query
        if (isset($_GET['orderby'])) {
            $orderby_param = wc_clean($_GET['orderby']);
            switch ($orderby_param) {
                case 'price':
                    $args['meta_key'] = '_price';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'ASC';
                    break;
                case 'price-desc':
                    $args['meta_key'] = '_price';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                    break;
                case 'popularity':
                    $args['meta_key'] = 'total_sales';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                    break;
                case 'rating':
                    $args['meta_key'] = '_wc_average_rating';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                    break;
                case 'date':
                    $args['orderby'] = 'date';
                    $args['order'] = 'DESC';
                    break;
                case 'menu_order':
                    $args['orderby'] = 'menu_order title';
                    $args['order'] = 'ASC';
                    break;
            }
        }

        // --- NEW: Advanced Attribute Filter Handler ---
        if (!empty($_GET['product_cat'])) {
            $cats = is_array($_GET['product_cat']) ? $_GET['product_cat'] : explode(',', $_GET['product_cat']);
            $args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => array_map('sanitize_title', $cats),
            ];
        }

        if (isset($_GET['min_price']) && isset($_GET['max_price'])) {
            if (!isset($args['meta_query'])) $args['meta_query'] = ['relation' => 'AND'];
            $args['meta_query'][] = [
                'key'     => '_price',
                'value'   => [floatval($_GET['min_price']), floatval($_GET['max_price'])],
                'type'    => 'NUMERIC',
                'compare' => 'BETWEEN',
            ];
        }

        if (!empty($_GET['mh_status'])) {
            $statuses = is_array($_GET['mh_status']) ? $_GET['mh_status'] : explode(',', $_GET['mh_status']);
            if (in_array('featured', $statuses)) {
                $args['tax_query'][] = [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => 'featured',
                ];
            }
            if (in_array('onsale', $statuses)) {
                $product_ids_on_sale = wc_get_product_ids_on_sale();
                $args['post__in'] = empty($args['post__in']) ? $product_ids_on_sale : array_intersect($args['post__in'], $product_ids_on_sale);
                if (empty($args['post__in'])) $args['post__in'] = [0]; // Force empty result if intersection is empty
            }
        }

        if (!empty($_GET['mh_rating'])) {
            $rating = intval($_GET['mh_rating']);
            if (!isset($args['meta_query'])) $args['meta_query'] = ['relation' => 'AND'];
            $args['meta_query'][] = [
                'key'     => '_wc_average_rating',
                'value'   => $rating,
                'type'    => 'DECIMAL',
                'compare' => '>=',
            ];
        }

        if (!empty($_GET['mh_search'])) {
            $args['s'] = sanitize_text_field($_GET['mh_search']);
        }

        // Handle dynamically added attributes (filter_color, filter_size, etc.)
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'filter_') === 0 && !empty($value)) {
                $attr_name = 'pa_' . str_replace('filter_', '', $key);
                $terms = is_array($value) ? $value : explode(',', $value);
                $args['tax_query'][] = [
                    'taxonomy' => sanitize_title($attr_name),
                    'field'    => 'slug',
                    'terms'    => array_map('sanitize_title', $terms),
                ];
            }
        }

        // Handle brand filter
        if (!empty($_GET['mh_brand'])) {
            $brand_slugs = is_array($_GET['mh_brand']) ? $_GET['mh_brand'] : explode(',', $_GET['mh_brand']);
            $brand_tax = null;
            foreach (['product_brand', 'pwb-brand', 'yith_product_brand', 'pa_brand'] as $bt) {
                if (taxonomy_exists($bt)) { $brand_tax = $bt; break; }
            }
            if ($brand_tax) {
                $args['tax_query'][] = [
                    'taxonomy' => $brand_tax,
                    'field'    => 'slug',
                    'terms'    => array_map('sanitize_title', $brand_slugs),
                ];
            }
        }

        $args['post_type'] = $post_type;
        $loop = new \WP_Query($args);

        if (!$loop->have_posts()) {
            echo '<p>' . esc_html__('No items found matching this criteria.', 'mh-plug-ecommerce-builder-widgets') . '</p>';
            return;
        }

        $grid_id = 'mh-grid-' . $this->get_id();

        $css = "";
        ob_start();
        ?>
            .mh-action-btn svg {
                width: 16px;
                height: 16px;
                display: inline-block;
                transition: fill 0.3s ease;
            }

            .mh-action-btn i {
                transition: color 0.3s ease;
            }

            .mh-product-grid .mh-quick-view-trigger i {
                color: inherit !important;
            }

            .mh-product-grid .mh-quick-view-trigger svg {
                fill: currentColor !important;
                color: inherit !important;
            }

            .mh-product-info .mh-product-rating {
                display: block;
                width: 100%;
                clear: both;
                line-height: 1;
            }

            .mh-product-info .mh-product-rating .star-rating {
                float: none !important;
                display: inline-block !important;
                vertical-align: middle;
            }

            .mh-product-info .mh-product-price {
                display: block;
                width: 100%;
                clear: both;
            }

            .mh-wishlist-btn .mh-icon-added {
                display: none !important;
            }

            .mh-wishlist-btn.mh-added .mh-icon-normal {
                display: none !important;
            }

            .mh-wishlist-btn.mh-added .mh-icon-added {
                display: flex !important;
            }

            .mh-wishlist-btn.mh-adding {
                opacity: 0.6;
                pointer-events: none;
                animation: mh-pulse 1s infinite;
            }

            @keyframes mh-pulse {
                0% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(0.9);
                }

                100% {
                    transform: scale(1);
                }
            }

            @media (min-width: 1025px) {
                #<?php echo esc_attr($grid_id); ?> .mh-product-card:nth-child(n+<?php echo $desktop_count + 1; ?>) {
                    display: none !important;
                }
            }

            @media (min-width: 768px) and (max-width: 1024px) {
                #<?php echo esc_attr($grid_id); ?> .mh-product-card:nth-child(n+<?php echo $tablet_count + 1; ?>) {
                    display: none !important;
                }
            }

            @media (max-width: 767px) {
                #<?php echo esc_attr($grid_id); ?> .mh-product-card:nth-child(n+<?php echo $mobile_count + 1; ?>) {
                    display: none !important;
                }
            }
        <?php
        $css .= ob_get_clean();
        // ── Load More: animation keyframes (once per widget) ──────────────────
        $lm_animation = $settings['load_more_animation'] ?? 'none';
        $lm_posts     = max(1, intval($settings['load_more_posts'] ?? 4));
        ob_start();
        ?>
            #<?php echo esc_attr($grid_id); ?>-lm-wrap {
                display: flex;
                width: 100%;
            }
            #<?php echo esc_attr($grid_id); ?>-lm-wrap .mh-load-more-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                cursor: pointer;
                border: none;
                font-weight: 600;
                text-decoration: none;
                transition: background-color .3s ease, color .3s ease, transform .2s ease, box-shadow .3s ease;
                line-height: 1.4;
                box-sizing: border-box;
                outline: none;
            }
            #<?php echo esc_attr($grid_id); ?>-lm-wrap .mh-load-more-btn:hover {
                transform: translateY(-2px);
            }
            #<?php echo esc_attr($grid_id); ?>-lm-wrap .mh-load-more-btn.mh-lm-loading {
                opacity: .7;
                pointer-events: none;
                animation: mh-lm-spin 1s linear infinite;
            }
            #<?php echo esc_attr($grid_id); ?>-lm-wrap .mh-load-more-btn.mh-lm-no-more {
                display: none !important;
            }
            @keyframes mh-lm-spin     { to { transform: rotate(360deg); } }
            @keyframes mh-lm-fadeIn    { from { opacity:0; } to { opacity:1; } }
            @keyframes mh-lm-slideUp   { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
            @keyframes mh-lm-slideDown { from { opacity:0; transform:translateY(-30px); } to { opacity:1; transform:translateY(0); } }
            @keyframes mh-lm-zoomIn    { from { opacity:0; transform:scale(.8); } to { opacity:1; transform:scale(1); } }
            @keyframes mh-lm-bounce    { 0%,100%{transform:translateY(0)} 40%{transform:translateY(-12px)} 60%{transform:translateY(-6px)} }
            @keyframes mh-lm-pulse     { 0%,100%{transform:scale(1)} 50%{transform:scale(1.06)} }
            <?php if ($lm_animation !== 'none'): ?>
            #<?php echo esc_attr($grid_id); ?>-lm-wrap .mh-load-more-btn {
                animation-name: mh-lm-<?php echo esc_attr($lm_animation); ?>;
                animation-fill-mode: both;
            }
            <?php endif; ?>
        <?php 
        $css .= ob_get_clean();
        if ($settings['card_layout'] === 'shop_card'): 
            ob_start();
        ?>
                .mh-shop-card .mh-product-info {
                    padding: 15px;
                }

                .mh-attr-wrap {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 5px;
                    margin: 8px 0;
                    align-items: center;
                }

                .mh-attr-tag {
                    display: inline-block;
                    padding: 3px 8px;
                    font-size: 11px;
                    border: 1px solid #e0e0e0;
                    border-radius: 4px;
                    white-space: nowrap;
                    max-width: 80px;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                .mh-attr-more {
                    font-size: 12px;
                    cursor: pointer;
                    text-decoration: none;
                    font-weight: 500;
                }

                .mh-atc-row {
                    display: flex;
                    align-items: stretch;
                    border-top: 1px solid #eee;
                    margin-top: auto;
                    width: 100%;
                }

                .mh-atc-btn {
                    display: flex !important;
                    align-items: center;
                    justify-content: center;
                    gap: 6px;
                    flex: 1;
                    padding: 12px 15px;
                    font-size: 13px;
                    font-weight: 600;
                    text-decoration: none;
                    text-transform: uppercase;
                    transition: all .3s ease;
                    cursor: pointer;
                    border: none;
                    line-height: 1;
                    box-sizing: border-box;
                    margin: 0;
                }

                .mh-atc-btn i {
                    font-size: 14px;
                }

                .mh-sc-wish {
                    display: flex !important;
                    align-items: center;
                    justify-content: center;
                    width: auto;
                    min-width: 50px;
                    border: none;
                    border-left: 1px solid #eee;
                    cursor: pointer;
                    transition: all .3s;
                    background: transparent;
                    padding: 10px;
                    box-sizing: border-box;
                    margin: 0;
                    line-height: 1;
                    gap: 8px;
                }

                .mh-sc-wish .mh-wish-label { font-size: 13px; font-weight: 600; }
                .mh-sc-wish.mh-added .mh-label-normal { display: none; }
                .mh-sc-wish:not(.mh-added) .mh-label-added { display: none; }
                .mh-sc-wish.mh-hide-text-active.mh-added .mh-wish-label { display: none; }

                .mh-sc-wish svg {
                    width: 20px;
                    height: 20px;
                    fill: #aaa;
                    transition: fill .3s;
                    display: block;
                }

                .mh-sc-wish:hover svg {
                    fill: #d63638;
                }

                .mh-sc-wish.mh-added svg {
                    fill: #d63638;
                }

                .mh-shop-card {
                    display: flex;
                    flex-direction: column;
                }
        <?php 
            $css .= ob_get_clean();
        endif; 
        
        wp_register_style( 'mh-product-grid-style', false );
        wp_enqueue_style( 'mh-product-grid-style' );
        wp_add_inline_style( 'mh-product-grid-style', $css );
        ?>

        <div class="mh-product-grid" id="<?php echo esc_attr($grid_id); ?>">
            <?php
            while ($loop->have_posts()):
                $loop->the_post();
                global $post;
                $post_id = get_the_ID();
                $is_product = (get_post_type() === 'product' && class_exists('WooCommerce') && function_exists('wc_get_product'));
                if ($is_product) {
                    $product = wc_get_product($post_id);
                    $in_wishlist = function_exists('mh_wishlist_has_product') ? mh_wishlist_has_product($post_id) : false;
                }

                if ($settings['card_layout'] === 'shop_card'):
                    // ═══════════ SHOP CARD LAYOUT ═══════════
                    ?>
                    <div class="mh-product-card mh-shop-card">
                        <div class="mh-product-image-wrap">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo $is_product ? $product->get_image('woocommerce_thumbnail') : get_the_post_thumbnail($post_id, 'medium'); ?>
                            </a>
                            <?php if ($is_product && $settings['show_badge'] === 'yes' && $product->is_on_sale()): ?>
                                <div class="mh-product-badges"><span class="mh-badge"><?php esc_html_e('Sale', 'mh-plug-ecommerce-builder-widgets'); ?></span></div>
                            <?php endif; ?>
                        </div>
                        <div class="mh-product-info">
                            <?php if ($settings['show_category'] === 'yes' && $is_product): ?>
                                <div class="mh-product-cat"><?php echo wc_get_product_category_list($post_id, ', '); ?></div>
                            <?php endif; ?>
                            <h3 class="mh-product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <?php if ($is_product): ?>
                                <div class="mh-product-price"><?php echo $product->get_price_html(); ?></div>
                            <?php endif; ?>
                            <?php if ($is_product && $settings['show_attributes'] === 'yes'):
                                $tax_slug = !empty($settings['attribute_taxonomy']) ? trim($settings['attribute_taxonomy']) : '';
                                $attrs = $product->get_attributes();
                                $terms = [];
                                // Specific taxonomy requested
                                if ($tax_slug) {
                                    $terms = wc_get_product_terms($post_id, $tax_slug, ['fields' => 'names']);
                                }
                                // Auto-detect: try all attributes
                                if (empty($terms) && !$tax_slug) {
                                    foreach ($attrs as $attr_key => $attr) {
                                        if (is_object($attr) && $attr->is_taxonomy()) {
                                            $terms = wc_get_product_terms($post_id, $attr->get_name(), ['fields' => 'names']);
                                        } elseif (is_object($attr) && !$attr->is_taxonomy()) {
                                            $terms = $attr->get_options();
                                        }
                                        if (!empty($terms))
                                            break;
                                    }
                                }
                                $max = intval($settings['max_attributes'] ?? 3);
                                if (!empty($terms)): ?>
                                    <div class="mh-attr-wrap">
                                        <?php foreach (array_slice((array) $terms, 0, $max) as $t): ?>
                                            <span class="mh-attr-tag"><?php echo esc_html($t); ?></span>
                                        <?php endforeach; ?>
                                        <?php if (count((array) $terms) > $max): ?>
                                            <a href="<?php the_permalink(); ?>" class="mh-attr-more"><?php esc_html_e('More', 'mh-plug-ecommerce-builder-widgets'); ?></a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif;
                            endif; ?>
                        </div>
                        <?php if ($is_product): ?>
                            <div class="mh-atc-row">
                                <?php if ($settings['show_add_to_cart'] === 'yes'): ?>
                                    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
                                        data-product_id="<?php echo esc_attr($post_id); ?>" data-quantity="1"
                                        class="mh-atc-btn add_to_cart_button ajax_add_to_cart <?php echo $product->is_purchasable() && $product->is_in_stock() ? '' : 'disabled'; ?>">
                                        <i class="fas fa-shopping-cart"></i> <?php echo esc_html($product->add_to_cart_text()); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($settings['show_wishlist_btn'] === 'yes'): ?>
                                    <button type="button" class="mh-sc-wish mh-wishlist-btn <?php echo $in_wishlist ? 'mh-added' : ''; ?> <?php echo $settings['hide_text_when_active'] === 'yes' ? 'mh-hide-text-active' : ''; ?>"
                                        data-product-id="<?php echo esc_attr($post_id); ?>" data-behavior="toggle" aria-label="Wishlist">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"
                                            style="width:20px;height:20px;">
                                            <path
                                                d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9C313.1 47 353.4 37.9 392.8 45.4 462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9z" />
                                        </svg>
                                        <?php if ($settings['show_wishlist_text'] === 'yes'): ?>
                                            <span class="mh-wish-label">
                                                <span class="mh-label-normal"><?php echo esc_html($settings['wishlist_text']); ?></span>
                                                <span class="mh-label-added"><?php echo esc_html($settings['wishlist_added_text']); ?></span>
                                            </span>
                                        <?php endif; ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else:
                    // ═══════════ CLASSIC LAYOUT ═══════════
                    ?>
                    <div class="mh-product-card">
                        <div class="mh-product-image-wrap">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo $is_product ? $product->get_image('woocommerce_thumbnail') : get_the_post_thumbnail($post_id, 'medium'); ?>
                            </a>
                            <?php if ($is_product && $settings['show_badge'] === 'yes' && $product->is_on_sale()): ?>
                                <div class="mh-product-badges"><span class="mh-badge"><?php esc_html_e('Sale', 'mh-plug-ecommerce-builder-widgets'); ?></span></div>
                            <?php endif; ?>
                            <div class="mh-product-actions">
                                <?php if ($is_product): ?>
                                    <?php $this->render_quick_view_button($post_id, $settings); ?>
                                    <?php if ($settings['show_compare'] === 'yes'): ?>
                                        <a href="#" class="mh-action-btn mh-compare-btn" data-product-id="<?php echo esc_attr($post_id); ?>"
                                            title="<?php esc_html_e('Compare', 'mh-plug-ecommerce-builder-widgets'); ?>"
                                            style="display:flex;align-items:center;justify-content:center;">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="#" class="mh-action-btn mh-wishlist-btn <?php echo $in_wishlist ? 'mh-added' : ''; ?>"
                                        data-product-id="<?php echo esc_attr($post_id); ?>" data-behavior="toggle"
                                        title="<?php esc_html_e('Wishlist', 'mh-plug-ecommerce-builder-widgets'); ?>"
                                        style="display:flex;align-items:center;justify-content:center;">
                                        <span class="mh-icon-normal" style="display:flex;align-items:center;justify-content:center;"><svg
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16" height="16"
                                                fill="currentColor">
                                                <path
                                                    d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9C313.1 47 353.4 37.9 392.8 45.4 462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 429.3l175.2-161.3c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2l-17.8 20c-.3.4-.7.7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z" />
                                            </svg></span>
                                        <span class="mh-icon-added" style="display:flex;align-items:center;justify-content:center;"><svg
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16" height="16"
                                                fill="currentColor">
                                                <path
                                                    d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z" />
                                            </svg></span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php the_permalink(); ?>" class="mh-action-btn"
                                        title="<?php esc_html_e('Read More', 'mh-plug-ecommerce-builder-widgets'); ?>"
                                        style="display:flex;align-items:center;justify-content:center;">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mh-product-info">
                            <?php if ($settings['show_category'] === 'yes'): ?>
                                <div class="mh-product-cat">
                                    <?php echo $is_product ? wc_get_product_category_list($post_id, ', ') : get_the_category_list(', ', '', $post_id); ?>
                                </div>
                            <?php endif; ?>
                            <h3 class="mh-product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <?php if ($is_product): ?>
                                <?php if ($settings['show_rating'] === 'yes'): ?>
                                    <div class="mh-product-rating"><?php echo wc_get_rating_html($product->get_average_rating()); ?></div>
                                <?php endif; ?>
                                <div class="mh-product-price"><?php echo $product->get_price_html(); ?></div>
                            <?php else: ?>
                                <div class="mh-post-date"><?php echo get_the_date(); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>

        <?php
        $total_products = $loop->post_count;
        // Only show the button if there are more products than the initial visible count
        if ( ($settings['show_load_more'] ?? '') === 'yes' && $total_products > $desktop_count ) :
            $lm_text       = !empty($settings['load_more_text']) ? $settings['load_more_text'] : __('Load More', 'mh-plug-ecommerce-builder-widgets');
            $lm_icon_pos   = $settings['load_more_icon_position'] ?? 'after';
            $lm_more_count = max(1, intval($settings['load_more_posts'] ?? 4));

            // Render icon HTML
            $lm_icon_html = '';
            if (!empty($settings['load_more_icon']['value'])) {
                ob_start();
                \Elementor\Icons_Manager::render_icon($settings['load_more_icon'], ['class' => 'mh-lm-icon', 'aria-hidden' => 'true']);
                $lm_icon_html = ob_get_clean();
            }
        ?>
        <div class="mh-load-more-wrap" id="<?php echo esc_attr($grid_id); ?>-lm-wrap"
             data-grid-id="<?php echo esc_attr($grid_id); ?>"
             data-load-count="<?php echo esc_attr($lm_more_count); ?>"
             data-total="<?php echo esc_attr($total_products); ?>"
             data-desktop="<?php echo esc_attr($desktop_count); ?>"
             data-tablet="<?php echo esc_attr($tablet_count); ?>"
             data-mobile="<?php echo esc_attr($mobile_count); ?>">
            <button type="button" class="mh-load-more-btn" aria-label="<?php echo esc_attr($lm_text); ?>">
                <?php if ($lm_icon_pos === 'before' && $lm_icon_html): echo $lm_icon_html; endif; ?>
                <span class="mh-lm-text"><?php echo esc_html($lm_text); ?></span>
                <?php if ($lm_icon_pos === 'after' && $lm_icon_html): echo $lm_icon_html; endif; ?>
            </button>
        </div>
        <?php
        ob_start();
        ?>
        (function() {
            var wrap       = document.getElementById('<?php echo esc_js($grid_id); ?>-lm-wrap');
            if (!wrap) return;
            var gridId     = wrap.dataset.gridId;
            var loadCount  = parseInt(wrap.dataset.loadCount, 10) || 4;
            var total      = parseInt(wrap.dataset.total,     10) || 0;
            var desktop    = parseInt(wrap.dataset.desktop,   10) || 8;
            var tablet     = parseInt(wrap.dataset.tablet,    10) || desktop;
            var mobile     = parseInt(wrap.dataset.mobile,    10) || tablet;
            var btn        = wrap.querySelector('.mh-load-more-btn');
            var grid       = document.getElementById(gridId);
            if (!btn || !grid) return;

            // Determine current visible limit by breakpoint
            function getInitialLimit() {
                var w = window.innerWidth;
                if (w <= 767)  return mobile;
                if (w <= 1024) return tablet;
                return desktop;
            }

            // Track how many cards are currently shown
            var shownCount = getInitialLimit();

            // Cards are all in DOM; CSS hides nth-child > limit.
            // We override that with inline display style on reveal.
            var cards = grid.querySelectorAll('.mh-product-card');

            function updateButton() {
                if (shownCount >= total) {
                    btn.classList.add('mh-lm-no-more');
                } else {
                    btn.classList.remove('mh-lm-no-more');
                }
            }

            function revealMore() {
                var revealed = 0;
                for (var i = 0; i < cards.length; i++) {
                    if (i < shownCount) continue; // already shown
                    if (revealed >= loadCount)   break; // enough for this click
                    cards[i].style.display = '';          // clear the CSS hide
                    cards[i].style.removeProperty('display');
                    // Force visibility by overriding nth-child CSS
                    cards[i].style.setProperty('display', 'flex', 'important');
                    revealed++;
                }
                shownCount += revealed;
                updateButton();
            }

            btn.addEventListener('click', revealMore);
            updateButton();

            // Re-evaluate on resize (breakpoint change)
            window.addEventListener('resize', function() {
                var newLimit = getInitialLimit();
                if (newLimit !== shownCount) {
                    // If user resizes to a larger breakpoint, auto-show up to that limit
                    if (newLimit > shownCount) {
                        for (var i = shownCount; i < newLimit && i < cards.length; i++) {
                            cards[i].style.setProperty('display', 'flex', 'important');
                        }
                        shownCount = Math.min(newLimit, cards.length);
                    }
                    updateButton();
                }
            });
        })();
        <?php
        $js = ob_get_clean();
        wp_add_inline_script( 'jquery-core', $js );
        endif; 
        ?>

        <?php
    }
}