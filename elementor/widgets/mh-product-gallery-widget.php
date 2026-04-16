<?php
/**
 * MH Product Image & Gallery Slider Widget
 *
 * Fully advanced, beautiful e-commerce Product Gallery with Slick Slider synchronization and full customization control.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

class MH_Product_Gallery_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_gallery'; }
    public function get_title() { return __( 'MH Product Image & Gallery Slider', 'mh-plug' ); }
    public function get_icon() { return 'eicon-product-gallery'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'gallery', 'image', 'slider', 'woocommerce', 'mh', 'advanced', 'beautiful', 'responsive' ]; }

    protected function register_controls() {

        /* ── CONTENT: RESPONSIVE LAYOUT & BEHAVIOR ── */
        $this->start_controls_section( 'section_layout', [
            'label' => __( 'Gallery Behavior & Layout', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'gallery_width', [
            'label'      => __( 'Gallery Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'vw' ],
            'range'      => [ 'px' => [ 'min' => 200, 'max' => 1200 ], '%' => [ 'min' => 20, 'max' => 100 ] ],
            'default'    => [ 'size' => 100, 'unit' => '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-advanced-gallery-wrapper' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'main_image_ratio', [
            'label'   => __( 'Main Image Aspect Ratio', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '1:1',
            'options' => [
                '1:1'   => '1:1',
                '4:3'   => '4:3',
                '16:9'  => '16:9',
                'auto'  => 'Auto (Native Image Size)',
            ],
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-main-viewport' => 'aspect-ratio: {{VALUE}};',
                '{{WRAPPER}} .mh-gallery-main-viewport[style*="aspect-ratio: auto"]' => 'aspect-ratio: unset;',
            ],
        ] );

        $this->add_responsive_control( 'thumbs_position', [
            'label'   => __( 'Thumbnails Position (Desktop Only)', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'bottom',
            'options' => [
                'bottom' => 'Bottom (Grid)',
                'left'   => 'Left (Vertical List)',
                'right'  => 'Right (Vertical List)',
            ],
            'prefix_class' => 'mh-advanced-gallery-thumbs-',
            'selectors'    => [
                // CSS to stack or align vertically
                '{{WRAPPER}} .mh-advanced-gallery-wrapper' => 'flex-direction: {{VALUE}} === "bottom" ? "column" : "row-reverse";',
                '{{WRAPPER}} .mh-advanced-gallery-wrapper[class*="mh-advanced-gallery-thumbs-left"]' => 'flex-direction: row;',
            ],
        ] );

        $this->add_control( 'transition_type', [
            'label'   => __( 'Slide Transition Type', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'fade',
            'options' => [
                'fade'  => 'Fade (Modern Minimal)',
                'slide' => 'Slide (Dynamic)',
            ],
            'separator' => 'before',
        ] );

        $this->add_control( 'autoplay', [
            'label'        => __( 'Autoplay', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'On', 'mh-plug' ),
            'label_off'    => __( 'Off', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'no',
        ] );

        $this->add_control( 'autoplay_speed', [
            'label'     => __( 'Autoplay Speed (ms)', 'mh-plug' ),
            'type'      => Controls_Manager::NUMBER,
            'default'   => 5000,
            'condition' => [ 'autoplay' => 'yes' ],
        ] );

        $this->add_control( 'loop', [
            'label'        => __( 'Infinite Loop', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'On', 'mh-plug' ),
            'label_off'    => __( 'Off', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_dots_on_mobile', [
            'label'        => __( 'Show Pagination Dots (Mobile Only)', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition'   => [ 'show_navigation' => 'no' ],
        ] );

        $this->end_controls_section();

        /* ── CONTENT: NAVIGATION ELEMENTS ── */
        $this->start_controls_section( 'section_navigation', [
            'label' => __( 'Navigation (Arrows)', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'show_navigation', [
            'label'        => __( 'Show Arrows on Hover', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'navigation_position', [
            'label'   => __( 'Arrows Vertical Alignment', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'center',
            'options' => [
                'center' => 'Center',
                'top'    => 'Top',
                'bottom' => 'Bottom',
            ],
            'selectors' => [
                '{{WRAPPER}} .mh-advanced-gallery-navigation' => 'top: {{VALUE}} === "center" ? "50%" : "{{VALUE}} === "top" ? "10%" : "90%"; transform: translateY(-50%);',
            ],
            'condition' => [ 'show_navigation' => 'yes' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: BEAUTIFUL DEFAULTS — MAIN GALLERY CONTAINER ── */
        $this->start_controls_section( 'style_main_gallery', [
            'label' => __( 'Overall Gallery (Beautiful Defaults)', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'advanced_gallery_heading', [
            'label'     => __( 'This section applies modern beautiful defaults.', 'mh-plug' ),
            'type'      => Controls_Manager::HEADING,
        ] );

        // 🚀 BEAUTIFUL DEFAULT: Apply soft shadow by default
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'advanced_gallery_shadow',
            'selector' => '{{WRAPPER}} .woocommerce-product-gallery__trigger',
            'default'   => [
                'horizontal' => 0,
                'vertical'   => 15,
                'blur'       => 50,
                'spread'     => 0,
                'color'      => 'rgba(0,0,0,0.2)',
            ],
        ] );

        // 🚀 BEAUTIFUL DEFAULT: Default Soft Rounded Corners
        $this->add_responsive_control( 'main_image_border_radius', [
            'label'      => __( 'Rounded Corners Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'default'    => [ 'size' => 12, 'unit' => 'px' ], // Soft curve
            'selectors'  => [
                '{{WRAPPER}} .woocommerce-product-gallery__trigger, {{WRAPPER}} .woocommerce-product-gallery__trigger img, {{WRAPPER}} .mh-gallery-thumb-slide img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: GALLERY THUMBNAILS ── */
        $this->start_controls_section( 'style_gallery_thumbs', [
            'label' => __( 'Gallery Thumbnails Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'thumb_columns', [
            'label'   => __( 'Visible Thumbnails (Desktop)', 'mh-plug' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 4,
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-thumb-slider' => '--slick-slides-show: {{VALUE}};',
            ],
            'prefix_class' => 'mh-gallery-thumbs-grid-',
        ] );

        $this->add_responsive_control( 'thumb_gap', [
            'label'      => __( 'Spacing (Gap)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'    => [ 'size' => 15, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .slick-list.mh-advanced-gallery-thumbnails__slider' => 'margin: 0 calc(-1 * {{SIZE}}{{UNIT}});',
                '{{WRAPPER}} .mh-gallery-thumb-slide' => 'padding: 0 {{SIZE}}{{UNIT}};',
            ],
        ] );

        // Apply distinct beautiful accent border radius control to thumbnails too
        $this->add_responsive_control( 'thumb_border_radius', [
            'label'      => __( 'Rounded Corners Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'default'    => [ 'size' => 8, 'unit' => 'px' ], 
            'selectors'  => [
                '{{WRAPPER}} .mh-gallery-thumb-slide img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->start_controls_tabs( 'thumbs_style_tabs' );

        /* Normal Thumbnails */
        $this->start_controls_tab( 'tab_thumbs_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        
        // Soften default thumb border
        $this->add_control( 'thumb_border_color', [
            'label'     => __( 'Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f3f3f3', // Very faint default border
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-thumb-slide img' => 'border-style: solid; border-width: 1px; border-color: {{VALUE}}; transition: all 0.3s ease;',
            ],
        ] );

        $this->end_controls_tab();

        /* Active Thumbnail (Beauty Accent) */
        $this->start_controls_tab( 'tab_thumbs_active', [ 'label' => __( 'Active (ACCENT)', 'mh-plug' ) ] );

        // 🚀 BEAUTIFUL ACCENT DEFAULT: Standard WooCommerce Accent Color
        $this->add_control( 'active_thumb_border_color', [
            'label'     => __( 'Border Color (ACCENT)', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [
                // Highlight active with accent border and 1px spread shadow for depth
                '{{WRAPPER}} .mh-gallery-thumb-slide.slick-current img' => 'border-color: {{VALUE}}; border-width: 2px; box-shadow: 0 0 5px rgba(214, 54, 56, 0.4);', 
            ],
        ] );
        
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /* ── STYLE: NAVIGATION ELEMENTS (ARROWS) ── */
        $this->start_controls_section( 'style_navigation', [
            'label'     => __( 'Navigation (Arrows) Style', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_navigation' => 'yes' ],
        ] );

        // Standard colors by default (semi-transparent background)
        $this->add_control( 'arrow_background_color', [
            'label'     => __( 'Arrow Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(0,0,0,0.6)',
            'selectors' => [
                '{{WRAPPER}} .mh-advanced-gallery-navigation' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'arrow_color', [
            'label'     => __( 'Arrow Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .mh-advanced-gallery-navigation' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'arrow_size', [
            'label'      => __( 'Arrow Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 60 ] ],
            'default'    => [ 'size' => 14, 'unit' => 'px' ], // Clean minimal default size
            'selectors'  => [
                '{{WRAPPER}} .mh-advanced-gallery-navigation' => 'font-size: {{SIZE}}{{UNIT}}; padding: {{SIZE}}{{UNIT}}; height: calc({{SIZE}}{{UNIT}} * 2.5); width: calc({{SIZE}}{{UNIT}} * 2.5); line-height: calc({{SIZE}}{{UNIT}} * 2.5);',
            ],
        ] );

        // 🚀 BEAUTIFUL DEFAULT: Circular Buttons with perfect centering
        $this->add_control( 'arrow_radius', [
            'label'     => __( 'Rounded Corners Radius', 'mh-plug' ),
            'type'      => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'default'    => [ 'size' => 50, 'unit' => '%' ], // Perfect circle default
            'selectors'  => [
                '{{WRAPPER}} .mh-advanced-gallery-navigation' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        // Add proper modern hover interaction: scale up!
        $this->add_control( 'arrow_hover_scale', [
            'label'      => __( 'Hover Transformation (Scale Up)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ], // Not using units, just scale factor
            'range'      => [ 'px' => [ 'min' => 1, 'max' => 2, 'step' => 0.05 ] ],
            'default'    => [ 'size' => 1.1 ], // Standard soft modern upscale
            'selectors'  => [
                '{{WRAPPER}} .mh-advanced-gallery-navigation:hover' => 'transform: translateY(-50%) scale({{SIZE}});',
            ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        // Standard Elementor pattern
        $settings = $this->get_settings_for_display();

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                $mock_products = wc_get_products( [ 'limit' => 1, 'status' => 'publish' ] );
                if ( ! empty( $mock_products ) ) $product = $mock_products[0];
            }
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 15px; border: 1px dashed #d63638; color: #d63638; text-align: center;"><strong>MH Plug:</strong> Please create a product to preview the gallery.</div>';
            }
            return;
        }

        // Get WooCommerce data
        $main_image_id = $product->get_image_id();
        $gallery_ids    = $product->get_gallery_image_ids();
        
        if ( ! $main_image_id && empty( $gallery_ids ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="color:#999; font-size:13px; text-align:center;"><em>This product has no images to display.</em></div>';
            }
            return; 
        }

        // Apply distinct beautiful accent border radius control to thumbnails too
        ?>
        <div class="mh-product-gallery-container woocommerce-product-gallery" data-mh-settings="<?php echo esc_attr( json_encode( $settings ) ); ?>" style="position: relative;">
            
            <div class="mh-advanced-gallery-wrapper" style="display: flex;">
                
                <?php // ────────────────────────────────────────────────────────
                // MAIN IMAGE SLIDER VIEWPORT
                // ──────────────────────────────────────────────────────── ?>
                <div class="mh-gallery-main-viewport" style="flex-grow: 1; overflow: hidden; position: relative;">
                    
                    <?php if ( $main_image_id ) : ?>
                        <div class="woocommerce-product-gallery__trigger mh-gallery-main-slide">
                            <?php echo wp_get_attachment_image( $main_image_id, 'woocommerce_single' ); ?>
                        </div>
                    <?php endif; ?>

                    <?php 
                    if ( ! empty( $gallery_ids ) ) :
                        foreach ( $gallery_ids as $attachment_id ) : ?>
                            <div class="mh-gallery-main-slide">
                                <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_single' ); ?>
                            </div>
                        <?php endforeach;
                    endif; ?>

                    <?php // Custom Navigation Arrows Container
                    if ( $settings['show_navigation'] === 'yes' ) : 
                    ?>
                        <div class="mh-advanced-gallery-navigation-wrap" style="position: absolute; top: 0; left: 0; height: 100%; width: 100%; pointer-events: none;">
                            <div class="mh-advanced-gallery-navigation mh-gallery-prev" style="pointer-events: auto; position: absolute; left: 15px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                <i class="eicon-chevron-left"></i>
                            </div>
                            <div class="mh-advanced-gallery-navigation mh-gallery-next" style="pointer-events: auto; position: absolute; right: 15px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                <i class="eicon-chevron-right"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php // ────────────────────────────────────────────────────────
                // THUMBNAILS SLIDER (linked to main)
                // ──────────────────────────────────────────────────────── ?>
                <?php if ( ! empty( $gallery_ids ) ) : ?>
                    <div class="mh-gallery-thumb-slider-container mh-advanced-gallery-thumbnails woocommerce-product-gallery__thumbnails" style="pointer-events: auto;">
                        <div class="mh-gallery-thumb-slider slick-list mh-advanced-gallery-thumbnails__slider">
                            
                            <?php if ( $main_image_id ) : ?>
                                <div class="mh-gallery-thumb-slide">
                                    <?php echo wp_get_attachment_image( $main_image_id, 'woocommerce_thumbnail' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php foreach ( $gallery_ids as $attachment_id ) : ?>
                                <div class="mh-gallery-thumb-slide">
                                    <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_thumbnail' ); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>
        <?php
    }
}