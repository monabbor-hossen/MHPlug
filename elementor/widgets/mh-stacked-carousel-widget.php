<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;

class MH_Stacked_Carousel_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'mh_stacked_carousel'; }
    public function get_title()      { return __( 'MH Stacked Carousel', 'mh-plug' ); }
    public function get_icon()       { return 'eicon-post-slider'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_script_depends() { return [ 'slick-js' ]; }

    protected function register_controls() {

        /* ============================================
         * CONTENT TAB — Slides
         * ============================================ */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Slides', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new Repeater();

        $repeater->add_control( 'image', [
            'label'   => __( 'Background Image', 'mh-plug' ),
            'type'    => Controls_Manager::MEDIA,
            'default' => [ 'url' => Utils::get_placeholder_image_src() ],
        ]);
        $repeater->add_control( 'title', [
            'label'       => __( 'Title', 'mh-plug' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Slide Title', 'mh-plug' ),
            'label_block' => true,
        ]);
        $repeater->add_control( 'subtitle', [
            'label'       => __( 'Subtitle', 'mh-plug' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'CATEGORY', 'mh-plug' ),
            'label_block' => true,
        ]);
        $repeater->add_control( 'description', [
            'label'   => __( 'Description', 'mh-plug' ),
            'type'    => Controls_Manager::TEXTAREA,
            'default' => __( 'A short description for this slide.', 'mh-plug' ),
        ]);
        $repeater->add_control( 'button_text', [
            'label'   => __( 'Button Text', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Learn More', 'mh-plug' ),
        ]);
        $repeater->add_control( 'button_link', [
            'label'       => __( 'Button Link', 'mh-plug' ),
            'type'        => Controls_Manager::URL,
            'placeholder' => 'https://your-link.com',
        ]);

        $this->add_control( 'slides', [
            'label'       => __( 'Slides', 'mh-plug' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'title' => 'Digital Dreams',  'subtitle' => 'TECHNOLOGY', 'description' => 'Explore the future of innovation.' ],
                [ 'title' => 'Bold Impact',     'subtitle' => 'DESIGN',     'description' => 'Make a statement that lasts.' ],
                [ 'title' => 'Clear Vision',    'subtitle' => 'LIFESTYLE',  'description' => 'See the world differently.' ],
                [ 'title' => 'Pure Motion',     'subtitle' => 'SPORTS',     'description' => 'Push beyond your limits.' ],
            ],
            'title_field' => '{{{ title }}}',
        ]);

        $this->end_controls_section();

        /* ============================================
         * CONTENT TAB — Display Toggles
         * ============================================ */
        $this->start_controls_section( 'section_display', [
            'label' => __( 'Display Options', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'show_subtitle', [
            'label'   => __( 'Show Subtitle', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $this->add_control( 'show_title', [
            'label'   => __( 'Show Title', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $this->add_control( 'show_description', [
            'label'   => __( 'Show Description', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $this->add_control( 'show_button', [
            'label'   => __( 'Show Button', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        /* ============================================
         * CONTENT TAB — Slider Settings
         * ============================================ */
        $this->start_controls_section( 'section_slider_settings', [
            'label' => __( 'Slider Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'slide_mode', [
            'label'   => __( 'Slide Mode', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'stacked',
            'options' => [
                'stacked'  => __( 'Stacked (Center Focus)', 'mh-plug' ),
                'regular'  => __( 'Regular Slide', 'mh-plug' ),
                'fade'     => __( 'Fade', 'mh-plug' ),
                'vertical' => __( 'Vertical (Top to Bottom)', 'mh-plug' ),
            ],
        ]);

        $this->add_responsive_control( 'slides_to_show', [
            'label'   => __( 'Slides to Show', 'mh-plug' ),
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 8,
            'default' => 3,
            'devices' => [ 'desktop', 'tablet', 'mobile' ],
            'desktop_default' => 3,
            'tablet_default'  => 2,
            'mobile_default'  => 1,
            'condition' => [ 'slide_mode' => [ 'regular', 'vertical' ] ],
        ]);

        $this->add_control( 'slides_to_scroll', [
            'label'   => __( 'Slides to Scroll', 'mh-plug' ),
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 8,
            'default' => 1,
            'condition' => [ 'slide_mode' => [ 'regular', 'vertical' ] ],
        ]);

        $this->add_control( 'slide_gap', [
            'label'   => __( 'Gap Between Slides (px)', 'mh-plug' ),
            'type'    => Controls_Manager::NUMBER,
            'min'     => 0,
            'max'     => 80,
            'default' => 12,
            'selectors' => [ '{{WRAPPER}} .mh-stacked-item' => 'margin: 0 {{VALUE}}px;' ],
        ]);

        $this->add_control( 'infinite_loop', [
            'label'   => __( 'Infinite Loop', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control( 'show_arrows', [
            'label'   => __( 'Show Arrows', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => '',
        ]);

        $this->add_control( 'show_dots', [
            'label'   => __( 'Show Dots', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => '',
        ]);

        $this->add_control( 'autoplay', [
            'label'   => __( 'Autoplay', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => '',
        ]);
        $this->add_control( 'autoplay_speed', [
            'label'     => __( 'Autoplay Speed (ms)', 'mh-plug' ),
            'type'      => Controls_Manager::NUMBER,
            'default'   => 3000,
            'condition' => [ 'autoplay' => 'yes' ],
        ]);
        $this->add_control( 'transition_speed', [
            'label'   => __( 'Transition Speed (ms)', 'mh-plug' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 800,
        ]);

        $this->end_controls_section();

        /* ============================================
         * STYLE TAB — Card
         * ============================================ */
        $this->start_controls_section( 'section_style_card', [
            'label' => __( 'Card', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control( 'card_height', [
            'label'   => __( 'Height', 'mh-plug' ),
            'type'    => Controls_Manager::SLIDER,
            'range'   => [ 'px' => [ 'min' => 200, 'max' => 700 ] ],
            'default' => [ 'unit' => 'px', 'size' => 420 ],
            'selectors' => [ '{{WRAPPER}} .mh-stacked-item' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->add_responsive_control( 'card_width', [
            'label'   => __( 'Width', 'mh-plug' ),
            'type'    => Controls_Manager::SLIDER,
            'range'   => [ 'px' => [ 'min' => 200, 'max' => 700 ] ],
            'default' => [ 'unit' => 'px', 'size' => 360 ],
            'selectors' => [ '{{WRAPPER}} .mh-stacked-item' => 'width: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->add_control( 'card_border_radius', [
            'label'   => __( 'Border Radius', 'mh-plug' ),
            'type'    => Controls_Manager::SLIDER,
            'range'   => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default' => [ 'unit' => 'px', 'size' => 16 ],
            'selectors' => [ '{{WRAPPER}} .mh-stacked-item' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->add_control( 'overlay_color', [
            'label'   => __( 'Overlay Color', 'mh-plug' ),
            'type'    => Controls_Manager::COLOR,
            'default' => 'rgba(0,0,0,0.35)',
            'selectors' => [ '{{WRAPPER}} .mh-stacked-wrap .mh-stacked-overlay' => 'background: {{VALUE}} !important;' ],
        ]);
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'card_box_shadow',
            'selector' => '{{WRAPPER}} .mh-stacked-item',
        ]);
        $this->add_responsive_control( 'content_padding', [
            'label'      => __( 'Content Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-stacked-wrap .mh-stacked-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;' ],
        ]);
        $this->add_control( 'content_valign', [
            'label'   => __( 'Vertical Position', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'flex-end',
            'options' => [
                'flex-start' => __( 'Top', 'mh-plug' ),
                'center'     => __( 'Center', 'mh-plug' ),
                'flex-end'   => __( 'Bottom', 'mh-plug' ),
            ],
            'selectors' => [ '{{WRAPPER}} .mh-stacked-wrap .mh-stacked-content' => 'justify-content: {{VALUE}} !important;' ],
        ]);

        $this->add_control( 'content_halign', [
            'label'   => __( 'Horizontal Alignment', 'mh-plug' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [ 'title' => __( 'Left', 'mh-plug' ),   'icon' => 'eicon-text-align-left' ],
                'center'     => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'flex-end'   => [ 'title' => __( 'Right', 'mh-plug' ),  'icon' => 'eicon-text-align-right' ],
            ],
            'default'   => 'flex-start',
            'selectors' => [ '{{WRAPPER}} .mh-stacked-wrap .mh-stacked-content' => 'align-items: {{VALUE}} !important;' ],
        ]);

        $this->add_control( 'text_align', [
            'label'   => __( 'Text Align', 'mh-plug' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'left'   => [ 'title' => __( 'Left', 'mh-plug' ),   'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'mh-plug' ),  'icon' => 'eicon-text-align-right' ],
            ],
            'default'   => 'left',
            'selectors' => [ '{{WRAPPER}} .mh-stacked-wrap .mh-stacked-content' => 'text-align: {{VALUE}} !important;' ],
        ]);

        $this->end_controls_section();

        /* ============================================
         * STYLE TAB — Image
         * ============================================ */
        $this->start_controls_section( 'section_style_image', [
            'label' => __( 'Image', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'image_fit', [
            'label'   => __( 'Object Fit', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'cover',
            'options' => [
                'cover'   => __( 'Cover', 'mh-plug' ),
                'contain' => __( 'Contain', 'mh-plug' ),
                'fill'    => __( 'Fill (Stretch)', 'mh-plug' ),
            ],
            'selectors' => [ '{{WRAPPER}} .mh-stacked-item' => 'background-size: {{VALUE}};' ],
        ]);

        $this->add_control( 'image_position', [
            'label'   => __( 'Position', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'center center',
            'options' => [
                'center center' => __( 'Center', 'mh-plug' ),
                'top center'    => __( 'Top', 'mh-plug' ),
                'bottom center' => __( 'Bottom', 'mh-plug' ),
                'center left'   => __( 'Left', 'mh-plug' ),
                'center right'  => __( 'Right', 'mh-plug' ),
            ],
            'selectors' => [ '{{WRAPPER}} .mh-stacked-item' => 'background-position: {{VALUE}};' ],
        ]);

        $this->add_control( 'image_hover_zoom', [
            'label'   => __( 'Hover Zoom', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => '',
            'description' => __( 'Subtle zoom effect on hover.', 'mh-plug' ),
        ]);

        $this->add_control( 'image_hover_zoom_scale', [
            'label'   => __( 'Zoom Scale', 'mh-plug' ),
            'type'    => Controls_Manager::SLIDER,
            'range'   => [ 'px' => [ 'min' => 1, 'max' => 1.5, 'step' => 0.05 ] ],
            'default' => [ 'unit' => 'px', 'size' => 1.1 ],
            'condition' => [ 'image_hover_zoom' => 'yes' ],
        ]);

        $this->end_controls_section();

        /* ============================================
         * STYLE TAB — Subtitle
         * ============================================ */
        $this->start_controls_section( 'section_style_subtitle', [
            'label'     => __( 'Subtitle', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_subtitle' => 'yes' ],
        ]);
        $this->add_control( 'subtitle_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(255,255,255,0.75)',
            'selectors' => [ '{{WRAPPER}} .mh-stacked-subtitle' => 'color: {{VALUE}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'subtitle_typo',
            'selector' => '{{WRAPPER}} .mh-stacked-subtitle',
        ]);
        $this->add_responsive_control( 'subtitle_spacing', [
            'label'     => __( 'Bottom Spacing', 'mh-plug' ),
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'   => [ 'unit' => 'px', 'size' => 6 ],
            'selectors' => [ '{{WRAPPER}} .mh-stacked-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();

        /* ============================================
         * STYLE TAB — Title
         * ============================================ */
        $this->start_controls_section( 'section_style_title', [
            'label'     => __( 'Title', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_title' => 'yes' ],
        ]);
        $this->add_control( 'title_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-stacked-title' => 'color: {{VALUE}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'title_typo',
            'selector' => '{{WRAPPER}} .mh-stacked-title',
        ]);
        $this->add_group_control( Group_Control_Text_Shadow::get_type(), [
            'name'     => 'title_shadow',
            'selector' => '{{WRAPPER}} .mh-stacked-title',
        ]);
        $this->add_responsive_control( 'title_spacing', [
            'label'     => __( 'Bottom Spacing', 'mh-plug' ),
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'   => [ 'unit' => 'px', 'size' => 8 ],
            'selectors' => [ '{{WRAPPER}} .mh-stacked-title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();

        /* ============================================
         * STYLE TAB — Description
         * ============================================ */
        $this->start_controls_section( 'section_style_desc', [
            'label'     => __( 'Description', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_description' => 'yes' ],
        ]);
        $this->add_control( 'desc_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(255,255,255,0.8)',
            'selectors' => [ '{{WRAPPER}} .mh-stacked-desc' => 'color: {{VALUE}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'desc_typo',
            'selector' => '{{WRAPPER}} .mh-stacked-desc',
        ]);
        $this->add_responsive_control( 'desc_spacing', [
            'label'     => __( 'Bottom Spacing', 'mh-plug' ),
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'   => [ 'unit' => 'px', 'size' => 14 ],
            'selectors' => [ '{{WRAPPER}} .mh-stacked-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();

        /* ============================================
         * STYLE TAB — Button
         * ============================================ */
        $this->start_controls_section( 'section_style_button', [
            'label'     => __( 'Button', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_button' => 'yes' ],
        ]);

        $this->start_controls_tabs( 'btn_tabs' );

        $this->start_controls_tab( 'btn_tab_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'btn_bg', [
            'label'     => __( 'Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-stacked-btn' => 'background: {{VALUE}};' ],
        ]);
        $this->add_control( 'btn_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#111111',
            'selectors' => [ '{{WRAPPER}} .mh-stacked-btn' => 'color: {{VALUE}};' ],
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab( 'btn_tab_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'btn_bg_hover', [
            'label'     => __( 'Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-stacked-btn:hover' => 'background: {{VALUE}};' ],
        ]);
        $this->add_control( 'btn_color_hover', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-stacked-btn:hover' => 'color: {{VALUE}};' ],
        ]);
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'      => 'btn_typo',
            'selector'  => '{{WRAPPER}} .mh-stacked-btn',
            'separator' => 'before',
        ]);
        $this->add_control( 'btn_border_radius', [
            'label'   => __( 'Border Radius', 'mh-plug' ),
            'type'    => Controls_Manager::SLIDER,
            'range'   => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default' => [ 'unit' => 'px', 'size' => 8 ],
            'selectors' => [ '{{WRAPPER}} .mh-stacked-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->add_responsive_control( 'btn_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '10', 'right' => '24', 'bottom' => '10', 'left' => '24', 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-stacked-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s  = $this->get_settings_for_display();
        $id = 'mh-sc-' . $this->get_id();

        if ( empty( $s['slides'] ) ) return;

        $mode     = $s['slide_mode'] ?? 'stacked';
        $autoplay = ( $s['autoplay'] ?? '' ) === 'yes';
        $speed    = intval( $s['transition_speed'] ?? 800 );
        $apSpeed  = intval( $s['autoplay_speed'] ?? 3000 );

        // Hover zoom class
        $zoom_class = '';
        if ( ( $s['image_hover_zoom'] ?? '' ) === 'yes' ) {
            $scale = floatval( $s['image_hover_zoom_scale']['size'] ?? 1.1 );
            $zoom_class = ' mh-sc-hover-zoom';
        }

        // Slides to show (with proper fallback)
        $show_d = intval( $s['slides_to_show'] ?? 3 );
        $show_t = intval( $s['slides_to_show_tablet'] ?? max( 2, $show_d - 1 ) );
        $show_m = intval( $s['slides_to_show_mobile'] ?? 1 );
        ?>
        <?php
        $css = "
        /* Critical structural styles for this widget instance */
        #" . esc_attr( $id ) . " .mh-stacked-item {
            position: relative;
            overflow: hidden;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
        }
        #" . esc_attr( $id ) . " .slick-slide,
        #" . esc_attr( $id ) . " .slick-slide > div {
            height: auto !important;
        }
        #" . esc_attr( $id ) . " .slick-slide > div {
            display: flex !important;
        }
        #" . esc_attr( $id ) . " .mh-stacked-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1;
            pointer-events: none;
            border-radius: inherit;
        }
        #" . esc_attr( $id ) . " .mh-stacked-content {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            display: flex !important;
            flex-direction: column;
            z-index: 2;
            color: #fff;
            box-sizing: border-box;
        }
        ";
        if ( ( $s['image_hover_zoom'] ?? '' ) === 'yes' ) {
            $zoom_val = intval( ( $s['image_hover_zoom_scale']['size'] ?? 1.1 ) * 100 );
            $css .= "
            #" . esc_attr( $id ) . " .mh-stacked-item:hover {
                background-size: " . $zoom_val . "% !important;
                transition: background-size 0.4s ease;
            }
            ";
        }
        echo "<style>
" . $css . "
</style>";
        ?>

        <div class="mh-stacked-wrap mh-sc-mode-<?php echo esc_attr( $mode ); ?><?php echo esc_attr( $zoom_class ); ?>">
            <div class="mh-stacked-slider" id="<?php echo esc_attr( $id ); ?>">
                <?php foreach ( $s['slides'] as $slide ) :
                    $img  = ! empty( $slide['image']['url'] ) ? $slide['image']['url'] : '';
                    $link = ! empty( $slide['button_link']['url'] ) ? $slide['button_link']['url'] : '#';
                    $tgt  = ! empty( $slide['button_link']['is_external'] ) ? ' target="_blank"' : '';
                    $rel  = ! empty( $slide['button_link']['nofollow'] ) ? ' rel="nofollow"' : '';
                ?>
                <div class="mh-stacked-item" <?php if ( $img ) echo 'style="background-image:url(' . esc_url( $img ) . ')"'; ?>>
                    <div class="mh-stacked-overlay"></div>
                    <div class="mh-stacked-content">
                        <?php if ( ( $s['show_subtitle'] ?? '' ) === 'yes' && ! empty( $slide['subtitle'] ) ) : ?>
                            <div class="mh-stacked-subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></div>
                        <?php endif; ?>
                        <?php if ( ( $s['show_title'] ?? '' ) === 'yes' && ! empty( $slide['title'] ) ) : ?>
                            <h3 class="mh-stacked-title"><?php echo esc_html( $slide['title'] ); ?></h3>
                        <?php endif; ?>
                        <?php if ( ( $s['show_description'] ?? '' ) === 'yes' && ! empty( $slide['description'] ) ) : ?>
                            <p class="mh-stacked-desc"><?php echo esc_html( $slide['description'] ); ?></p>
                        <?php endif; ?>
                        <?php if ( ( $s['show_button'] ?? '' ) === 'yes' && ! empty( $slide['button_text'] ) ) : ?>
                            <a class="mh-stacked-btn" href="<?php echo esc_url( $link ); ?>"<?php echo $tgt . $rel; ?>><?php echo esc_html( $slide['button_text'] ); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php
        $js = "
        jQuery(document).ready(function($){
            var \$el = $('#" . esc_js( $id ) . "');
            if ( !\$el.length || typeof $.fn.slick !== 'function' ) return;

            var mode = '" . esc_js( $mode ) . "';
            var isStacked  = mode === 'stacked';
            var isFade     = mode === 'fade';
            var isVertical = mode === 'vertical';

            var showDesktop = " . $show_d . ";
            var showTablet  = " . $show_t . ";

            var opts = {
                vertical:       isVertical,
                verticalSwiping:isVertical,
                centerMode:     isStacked,
                centerPadding:  isStacked ? '60px' : '0px',
                variableWidth:  false,
                fade:           isFade,
                slidesToShow:   (isStacked || isFade) ? 1 : showDesktop,
                slidesToScroll: " . intval( $s['slides_to_scroll'] ?? 1 ) . ",
                arrows:         " . ( ( ( $s['show_arrows'] ?? '' ) === 'yes' ) ? 'true' : 'false' ) . ",
                dots:           " . ( ( ( $s['show_dots'] ?? '' ) === 'yes' ) ? 'true' : 'false' ) . ",
                infinite:       " . ( ( ( $s['infinite_loop'] ?? 'yes' ) === 'yes' ) ? 'true' : 'false' ) . ",
                focusOnSelect:  isStacked,
                autoplay:       " . ( $autoplay ? 'true' : 'false' ) . ",
                autoplaySpeed:  " . $apSpeed . ",
                speed:          " . $speed . ",
                cssEase:        isFade ? 'linear' : 'cubic-bezier(0.25,1,0.5,1)',
                responsive: [
                    { breakpoint: 1025, settings: { slidesToShow: (isStacked || isFade) ? 1 : showTablet, centerPadding: isStacked ? '30px' : '0px' } },
                    { breakpoint: 768,  settings: { slidesToShow: 1, centerPadding: isStacked ? '20px' : '0px', vertical: false, verticalSwiping: false } }
                ]
            };

            \$el.slick(opts);

            if ( isStacked ) {
                function depth(){
                    \$el.find('.slick-slide').removeClass('prev next');
                    var \$c = \$el.find('.slick-center');
                    \$c.prev('.slick-slide').addClass('prev');
                    \$c.next('.slick-slide').addClass('next');
                }
                \$el.on('afterChange', depth);
                setTimeout(depth, 200);
            }
        });
        ";
        echo "<script type='text/javascript'>\n" . $js . "\n</script>";
            }
}
