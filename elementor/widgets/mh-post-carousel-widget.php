<?php
/**
 * MH Post Carousel Widget (Final Version - Fixed)
 * * Fix: Removes duplicate method declaration.
 * Includes:
 * - Custom Post Types
 * - Drag & Drop Layout Builder
 * - Slider/Grid Modes
 * - Equal Height & Bottom Buttons
 * - All Styling Options
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Repeater;

class MH_Post_Carousel_Widget extends Widget_Base {

    public function get_name() {
        return 'mh-post-carousel';
    }

    public function get_title() {
        return esc_html__('MH Post Carousel', 'mh-plug');
    }

    public function get_icon() {
        return 'eicon-posts-carousel';
    }

    public function get_categories() {
        return ['mh-plug-widgets'];
    }

    /**
     * Helper to get all public post types
     * DEFINED ONLY ONCE HERE
     */
    protected function get_supported_post_types() {
        $args = [
            'public' => true,
            'show_in_nav_menus' => true
        ];
        $post_types = get_post_types($args, 'objects');
        $options = [];
        
        foreach ($post_types as $post_type) {
            // Exclude attachments
            if ($post_type->name !== 'attachment') {
                $options[$post_type->name] = $post_type->label;
            }
        }
        return $options;
    }

    protected function register_controls() {

        // --- 1. QUERY SECTION ---
        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Query', 'mh-plug'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Post Type Select
        $this->add_control(
            'selected_post_type',
            [
                'label' => esc_html__('Post Type', 'mh-plug'),
                'type' => Controls_Manager::SELECT,
                'default' => 'post',
                'options' => $this->get_supported_post_types(), // Uses the helper method
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts Per Page', 'mh-plug'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $this->end_controls_section();

        // --- 2. LAYOUT BUILDER ---
        $this->start_controls_section(
            'section_layout_builder',
            [
                'label' => esc_html__('Card Layout Builder', 'mh-plug'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'layout_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => '<small>' . __('Drag & Drop to reorder. Use "Inline" width for side-by-side items. Open an item to style it.', 'mh-plug') . '</small>',
                'content_classes' => 'elementor-descriptor',
            ]
        );

        $repeater = new Repeater();

        // --- Content Controls ---
        $repeater->add_control(
            'element_type',
            [
                'label' => esc_html__('Element Type', 'mh-plug'),
                'type' => Controls_Manager::SELECT,
                'default' => 'title',
                'options' => [
                    'image'       => 'Featured Image',
                    'title'       => 'Title',
                    'excerpt'     => 'Description',
                    'button'      => 'Read More Button',
                    'date'        => 'Date',
                    'author'      => 'Author',
                    'category'    => 'Category',
                    'tags'        => 'Tags',
                ],
            ]
        );

        $repeater->add_control(
            'element_width',
            [
                'label' => esc_html__( 'Width', 'mh-plug' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'full',
                'options' => [
                    'full' => 'Full Width (Stack)',
                    'inline' => 'Inline (Side by Side)',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'element_align',
            [
                'label' => esc_html__( 'Alignment', 'mh-plug' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ],
                    'center' => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
                    'right' => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'text-align: {{VALUE}}; justify-content: {{VALUE}};',
                ],
            ]
        );

        // --- Content Specific Settings ---
        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'default' => 'medium_large',
                'condition' => ['element_type' => 'image'],
            ]
        );

        $repeater->add_control( 'excerpt_length', [ 'label' => 'Length', 'type' => Controls_Manager::NUMBER, 'default' => 15, 'condition' => ['element_type' => 'excerpt'] ] );
        $repeater->add_control( 'button_text', [ 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Read More', 'condition' => ['element_type' => 'button'] ] );
        $repeater->add_control( 'meta_icon', [ 'label' => 'Icon', 'type' => Controls_Manager::ICONS, 'condition' => ['element_type' => ['date', 'author', 'category', 'tags']] ] );

        // --- STYLING TAB (Inside Repeater) ---
        $repeater->add_control( 'style_heading', [ 'label' => 'Styling', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

        $repeater->start_controls_tabs( 'tabs_item_style' );

        // Normal State
        $repeater->start_controls_tab( 'tab_item_normal', [ 'label' => 'Normal' ] );
        
        $repeater->add_control(
            'item_text_color',
            [
                'label' => esc_html__('Text Color', 'mh-plug'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} i' => 'color: {{VALUE}};',
                ],
                'condition' => ['element_type!' => ['image']],
            ]
        );

        $repeater->add_control( 'btn_bg_color', [ 'label' => 'Button Background', 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .mh-post-button' => 'background-color: {{VALUE}};' ], 'condition' => ['element_type' => 'button'] ] );
        
        $repeater->add_responsive_control(
            'image_border_radius',
            [
                'label' => 'Border Radius',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
                'condition' => ['element_type' => 'image'],
            ]
        );
        $repeater->end_controls_tab();

        // Hover State
        $repeater->start_controls_tab( 'tab_item_hover', [ 'label' => 'Hover' ] );
        
        $repeater->add_control(
            'item_text_color_hover',
            [
                'label' => 'Text Color',
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-post-card:hover {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .mh-post-card:hover {{CURRENT_ITEM}} a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .mh-post-card:hover {{CURRENT_ITEM}} i' => 'color: {{VALUE}};',
                ],
                'condition' => ['element_type!' => ['image']],
            ]
        );
        
        $repeater->add_control( 'btn_bg_color_hover', [ 'label' => 'Button Background', 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-post-card:hover {{CURRENT_ITEM}} .mh-post-button' => 'background-color: {{VALUE}};' ], 'condition' => ['element_type' => 'button'] ] );

        $repeater->add_control(
            'image_scale_hover',
            [
                'label' => 'Scale',
                'type' => Controls_Manager::SLIDER,
                'range' => [ 'px' => [ 'min' => 0.5, 'max' => 2, 'step' => 0.05 ] ],
                'default' => [ 'unit' => 'px', 'size' => 1 ],
                'selectors' => [ '{{WRAPPER}} .mh-post-card:hover {{CURRENT_ITEM}} img' => 'transform: scale({{SIZE}});' ],
                'condition' => ['element_type' => 'image'],
            ]
        );
        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();
        
        $repeater->add_control(
            'item_transition',
            [
                'label' => 'Transition (s)',
                'type' => Controls_Manager::SLIDER,
                'range' => [ 'px' => [ 'min' => 0, 'max' => 3, 'step' => 0.1 ] ],
                'default' => [ 'unit' => 'px', 'size' => 0.3 ],
                'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}}, {{WRAPPER}} {{CURRENT_ITEM}} *' => 'transition: all {{SIZE}}s ease;' ],
                'separator' => 'before',
            ]
        );

        $repeater->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'item_typography', 'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}, {{WRAPPER}} {{CURRENT_ITEM}} a', 'condition' => ['element_type!' => ['image', 'button']] ] );

        $repeater->add_responsive_control( 'btn_padding', [ 'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px'], 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .mh-post-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ], 'condition' => ['element_type' => 'button'] ] );
        
        $repeater->add_responsive_control( 'item_margin', [ 'label' => 'Margin', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ], 'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ], 'default' => [ 'top' => 0, 'right' => 0, 'bottom' => 10, 'left' => 0, 'unit' => 'px', 'isLinked' => false ], 'separator' => 'before' ] );

        $this->add_control(
            'card_elements',
            [
                'label' => esc_html__('Elements', 'mh-plug'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [ 'element_type' => 'image', 'thumbnail_size' => 'medium_large', 'item_margin' => ['bottom' => 15, 'unit' => 'px'], 'image_scale_hover' => ['size' => 1.05] ],
                    [ 'element_type' => 'category', 'element_width' => 'inline', 'item_text_color' => '#004265' ],
                    [ 'element_type' => 'date', 'element_width' => 'inline', 'meta_icon' => [ 'value' => 'far fa-calendar-alt', 'library' => 'regular' ] ],
                    [ 'element_type' => 'title', 'item_typography_font_size' => ['size' => 20, 'unit' => 'px'], 'item_text_color_hover' => '#004265' ],
                    [ 'element_type' => 'excerpt', 'item_text_color' => '#666666' ],
                    [ 'element_type' => 'button', 'btn_bg_color' => '#004265', 'btn_text_color' => '#ffffff', 'btn_bg_color_hover' => '#333333', 'item_margin' => ['top' => 'auto'] ],
                ],
                'title_field' => '{{{ element_type }}}',
            ]
        );
        
        $this->add_responsive_control(
            'content_align',
            [
                'label' => esc_html__('Global Alignment', 'mh-plug'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ],
                    'center' => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
                    'right' => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ],
                ],
                'default' => 'left',
                'prefix_class' => 'mh-align%s-',
            ]
        );

        $this->end_controls_section();

        // --- 3. SLIDER SETTINGS ---
        $this->start_controls_section( 'section_slider_settings', [ 'label' => 'Slider / Layout', 'tab' => Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'enable_slider', [ 'label' => 'Enable Slider', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_responsive_control( 'slides_to_show', [ 'label' => 'Columns', 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 6, 'default' => 3, 'tablet_default' => 2, 'mobile_default' => 1 ] );
        $this->add_control( 'autoplay', [ 'label' => 'Autoplay', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'enable_slider' => 'yes' ] ] );
        $this->add_control( 'autoplay_speed', [ 'label' => 'Speed (ms)', 'type' => Controls_Manager::NUMBER, 'default' => 3000, 'condition' => [ 'enable_slider' => 'yes', 'autoplay' => 'yes' ] ] );
        $this->add_control( 'show_arrows', [ 'label' => 'Arrows', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'enable_slider' => 'yes' ] ] );
        $this->add_control( 'arrow_prev_icon', [ 'label' => 'Prev Icon', 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'eicon-chevron-left', 'library' => 'eicons' ], 'condition' => [ 'enable_slider' => 'yes', 'show_arrows' => 'yes' ] ] );
        $this->add_control( 'arrow_next_icon', [ 'label' => 'Next Icon', 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'eicon-chevron-right', 'library' => 'eicons' ], 'condition' => [ 'enable_slider' => 'yes', 'show_arrows' => 'yes' ] ] );
        $this->add_control( 'show_dots', [ 'label' => 'Dots', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'enable_slider' => 'yes' ] ] );
        $this->add_responsive_control( 'grid_gap', [ 'label' => 'Gap', 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 20 ], 'selectors' => [ '{{WRAPPER}} .mh-post-grid' => 'gap: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-post-carousel-item' => 'padding: 0 calc({{SIZE}}{{UNIT}} / 2);', '{{WRAPPER}} .mh-post-carousel .slick-list' => 'margin: 0 calc(-{{SIZE}}{{UNIT}} / 2);' ] ] );
        $this->end_controls_section();

        // --- STYLES: CARD BOX ---
        $this->start_controls_section( 'section_style_card', [ 'label' => 'Card Box', 'tab' => Controls_Manager::TAB_STYLE ] );
        $this->add_responsive_control( 'box_padding', [ 'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'default' => [ 'top' => 0, 'right' => 20, 'bottom' => 20, 'left' => 20, 'unit' => 'px', 'isLinked' => false ], 'selectors' => [ '{{WRAPPER}} .mh-post-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', '{{WRAPPER}} .mh-post-element-image' => 'margin-left: -{{LEFT}}{{UNIT}}; margin-right: -{{RIGHT}}{{UNIT}}; width: calc(100% + {{LEFT}}{{UNIT}} + {{RIGHT}}{{UNIT}}); max-width: none;', '{{WRAPPER}} .mh-post-element-image:first-child' => 'margin-top: -{{TOP}}{{UNIT}};', '{{WRAPPER}} .mh-post-element-image:last-child' => 'margin-bottom: -{{BOTTOM}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'box_radius', [ 'label' => 'Radius', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => [ '{{WRAPPER}} .mh-post-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', '{{WRAPPER}} .mh-post-element-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;' ] ] );
        $this->start_controls_tabs( 'tabs_card_style' );
        $this->start_controls_tab( 'tab_card_normal', [ 'label' => 'Normal' ] );
        $this->add_control( 'box_bg_color', [ 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-post-card' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'box_border', 'selector' => '{{WRAPPER}} .mh-post-card' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'box_shadow', 'selector' => '{{WRAPPER}} .mh-post-card' ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_card_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'box_bg_color_hover', [ 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-post-card:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'box_border_hover', 'selector' => '{{WRAPPER}} .mh-post-card:hover' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'box_shadow_hover', 'selector' => '{{WRAPPER}} .mh-post-card:hover' ] );
        $this->add_control( 'hover_animation', [ 'label' => 'Hover Animation', 'type' => Controls_Manager::HOVER_ANIMATION ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

        // --- STYLES: NAVIGATION ---
        $this->start_controls_section( 'section_style_navigation', [ 'label' => 'Navigation', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'enable_slider' => 'yes' ] ] );
        $this->add_control( 'heading_style_arrows', [ 'label' => 'Arrows', 'type' => Controls_Manager::HEADING, 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->add_responsive_control( 'arrow_size', [ 'label' => 'Size', 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 10, 'max' => 100 ] ], 'default' => [ 'unit' => 'px', 'size' => 20 ], 'selectors' => [ '{{WRAPPER}} .mh-post-carousel .slick-arrow' => 'font-size: {{SIZE}}{{UNIT}};' ], 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->start_controls_tabs( 'tabs_arrow_style' );
        $this->start_controls_tab( 'tab_arrow_normal', [ 'label' => 'Normal', 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->add_control( 'arrow_color_normal', [ 'label' => 'Color', 'type' => Controls_Manager::COLOR, 'default' => '#333', 'selectors' => [ '{{WRAPPER}} .mh-post-carousel .slick-arrow' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'arrow_bg_color_normal', [ 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#fff', 'selectors' => [ '{{WRAPPER}} .mh-post-carousel .slick-arrow' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_arrow_hover', [ 'label' => 'Hover', 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->add_control( 'arrow_color_hover', [ 'label' => 'Color', 'type' => Controls_Manager::COLOR, 'default' => '#fff', 'selectors' => [ '{{WRAPPER}} .mh-post-carousel .slick-arrow:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'arrow_bg_color_hover', [ 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#004265', 'selectors' => [ '{{WRAPPER}} .mh-post-carousel .slick-arrow:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control( 'heading_style_dots', [ 'label' => 'Dots', 'type' => Controls_Manager::HEADING, 'condition' => [ 'show_dots' => 'yes' ], 'separator' => 'before' ] );
        $this->add_responsive_control( 'dots_size', [ 'label' => 'Size', 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 5, 'max' => 20 ] ], 'default' => [ 'unit' => 'px', 'size' => 12 ], 'selectors' => [ '{{WRAPPER}} .mh-post-carousel .slick-dots li button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-post-carousel .slick-dots li' => 'margin: 0 calc({{SIZE}}{{UNIT}} / 2);' ], 'condition' => [ 'show_dots' => 'yes' ] ] );
        $this->add_control( 'dots_color_normal', [ 'label' => 'Color', 'type' => Controls_Manager::COLOR, 'default' => '#cccccc', 'selectors' => [ '{{WRAPPER}} .mh-post-carousel .slick-dots li button' => 'border-color: {{VALUE}}; background-color: transparent;' ], 'condition' => [ 'show_dots' => 'yes' ] ] );
        $this->add_control( 'dots_color_active', [ 'label' => 'Active Color', 'type' => Controls_Manager::COLOR, 'default' => '#004265', 'selectors' => [ '{{WRAPPER}} .mh-post-carousel .slick-dots li.slick-active button' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ], 'condition' => [ 'show_dots' => 'yes' ] ] );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        
        $args = [
            'post_type'      => $settings['selected_post_type'],
            'posts_per_page' => $settings['posts_per_page'],
            'post_status'    => 'publish',
        ];
        $query = new \WP_Query($args);

        if (!$query->have_posts()) return;

        if ( 'yes' === $settings['enable_slider'] ) {
            $slick_options = [
                'slidesToShow' => (int)$settings['slides_to_show'],
                'slidesToScroll' => 1,
                'autoplay' => ($settings['autoplay'] === 'yes'),
                'autoplaySpeed' => (int)$settings['autoplay_speed'],
                'arrows' => ($settings['show_arrows'] === 'yes'),
                'dots' => ($settings['show_dots'] === 'yes'),
                'infinite' => true,
                'responsive' => [
                    [ 'breakpoint' => 1024, 'settings' => [ 'slidesToShow' => (int)($settings['slides_to_show_tablet'] ?: 2) ] ],
                    [ 'breakpoint' => 767, 'settings' => [ 'slidesToShow' => (int)($settings['slides_to_show_mobile'] ?: 1) ] ]
                ]
            ];

            if ($settings['show_arrows'] === 'yes') {
                ob_start();
                Icons_Manager::render_icon( $settings['arrow_prev_icon'], [ 'aria-hidden' => 'true' ] );
                $prev_icon_html = ob_get_clean();
                if(empty($prev_icon_html)) $prev_icon_html = '<i class="eicon-chevron-left"></i>';

                ob_start();
                Icons_Manager::render_icon( $settings['arrow_next_icon'], [ 'aria-hidden' => 'true' ] );
                $next_icon_html = ob_get_clean();
                if(empty($next_icon_html)) $next_icon_html = '<i class="eicon-chevron-right"></i>';

                $slick_options['prevArrow'] = '<button type="button" class="slick-prev">' . $prev_icon_html . '</button>';
                $slick_options['nextArrow'] = '<button type="button" class="slick-next">' . $next_icon_html . '</button>';
            }
            ?>
            <div class="mh-post-carousel-wrapper">
                <div class="mh-post-carousel" data-slick='<?php echo json_encode($slick_options); ?>'>
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <div class="mh-post-carousel-item">
                            <?php $this->render_post_card($settings); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <script>jQuery(document).ready(function($){var s=$('.elementor-element-<?php echo esc_attr($widget_id); ?> .mh-post-carousel');if(s.length&&$.fn.slick){s.slick(s.data('slick'));}});</script>
            </div>
            <?php
        } else {
            $grid_style = '--mh-grid-cols: ' . $settings['slides_to_show'] . ';';
            if ( ! empty( $settings['slides_to_show_tablet'] ) ) $grid_style .= ' --mh-grid-cols-tablet: ' . $settings['slides_to_show_tablet'] . ';';
            if ( ! empty( $settings['slides_to_show_mobile'] ) ) $grid_style .= ' --mh-grid-cols-mobile: ' . $settings['slides_to_show_mobile'] . ';';
            ?>
            <div class="mh-post-grid-wrapper" style="<?php echo esc_attr($grid_style); ?>">
                <div class="mh-post-grid">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <div class="mh-post-grid-item">
                            <?php $this->render_post_card($settings); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php
        }
        wp_reset_postdata();
    }

    protected function render_post_card($settings) {
        $card_class = 'mh-post-card';
        if ( ! empty( $settings['hover_animation'] ) ) {
            $card_class .= ' elementor-animation-' . $settings['hover_animation'];
        }

        echo '<div class="' . esc_attr($card_class) . '">';
        echo '<div class="mh-post-content">';

        if ( ! empty( $settings['card_elements'] ) ) {
            $elements = $settings['card_elements'];
            $count = count($elements);
            $in_inline_group = false;

            for ($i = 0; $i < $count; $i++) {
                $element = $elements[$i];
                $is_inline = ( isset($element['element_width']) && $element['element_width'] === 'inline' );

                if ( $is_inline && ! $in_inline_group ) {
                    echo '<div class="mh-inline-group">';
                    $in_inline_group = true;
                }
                if ( ! $is_inline && $in_inline_group ) {
                    echo '</div>'; 
                    $in_inline_group = false;
                }

                $this->render_single_element($element);

                if ( $is_inline && $in_inline_group && $i === $count - 1 ) {
                    echo '</div>';
                }
            }
        }

        echo '</div></div>';
    }

    protected function render_single_element($element) {
        $repeater_id_class = 'elementor-repeater-item-' . $element['_id'];
        $type_class = 'mh-post-element-' . $element['element_type'];
        $wrapper_class = $repeater_id_class . ' ' . $type_class;

        switch ( $element['element_type'] ) {
            case 'image':
                if ( has_post_thumbnail() ) {
                    $thumb_size = isset($element['thumbnail_size']) ? $element['thumbnail_size'] : 'medium_large';
                    echo '<div class="' . esc_attr($wrapper_class) . '">';
                    echo '<a href="' . get_permalink() . '">';
                    the_post_thumbnail( $thumb_size );
                    echo '</a>';
                    echo '</div>';
                }
                break;
            case 'title':
                echo '<h3 class="' . esc_attr($wrapper_class) . '"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
                break;
            case 'excerpt':
                echo '<div class="' . esc_attr($wrapper_class) . '">' . wp_trim_words( get_the_excerpt(), isset($element['excerpt_length']) ? $element['excerpt_length'] : 15 ) . '</div>';
                break;
            case 'button':
                echo '<div class="' . esc_attr($wrapper_class) . ' mh-post-button-wrapper">';
                echo '<a href="' . get_permalink() . '" class="mh-post-button">' . esc_html( isset($element['button_text']) ? $element['button_text'] : 'Read More' ) . '</a>';
                echo '</div>';
                break;
            case 'date':
                $this->render_meta_item( $element, get_the_date(), $wrapper_class );
                break;
            case 'author':
                $this->render_meta_item( $element, get_the_author(), $wrapper_class );
                break;
            case 'category':
                $cats = get_the_category_list( ', ' );
                if ( $cats ) $this->render_meta_item( $element, $cats, $wrapper_class );
                break;
            case 'tags':
                $tags = get_the_tag_list( '', ', ' );
                if ( $tags ) $this->render_meta_item( $element, $tags, $wrapper_class );
                break;
        }
    }

    protected function render_meta_item( $element, $content, $wrapper_class ) {
        echo '<div class="mh-post-meta-item ' . esc_attr($wrapper_class) . '">';
        if ( ! empty( $element['meta_icon']['value'] ) ) {
            Icons_Manager::render_icon( $element['meta_icon'], [ 'aria-hidden' => 'true', 'class' => 'mh-meta-icon' ] );
        }
        echo '<span class="mh-meta-text">' . $content . '</span>';
        echo '</div>';
    }
}