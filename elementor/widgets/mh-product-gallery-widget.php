<?php
/**
 * MH Product Image & Gallery Slider Widget
 *
 * Premium E-Commerce Layout (Main Image + Floating Arrows + Bottom Thumbnails)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

class MH_Product_Gallery_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_gallery'; }
    public function get_title() { return __( 'MH Product Gallery', 'mh-plug' ); }
    public function get_icon() { return 'eicon-product-gallery'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'gallery', 'image', 'slider', 'woocommerce', 'mh' ]; }

    protected function register_controls() {

        /* ── CONTENT: RESPONSIVE LAYOUT & BEHAVIOR ── */
        $this->start_controls_section( 'section_layout', [
            'label' => __( 'Gallery Layout', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'gallery_width', [
            'label'      => __( 'Gallery Max Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [ 'px' => [ 'min' => 200, 'max' => 1200 ], '%' => [ 'min' => 20, 'max' => 100 ] ],
            'default'    => [ 'size' => 100, 'unit' => '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-premium-gallery-container' => 'max-width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
            ],
        ] );

        $this->add_control( 'transition_type', [
            'label'   => __( 'Transition Type', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'fade',
            'options' => [
                'fade'  => 'Fade (Smooth)',
                'slide' => 'Slide',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: MAIN IMAGE & ARROWS ── */
        $this->start_controls_section( 'style_main_gallery', [
            'label' => __( 'Main Image & Arrows', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'main_image_border_radius', [
            'label'      => __( 'Image Rounded Corners', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'default'    => [ 'top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10, 'unit' => 'px', 'isLinked' => true ],
            'selectors'  => [
                '{{WRAPPER}} .mh-main-slide-item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'arrow_color', [
            'label'     => __( 'Arrow Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#111111',
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-arrow' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'arrow_bg_color', [
            'label'     => __( 'Arrow Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .mh-gallery-arrow' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'arrow_shadow',
            'label'    => __( 'Arrow Shadow', 'mh-plug' ),
            'selector' => '{{WRAPPER}} .mh-gallery-arrow',
        ] );

        $this->end_controls_section();

        /* ── STYLE: THUMBNAILS ── */
        $this->start_controls_section( 'style_gallery_thumbs', [
            'label' => __( 'Thumbnails', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'thumb_gap', [
            'label'      => __( 'Spacing Between Thumbs', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'size' => 10, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-gallery-thumb-slider' => 'margin: 0 calc(-{{SIZE}}{{UNIT}} / 2);',
                '{{WRAPPER}} .mh-thumb-slide-item' => 'padding: 0 calc({{SIZE}}{{UNIT}} / 2);',
            ],
        ] );

        $this->add_control( 'active_thumb_border', [
            'label'     => __( 'Active Thumbnail Accent', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#000000',
            'selectors' => [
                '{{WRAPPER}} .mh-thumb-slide-item.slick-current img' => 'border: 2px solid {{VALUE}}; opacity: 1;',
                '{{WRAPPER}} .mh-thumb-slide-item img' => 'opacity: 0.6; transition: 0.3s ease; border: 2px solid transparent;',
                '{{WRAPPER}} .mh-thumb-slide-item:hover img' => 'opacity: 1;',
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
            return;
        }

        $main_image_id = $product->get_image_id();
        $gallery_ids   = $product->get_gallery_image_ids();
        
        if ( ! $main_image_id && empty( $gallery_ids ) ) {
            return; 
        }

        ?>
        <style>
            .mh-premium-gallery-container { width: 100%; display: flex; flex-direction: column; }
            
            /* Main Image Area */
            .mh-main-slider-wrapper { position: relative; width: 100%; margin-bottom: 15px; }
            .mh-gallery-main-viewport { width: 100%; overflow: hidden; }
            .mh-main-slide-item img { width: 100%; height: auto; display: block; object-fit: contain; aspect-ratio: 1/1; background: #f9f9f9; }
            
            /* Floating Arrows (Over Image) */
            .mh-gallery-arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; cursor: pointer; z-index: 10; font-size: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: 0.3s; }
            .mh-gallery-arrow:hover { transform: translateY(-50%) scale(1.1); }
            .mh-gallery-prev { left: 10px; }
            .mh-gallery-next { right: 10px; }
            
            /* Thumbnails Area */
            .mh-thumb-slider-wrapper { width: 100%; padding: 0 10px; }
            .mh-thumb-slide-item { cursor: pointer; outline: none; }
            .mh-thumb-slide-item img { width: 100%; height: auto; display: block; aspect-ratio: 1/1; object-fit: cover; border-radius: 6px; }
            
            /* Fallback before Slick loads */
            .mh-gallery-thumb-slider:not(.slick-initialized) { display: flex; overflow: hidden; }
            .mh-gallery-thumb-slider:not(.slick-initialized) .mh-thumb-slide-item { width: 25%; flex-shrink: 0; }
        </style>

        <div class="mh-premium-gallery-container" data-mh-settings="<?php echo esc_attr( wp_json_encode( $settings ) ); ?>">
            
            <div class="mh-main-slider-wrapper">
                <div class="mh-gallery-main-viewport">
                    <?php if ( $main_image_id ) : ?>
                        <div class="mh-main-slide-item">
                            <?php echo wp_get_attachment_image( $main_image_id, 'woocommerce_single' ); ?>
                        </div>
                    <?php endif; ?>
                    <?php foreach ( $gallery_ids as $attachment_id ) : ?>
                        <div class="mh-main-slide-item">
                            <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_single' ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mh-gallery-arrow mh-gallery-prev"><i class="eicon-chevron-left"></i></div>
                <div class="mh-gallery-arrow mh-gallery-next"><i class="eicon-chevron-right"></i></div>
            </div>

            <?php if ( ! empty( $gallery_ids ) ) : ?>
                <div class="mh-thumb-slider-wrapper">
                    <div class="mh-gallery-thumb-slider">
                        <?php if ( $main_image_id ) : ?>
                            <div class="mh-thumb-slide-item">
                                <?php echo wp_get_attachment_image( $main_image_id, 'woocommerce_thumbnail' ); ?>
                            </div>
                        <?php endif; ?>
                        <?php foreach ( $gallery_ids as $attachment_id ) : ?>
                            <div class="mh-thumb-slide-item">
                                <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_thumbnail' ); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <?php
    }
}