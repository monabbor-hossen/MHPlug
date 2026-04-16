<?php
/**
 * MH Product Image & Gallery Slider Widget
 *
 * Highly customizable, responsive WooCommerce Product Gallery.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;

class MH_Product_Gallery_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_gallery'; }
    public function get_title() { return __( 'MH Product Image & Gallery Slider', 'mh-plug' ); }
    public function get_icon() { return 'eicon-product-gallery'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'gallery', 'image', 'slider', 'woocommerce', 'mh', 'advanced', 'responsive' ]; }

    protected function register_controls() {

        /* ── CONTENT: LAYOUT & RESPONSIVE ── */
        $this->start_controls_section( 'section_layout', [
            'label' => __( 'Gallery Layout', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'gallery_width', [
            'label'      => __( 'Gallery Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'vw' ],
            'range'      => [ 'px' => [ 'min' => 200, 'max' => 1200 ], '%' => [ 'min' => 20, 'max' => 100 ] ],
            'default'    => [ 'size' => 100, 'unit' => '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-gallery-container' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%;',
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

        $this->add_responsive_control( 'align', [
            'label'   => __( 'Gallery Alignment', 'mh-plug' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'left'   => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default'   => 'center',
            'selectors' => [
                '{{WRAPPER}} .mh-product-gallery-container' => 'margin: 0 {{VALUE}} === "center" ? "auto" : "unset";',
                '{{WRAPPER}} .mh-product-gallery-container[style*="margin: 0 left"]' => 'margin: 0 auto 0 0;',
                '{{WRAPPER}} .mh-product-gallery-container[style*="margin: 0 right"]' => 'margin: 0 0 0 auto;',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: MAIN IMAGE & CONTAINER ── */
        $this->start_controls_section( 'style_main_image', [
            'label' => __( 'Main Image Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'main_image_border',
            'selector' => '{{WRAPPER}} .mh-gallery-main-viewport',
        ] );

        $this->add_responsive_control( 'main_image_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-gallery-main-viewport, {{WRAPPER}} .mh-gallery-main-viewport img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'main_image_shadow',
            'selector' => '{{WRAPPER}} .mh-gallery-main-viewport',
        ] );

        $this->end_controls_section();

        /* ── STYLE: GALLERY THUMBS ── */
        $this->start_controls_section( 'style_gallery_thumbs', [
            'label' => __( 'Gallery Thumbnails', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'thumbs_position', [
            'label'   => __( 'Position', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'bottom',
            'options' => [
                'bottom' => 'Bottom',
                'left'   => 'Left',
                'right'  => 'Right',
            ],
            'prefix_class' => 'mh-gallery-thumbs-',
            'selectors'    => [
                '{{WRAPPER}} .mh-product-gallery-container' => 'flex-direction: {{VALUE}} === "bottom" ? "column" : "row-reverse";',
                '{{WRAPPER}} .mh-product-gallery-container[class*="mh-gallery-thumbs-left"]' => 'flex-direction: row;',
            ],
        ] );

        $this->add_responsive_control( 'thumb_size', [
            'label'      => __( 'Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 40, 'max' => 150 ] ],
            'default'    => [ 'size' => 10, 'unit' => 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-gallery-thumbnails img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
            ],
        ] );

        $this->add_responsive_control( 'thumb_gap', [
            'label'      => __( 'Spacing', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'size' => 10, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-gallery-thumbnails' => 'gap: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-gallery-thumbnails-bottom' => 'margin-top: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'thumb_border_color', [
            'label'     => __( 'Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#e2e2e2',
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-thumbnails img' => 'border-color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'active_thumb_border_color', [
            'label'     => __( 'Active Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-thumbnails .active-thumb img' => 'border-color: {{VALUE}}; border-width: 2px;',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: NAVIGATION ELEMENTS ── */
        $this->start_controls_section( 'style_navigation', [
            'label' => __( 'Navigation Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'arrow_color', [
            'label'     => __( 'Arrow Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-arrow' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'arrow_background_color', [
            'label'     => __( 'Arrow Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(0,0,0,0.5)',
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-arrow' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'arrow_size', [
            'label'      => __( 'Arrow Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 15, 'max' => 60 ] ],
            'default'    => [ 'size' => 10, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-gallery-arrow' => 'font-size: {{SIZE}}{{UNIT}}; padding: {{SIZE}}{{UNIT}} === "px" ? 1.5 : 0.5 * {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
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

        $main_image_id = $product->get_image_id();
        $gallery_ids    = $product->get_gallery_image_ids();
        
        // Final fallback if the product has zero images
        if ( ! $main_image_id && empty( $gallery_ids ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="color:#999; font-size:13px; text-align:center;"><em>This product has no images to display.</em></div>';
            }
            return; 
        }

        // Render the gallery container with standard class for theme CSS compatibility
        ?>
        <div class="mh-product-gallery-container woocommerce-product-gallery" style="display: flex; position: relative;">
            
            <?php // ── Main Slider Viewport ── ?>
            <div class="mh-gallery-main-viewport" style="flex-grow: 1; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center; border-style: solid; border-width: 0;">
                
                <?php if ( $main_image_id ) : ?>
                    <div class="mh-gallery-slide active-slide" style="width: 100%; height: auto; position: absolute; transition: opacity 0.5s ease; opacity: 1;">
                        <?php echo wp_get_attachment_image( $main_image_id, 'woocommerce_single' ); ?>
                    </div>
                <?php endif; ?>

                <?php 
                // Render additional gallery slides (hidden by default)
                if ( ! empty( $gallery_ids ) ) :
                    foreach ( $gallery_ids as $attachment_id ) : ?>
                        <div class="mh-gallery-slide" style="width: 100%; height: auto; position: absolute; transition: opacity 0.5s ease; opacity: 0;">
                            <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_single' ); ?>
                        </div>
                    <?php endforeach;
                endif; ?>

                <?php // ── Navigation Arrows ── ?>
                <div class="mh-gallery-arrow mh-gallery-prev" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); border-radius: 50%; cursor: pointer;">
                    <i class="eicon-chevron-left"></i>
                </div>
                <div class="mh-gallery-arrow mh-gallery-next" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border-radius: 50%; cursor: pointer;">
                    <i class="eicon-chevron-right"></i>
                </div>
            </div>

            <?php // ── Gallery Thumbnails ── ?>
            <?php if ( ! empty( $gallery_ids ) ) : ?>
                <div class="mh-gallery-thumbnails mh-gallery-thumbnails-bottom" style="display: flex; flex-wrap: wrap; justify-content: flex-start; margin-top: 10px;">
                    
                    <?php if ( $main_image_id ) : ?>
                        <div class="mh-gallery-thumbnail-item active-thumb" style="cursor: pointer;">
                            <?php echo wp_get_attachment_image( $main_image_id, 'woocommerce_thumbnail', false, [ 'style' => 'border-style: solid; border-width: 1px;' ] ); ?>
                        </div>
                    <?php endif; ?>

                    <?php foreach ( $gallery_ids as $attachment_id ) : ?>
                        <div class="mh-gallery-thumbnail-item" style="cursor: pointer;">
                            <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_thumbnail', false, [ 'style' => 'border-style: solid; border-width: 1px;' ] ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
        <?php
    }
}