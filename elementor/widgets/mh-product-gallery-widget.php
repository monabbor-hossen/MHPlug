<?php
/**
 * MH Product Gallery Slider Widget
 * Includes Premium Layout, Native Elementor Lightbox Integration & Structural Slick Fixes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class MH_Product_Gallery_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'mh_product_gallery'; }
    public function get_title() { return __( 'MH Product Gallery', 'mh-plug-ecommerce-builder-widgets' ); }
    public function get_icon() { return 'eicon-product-gallery'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    public function get_style_depends() { return [ 'mh-widgets-css' ]; }
    
    // 🚀 THE FIX: This forces Elementor to load the Slick Slider engine for this widget
    public function get_script_depends() { return [ 'slick-js' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'section_layout', [
            'label' => __( 'Gallery Settings', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'gallery_width', [
            'label'      => __( 'Gallery Max Width', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-premium-gallery-container' => 'max-width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
            ],
        ] );

        $this->add_control( 'enable_lightbox', [
            'label'        => __( 'Enable Image Lightbox', 'mh-plug-ecommerce-builder-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'mh-plug-ecommerce-builder-widgets' ),
            'label_off'    => __( 'No', 'mh-plug-ecommerce-builder-widgets' ),
            'return_value' => 'yes',
            'default'      => 'yes',
            'separator'    => 'before',
            'description'  => __( 'Clicking the main image will open it in a fullscreen popup.', 'mh-plug-ecommerce-builder-widgets' ),
        ] );

        $this->end_controls_section();

        // Style Tab -> Main Image
        $this->start_controls_section( 'section_style_main_image', [
            'label' => __( 'Main Image', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'main_image_gap', [
            'label' => __( 'Gap Below Image', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem' ],
            'range' => [
                'px' => [ 'min' => 0, 'max' => 100 ],
            ],
            'selectors' => [
                '{{WRAPPER}} .mh-main-slider-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'main_image_border_radius', [
            'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .mh-main-slider-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'main_image_border',
                'selector' => '{{WRAPPER}} .mh-main-slider-wrapper',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'main_image_box_shadow',
                'selector' => '{{WRAPPER}} .mh-main-slider-wrapper',
            ]
        );

        $this->end_controls_section();

        // Style Tab -> Thumbnails
        $this->start_controls_section( 'section_style_thumbnails', [
            'label' => __( 'Thumbnails', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'thumbnail_spacing', [
            'label' => __( 'Spacing', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range' => [
                'px' => [ 'min' => 0, 'max' => 50 ],
            ],
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-thumb-slider .slick-slide' => 'padding: 0 {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-gallery-thumb-slider:not(.slick-initialized)' => 'gap: calc({{SIZE}}{{UNIT}} * 2);',
            ],
        ] );

        $this->add_control( 'thumbnail_border_radius', [
            'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-thumb-slider .slick-slide img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->start_controls_tabs( 'tabs_thumbnail_style' );

        // Normal Tab
        $this->start_controls_tab(
            'tab_thumb_normal',
            [
                'label' => __( 'Normal', 'mh-plug-ecommerce-builder-widgets' ),
            ]
        );

        $this->add_control( 'thumb_opacity', [
            'label' => __( 'Opacity', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [ 'max' => 1, 'min' => 0.10, 'step' => 0.01 ],
            ],
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-thumb-slider .slick-slide img' => 'opacity: {{SIZE}};',
            ],
        ] );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'thumb_border',
                'selector' => '{{WRAPPER}} .mh-gallery-thumb-slider .slick-slide img',
            ]
        );

        $this->end_controls_tab();

        // Active/Hover Tab
        $this->start_controls_tab(
            'tab_thumb_active',
            [
                'label' => __( 'Active / Hover', 'mh-plug-ecommerce-builder-widgets' ),
            ]
        );

        $this->add_control( 'thumb_opacity_active', [
            'label' => __( 'Opacity', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [ 'max' => 1, 'min' => 0.10, 'step' => 0.01 ],
            ],
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-thumb-slider .slick-current img, {{WRAPPER}} .mh-gallery-thumb-slider .slick-slide:hover img' => 'opacity: {{SIZE}};',
            ],
        ] );

        $this->add_control( 'thumb_border_color_active', [
            'label' => __( 'Border Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-thumb-slider .slick-current img, {{WRAPPER}} .mh-gallery-thumb-slider .slick-slide:hover img' => 'border-color: {{VALUE}};',
            ],
        ] );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // Style Tab -> Main Arrows
        $this->start_controls_section( 'section_style_main_arrows', [
            'label' => __( 'Main Arrows', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control(
            'main_arrow_size',
            [
                'label' => __( 'Icon Size', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .mh-gallery-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'main_arrow_box_size',
            [
                'label' => __( 'Box Size', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .mh-gallery-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'main_arrow_offset',
            [
                'label' => __( 'Position Offset', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em' ],
                'range' => [
                    'px' => [ 'min' => -50, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mh-main-prev' => 'left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mh-main-next' => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_main_arrow_style' );

        $this->start_controls_tab(
            'tab_main_arrow_normal',
            [
                'label' => __( 'Normal', 'mh-plug-ecommerce-builder-widgets' ),
            ]
        );

        $this->add_control(
            'main_arrow_color',
            [
                'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-gallery-arrow' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'main_arrow_bg_color',
            [
                'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-gallery-arrow' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'main_arrow_border',
                'selector' => '{{WRAPPER}} .mh-gallery-arrow',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'main_arrow_box_shadow',
                'selector' => '{{WRAPPER}} .mh-gallery-arrow',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_main_arrow_hover',
            [
                'label' => __( 'Hover', 'mh-plug-ecommerce-builder-widgets' ),
            ]
        );

        $this->add_control(
            'main_arrow_hover_color',
            [
                'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-gallery-arrow:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'main_arrow_hover_bg_color',
            [
                'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-gallery-arrow:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'main_arrow_hover_border_color',
            [
                'label' => __( 'Border Color', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-gallery-arrow:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'main_arrow_border_radius',
            [
                'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .mh-gallery-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        // Style Tab -> Thumbnail Arrows
        $this->start_controls_section( 'section_style_thumb_arrows', [
            'label' => __( 'Thumbnail Arrows', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control(
            'thumb_arrow_size',
            [
                'label' => __( 'Icon Size', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .mh-thumb-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumb_arrow_box_size',
            [
                'label' => __( 'Box Size', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .mh-thumb-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumb_arrow_offset',
            [
                'label' => __( 'Position Offset', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em' ],
                'range' => [
                    'px' => [ 'min' => -50, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mh-thumb-prev' => 'left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mh-thumb-next' => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_thumb_arrow_style' );

        $this->start_controls_tab(
            'tab_thumb_arrow_normal',
            [
                'label' => __( 'Normal', 'mh-plug-ecommerce-builder-widgets' ),
            ]
        );

        $this->add_control(
            'thumb_arrow_color',
            [
                'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-thumb-arrow' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'thumb_arrow_bg_color',
            [
                'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-thumb-arrow' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'thumb_arrow_border',
                'selector' => '{{WRAPPER}} .mh-thumb-arrow',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'thumb_arrow_box_shadow',
                'selector' => '{{WRAPPER}} .mh-thumb-arrow',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_thumb_arrow_hover',
            [
                'label' => __( 'Hover', 'mh-plug-ecommerce-builder-widgets' ),
            ]
        );

        $this->add_control(
            'thumb_arrow_hover_color',
            [
                'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-thumb-arrow:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'thumb_arrow_hover_bg_color',
            [
                'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-thumb-arrow:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'thumb_arrow_hover_border_color',
            [
                'label' => __( 'Border Color', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-thumb-arrow:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'thumb_arrow_border_radius',
            [
                'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .mh-thumb-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $enable_lightbox = $settings['enable_lightbox'] === 'yes';
        $widget_id = $this->get_id(); // Unique ID for Lightbox and Slick targeting

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 10px; color: #d63638; text-align: center;">Please assign a product to preview the gallery.</div>';
            }
            return;
        }

        $main_image_id = $product->get_image_id();
        $gallery_ids   = $product->get_gallery_image_ids();
        if ( ! is_array( $gallery_ids ) ) $gallery_ids = [];
        
        if ( ! $main_image_id && empty( $gallery_ids ) ) return; 

        $all_image_ids = array_merge( [ $main_image_id ], $gallery_ids );
        ?>

        <?php
        $css = "
            .mh-premium-gallery-container { display: block; width: 100%; position: relative; }
            
            /* Main Slider Styles */
            .mh-main-slider-wrapper { position: relative; margin-bottom: 10px; border-radius: 8px; overflow: hidden; }
            .mh-gallery-main-viewport { display: block; width: 100%; }
            .mh-gallery-main-viewport:not(.slick-initialized) .mh-main-slide-item:not(:first-child) { display: none; } /* Hide all but first image before load */
            .mh-gallery-main-viewport .slick-slide img { width: 100%; height: auto; display: block; object-fit: cover; }
            
            /* Thumbnail Slider Styles */
            .mh-thumb-slider-wrapper { position: relative; padding: 0 15px; }
            .mh-gallery-thumb-slider { display: block; width: 100%; }
            .mh-gallery-thumb-slider:not(.slick-initialized) { display: flex; overflow: hidden; gap: 10px; } /* Prevents giant stacking before load */
            .mh-gallery-thumb-slider:not(.slick-initialized) .mh-thumb-slide-item { width: 25%; flex-shrink: 0; }
            .mh-gallery-thumb-slider .slick-slide { padding: 0 5px; cursor: pointer; outline: none; }
            .mh-gallery-thumb-slider .slick-slide img { width: 100%; height: auto; display: block; border-radius: 6px; opacity: 0.5; transition: 0.3s; border: 2px solid transparent; }
            .mh-gallery-thumb-slider .slick-current img, 
            .mh-gallery-thumb-slider .slick-slide:hover img { opacity: 1; border-color: #2293e9; }
            
            /* Arrow Controls */
            .mh-gallery-arrow, .mh-thumb-arrow { position: absolute; top: 50%; transform: translateY(-50%); z-index: 10; background: #fff; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: 0.3s; }
            .mh-gallery-arrow:hover, .mh-thumb-arrow:hover { background: #2293e9; color: #fff; }
            .mh-main-prev { left: 10px; } .mh-main-next { right: 10px; }
            .mh-thumb-prev { left: -10px; width: 25px; height: 25px; } .mh-thumb-next { right: -10px; width: 25px; height: 25px; }
            .mh-gallery-arrow i, .mh-thumb-arrow i { line-height: 1; }
        ";
        wp_register_style( 'mh-product-gallery-style', false );
        wp_enqueue_style( 'mh-product-gallery-style' );
        wp_add_inline_style( 'mh-product-gallery-style', $css );
        ?>

        <div class="mh-premium-gallery-container" id="mh-gallery-container-<?php echo esc_attr( $widget_id ); ?>">
            
            <div class="mh-main-slider-wrapper">
                <div class="mh-gallery-main-viewport" id="mh-main-gallery-<?php echo esc_attr( $widget_id ); ?>">
                    <?php foreach ( $all_image_ids as $attachment_id ) : 
                        $full_image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
                    ?>
                        <div class="mh-main-slide-item">
                            <?php if ( $enable_lightbox && $full_image_url ) : ?>
                                <a href="<?php echo esc_url( $full_image_url ); ?>" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="gallery-<?php echo esc_attr( $widget_id ); ?>">
                                    <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_single' ); ?>
                                </a>
                            <?php else : ?>
                                <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_single' ); ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ( count( $all_image_ids ) > 1 ) : ?>
                    <div class="mh-gallery-arrow mh-main-prev" id="mh-main-prev-<?php echo esc_attr( $widget_id ); ?>"><i class="eicon-chevron-left"></i></div>
                    <div class="mh-gallery-arrow mh-main-next" id="mh-main-next-<?php echo esc_attr( $widget_id ); ?>"><i class="eicon-chevron-right"></i></div>
                <?php endif; ?>
            </div>

            <?php if ( count( $all_image_ids ) > 1 ) : ?>
                <div class="mh-thumb-slider-wrapper">
                    <div class="mh-gallery-thumb-slider" id="mh-thumb-gallery-<?php echo esc_attr( $widget_id ); ?>">
                        <?php foreach ( $all_image_ids as $attachment_id ) : ?>
                            <div class="mh-thumb-slide-item">
                                <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_thumbnail' ); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mh-thumb-arrow mh-thumb-prev" id="mh-thumb-prev-<?php echo esc_attr( $widget_id ); ?>"><i class="eicon-chevron-left"></i></div>
                    <div class="mh-thumb-arrow mh-thumb-next" id="mh-thumb-next-<?php echo esc_attr( $widget_id ); ?>"><i class="eicon-chevron-right"></i></div>
                </div>
            <?php endif; ?>
            
        </div>

        <?php
        $js = "
        jQuery(document).ready(function($) {
            var initProductGallery = function() {
                var \$main = $('#mh-main-gallery-" . esc_js( $widget_id ) . "');
                var \$thumbs = $('#mh-thumb-gallery-" . esc_js( $widget_id ) . "');

                // Initialize Main Slider
                if (\$main.length && typeof $.fn.slick === 'function' && !\$main.hasClass('slick-initialized')) {
                    \$main.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: " . ( count( $all_image_ids ) > 1 ? 'true' : 'false' ) . ",
                        fade: false,
                        asNavFor: \$thumbs.length ? '#mh-thumb-gallery-" . esc_js( $widget_id ) . "' : null,
                        prevArrow: '#mh-main-prev-" . esc_js( $widget_id ) . "',
                        nextArrow: '#mh-main-next-" . esc_js( $widget_id ) . "'
                    });
                }

                // Initialize Thumbnail Slider
                if (\$thumbs.length && typeof $.fn.slick === 'function' && !\$thumbs.hasClass('slick-initialized')) {
                    \$thumbs.slick({
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        asNavFor: '#mh-main-gallery-" . esc_js( $widget_id ) . "',
                        dots: false,
                        arrows: true,
                        focusOnSelect: true,
                        prevArrow: '#mh-thumb-prev-" . esc_js( $widget_id ) . "',
                        nextArrow: '#mh-thumb-next-" . esc_js( $widget_id ) . "',
                        responsive: [
                            { breakpoint: 768, settings: { slidesToShow: 3 } },
                            { breakpoint: 480, settings: { slidesToShow: 2 } }
                        ]
                    });
                }
            };

            // Run immediately
            initProductGallery();

            // Run if loaded inside Elementor Editor
            if (typeof elementorFrontend !== 'undefined') {
                elementorFrontend.hooks.addAction('frontend/element_ready/mh_product_gallery.default', initProductGallery);
            }
        });
        ";
        wp_add_inline_script( 'jquery-core', $js );
        ?>
        <?php
    }
}